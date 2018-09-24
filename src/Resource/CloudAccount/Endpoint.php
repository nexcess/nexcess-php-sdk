<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use Nexcess\Sdk\ {
  Exception\ApiException,
  Resource\CloudAccount\Resource,
  Resource\VirtGuestCloud\Endpoint as ServiceEndpoint,
  Resource\Modelable as Model,
  Resource\WritableEndpoint
};

/**
 * API endpoint for Cloud Accounts (virtual hosting).
 */
class Endpoint extends WritableEndpoint {

  /** {@inheritDoc} */
  protected const _URI = 'cloud-account';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;

  /**
   * Requests cancellation of the associated service.
   *
   * @param Resource $resource Cloud Server resource
   * @param array $survey Cancellation survey
   * @return Endpoint $this
   */
  public function cancel(Resource $resource, array $survey) : Endpoint {
    return $this->_client
      ->getEndpoint(ServiceEndpoint::class)
      ->cancel($resource->get('service'), $survey);
  }

  /**
   * {@inheritDoc}
   */
  public function create(array $data) : Model {
    $resource = $this->getModel()->sync(
      $this->_client->request(
        'POST',
        static::_URI_CREATE ?? static::_URI,
        ['json' => $data]
      )['cloud_account']
    );

    $this->_wait($this->_waitUntilCreate($resource));
    return $resource;
  }

  /**
   * Switches PHP versions active on an existing cloud server.
   *
   * @param Resource $resource Cloud server resource
   * @param string $version Desired PHP version
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(
    Resource $resource,
    string $version
  ) : Endpoint {
    $this->_client
      ->getEndpoint(ServiceEndpoint::class)
      ->setPhpVersion($resource->get('service'), $version);

    $this->_wait(function ($endpoint) use ($resource, $version) {
      if (
        $endpoint->retrieve($resource->getId())->get('php_version') === $version
      ) {
        $endpoint->sync($resource);
        return true;
      }
    });

    return $this;
  }
}
