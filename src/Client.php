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
  Config,
  Endpoint,
  Exception\ApiException,
  Exception\SdkException
};

/**
 * API client for nexcess.net / thermo.io
 */
class Client {

  /** @var string SDK root namespace. */
  const SDK_NAMESPACE = __NAMESPACE__;

  /** @var string SDK root directory. */
  const SDK_ROOT = __DIR__ . '/..';

  /** @var Guzzle The Guzzle http client. */
  private $_client;

  /** @var Config Client configuration object. */
  protected $_config;

  /**
   * @param Config $config Client configuration object
   */
  public function __construct(Config $config) {
    $this->_config = $config;
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
    $fqcn = strpos($name, '\\') === 0 ?
      $name :
      self::SDK_NAMESPACE . "\\Endpoint\\{$name}";

    if (! is_a(Endpoint::class, $fqcn, true)) {
      throw new ApiException(
        ApiException::NO_SUCH_ENDPOINT,
        ['name' => $name]
      );
    }

    return new $fqcn($this->_getHttpClient());
  }

  /**
   * Does the SDK need to be updated?
   *
   * @return bool True if a newer SDK version is available; false otherwise
   */
  public function shouldUpdate() : bool {
    throw new SdkException(SdkException::NOT_IMPLEMENTED);
  }
}
