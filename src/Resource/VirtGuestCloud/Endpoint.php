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
  Resource\VirtGuestCloud\VirtGuestCloud
};

/**
 * API endpoint for cloud account services.
 */
class Endpoint extends ServiceEndpoint {

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = VirtGuestCloud::class;

  /** {@inheritDoc} */
  protected const _SERVICE_TYPE = 'virt-guest-cloud';
}
