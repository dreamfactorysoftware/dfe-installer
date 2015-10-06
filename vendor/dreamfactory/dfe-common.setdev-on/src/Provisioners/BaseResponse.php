<?php namespace DreamFactory\Enterprise\Common\Provisioners;

use DreamFactory\Enterprise\Common\Traits\HasResults;
use DreamFactory\Enterprise\Common\Traits\HasTimer;
use Illuminate\Http\Response;

class BaseResponse extends Response
{
    //******************************************************************************
    //* Traits
    //******************************************************************************

    use HasResults, HasTimer;

    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type mixed|null The output, if any, of the provisioning request
     */
    protected $output;
    /**
     * @type BaseRequest The original request
     */
    protected $request;
    /**
     * @type bool Self-describing
     */
    protected $success = false;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param bool                    $success
     * @param array                   $content
     * @param ProvisionServiceRequest $request
     * @param mixed|null              $result
     * @param mixed|null              $output
     * @param int|null                $httpCode
     * @param array                   $headers
     *
     * @return \DreamFactory\Enterprise\Common\Provisioners\ProvisionServiceResponse
     */
    public static function make($success, $request, $result = null, $content = [], $output = null, $httpCode = Response::HTTP_OK, $headers = [])
    {
        $_response = new static($content, $httpCode, $headers);

        /** @noinspection PhpUndefinedMethodInspection */

        return $_response->setRequest($request)->setResult($result)->setOutput($output)->setSuccess($success);
    }

    /**
     * Create a generic success response
     *
     * @param ProvisionServiceRequest $request
     * @param mixed|null              $result
     * @param mixed|null              $content
     * @param mixed|null              $output
     * @param int|null                $httpCode
     * @param array                   $headers
     *
     * @return $this
     */
    public static function makeSuccess($request, $result = null, $content = null, $output = null, $httpCode = null, $headers = [])
    {
        return static::make(true, $content, $request, $result, $output, $httpCode, $headers);
    }

    /**
     * Create a generic failure response
     *
     * @param ProvisionServiceRequest $request
     * @param mixed|null              $result
     * @param mixed|null              $content
     * @param mixed|null              $output
     * @param int|null                $httpCode
     * @param array                   $headers
     *
     * @return $this
     */
    public static function makeFailure($request, $result = null, $content = null, $output = null, $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR, $headers = [])
    {
        return static::make(false, $content, $request, $result, $output, $httpCode, $headers);
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return mixed|null
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @return BaseRequest|ProvisionServiceRequest|PortableServiceRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \DreamFactory\Enterprise\Database\Models\Instance
     */
    public function getInstance()
    {
        return $this->getRequest()->getInstance();
    }

    /**
     * @param boolean $success
     *
     * @return $this
     */
    public function setSuccess($success)
    {
        $this->success = !!$success;

        return $this;
    }

    /**
     * @param mixed|null $output
     *
     * @return BaseResponse|$this
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * @param BaseRequest $request
     *
     * @return $this
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }
}
