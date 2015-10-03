<?php
//******************************************************************************
//* Form logic pulled in here...
//******************************************************************************
require __DIR__ . '/form-logic.php';
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>DreamFactory on Verizon Cloud</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="//fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">

    <style type="text/css">
        html, body {
            width:      100%;
            min-width:  550px;
            height:     100%;
            max-height: 100%;
        }

        body {
            font-family: "Open Sans", "Droid Sans", Arial, sans-serif;
        }

        .navbar-inverse {
            background-color: #000000;
        }

        .navbar-title-row {
            margin-top: 10px;
        }

        .navbar-title {
            color:     rgba(255, 255, 255, 1.0);
            font-size: 3em;
            margin:    5px 0 0;
            padding:   0;
        }

        .align-left {
            text-align: left;
        }

        .align-right {
            text-align: right;
        }

        .align-center {
            text-align: center;
        }

        .btn-create {
            color:                 #333;
            background-color:      #FA2;
            border-radius:         5px;
            -moz-border-radius:    5px;
            -webkit-border-radius: 5px;
            border:                none;
            font-size:             16px;
            font-weight:           700;
            height:                32px;
            padding:               4px 16px;
            width:                 200px;
        }

        label {
            display: inline-block;
            width:   150px;
        }

        .has-error {
            font-size:   11px;
            text-align:  left;
            margin-left: 150px;
            color:       darkred;
        }

        ul {
            font-size: 13px;
        }

        input {
            width: 350px;
        }

        p {
            font-size: 16px;
        }

        @media (max-width: 2048px) {
            .navbar-title {
                font-size: 3.5em;
                margin:    20px 0 0;
            }
        }

        @media (max-width: 1415px) {

            .navbar-title {
                margin: 5px 0 0;
            }
        }

        @media (max-width: 960px) {

            .navbar-title {
                font-size: 2.5em;
                margin:    5px 0 0;
            }
        }

    </style>

    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!--[if lt IE 9]>
    <script src="//oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js"></script>
    <script src="//oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body style="margin: 0;">

<div style="width: 100%; height: 124px; background: black; margin: 0;">
    <img src="images/factory_right.png" style="float: right;">
    <img src="images/verizon_left.png" style="float: left;">

    <p style="color: white; font-size: 4vw; line-height: 120%; text-align: center;">DreamFactory Hosted on Verizon
        Cloud</p>
</div>

<div class="container">
    <div class="row" style="text-align: center;">
        <div class="col-xs-6 col-sm-6 col-md-6"
             style="display: inline-block; float: none; width: 550px; text-align: left; vertical-align: top;">

            <h3>The DreamFactory Open Source REST API</h3>

            <p>DreamFactory provides all of the RESTful web services you need to build fantastic mobile, web, and IoT
                applications. Sign up below for a Free Developer Environment hosted on the Verizon Cloud and get started
                today.</p>

            <ul>
                <li>Connect to any backend data source: SQL, NoSQL, or Files</li>
                <li>Instantly get a full palette of REST APIs with live documentation</li>
                <li>Set up users and role-based access controls for data security</li>
                <li>Client SDKs for Android, iOS, HTML5, AngularJS, jQuery, more</li>
                <li>There are no time or transaction limits on your developer account</li>
                <li>Free support for first 30 days (<a href="https://www.dreamfactory.com/developers/support">extended
                        support plans available</a>)
                </li>
            </ul>

            <div class="align-center" style="margin: 35px 0;">
                <img src="images/verizon_diagram.jpg">
            </div>
            <br>

            <p>DreamFactory is a free, open source RESTful backend integration platform for mobile, web, and IoT
                applications. It provides RESTful web services for any data source so you can start front-end
                development with robust REST APIs on day one.</p>

            <p>DreamFactory provides pre-built connectors to SQL, NoSQL, file storage systems, and web services. With a
                few clicks, you instantly get a comprehensive palette of secure, reliable, customizable REST APIs and
                live API documentation.</p>

            <p>Here is a short movie that shows how to use the DreamFactory Admin Console and set up your backend
                platform. Many additional assets and full documentation are available in the Admin Console.</p>

        </div>
        <div class="col-xs-6 col-sm-6 col-md-6"
             style="display: inline-block; float: none; width: 550px; text-align: left; vertical-align: top;">

            <h3>Verizon Cloud Spaces Meet Your Demands</h3>

            <p>​Verizon knows that no single solution fits all. That's why Verizon created a cloud that helps you do
                more.</p>

            <ul>
                <li>Flexible deployment model</li>
                <li>Centralized management from a single user interface</li>
                <li>Flexible managed services to match your needs</li>
            </ul>

            <p>Sign up for a free developer sandbox environment and try DreamFactory hosted on Verizon Cloud. You can
                move your DreamFactory instance to a new or existing Verizon Cloud account at any time for further
                evaluation or production.</p>
            <br>

            <p style="font-size: 24px; font-weight: bold;">Sign up for your Free Developer<br>Environment today:</p>

            <p style="font-size: 13px;">Already registered? <a href="https://dashboard.vz.dreamfactory.com/">Click here
                    to log in to the Enterprise Dashboard</a></p>

            <form method="post">

                <label>First Name *</label><input type="text" name="firstname" value="<?php echo $firstname; ?>">
                <br>

                <p class="has-error"><?php echo $firstNameErr; ?></p>

                <label>Last Name *</label><input type="text" name="lastname" value="<?php echo $lastname; ?>">
                <br>

                <p class="has-error"><?php echo $lastNameErr; ?></p>

                <label>E-mail *</label><input type="text" name="email" value="<?php echo $email; ?>">
                <br>

                <p class="has-error"><?php echo $emailErr; ?></p>

                <label>Password *</label><input type="password" name="password" value="<?php echo $password; ?>">
                <br>

                <p class="has-error"><?php echo $passwordErr; ?></p>

                <label>Confirm Password *</label><input type="password" name="confirm" value="<?php echo $confirm; ?>">
                <br>

                <p class="has-error"><?php echo $confirmErr; ?></p>

                <label>Phone Number *</label><input type="text" name="phone" value="<?php echo $phone; ?>">
                <br>

                <p class="has-error"><?php echo $phoneErr; ?></p>

                <label>Company Name *</label><input type="text" name="company" value="<?php echo $company; ?>">
                <br>

                <p class="has-error"><?php echo $companyErr; ?></p>

                <div style="margin: 15px 0;">
                    <button type="submit" class="btn btn-warning">Create Account</button>
                </div>

            </form>

            <br><br>

            <div style="margin: 15px auto;" class="align-center">
                <iframe seamless="seamless"
                        width="420"
                        height="315"
                        src="//www.youtube.com/embed/hxOMl8V6GRQ"
                        align="center"
                        frameborder="0"
                        allowfullscreen>
                </iframe>
            </div>

        </div>
    </div>
</div>

<p style="margin-top: 25px; text-align: center;">© <?php echo date("Y"); ?> DreamFactory Software, Inc.</p>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
</body>
</html>
