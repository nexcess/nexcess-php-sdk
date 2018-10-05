<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\VirtGuestCloud;

use Nexcess\Sdk\ {
  Resource\Service\Endpoint as ServiceEndpoint,
  Resource\VirtGuestCloud\Resource
};

/**
 * API endpoint for cloud account services.
 */
class Endpoint extends ServiceEndpoint {

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;

  /** {@inheritDoc} */
  protected const _SERVICE_TYPE = 'virt-guest-cloud';

  /**
   * Switches PHP versions active on a service's primary cloud account.
   *
   * @param Resource $resource Service instance
   * @param string $version Desired PHP version
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(
    Resource $resource,
    string $version
  ) : Endpoint {
    $resource->get('cloud_account')->setPhpVersion($version);
    return $this;
  }
}
