<?php namespace DreamFactory\Enterprise\Common\Traits;

use DreamFactory\Enterprise\Common\Enums\EnterpriseDefaults;
use DreamFactory\Enterprise\Database\Models\AppKey;

/**
 * A trait that adds signature verification functionality
 *
 * Be sure to call setSigningCredentials() before trying to verify
 */
trait VerifiesSignatures
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * @type string
     */
    protected $vsSignature = null;
    /**
     * @type string
     */
    protected $vsClientId = null;
    /**
     * @type string
     */
    protected $vsClientSecret = null;

    //******************************************************************************
    //* Methods
    //******************************************************************************

    /**
     * Validates a client key pair and generates a signature for verification.
     *
     * @param string $clientId
     * @param string $clientSecret
     *
     * @return $this
     */
    protected function setSigningCredentials($clientId, $clientSecret)
    {
        $_key = AppKey::byClientId($clientId)->first();

        if (empty($_key) || $clientSecret != $_key->client_secret) {
            throw new \InvalidArgumentException('Invalid credentials.');
        }

        //  Looks good
        $this->vsClientId = $_key->client_id;
        $this->vsClientSecret = $_key->client_secret;
        $this->vsSignature = $this->generateSignature();

        return $this;
    }

    /**
     * @param string $token        The client-provided "access token"
     * @param string $clientId     The client-provided "client-id"
     * @param string $clientSecret The actual client-secret associated with client-provided "client-id"
     *
     * @return bool
     */
    protected function verifySignature($token, $clientId, $clientSecret)
    {
        if (empty($this->vsSignature)) {
            $this->vsSignature = $this->setSigningCredentials( $clientId, $clientSecret )->generateSignature();
        }

        return $token === $this->vsSignature;
    }

    /**
     * @return string
     */
    protected function generateSignature()
    {
        return hash_hmac(config('dfe.signature-method', EnterpriseDefaults::DEFAULT_SIGNATURE_METHOD),
            $this->vsClientId,
            $this->vsClientSecret);
    }

    /**
     * @param array $payload
     *
     * @return array
     */
    protected function signRequest(array $payload)
    {
        return array_merge([
            'client-id'    => $this->vsClientId,
            'access-token' => $this->vsSignature,
        ],
            $payload ?: []);
    }
}
