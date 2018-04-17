<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use GuzzleHttp\Client as Guzzle;

use Nexcess\SDK\ {
  Config,
  ApiException,
  Response
};

/**
 * API client for nexcess.net / thermo.io
 */
class Client {

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
   * @param string $name The endpoint to get
   * @return Endpoint
   * @throws ApiException If there is an error, or the endpoint is unknown
   */
  public function endpoint(string $name) : Endpoint {
    $fqcn = is_a(Endpoint::class, __NAMESPACE__ . "\\{$name}", true) ?
      __NAMESPACE__ . "\\{$name}" :
      $name;
    if (! is_a(Endpoint::class, $fqcn, true)) {
      throw new ApiException(
        ApiException::NO_SUCH_ENDPOINT,
        ['name' => $name]
      );
    }
  }
}
