<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\NewRelicReporting\Model;

use Magento\Framework\App\State;
use Magento\Framework\App\ObjectManager;
use Throwable;

/**
 * Wrapper for New Relic functions
 *
 * @codeCoverageIgnore
 */
class NewRelicWrapper
{
    private const NEWRELIC_APPNAME = 'newrelic.appname';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var State
     */
    private $state;

    /**
     * @param ?Config $config
     * @param ?State $state
     */
    public function __construct(?Config $config = null, ?State $state = null)
    {
        $this->config = $config ?? ObjectManager::getInstance()->get(Config::class);
        $this->state = $state ?? ObjectManager::getInstance()->get(State::class);
    }

    /**
     * Wrapper for 'newrelic_add_custom_parameter' function
     *
     * @param string $param
     * @param string|int $value
     * @return bool
     */
    public function addCustomParameter($param, $value)
    {
        if ($this->isExtensionInstalled()) {
            newrelic_add_custom_parameter($param, $value);
            return true;
        }
        return false;
    }

    /**
     * Wrapper for 'newrelic_notice_error' function
     *
     * @param Throwable $exception
     * @return void
     */
    public function reportError(Throwable $exception)
    {
        if ($this->isExtensionInstalled()) {
            newrelic_notice_error($exception->getMessage(), $exception);
        }
    }

    /**
     * Wrapper for 'newrelic_set_appname'
     *
     * @param string $appName
     * @return void
     */
    public function setAppName(string $appName)
    {
        if ($this->isExtensionInstalled()) {
            newrelic_set_appname($appName);
        }
    }

    /**
     * Wrapper for 'newrelic_name_transaction'
     *
     * @param string $transactionName
     * @return void
     */
    public function setTransactionName(string $transactionName): void
    {
        if ($this->isExtensionInstalled()) {
            newrelic_name_transaction($transactionName);
        }
    }

    /**
     * Wrapper to start background transaction
     *
     * @return void
     */
    public function startBackgroundTransaction()
    {
        if ($this->isExtensionInstalled()) {
            $name = $this->getCurrentAppName();
            newrelic_start_transaction($name);
            newrelic_background_job();
        }
    }

    /**
     * Wrapper for 'newrelic_end_transaction'
     *
     * @param bool $ignore
     * @return void
     */
    public function endTransaction($ignore = false)
    {
        if ($this->isExtensionInstalled()) {
            newrelic_end_transaction($ignore);
        }
    }

    /**
     * Checks whether newrelic-php5 agent is installed
     *
     * @return bool
     */
    public function isExtensionInstalled()
    {
        return extension_loaded('newrelic');
    }

    /**
     * Get current App name for NR transactions
     *
     * @return string
     */
    public function getCurrentAppName()
    {
        if ($this->config->isSeparateApps() &&
            $this->config->getNewRelicAppName() &&
            $this->config->isNewRelicEnabled()) {
            $code = $this->state->getAreaCode();
            $current = $this->config->getNewRelicAppName();
            return $current . ';' . $current . '_' . $code;
        }
        return ini_get(self::NEWRELIC_APPNAME);
    }
}
