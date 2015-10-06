<?php
namespace DreamFactory\Enterprise\Common\Services;

use DreamFactory\Enterprise\Common\Packets\BasePacket;
use DreamFactory\Enterprise\Common\Packets\ErrorPacket;
use DreamFactory\Enterprise\Common\Packets\SuccessPacket;
use Symfony\Component\HttpFoundation\Response;

class PacketService extends BaseService
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string|null The version/type of the packet to generate
     */
    protected $version;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * @param string $version
     */
    public function __construct($version = null)
    {
        parent::__construct();

        $this->version = $version ?: BasePacket::PACKET_VERSION;
    }

    /**
     * @param mixed|null $contents
     * @param int        $code
     *
     * @return array
     */
    public function success($contents = null, $code = Response::HTTP_OK)
    {
        return SuccessPacket::create($contents, $code);
    }

    /**
     * @param mixed|null        $contents
     * @param int               $code
     * @param string|\Exception $message
     *
     * @return array
     */
    public function failure($contents = null, $code = Response::HTTP_NOT_FOUND, $message = null)
    {
        return ErrorPacket::create($contents, $code, $message);
    }
}
