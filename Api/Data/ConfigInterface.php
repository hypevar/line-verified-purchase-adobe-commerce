<?php
/**
 * Copyright © 2024 Line. All rights reserved.
 */

declare(strict_types=1);

namespace Line\VerifiedPurchase\Api\Data;

/**
 * Interface exposing Module Configuration options
 */
interface ConfigInterface
{
    /**#@+
     * @access public
     * @var string
     */
    public const API_VERSION = 'v2';
    public const MODULE_VERSION = '0.5.0';
    public const XPATH_BASE = 'payment/verified_purchase/';

    public const XPATH_MODULE_ENABLED = 'active';
    public const XPATH_MODULE_MODE = 'module_mode';
    public const MODE_SANDBOX_VALUE = 'sandbox';
    public const MODE_PRODUCTION_VALUE = 'production';

    public const XPATH_SANDBOX_API_KEY = 'sandbox_api_key';
    public const XPATH_PRODUCTION_API_KEY = 'production_api_key';
    public const XPATH_SANDBOX_ENDPOINT_URL = 'sandbox_url';
    public const XPATH_PRODUCTION_ENDPOINT_URL = 'production_url';

    public const XPATH_DEBUG_ENABLED = 'debug_enabled';
    public const XPATH_VERIFICATION_MODE = 'verification_mode';
    public const XPATH_CREDIT_CARD_SUMMARY_DESCRIPTION = 'credit_card_summary_description';
    public const XPATH_TIME_UNIT = 'time_unit';
    public const XPATH_TIME_AMOUNT = 'time_amount';
    public const XPATH_MAX_TRIES = 'max_tries';

    public const MODE_SANDBOX = 'sandbox';
    public const MODE_PRODUCTION = 'production';

    /**
     * Config path of the status that the Order will be left
     * once the process starts
     */
    public const XPATH_ORDER_STATUS_PENDING = 'order_status_pending';

    /**
     * Config path of the status that the Order will be left
     * once the process gets completed
     */
    public const XPATH_ORDER_STATUS_COMPLETED = 'order_status_complete';

    /**
     * Config path of the status that the Order will be left
     * once the process is canceled (due to max tries or expiration)
     */
    public const XPATH_ORDER_STATUS_FAILED = 'order_status_failed';

    public const XPATH_EMAIL_SENDER = 'email_sender_identity';
    public const XPATH_EMAIL_TEMPLATE_PENDING = 'email_template_pending';
    public const XPATH_EMAIL_TEMPLATE_COMPLETED = 'email_template_completed';
    public const XPATH_EMAIL_TEMPLATE_CANCELED = 'email_template_canceled';

    public const XPATH_API_VERSION = 'api_version';
    /**#@-*/

    /**
     * Return module's configuration value
     *
     * @param string $xpath
     * @param null|int|string $storeId
     *
     * @return mixed
     */
    public function getConfigValue(string $xpath, $storeId): mixed;

    /**
     * Returns a module's configuration flag value
     *
     * @param string $xpath
     * @param null|int|string $storeId
     *
     * @return bool
     */
    public function getConfigFlag(string $xpath, $storeId): bool;

    /**
     * Whether module is enabled or not
     *
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Whether Sandbox mode is the current mode
     *
     * @return bool
     */
    public function isProductionModeEnabled(): bool;

    /**
     * Returns Production api key
     *
     * @return string
     */
    public function getProductionApiKey(): string;

    /**
     * Returns Producution endpoint url
     *
     * @return string
     */
    public function getProductionEndpointUrl(): string;

    /**
     * Whether Sandbox mode is the current mode
     *
     * @return bool
     */
    public function isSandboxModeEnabled(): bool;

    /**
     * Returns Sandbox Api key
     *
     * @return string
     */
    public function getSandboxApiKey(): string;

    /**
     * Returns Sandbox endpoint url
     *
     * @return string
     */
    public function getSandboxEndpointUrl(): string;

    /**
     * Returns Api credentials, based on the current configured env
     *
     * @return string
     */
    public function getApiCredential(): string;

    /**
     * Returns Endpoint credentials, based on the current configured env
     *
     * @return string
     */
    public function getApiEndpointUrl(): string;

    /**
     * Returns api version to be used
     *
     * @return string
     */
    public function getApiVersion(): string;

    /**
     * Whether module's debug log is enabled or not
     *
     * @return bool
     */
    public function isDebugEnabled(): bool;

    /**
     * Returns which verification mode value needs to be used
     *
     * @return string
     */
    public function getVerificationMode(): string;

    /**
     * Returns summary that will be displayed in CC or Bank account
     *
     * @return string
     */
    public function getCreditCardSummaryDescription(): string;

    /**
     * Returns time unit value for process lifetime
     *
     * @return string
     */
    public function getTimeUnit(): string;

    /**
     * Returns time amount value for process lifetime
     *
     * @return int
     */
    public function getTimeAmount(): int;

    /**
     * Returns configured max intent to get the process validated
     *
     * @return int
     */
    public function getMaxTries(): int;

    /**
     * Returns which status order needs to be set when process starts
     *
     * @return string
     */
    public function getOrderStatusPending(): string;

    /**
     * Returns which status order needs to be set when process completes
     *
     * @return string
     */
    public function getOrderStatusCompleted(): string;

    /**
     * Returns which status order needs to be set when process fails
     *
     * @return string
     */
    public function getOrderStatusFailed(): string;

    /**
     * Email Sender entity to be used in all email communications
     *
     * @return string
     */
    public function getEmailSender(): string;

    /**
     * Email Template for Pending notification process status
     *
     * @return string
     */
    public function getEmailProcessPendingTemplate(): string;

    /**
     * Email Template for Complete notification process status
     *
     * @return string
     */
    public function getEmailProcessCompleteTemplate(): string;

    /**
     * Email Template for fail/cancel notification process status
     *
     * @return string
     */
    public function getEmailProcessCanceledTemplate(): string;
}
