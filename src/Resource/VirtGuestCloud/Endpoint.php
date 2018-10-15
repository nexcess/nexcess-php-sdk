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
  Resource\VirtGuestCloud\Entity
};

/**
 * API endpoint for cloud account services.
 */
class Endpoint extends ServiceEndpoint {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'VirtGuestCloud';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _SERVICE_TYPE = 'virt-guest-cloud';

  /**
   * Switches PHP versions active on a service's primary cloud account.
   *
   * @param Entity $entity Service instance
   * @param string $version Desired PHP version
   * @return Endpoint $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(Entity $entity, string $version) : Endpoint {
    $entity->get('cloud_account')->setPhpVersion($version);
    return $this;
  }
}
