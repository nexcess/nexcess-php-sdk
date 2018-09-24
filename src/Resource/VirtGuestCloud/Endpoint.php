<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\VirtGuestCloud;

use Nexcess\Sdk\ {
  Resource\ServiceEndpoint,
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
   * Switches PHP versions active on an existing cloud service.
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
    $this->_client->request(
      'POST',
      self::_URI . "/{$resource->getId()}",
      ['json' => ['_action' => 'set-php-version', 'php_version' => $version]]
    );

    $cloud = $resource->get('cloud_account');
    $this->_wait(function ($endpoint) use ($cloud, $version) {
      if (
        $endpoint->retrieve($cloud->getId())->get('php_version') === $version
      ) {
        $cloud->sync(['php_version' => $version]);
        return true;
      }
    });
    return $this;
  }
}
