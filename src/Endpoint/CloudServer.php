<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\Service,
  Exception\ApiException,
  Model\Modelable as Model
};

/**
 * API actions for Cloud Servers (virtual machines).
 */
class CloudServer extends Service {

  /** {@inheritDoc} */
  const SERVICE_TYPE = 'virt-guest';

  /**
   * Reboots an existing cloud server.
   *
   * @param int $cloud_server_id Service id
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function reboot(int $cloud_server_id) : Model {
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
  public function resize(int $cloud_server_id, int $package_id) : Model {
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
  public function start(int $cloud_server_id) : Model {
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
  public function stop(int $cloud_server_id) : Model {
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
  public function viewConsoleLog(int $cloud_server_id) : Model {
    return $this->_request(
      'GET',
      self::ENDPOINT . "/{$cloud_server_id}/console-log"
    );
  }
}
