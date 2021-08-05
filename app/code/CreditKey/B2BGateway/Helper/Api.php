<?php
namespace CreditKey\B2BGateway\Helper;

use Magento\Store\Model\ScopeInterface;

/**
 * API Helper
 */
class Api
{
    /**
     * Config helper
     *
     * @var Config
     */
    private $config;

    /**
     * Cached value for Public Key
     *
     * @var string
     */
    private $publicKey;

    /**
     * Construct
     *
     * @param \CreditKey\B2BGateway\Helper\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Configure the API Client
     *
     * @return void
     */
    public function configure()
    {
        $endpoint = $this->config->getEndpoint();
        $publicKey = $this->getPublicKey();
        $sharedSecret = $this->config->getSharedSecret();
        \CreditKey\Api::configure($endpoint, $publicKey, $sharedSecret);
    }

    /**
     * Get Public Key value
     *
     * @return string
     */
    public function getPublicKey()
    {
        if (!$this->publicKey) {
            $this->publicKey = $this->config->getPublicKey();
        }
        return $this->publicKey;
    }
}
