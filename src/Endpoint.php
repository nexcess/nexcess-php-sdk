<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use Throwable;

use GuzzleHttp\ {
  Client as Guzzle,
  Exception\ClientException,
  Exception\ConnectException,
  Exception\RequestException,
  Exception\ServerException,
  Exception\TransferException
};

use Nexcess\Sdk\ {
  Exception\ApiException,
  Exception\SdkException,
  Response,
  Util\Config,
  Util\Util
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
      $guzzle_options = ['base_uri' => $this->_config->get('base_uri')];
      $guzzle_defaults = $this->_config->get('guzzle_defaults');
      if ($guzzle_defaults) {
        $guzzle_options =
          Util::extendRecursive($guzzle_options, $guzzle_defaults);
      }

      $this->_client = new Guzzle($guzzle_options);
    }

    return $this->_client;
  }

  /**
   * Gets default headers for API requests.
   *
   * @return array Map of http headers
   */
  private function _getDefaultHeaders() : array {
    $headers = [
      'Accept' => 'application/json',
      'Accept-language' => $this->_config->get('language')
    ];
    $api_token = $this->_config->get('api_token');
    if ($api_token) {
      $headers['Authorization'] = "Bearer {$api_token}";
    }

    return $headers;
  }

  /**
   * Makes a request to the API.
   *
   * @param string $method HTTP method to use
   * @param string $endpoint API endpoint to request
   * @param array $params Request parameters (json, body, headers, ...)
   * @return array Response data
   * @throws ApiException If request fails
   */
  protected function _request(
    string $method,
    string $endpoint,
    array $params = []
  ) : Response {
    try {
      $params['headers'] = ($params['headers'] ?? []) + $this->_getDefaultHeaders();

      return new Response(
        $this->_getClient()->request($method, $endpoint, $params)
      );

    } catch (ConnectException $e) {
      throw new ApiException(ApiException::CANNOT_CONNECT, $e);
    } catch (ClientException $e) {
      throw new ApiException(ApiException::BAD_REQUEST, $e);
    } catch (ServerException $e) {
      throw new ApiException(ApiException::SERVER_ERROR, $e);
    } catch (RequestException $e) {
      throw new ApiException(ApiException::REQUEST_FAILED, $e);
    } catch (Throwable $e) {
      throw new SdkException(SdkException::UNKNOWN_ERROR, $e);
    }
  }
}
