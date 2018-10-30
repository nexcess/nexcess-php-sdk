<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudServer;

use Nexcess\Sdk\ {
  Resource\CloudServer\Entity,
  Resource\PromisedResource,
  Resource\Service\Endpoint as ServiceEndpoint
};

/**
 * API actions for Cloud Servers (virtual machines).
 */
class Endpoint extends ServiceEndpoint {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'CloudServer';

  /** {@inheritDoc} */
  protected const _SERVICE_TYPE = 'virt-guest';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Entity::class;

  /**
   * Reboots an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function reboot(Entity $entity) : PromisedResource {
    $this->_post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'reboot']]
    );

    return $this->_buildPromise($entity)
      ->waitUntil($this->_waitForStart());
  }

  /**
   * Resizes an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @param int $package_id Desired package id
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function resize(Entity $entity, int $package_id) : PromisedResource {
    $this->_post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    return $this->_buildPromise($entity)
      ->waitUntil($this->_waitForResize());
  }

  /**
   * Starts an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function start(Entity $entity) : PromisedResource {
    $this->_post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'start']]
    );


    return $this->_buildPromise($entity)
      ->waitUntil($this->_waitForStart());
  }

  /**
   * Stops an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function stop(Entity $entity) : PromisedResource {
    $this->_post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'stop']]
    );


    return $this->_buildPromise($entity)
      ->waitUntil($this->_waitForStop());
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param Entity $entity Cloud Server model
   * @return string[] Lines from console log file
   * @throws ApiException If request fails
   */
  public function viewConsoleLog(Entity $entity) : array {
    return explode(
      "\n",
      Util::filter(
        $this->_post(
          self::_URI . "/{$entity->getId()}",
          ['_action' => 'console-log']
        )->getBody(),
        Util::FILTER_STRING
      )
    );
  }

  /**
   * Builds callback to wait() for a cloud server to turn on.
   *
   * @return Closure Callback for wait()
   */
  protected function _waitForStart() : Closure {
    return function ($entity) {
      $this->sync($entity);
      return $entity->get('power_status') === 'on';
    };
  }

  /**
   * Builds callback to wait() for a cloud server to turn off.
   *
   * @return Closure Callback for wait()
   */
  protected function _waitForStop() : Closure {
    return function ($entity) {
      $this->sync($entity);
      return $entity->get('power_status') === 'off';
    };
  }

  /**
   * Builds callback to wait() for a cloud server to be resized.
   *
   * @param int $package_id The resized package id
   * @return Closure Callback for wait()
   */
  protected function _waitForResize(int $package_id) : Closure {
    return function ($entity) use ($package_id) {
      $this->sync($entity);
      return $entity->get('package_id') === $package_id &&
        $entity->get('state') === 'stable';
    };
  }
}
