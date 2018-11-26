<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\VirtGuestCloud;

use Nexcess\Sdk\ {
  ApiException,
  Resource\Service\Endpoint as ServiceEndpoint,
  Resource\VirtGuestCloud\VirtGuestCloud,
  Util\Util
};

/**
 * API endpoint for cloud account services.
 */
class Endpoint extends ServiceEndpoint {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'VirtGuestCloud';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = VirtGuestCloud::class;

  /** {@inheritDoc} */
  protected const _SERVICE_TYPE = 'virt-guest-cloud';

  /**
   * Gets php versions available for a given cloud account to use.
   *
   * @param VirtGuestCloud $virtguestcloud The subject cloud account
   * @return string[] List of available php major.minor versions
   */
  public function getAvailablePhpVersions(
    VirtGuestCloud $virtguestcloud
  ) : array {
    return Util::decodeResponse(
      $this->_client
        ->get(static::_URI . "/{$virtguestcloud->getId()}/get-php-versions")
    );
  }

  /**
   * Switches PHP versions active on a service's primary cloud account.
   *
   * @param VirtGuestCloud $virtguestcloud Service instance
   * @param string $version Desired PHP version
   * @return VirtGuestCloud
   * @throws ApiException If request fails
   */
  public function setPhpVersion(
    VirtGuestCloud $virtguestcloud,
    string $version
  ) : VirtGuestCloud {
    $virtguestcloud->get('cloud_account')->setPhpVersion($version);
    return $virtguestcloud;
  }
}
