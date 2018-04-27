<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use Nexcess\Sdk\ {
  Exception\ModelException,
  Model\Collector as Collection,
  Model\Location,
  Model\Service,
  Util\Util
};

/**
 * Cloud Server (virtual machine).
 */
class CloudServer extends Service {

  const PROPERTY_ALIASES = [
    'cloud' => 'location',
    'id' => 'service_id'
  ];

  const PROPERTY_COLLAPSED = [
    'location' => 'cloud_id',
    'package' => 'package_id'
  ];

  /** {@inheritDoc} */
  const PROPERTY_MODELS = ['location' => Location::class];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['service_id'];

  /** {@inheritDoc} */
  const READONLY_NAMES = [
    'bandwidth',
    'billing_term',
    'client',
    'description',
    'host',
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
