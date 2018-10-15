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
  public function reboot(Entity $entity) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'reboot']]
    );

    $this->_wait($this->_waitForStart($entity));

    return $this;
  }

  /**
   * Resizes an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @param int $package_id Desired package id
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function resize(Entity $entity, int $package_id) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    $this->_wait($this->_waitForResize($entity));

    return $this;
  }

  /**
   * Starts an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function start(Entity $entity) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'start']]
    );

    $this->_wait($this->_waitForStart($entity));

    return $this;
  }

  /**
   * Stops an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function stop(Entity $entity) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'stop']]
    );

    $this->_wait($this->_waitForStop($entity));

    return $this;
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
      $this->_client->request(
        'POST',
        self::_URI . "/{$entity->getId()}",
        ['_action' => 'console-log']
      )
    );
  }

  /**
   * Builds callback to wait() for a cloud server to turn on.
   *
   * @param Entity $entity The CloudServer instance to check
   * @return Closure Callback for wait()
   */
  protected function _waitForStart(Entity $entity) : Closure {
    return function (Endpoint $endpoint) use ($entity) {
      $entity->sync($this->_retrieve($entity->getId()));
      return $entity->get('power_status') === 'on';
    };
  }

  /**
   * Builds callback to wait() for a cloud server to turn off.
   *
   * @param Entity $entity The CloudServer instance to check
   * @return Closure Callback for wait()
   */
  protected function _waitForStop(Entity $entity) : Closure {
    return function (Endpoint $endpoint) use ($entity) {
      $entity->sync($this->_retrieve($entity->getId()));
      return $entity->get('power_status') === 'off';
    };
  }

  /**
   * Builds callback to wait() for a cloud server to be resized.
   *
   * @param Entity $entity The CloudServer instance to check
   * @param int $package_id The resized package id
   * @return Closure Callback for wait()
   */
  protected function _waitForResize(
    Entity $entity,
    int $package_id
  ) : Closure {
    return function (Endpoint $endpoint) use ($entity, $package_id) {
      $entity->sync($this->_retrieve($entity->getId()));
      return $entity->get('package_id') === $package_id &&
        $entity->get('state') === 'stable';
    };
  }
}
