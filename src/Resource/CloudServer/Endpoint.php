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
   * @return Entity
   * @throws ApiException If request fails
   */
  public function reboot(Entity $entity) : Entity {
    $this->_client->post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'reboot']]
    );

    return $entity;
  }

  /**
   * Resizes an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @param int $package_id Desired package id
   * @return Entity
   * @throws ApiException If request fails
   */
  public function resize(Entity $entity, int $package_id) : Entity {
    $this->_client->post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    return $entity;
  }

  /**
   * Starts an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @return Entity
   * @throws ApiException If request fails
   */
  public function start(Entity $entity) : Entity {
    $this->_client->post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'start']]
    );


    return $entity;
  }

  /**
   * Stops an existing cloud server.
   *
   * @param Entity $entity Cloud Server model
   * @return Entity
   * @throws ApiException If request fails
   */
  public function stop(Entity $entity) : Entity {
    $this->_client->post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'stop']]
    );


    return $entity;
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
        $this->_client->post(
          self::_URI . "/{$entity->getId()}",
          ['_action' => 'console-log']
        )->getBody(),
        Util::FILTER_STRING
      )
    );
  }

  /**
   * Resolves when given cloud server is turned on.
   *
   * @param Entity The cloud server to wait for
   * @param array $options Promise options
   * @return Promise Entity[power_status] = on
   */
  public function whenStarted(Entity $entity, array $options = []) : Promise {
    return $this->_promise(
      $entity,
      function ($entity) {
        $this->sync($entity);
        return $entity->get('power_status') === 'on';
      },
      $options
    );
  }

  /**
   * Resolves when given cloud server is turned off.
   *
   * @param Entity The cloud server to wait for
   * @param array $options Promise options
   * @return Promise Entity[power_status] = off
   */
  public function whenStopped(Entity $entity, array $options = []) : Promise {
    return $this->_promise(
      $entity,
      function ($entity) {
        $this->sync($entity);
        return $entity->get('power_status') === 'off';
      },
      $options
    );
  }

  /**
   * Builds callback to wait() for a cloud server to be resized.
   *
   * @param Entity The cloud server to wait for
   * @param int $package_id The resized package id
   * @param array $options Promise options
   * @return Promise Entity[package_id] = $package_id
   */
  protected function whenResized(
    Entity $entity,
    int $package_id,
    array $options = []
  ) : Closure {
    return $this->_promise(
      $entity,
      function ($entity) use ($package_id) {
        $this->sync($entity);
        return $entity->get('package_id') === $package_id &&
          $entity->get('state') === 'stable';
      },
      $options
    );
  }
}
