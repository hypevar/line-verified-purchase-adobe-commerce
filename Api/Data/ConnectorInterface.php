<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Data;

/**
 * Service connector for external service interaction
 */
interface ConnectorInterface
{
    /** @var string Header auth key name */
    public const HEADER_AUTH_KEY_NAME = 'ApiKey';

    /** @var int Request Timeout configuration value */
    public const REQUEST_TIMEOUT = 30;

    /**#@+
     * @var string
     * @access public
     */
    public const METHOD_GET = 'GET';
    public const METHOD_POST = 'POST';
    /**#@-*/

    /**
     * Sets the api url base url for the request
     *
     * @param string $value
     *
     * @return ConnectorInterface
     */
    public function setBaseUrl(string $value): ConnectorInterface;

    /**
     * Sets authorization api key to be used in every requests' header
     *
     * @param string $value api key from the environment
     *
     * @return ConnectorInterface
     */
    public function setAuthorizationKey(string $value): ConnectorInterface;

    /**
     * Request Header `User-Agent` value
     *
     * @param string $value
     *
     * @return ConnectorInterface
     */
    public function setUserAgent(string $value): ConnectorInterface;

    /**
     * Request Header `X-ApiVersion` value
     *
     * @param string $value
     *
     * @return ConnectorInterface
     */
    public function setApiVersion(string $value): ConnectorInterface;

    /**
     * GET call action
     *
     * @param string $url
     * @param array $params
     *
     * @return array
     */
    public function get($url, $params): array;

    /**
     * POST call action
     *
     * @param string $url
     * @param array $params
     *
     * @return array
     */
    public function post($url, $params): array;
}
