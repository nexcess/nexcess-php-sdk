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
  Resource\CloudAccount\Resource,
  Resource\Createable,
  Resource\Endpoint as BaseEndpoint
};

/**
 * API endpoint for Cloud Accounts (virtual hosting).
 */
class Endpoint extends BaseEndpoint implements Creatable {
  use CanCreate;

  /** {@inheritDoc} */
  protected const _URI = 'cloud-account';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;

  /**
   * Requests cancellation of the service associated with a cloud account.
   *
   * Note, this creates a cancellation request,
   * and does not delete the cloud account directly.
   * Use this method to cancel a primary cloud account, not a dev account.
   *
   * @param Resource $resource Cloud Server resource
   * @param array $survey Cancellation survey
   * @return Endpoint $this
   */
  public function cancel(Resource $resource, array $survey) : Endpoint {
    $resource->get('service')->cancel($survey);
    return $this;
  }

  /**
   * Switches PHP versions active on an existing cloud account.
   *
   * @param Resource $resource Cloud server instance
   * @param string $version Desired PHP version
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(
    Resource $resource,
    string $version
  ) : Endpoint {
    $this->_client->request(
      'POST',
      self::_URI . "/{$resource->getId()}",
      ['json' => ['_action' => 'set-php-version', 'php_version' => $version]]
    );

    $this->_wait($this->_waitForPhpVersion($resource, $version));

    return $this;
  }

  /**
   * Builds callback to wait() for a cloud account to update php versions.
   *
   * @param Resource $resource The CloudAccount instance to check
   * @param string $version The target php version
   * @return Closure Callback for wait()
   */
  protected function _waitForPhpVersion(
    Resource $resource,
    string $version
  ) : Closure {
    return function(Endpoint $endpoint) use ($resource, $version) {
      $resource->sync($this->_retrieve($resource->getId()));
      return $resource->get('php_version') === $version;
    };
  }
}
