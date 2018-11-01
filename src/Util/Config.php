<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

use Nexcess\Sdk\ {
  SdkException,
  Util\Language,
  Util\Util
};

/**
 * Config container for SDK.
 *
 * Config options:
 *  - string "api_token" The API Token to use for requests.
 *  - string "base_uri" The API URL.
 *      This is provided by the SDK Client; you don't need to set it.
 *  - bool "debug" Enable debug mode?
 *      Debug mode does override some other settings (e.g., logging).
 *  - array "guzzle_defaults" Map of options for the Guzzle client.
 *      In most cases, you shouldn't provide anything here.
 *  - string "language" The language/locale to use. Defaults to "en_US".
 *  - array "list" Default options for list() requests.
 *    - int "page_size" Number of results to return per "page."
 *      The API itself defaults to 25; the maximum value is 250.
 *  - array "request" Map of options for handling requests.
 *    - bool "log" Enable request+response logging?
 *  - array "wait" Map of options for "waiting" (keeping things synchronous).
 *      By default, the API treats most actions as commands,
 *      which are queued and performed asynchronously.
 *      "Waiting" blocks the SDK until the action has completed.
 *    - bool "always" Enable waiting always (even if wait() is not called)?
 *    - int "interval" Polling interval while waiting. Defaults to 1s.
 *    - callable "tick_function" Callback to invoke each interval when waiting.
 *    - int "timeout" Maximum time to wait. Defaults to 30s.
 */
class Config {

  /** @var string Company identifier for Nexcess. */
  public const COMPANY_NEXCESS = 'nexcess';

  /** @var string Company identifier for Thermo. */
  public const COMPANY_THERMO = 'thermo';

  /** @var string One of the COMPANY_* constants. */
  public const COMPANY = self::COMPANY_NEXCESS;

  /** @var array Map of option override values for debug mode. */
  protected const _DEBUG_OVERRIDES = ['request' => ['log' => true]];

  /** @var array Map of default options. */
  protected const _DEFAULT_OPTIONS = [];

  /** @var array Map of rules for option validation. */
  protected const _RULES = [
    'api_token' => 'string',
    'base_uri' => 'string',
    'debug' => 'boolean',
    'guzzle_defaults' => 'array',
    'language' => 'string',
    'list' => 'array',
    'list.page_size' => 'integer',
    'request' => 'array',
    'request.log' => 'boolean',
    'wait' => 'array',
    'wait.always' => 'boolean',
    'wait.interval' => 'integer',
    'wait.tick_function' => 'callable',
    'wait.timeout' => 'integer',
  ];

  /** @var array Config options. */
  private $_options = [];

  /**
   * @param array $options Map of config options to set
   */
  public function __construct(array $options = []) {
    $this->_options = $options;
    $this->_checkOptions();
  }

  /**
   * Allows config options to be accessed like properties.
   * {@inheritDoc}
   * @see https://php.net/__get
   */
  public function __get(string $name) {
    return $this->get($name);
  }

  /**
   * Allows config options to be accessed like properties.
   * {@inheritDoc}
   * @see https://php.net/__get
   */
  public function __set(string $name, $value) {
    return $this->set($name, $value);
  }

  /**
   * Gets a config option.
   *
   * @param string $name Name of option to get
   * @return mixed Option value on success; null otherwise
   */
  public function get(string $name) {
    return $this->getDebugOverride($name) ??
      Util::dig($this->_options, $name) ??
      $this->getDefault($name);
  }

  /**
   * Gets an override option if debug mode is enabled.
   *
   * @param string $name Name of option to get
   * @return mixed Option value on success; null otherwise
   */
  public function getDebugOverride(string $name) {
    if (empty($this->_options['debug'])) {
      return null;
    }

    return Util::dig(static::_DEBUG_OVERRIDES, $name);
  }

  /**
   * Gets a default config option.
   *
   * @param string $name Name of option to get
   * @return mixed Option value on success; null otherwise
   */
  public function getDefault(string $name) {
    return Util::dig(static::_DEFAULT_OPTIONS, $name);
  }

  /**
   * Sets (overwrites) a config option.
   *
   * @param string $name Name of option to set
   * @param mixed $value Value to set
   * @param bool $extend Merge array values (overwrites otherwise)?
   */
  public function set(string $name, $value, bool $extend = false) {
    try {
      $prior = $this->_options;

      if (
        $extend &&
        isset($this->_options[$name]) &&
        is_array($this->_options[$name])
      ) {
        $this->_options[$name] =
          Util::extendRecursive($value, $this->_options[$name]);
      } else {
        $this->_options[$name] = $value;
      }

      $this->_checkOptions();
    } catch (Throwable $e) {
      $this->_options = $prior;
      throw $e;
    }
  }

  /**
   * Checks all options against defined validation rules.
   * @see Config::_checkOption
   */
  protected function _checkOptions() {
    foreach (static::_RULES as $option => $rule) {
      $this->_checkOption($option, $this->get($option), $rule);
    }
  }

  /**
   * Checks that a value is a given type, callable,
   * or an instance of a given class or interface.
   *
   * @param string $key The option key
   * @param mixed $value The option value
   * @param mixed The rule to apply
   * @throws SdkException If the rule is not met
   */
  protected function _checkOption(string $key, $value, $rule = null) {
    $rule = $rule ?? static::_RULES[$option] ?? null;
    if ($value === null || $rule === null) {
      return;
    }

    if (
      is_string($rule) && (
        $value instanceof $rule ||
        gettype($value) === $rule ||
        ($rule === 'callable' && is_callable($value))
      )
    ) {
      return;
    }

    if (is_array($rule) && in_array($value, $rule)) {
      return;
    }

    if (is_array($rule)) {
      $rule = implode('|', $rule);
    }
    throw new SdkException(
      SdkException::INVALID_CONFIG_OPTION,
      ['option' => $key, 'rule' => $rule, 'value' => $value]
    );
  }
}
