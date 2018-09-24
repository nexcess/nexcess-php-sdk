<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudServer;

use Nexcess\Sdk\ {
  Resource\CloudServer\Resource,
  Resource\ServiceEndpoint
};

/**
 * API actions for Cloud Servers (virtual machines).
 */
class Endpoint extends ServiceEndpoint {

  /** {@inheritDoc} */
  protected const _SERVICE_TYPE = 'virt-guest';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;

  /**
   * Reboots an existing cloud server.
   *
   * @param Resource $resource Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function reboot(Resource $resource) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$resource->getId()}",
      ['json' => ['_action' => 'reboot']]
    );

    return $this;
  }

  /**
   * Resizes an existing cloud server.
   *
   * @param Resource $resource Cloud Server model
   * @param int $package_id Desired package id
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function resize(
    Resource $resource,
    int $package_id
  ) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$resource->getId()}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    return $this;
  }

  /**
   * Starts an existing cloud server.
   *
   * @param Resource $resource Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function start(Resource $resource) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$resource->getId()}",
      ['json' => ['_action' => 'start']]
    );

    return $this;
  }

  /**
   * Stops an existing cloud server.
   *
   * @param Resource $resource Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function stop(Resource $resource) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$resource->getId()}",
      ['json' => ['_action' => 'stop']]
    );

    return $this;
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param Resource $resource Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function viewConsoleLog(Resource $resource) : Endpoint {
    $this->_client->request(
      'GET',
      self::_URI . "/{$resource->getId()}/console-log"
    );

    return $this;
  }
}
