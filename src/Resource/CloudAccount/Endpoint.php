<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use Nexcess\Sdk\ {
  ApiException,
  Resource\CanCreate,
  Resource\CloudAccount\Entity,
  Resource\Creatable,
  Resource\Endpoint as BaseEndpoint,
  Util\Util
};

/**
 * API endpoint for Cloud Accounts (virtual hosting).
 */
class Endpoint extends BaseEndpoint implements Creatable {
  use CanCreate;

  /** {@inheritDoc} */
  public const MODULE_NAME = 'CloudAccount';

  /** {@inheritDoc} */
  protected const _URI = 'cloud-account';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _PARAMS = [
    'create' => [
      'app_id' => [Util::TYPE_INT],
      'cloud_id' => [Util::TYPE_INT],
      'domain' => [Util::TYPE_STRING],
      'install_app' => [Util::TYPE_BOOL, false],
      'package_id' => [Util::TYPE_INT]
    ],
    'setPhpVersion' => ['version' => [Util::TYPE_STRING]]
  ];

  /**
   * Requests cancellation of the service associated with a cloud account.
   *
   * Note, this creates a cancellation request,
   * and does not delete the cloud account directly.
   * Use this method to cancel a primary cloud account, not a dev account.
   *
   * @param Entity $entity Cloud Server instance
   * @param array $survey Cancellation survey
   * @return Endpoint $this
   */
  public function cancel(Entity $entity, array $survey) : Endpoint {
    $entity->get('service')->cancel($survey);
    return $this;
  }

  /**
   * Switches PHP versions active on an existing cloud account.
   *
   * @param Entity $entity Cloud server instance
   * @param string $version Desired PHP version
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(
    Entity $entity,
    string $version
  ) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'set-php-version', 'php_version' => $version]]
    );

    $this->_wait($this->_waitForPhpVersion($entity, $version));

    return $this;
  }

  /**
   * Builds callback to wait() for a cloud account to update php versions.
   *
   * @param Entity $entity The CloudAccount instance to check
   * @param string $version The target php version
   * @return Closure Callback for wait()
   */
  protected function _waitForPhpVersion(
    Entity $entity,
    string $version
  ) : Closure {
    return function (Endpoint $endpoint) use ($entity, $version) {
      $entity->sync($this->_retrieve($entity->getId()));
      return $entity->get('php_version') === $version;
    };
  }

  /**
   * Clear Nginx Cache
   *
   * @return Endpoint $this
   * @throws ResourceException If endpoint not available
   * @throws ApiException If request fails
   */
  public function clearNginxCache(Entity $entity) : Entity {
    $this->_client->request(
      'POST',
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'purge-cache']]
    );

    return $this;
  }

}
