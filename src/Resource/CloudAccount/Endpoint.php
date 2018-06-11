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
  Resource\CloudAccount\CloudAccount,
  Resource\VirtGuestCloud\Endpoint as ServiceEndpoint,
  Resource\WritableEndpoint
};

/**
 * API endpoint for Cloud Accounts (virtual hosting).
 */
class Endpoint extends WritableEndpoint {

  /** {@inheritDoc} */
  protected const _URI = 'cloud-account';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = CloudAccount::class;

  /**
   * Requests cancellation of the associated service.
   *
   * @param array $survey Cancellation survey
   * @return Endpoint $this
   */
  public function cancel(CloudAccount $model, array $survey) : Endpoint {
    return $this->_client
      ->getEndpoint(ServiceEndpoint::class)
      ->cancel($model->get('service'), $survey);
  }

  /**
   * Switches PHP versions active on an existing cloud server.
   *
   * @param Model $model Cloud server instance
   * @param string $version Desired PHP version
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(
    CloudAccount $model,
    string $version
  ) : Endpoint {
    $this->_client
      ->getEndpoint(ServiceEndpoint::class)
      ->setPhpVersion($model->get('service'), $version);

    $this->_wait(function ($endpoint) use ($model, $version) {
      if (
        $endpoint->retrieve($model->getId())->get('php_version') === $version
      ) {
        $endpoint->sync($model);
        return true;
      }
    });
    return $this;
  }
}
