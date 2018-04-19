<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

use Nexcess\Sdk\ {
  Client,
  Exception\SdkException,
  Util\UsesJsonFile,
  Util\Util
};

/**
 * Config class for SDK.
 */
class Config {
  use UsesJsonFile;

  /** @var array Config options. */
  private $_config = [];

  /** @var array Config overrides. */
  private $_options = [];

  public function __construct(array $options = [], string ...$files) {
    $this->_config = $options;
    $this->_options = $options;

    array_unshift($files, Client::SDK_ROOT . '/src/config/config.json');
    foreach ($files as $file) {
      $this->addFile($file);
    }
  }

  /**
   * Allows config options to be accessed like properties.
   * @see https://php.net/__get
   */
  public function __get(string $name) {
    return $this->get($name);
  }

  /**
   * Adds data from a .json config file to the config.
   *
   * @param string $filepath
   * @throws SdkException On failure
   */
  public function addFile(string $filepath) {
    $this->_config = Util::extendRecursive(
      $this->_config,
      $this->_readJsonFile($filepath),
      $this->_options
    );
  }

  /**
   * Gets a config option.
   *
   * @param string $name Name of option to get
   * @return mixed Option value on success; null otherwise
   */
  public function get(string $name) {
    return $this->_config[$name] ?? null;
  }
}
