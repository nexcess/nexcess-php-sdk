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
  Model\CloudServer as Model
};

/**
 * API actions for Cloud Servers (virtual machines).
 */
class CloudServer extends Service {

  /** {@inheritDoc} */
  const SERVICE_TYPE = 'virt-guest';

  /** {@inheritDoc} */
  const MODEL = Model::class;

  /**
   * Reboots an existing cloud server.
   *
   * @param Model $cloud_server Cloud Server model
   * @return CloudServer $this
   * @throws ApiException If request fails
   */
  public function reboot(Model $cloud_server) : CloudServer {
    $response = $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'reboot']]
    );

    return $this;
  }

  /**
   * Resizes an existing clous server.
   *
   * @param Model $cloud_server Cloud Server model
   * @param int $package_id Desired package id
   * @return CloudServer $this
   * @throws ApiException If request fails
   */
  public function resize(Model $cloud_server, int $package_id) : CloudServer {
    $response = $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    return $this;
  }

  /**
   * Starts an existing cloud server.
   *
   * @param Model $cloud_server Cloud Server model
   * @return CloudServer $this
   * @throws ApiException If request fails
   */
  public function start(Model $cloud_server) : CloudServer {
    $response = $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'start']]
    );

    return $this;
  }

  /**
   * Stops an existing cloud server.
   *
   * @param Model $cloud_server Cloud Server model
   * @return CloudServer $this
   * @throws ApiException If request fails
   */
  public function stop(Model $cloud_server) : CloudServer {
    $response = $this->_client->request(
      'POST',
      self::ENDPOINT . "/{$cloud_server_id}",
      ['json' => ['_action' => 'stop']]
    );

    return $this;
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param Model $cloud_server Cloud Server model
   * @return CloudServer $this
   * @throws ApiException If request fails
   */
  public function viewConsoleLog(Model $cloud_server) : CloudServer {
    $response = $this->_client->request(
      'GET',
      self::ENDPOINT . "/{$cloud_server_id}/console-log"
    );

    return $this;
  }
}
