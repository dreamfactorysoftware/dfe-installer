<?php namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Traits\Guzzler;
use Illuminate\Http\Request;

class GuzzleService extends BaseService
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use Guzzler;

    //*************************************************************************
    //* Methods
    //*************************************************************************

    /**
     * Initialize and set up the transport layer
     *
     * @param string $url          The url of the app server to use
     * @param string $clientId     Your application's client ID
     * @param string $clientSecret Your application's secret ID
     * @param array  $config       Any GuzzleHttp options
     *
     * @return $this
     */
    public function connect($url, $clientId, $clientSecret, $config = [])
    {
        return $this->createRequest($url, ['client-id' => $clientId, 'client-secret' => $clientSecret], $config);
    }

    /**
     * Perform a GET
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options Any guzzlehttp options
     * @param bool   $object  If true, results are returned as an object. Otherwise data is in array form
     *
     * @return array|bool
     */
    public function get($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleGet($uri, $payload, $options, $object);
    }

    /**
     * Perform a POST
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options Any guzzlehttp options
     * @param bool   $object  If true, results are returned as an object. Otherwise data is in array form
     *
     * @return \stdClass|array|bool
     */
    public function post($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzlePost($uri, $payload, $options, $object);
    }

    /**
     * Perform a DELETE
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options Any guzzlehttp options
     * @param bool   $object  If true, results are returned as an object. Otherwise data is in array form
     *
     * @return \stdClass|array|bool
     */
    public function delete($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzleDelete($uri, $payload, $options, $object);
    }

    /**
     * Perform a PUT
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options Any guzzlehttp options
     * @param bool   $object  If true, results are returned as an object. Otherwise data is in array form
     *
     * @return \stdClass|array|bool
     */
    public function put($uri, $payload = [], $options = [], $object = true)
    {
        return $this->guzzlePut($uri, $payload, $options, $object);
    }

    /**
     * Handle any other requests as POSTs
     *
     * @param string $uri
     * @param array  $payload
     * @param array  $options Any guzzlehttp options
     * @param string $method
     * @param bool   $object  If true, results are returned as an object. Otherwise data is in array form
     *
     * @return array|bool|\stdClass
     */
    public function any($uri, $payload = [], $options = [], $method = Request::METHOD_POST, $object = true)
    {
        return $this->guzzleAny($uri, $payload, $options, $method, $object);
    }
}
