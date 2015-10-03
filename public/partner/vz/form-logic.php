<?php
//******************************************************************************
//* Form logic
//******************************************************************************

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

$firstNameErr = $emailErr = $lastNameErr = $passwordErr = $phoneErr = $companyErr = $confirmErr = null;
$firstname = $email = $lastname = $password = $confirm = $phone = $company = null;

if ('POST' == $_SERVER['REQUEST_METHOD']) {
    if (empty($_POST['firstname'])) {
        $firstNameErr = 'First name is required';
    } else {
        $firstname = test_input($_POST['firstname']);
        if (!preg_match('/^[a-zA-Z ]*$/', $firstname)) {
            $firstNameErr = 'Only letters and white space allowed';
        }
    }

    if (empty($_POST['lastname'])) {
        $lastNameErr = 'Last name is required';
    } else {
        $lastname = test_input($_POST['lastname']);
        if (!preg_match('/^[a-zA-Z ]*$/', $lastname)) {
            $lastNameErr = 'Only letters and white space allowed';
        }
    }

    if (empty($_POST['company'])) {
        $companyErr = 'Company name is required';
    } else {
        $company = test_input($_POST['company']);
        if (!preg_match('/^[a-zA-Z ]*$/', $company)) {
            $companyErr = 'Only letters and white space allowed';
        }
    }

    if (empty($_POST['phone'])) {
        $phoneErr = 'Phone number is required';
    } else {
        $phone = test_input($_POST['phone']);
        if (!preg_match('/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/', $phone)) {
            $phoneErr = 'Only numbers and dashes allowed';
        }
    }

    if (empty($_POST['email'])) {
        $emailErr = 'Email is required';
    } else {
        $email = test_input($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailErr = 'Invalid email format';
        }
    }

    if (empty($_POST['password'])) {
        $passwordErr = 'Password is required';
    } else {
        $password = test_input($_POST['password']);
        if (strlen($password) < 3) {
            $passwordErr = 'Password must have at least 3 characters';
        }
    }

    if ($_POST['password'] != $_POST['confirm']) {
        $confirmErr = 'Passwords do not match';
    } else {
        $confirm = test_input($_POST['confirm']);
    }

    if ($firstNameErr == '' and $emailErr == '' and $lastNameErr == '') {
        if ($passwordErr == '' and $phoneErr == '' and $companyErr == '' and $confirmErr == '') {
            post_dreamfactory($firstname, $lastname, $email, $phone, $company, $password);
            post_hubspot($firstname, $lastname, $email, $phone, $company);
            //  If we get here, there was an error or no redirect...
        }
    }
}

function post_dreamfactory($fn, $ln, $em, $ph, $co, $pw)
{
    $_postData = [
        'pid'       => 'vz',
        'command'   => 'register',
        'firstname' => $fn,
        'lastname'  => $ln,
        'email'     => $em,
        'phone'     => $ph,
        'company'   => $co,
        'password'  => $pw,
    ];

    $endpoint = 'https://console.vz.dreamfactory.com/api/v1/ops/partner';

    $ch = @curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_postData, JSON_UNESCAPED_SLASHES));
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json',]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = @curl_exec($ch);
    $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
    @curl_close($ch);

    is_string($response) && ($response = json_decode($response, true));
    _log('df post ' . $status_code . ': ' . print_r($response, true), true);

    return $status_code;
}

function post_hubspot($fn, $ln, $em, $ph, $co)
{
    $hs_context = [
        'hutk'        => isset($_COOKIE, $_COOKIE['hubspotutk']) ? $_COOKIE['hubspotutk'] : null,
        'ipAddress'   => isset($_SERVER, $_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
        'pageUrl'     => 'verizon.dreamfactory.com',
        'pageName'    => 'DreamFactory on Verizon Cloud',
        //  FYI: this value will not be used if the landing page has a redirect defined in hubspot
        'redirectUrl' => 'https://dashboard.vz.dreamfactory.com/?pid=vz',
    ];
    $hs_context_json = json_encode($hs_context, JSON_UNESCAPED_SLASHES);

    $str_post = 'firstname=' . urlencode($fn)
        . '&lastname=' . urlencode($ln)
        . '&email=' . urlencode($em)
        . '&phone=' . urlencode($ph)
        . '&company=' . urlencode($co)
        . '&mobile_lead=No'
        . '&installation_source=Verizon'
        . '&website_lead_source=verizon.dreamfactory.com'
        . '&local_installation=No'
        . '&local_installation_skipped=No'
        . '&hs_context=' . urlencode($hs_context_json);

    $endpoint = 'https://forms.hubspot.com/uploads/form/v2/247169/d48b5b8e-2274-488b-9448-156965d38048';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $str_post);
    curl_setopt($ch, CURLOPT_URL, $endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded',]);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    $response = @curl_exec($ch);
    $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);

    _log('hs post ' . $status_code . ': ' . print_r($response, true), true);

    if (400 > $status_code) {
        $_headers = _parseHeaders($response);

        $_location =
            (isset($_headers['location'])
                ? $_headers['location']
                : 'https://dashboard.vz.dreamfactory.com/?pid=vz&submissionGuid=false') .
            '&pem=' . urlencode($em);

        _log('--==**>> redirecting "' . $em . '" to ' . $_location);
        header('Location: ' . $_location);
        die();
    }

    @curl_close($ch);

    return $status_code;
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

function _log($message, $header = false)
{
    $header && error_log('----------------------------------------------------------------------');

    error_log($message . PHP_EOL, 3, __DIR__ . '/log');
}

function _parseHeaders($headers)
{
    $_result = [];
    $_fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $headers));
    foreach ($_fields as $_field) {
        if (preg_match('/([^:]+): (.+)/m', $_field, $_match)) {
            $_match[1] = strtolower(trim($_match[1]));
            if (isset($_result[$_match[1]])) {
                $_result[$_match[1]] = [$_result[$_match[1]], $_match[2]];
            } else {
                $_result[$_match[1]] = trim($_match[2]);
            }
        }
    }

    return $_result;
}
