<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use GuzzleHttp\ {
  Client as Guzzle,
  Exception\ClientException,
  Exception\ConnectException,
  Exception\ServerException,
  Exception\TransferException
};

use Nexcess\Sdk\ {
  Exception\ApiException,
  Exception\SdkException,
  Response,
  Util\Config
};

/**
 * Represents an API endpoint for nexcess.net / thermo.io
 */
abstract class Endpoint {

  /** @var Guzzle The Guzzle http client. */
  private $_client;

  /** @var Config Client configuration object. */
  protected $_config;

  public function __construct(Config $config) {
    $this->_config = $config;
  }

  /**
   * Gets the http client.
   *
   * @return Guzzle
   */
  private function _getClient() : Guzzle {
    if (! $this->_client) {
      $this->_client = new Guzzle([
        'base_uri' => $this->_config->get('base_uri'),
        "headers" => [
          "Authorization" => "Bearer {$this->_config->get('api_token')}",
          "Accept" => "application/json"
        ]
      ]);
    }

    return $this->_client;
  }

  /**
   * Makes a request to the API.
   *
   * @param string $method HTTP method to use
   * @param string $endpoint API endpoint to request
   * @param array $params Request parameters (data, body, headers, ...)
   * @return Response
   * @throws ApiException If request fails
   */
  protected function _request(
    string $method,
    string $endpoint,
    array $params = []
  ) : Response {
    try {
      $response = $this->_getClient()->request($method, $endpoint, $params);

      $data = ($response->getHeader('Content-type') === 'application/json') ?
        json_decode($response->getBody(), true) :
        ['response' => (string) $response->getBody()];

      $data['http_status'] = [
        'code' => $response->getStatusCode(),
        'reason' => $response->getReasonPhrase()
      ];

      return $data;

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
