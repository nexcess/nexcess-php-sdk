<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudServer;

use Nexcess\Sdk\ {
  ApiException,
  Resource\CloudServer\CloudServer,
  Resource\Promise,
  Resource\Service\Endpoint as ServiceEndpoint,
  Util\Util
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
  protected const _MODEL_FQCN = CloudServer::class;

  /**
   * Reboots an existing cloud server.
   *
   * @param CloudServer $cloudserver Cloud Server model
   * @return CloudServer
   * @throws ApiException If request fails
   */
  public function reboot(CloudServer $cloudserver) : CloudServer {
    $this->_client->post(
      self::_URI . "/{$cloudserver->getId()}",
      ['json' => ['_action' => 'reboot']]
    );

    return $cloudserver;
  }

  /**
   * Resizes an existing cloud server.
   *
   * @param CloudServer $cloudserver Cloud Server model
   * @param int $package_id Desired package id
   * @return CloudServer
   * @throws ApiException If request fails
   */
  public function resize(
    CloudServer $cloudserver,
    int $package_id
  ) : CloudServer {
    $this->_client->post(
      self::_URI . "/{$cloudserver->getId()}",
      ['json' => ['_action' => 'resize', 'package_id' => $package_id]]
    );

    return $cloudserver;
  }

  /**
   * Starts an existing cloud server.
   *
   * @param CloudServer $cloudserver Cloud Server model
   * @return CloudServer
   * @throws ApiException If request fails
   */
  public function start(CloudServer $cloudserver) : CloudServer {
    $this->_client->post(
      self::_URI . "/{$cloudserver->getId()}",
      ['json' => ['_action' => 'start']]
    );


    return $cloudserver;
  }

  /**
   * Stops an existing cloud server.
   *
   * @param CloudServer $cloudserver Cloud Server model
   * @return CloudServer
   * @throws ApiException If request fails
   */
  public function stop(CloudServer $cloudserver) : CloudServer {
    $this->_client->post(
      self::_URI . "/{$cloudserver->getId()}",
      ['json' => ['_action' => 'stop']]
    );


    return $cloudserver;
  }

  /**
   * Views an existing cloud server's console log.
   *
   * @param CloudServer $cloudserver Cloud Server model
   * @return string[] Lines from console log file
   * @throws ApiException If request fails
   */
  public function viewConsoleLog(CloudServer $cloudserver) : array {
    return explode(
      "\n",
      Util::filter(
        $this->_client->post(
          self::_URI . "/{$cloudserver->getId()}",
          ['_action' => 'console-log']
        )->getBody(),
        Util::FILTER_STRING
      )
    );
  }

  /**
   * Resolves when given cloud server is turned on.
   *
   * @param CloudServer $cloudserver The cloud server to wait for
   * @param array $options Promise options
   * @return Promise CloudServer[power_status] = on
   */
  public function whenStarted(
    CloudServer $cloudserver,
    array $options = []
  ) : Promise {
    return $this->_promise(
      $cloudserver,
      function ($cloudserver) {
        $this->sync($cloudserver);
        return $cloudserver->get('power_status') === 'on';
      },
      $options
    );
  }

  /**
   * Resolves when given cloud server is turned off.
   *
   * @param CloudServer $cloudserver The cloud server to wait for
   * @param array $options Promise options
   * @return Promise CloudServer[power_status] = off
   */
  public function whenStopped(
    CloudServer $cloudserver,
    array $options = []
  ) : Promise {
    return $this->_promise(
      $cloudserver,
      function ($cloudserver) {
        $this->sync($cloudserver);
        return $cloudserver->get('power_status') === 'off';
      },
      $options
    );
  }

  /**
   * Builds callback to wait() for a cloud server to be resized.
   *
   * @param CloudServer $cloudserver The cloud server to wait for
   * @param int $package_id The resized package id
   * @param array $options Promise options
   * @return Promise CloudServer[package_id] = $package_id
   */
  protected function whenResized(
    CloudServer $cloudserver,
    int $package_id,
    array $options = []
  ) : Promise {
    return $this->_promise(
      $cloudserver,
      function ($cloudserver) use ($package_id) {
        $this->sync($cloudserver);
        return $cloudserver->get('package_id') === $package_id &&
          $cloudserver->get('state') === 'stable';
      },
      $options
    );
  }
}
