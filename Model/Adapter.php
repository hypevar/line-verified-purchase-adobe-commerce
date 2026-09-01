<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Model;

use Line\VerifiedPurchase\Api\Data\ConfigInterface;
use Line\VerifiedPurchase\Api\Data\ConnectorInterface;
use Line\VerifiedPurchase\Model\Client\DataConverter;

class Adapter
{
    /**
     * @var ConfigInterface
     */
    protected ConfigInterface $configuration;

    /**
     * @var ConnectorInterface
     */
    protected ConnectorInterface $connector;

    /**
     * @var DataConverter
     */
    protected DataConverter $dataConverter;

    /**
     * @var array
     */
    protected $credentials = [];

    /**
     * Class constructor
     *
     * @param ConfigInterface $module
     * @param ConnectorInterface $connector
     * @param DataConverter $dataConverter
     */
    public function __construct(
        ConfigInterface $module,
        ConnectorInterface $connector,
        DataConverter $dataConverter
    ) {
        $this->configuration = $module;
        $this->connector = $connector;
        $this->dataConverter = $dataConverter;

        $this->setupConnector();
    }

    /**
     * Initializes the Connector with credentials
     *
     * @return $this
     */
    protected function setupConnector()
    {
        $key = $this->configuration->getApiCredential();
        $base = $this->configuration->getApiEndpointUrl();
        $agent = 'Magento Line VerifiedPurchase ' . ConfigInterface::MODULE_VERSION;
        $version = $this->configuration->getApiVersion();

        $this->connector->setBaseUrl($base)
            ->setAuthorizationKey($key)
            ->setUserAgent($agent)
            ->setApiVersion($version);

        return $this;
    }

    /**
     * Executes a call against the connector
     *
     * @param string $method
     * @param string $url
     * @param array $params
     *
     * @return ResponseInterface
     */
    public function call(string $method, string $url, array $params)
    {
        $response = $this->connector->{$method}($url, $params);

        return $this->dataConverter->convert($response);
    }

    /**
     * Executes a verification process
     *
     * @param array $attributes
     *
     * @return ResponseInterface
     */
    public function validate(array $attributes)
    {
        return $this->call(
            'post',
            '/compraverificada/verificar',
            $attributes
        );
    }
}
