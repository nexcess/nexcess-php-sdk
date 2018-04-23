<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use Nexcess\Sdk\ {
  Model\Collection,
  Model\SshKey
};

/**
 * Cloud Server (virtual machine).
 */
class CloudServer extends ServiceModel {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = ['id' => 'service_id'];

  const PROPERTY_COLLAPSED = [
    'cloud' => 'cloud_id',
    'package' => 'package_id',
    'ssh_keys' => 'ssh_key_ids',
    'template' => 'template_id'
  ];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = [
    'service_id',
    'cloud',
    'hostname',
    'package',
    'ssh_keys',
    'template'
  ];

  /** {@inheritDoc} */
  const SERVICE_TYPE = 'virt-guest';

  /**
   * Reboots the cloud server.
   *
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function reboot() : CloudServer {
    $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'reboot']]
    );

    return $this;
  }

  /**
   * Resizes the cloud server.
   *
   * @param int $package_id Desired package id
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function resize(int $package_id) : CloudServer {
    $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    return $this;
  }

  /**
   *
   */
  public function setSshKeys($value) {}

  /**
   * Starts (powers on) the cloud server.
   *
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function start() : CloudServer {
    $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'start']]
    );

    return $this;
  }

  /**
   * Stops (powers down) the cloud server.
   *
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function stop() : CloudServer {
    $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'stop']]
    );

    return $this;
  }

  /**
   * Views the cloud server's console log.
   *
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function viewConsoleLog() : CloudServer {
    $this->_client->request(
      'GET',
      self::ENDPOINT . "/{$cloud_server_id}/console-log"
    );

    return $this;
  }
}
