<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\ServiceEndpoint,
  Exception\ApiException,
  Response
};

/**
 * API actions for Cloud Servers (virtual machines).
 */
class CloudServer extends ServiceEndpoint {

  /** {@inheritDoc} */
  const BASE_LIST_FILTER = ['type' => 'virt-guest'];

  /** @var string Value for add() "_secure_type". */
  const SECURE_TYPE_PASSWORD = 'password';

  /** @var string Value for add() "_secure_type". */
  const SECURE_TYPE_KEY = 'key';

  /**
   * {@inheritDoc}
   *
   * - int "cloud_id": Cloud (location) id
   * - string "hostname": Desired hostname
   * - int "package_id": Service package id
   * - int[] "ssh_key_ids": Optional if _secure_type is "password"
   * - int "template_id": Cloud template id
   * - string "_secure_type": One of "key"|"password"
   */
  const ADD_VALUE_MAP = [
    'cloud_id' => 0,
    'hostname' => '',
    'package_id' => 0,
    'ssh_keys' => [],
    'template_id' => 0,
    '_secure_type' => self::SECURE_TYPE_PASSWORD
  ];

  /**
   * Reboots an existing cloud server.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function reboot(int $cloud_server_id) : Response {
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
   * @throws ApiException If request fails
   */
  public function resize(int $cloud_server_id, int $package_id) : Response {
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
   * @throws ApiException If request fails
   */
  public function start(int $cloud_server_id) : Response {
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
   * @throws ApiException If request fails
   */
  public function stop(int $cloud_server_id) : Response {
    return $this->_request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'stop']]
    );
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function viewConsoleLog(int $cloud_server_id) : Response {
    return $this->_request(
      'GET',
      self::ENDPOINT . "/{$cloud_server_id}/console-log"
    );
  }
}
