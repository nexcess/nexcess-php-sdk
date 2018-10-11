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
  Resource\Service\Endpoint as ServiceEndpoint
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

    $this->_wait($this->_waitForStart($resource));

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

    $this->_wait($this->_waitForResize($resource));

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

    $this->_wait($this->_waitForStart($resource));

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

    $this->_wait($this->_waitForStop($resource));

    return $this;
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param Resource $resource Cloud Server model
   * @return string[] Lines from console log file
   * @throws ApiException If request fails
   */
  public function viewConsoleLog(Resource $resource) : array {
    return explode(
      "\n",
      $this->_client->request(
        'POST',
        self::_URI . "/{$resource->getId()}",
        ['_action' => 'console-log']
      )
    );
  }

  /**
   * Builds callback to wait() for a cloud server to turn on.
   *
   * @param Resource $resource The CloudServer instance to check
   * @return Closure Callback for wait()
   */
  protected function _waitForStart(Resource $resource) : Closure {
    return function (Endpoint $endpoint) use ($resource) {
      $resource->sync($this->_retrieve($resource->getId()));
      return $resource->get('power_status') === 'on';
    };
  }

  /**
   * Builds callback to wait() for a cloud server to turn off.
   *
   * @param Resource $resource The CloudServer instance to check
   * @return Closure Callback for wait()
   */
  protected function _waitForStop(Resource $resource) : Closure {
    return function (Endpoint $endpoint) use ($resource) {
      $resource->sync($this->_retrieve($resource->getId()));
      return $resource->get('power_status') === 'off';
    };
  }

  /**
   * Builds callback to wait() for a cloud server to be resized.
   *
   * @param Resource $resource The CloudServer instance to check
   * @param int $package_id The resized package id
   * @return Closure Callback for wait()
   */
  protected function _waitForResize(
    Resource $resource,
    int $package_id
  ) : Closure {
    return function (Endpoint $endpoint) use ($resource, $package_id) {
      $resource->sync($this->_retrieve($resource->getId()));
      return $resource->get('package_id') === $package_id &&
        $resource->get('state') === 'stable';
    };
  }
}
