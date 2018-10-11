<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudServer;

use Nexcess\Sdk\ {
  Resource\Cloud\Resource as Cloud,
  Resource\Location\Resource as Location,
  Resource\Package\Resource as Package,
  Resource\Service\Resource as Service
};

/**
 * Cloud Server (virtual machine).
 */
class Resource extends Service {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'CloudServer';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = [
    'cloud' => 'location',
    'id' => 'service_id'
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [
    'location' => 'cloud_id',
    'package' => 'package_id'
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [
    'location' => Cloud::class,
    'package' => Package::class
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['service_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'bandwidth',
    'billing_term',
    'client',
    'description',
    'host',
    'identity',
    'last_bill_date',
    'location',
    'network',
    'next_bill_date',
    'os',
    'package',
    'power_status',
    'start_date',
    'state',
    'status'
  ];
}
