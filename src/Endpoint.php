<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\SDK;

use GuzzleHttp\ {
  Client as Guzzle,
  Exception\ClientException,
  Exception\ConnectException,
  Exception\ServerException,
  Exception\TransferException
};

use Nexcess\Sdk\ {
  Exception\ApiException,
  Response
};

/**
 * Represents an API endpoint for nexcess.net / thermo.io
 */
abstract class Endpoint {

  /** @var Guzzle The Guzzle http client. */
  private $_client;

  public function __construct(Guzzle $client) {
    $this->_client = $client;
  }

  /**
   * Makes a request to the API.
   *
   * @param string $method HTTP method to use
   * @param string $endpoint API endpoint to request
   * @param array $params Request parameters (data, body, headers, ...)
   * @return Response
   */
  protected function _request(
    string $method,
    string $endpoint,
    array $params = []
  ) : Response {
    try {
      $this->_getClient()->request('GET', $endpoint, $params);
    } catch (ConnectException $e) {
      throw new ApiException(ApiException::CANNOT_CONNECT, $e);
    } catch (ClientException $e) {
      throw new ApiException(ApiException::BAD_REQUEST, $e);
    } catch (ServerException $e) {
      throw new ApiException(ApiException::SERVER_ERROR, $e);
    } catch (TransferException $e) {
      throw new ApiException(ApiException::UNKNOWN_ERROR, $e);
    }
  }
}
