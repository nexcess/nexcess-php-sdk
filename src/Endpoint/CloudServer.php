<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint,
  Exception\ApiException
};

/**
 * API actions for Cloud Servers (virtual machines).
 */
class CloudServer extends Endpoint {

  /** @var string API endpoint. */
  const ENDPOINT = 'cloud-server';

  /**
   * Creates a new cloud server.
   *
   * @param int $cloud_id Cloud (location) id
   * @param string $hostname Desired hostname
   * @param int $package_id Service package id
   * @param int $template_id Cloud template id
   * @param string $_secure_type One of "key"|"password"
   * @param int[] $ssh_key_ids Optional if _secure_type is "password"
   * @return array API response data
   * @throws ApiException On failure
   */
  public function add(
    int $cloud_id,
    string $hostname,
    int $package_id,
    int $template_id,
    string $_secure_type,
    array $ssh_key_ids = []
  ) : array {
    return $this->_request(
      'POST',
      self::ENDPOINT,
      [
        "json" => [
          'cloud_id' => $cloud_id,
          'hostname' => $hostname,
          'package_id' => $package_id,
          'template_id' => $template_id,
          '_secure_type' => $_secure_type,
          'ssh_key_ids' => $ssh_key_ids
        ]
      ]
    );
  }

  /**
   * Deletes an existing cloud server.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function delete(int $cloud_server_id) : array {
    return $this->_request('DELETE', self::ENDPOINT . "/{$cloud_server_id}");
  }

  /**
   * Reboots an existing cloud server.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function reboot(int $cloud_server_id) {
    return $this->_request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'reboot']]
    );
  }

  /**
   * Resizes an existing clous server.
   *
   * @param int $cloud_server_id Service id
   * @param int $package_id Desired package id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function resize(int $cloud_server_id, int $package_id) : array {
    return $this->_request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );
  }

  /**
   * Starts an existing cloud server.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function start(int $cloud_server_id) {
    return $this->_request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'start']]
    );
  }

  /**
   * Stops an existing cloud server.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function stop(int $cloud_server_id) {
    return $this->_request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'stop']]
    );
  }

  /**
   * Gets information about an existing cloud server.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function view(int $cloud_server_id) : array {
    return $this->_request('GET', self::ENDPOINT . "/{$cloud_server_id}");
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function viewConsoleLog(int $cloud_server_id) : array {
    return $this->_request(
      'GET',
      self::ENDPOINT . "/{$cloud_server_id}/console-log"
    );
  }
}
