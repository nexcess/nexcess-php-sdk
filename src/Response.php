<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use JsonSerializable;

use GuzzleHttp\Psr7\Response as GuzzleResponse;

use Nexcess\Sdk\ {
  Endpoint,
  Exception\SdkException
};

/**
 * Represents an API response.
 *
 * Can be json-encoded or printed as a string.
 */
class Response implements JsonSerializable {

  /** @var GuzzleResponse The guzzle psr-7 Response object. */
  private $_guzzle_response;

  /**
   * @param GuzzleResponse The guzzle response object to wrap
   */
  public function __construct(GuzzleResponse $response) {
    $this->_guzzle_response = $response;
  }

  /**
   * @see https://php.net/__toString
   */
  public function __toString() {
    try {
      $json = json_encode(
        $this,
        JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
      );
    } catch (Throwable $e) {
      // __toString must not throw
    }
    // __toString must return a string
    return is_string($json) ? $json : '';
  }

  /**
   * Proxies GuzzleResponse::getBody
   * @see http://docs.guzzlephp.org/en/stable/psr7.html#responses
   */
  public function getBody() : array {
    return $this->_guzzle_response->getBody();
  }

  /**
   * Proxies GuzzleResponse::getHeader
   * @see http://docs.guzzlephp.org/en/stable/psr7.html#responses
   */
  public function getHeader(string $name) : array {
    return $this->_guzzle_response->getHeader($name);
  }

  /**
   * Proxies GuzzleResponse::getHeaders
   * @see http://docs.guzzlephp.org/en/stable/psr7.html#responses
   */
  public function getHeaders(string $name) : array {
    return $this->_guzzle_response->getHeaders($name);
  }

  /**
   * Proxies GuzzleResponse::getProtocolVersion
   * @see http://docs.guzzlephp.org/en/stable/psr7.html#responses
   */
  public function getProtocolVersion() : string {
    return $this->_guzzle_response->getProtocolVersion();
  }

  /**
   * Proxies GuzzleResponse::getReasonPhrase
   * @see http://docs.guzzlephp.org/en/stable/psr7.html#responses
   */
  public function getReasonPhrase() : string {
    return $this->_guzzle_response->getReasonPhrase();
  }

  /**
   * Proxies GuzzleResponse::getStatusCode
   * @see http://docs.guzzlephp.org/en/stable/psr7.html#responses
   */
  public function getStatusCode() : int {
    return $this->_guzzle_response->getStatusCode();
  }

  /**
   * @see https://php.net/JsonSerializable.jsonSerialize
   */
  public function jsonSerialize() {
    return $this->toArray();
  }

  /**
   * Gets the API response as an array.
   *
   * @return array
   */
  public function toArray() : array {
    $content_type = $this->_guzzle_response->getHeader('Content-type');

    return (reset($content_type) === 'application/json') ?
      json_decode((string) $this->_guzzle_response->getBody(), true) :
      ['response' => $this->_guzzle_response->getBody()];
  }
}
