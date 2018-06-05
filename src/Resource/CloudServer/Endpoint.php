<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudServer;

use Nexcess\Sdk\ {
  Resource\CloudServer\CloudServer,
  Resource\ServiceEndpoint
};

/**
 * API actions for Cloud Servers (virtual machines).
 */
class Endpoint extends ServiceEndpoint {

  /** {@inheritDoc} */
  protected const _SERVICE_TYPE = 'virt-guest';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = CloudServer::class;

  /**
   * Reboots an existing cloud server.
   *
   * @param CloudServer $cloud_server Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function reboot(CloudServer $cloud_server) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$cloud_server->getId()}",
      ['json' => ['_action' => 'reboot']]
    );

    return $this;
  }

  /**
   * Resizes an existing cloud server.
   *
   * @param CloudServer $cloud_server Cloud Server model
   * @param int $package_id Desired package id
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function resize(
    CloudServer $cloud_server,
    int $package_id
  ) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$cloud_server->getId()}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    return $this;
  }

  /**
   * Starts an existing cloud server.
   *
   * @param CloudServer $cloud_server Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function start(CloudServer $cloud_server) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$cloud_server->getId()}",
      ['json' => ['_action' => 'start']]
    );

    return $this;
  }

  /**
   * Stops an existing cloud server.
   *
   * @param CloudServer $cloud_server Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function stop(CloudServer $cloud_server) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$cloud_server->getId()}",
      ['json' => ['_action' => 'stop']]
    );

    return $this;
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param CloudServer $cloud_server Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function viewConsoleLog(CloudServer $cloud_server) : Endpoint {
    $this->_client->request(
      'GET',
      self::_URI . "/{$cloud_server->getId()}/console-log"
    );

    return $this;
  }
}
