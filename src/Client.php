<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use GuzzleHttp\Client as Guzzle;

use Nexcess\Sdk\ {
  Endpoint,
  Exception\ApiException,
  Exception\SdkException,
  Util\Config
};

/**
 * API client for nexcess.net / thermo.io
 *
 * @property Endpoint $CloudServer
 */
class Client {

  /** @var string SDK root namespace. */
  const SDK_NAMESPACE = __NAMESPACE__;

  /** @var string SDK root directory. */
  const SDK_ROOT = __DIR__ . '/..';

  /** @var Config Client configuration object. */
  protected $_config;

  /**
   * @param Config $config Client configuration object
   */
  public function __construct(Config $config) {
    $this->_config = $config;
  }

  /**
   * Allows endpoint objects to be accessed like properties.
   * @see https://php.net/__get
   */
  public function __get(string $name) {
    return $this->endpoint($name);
  }

  /**
   * Gets an API endpoint instance.
   *
   * For convenience, endpoints can also be accessed as instance properties.
   * @see Client::__get
   *
   * @param string $name Endpoint classname (short name or fully qualified)
   * @return Endpoint
   * @throws ApiException If there is an error, or the endpoint is unknown
   */
  public function endpoint(string $name) : Endpoint {
    $fqcn = is_a($name, Endpoint::class, true) ?
      $name :
      self::SDK_NAMESPACE . "\\Endpoint\\{$name}";

    if (! is_a($fqcn, Endpoint::class, true)) {
      throw new ApiException(
        ApiException::NO_SUCH_ENDPOINT,
        ['name' => $name]
      );
    }

    return new $fqcn($this->_config);
  }

  /**
   * Does the SDK need to be updated?
   *
   * @return bool True if a newer SDK version is available; false otherwise
   */
  public function shouldUpdate() : bool {
    throw new SdkException(SdkException::NOT_IMPLEMENTED);
  }

  /**
   * Perform self-update
   *
   * @return array Information about the update.
   * @throws SdkException If update fails
   */
  public function update() : array {
    throw new SdkException(SdkException::NOT_IMPLEMENTED);
  }
}
