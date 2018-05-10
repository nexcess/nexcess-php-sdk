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
  Model\Cloud,
  Model\Collector as Collection,
  Model\Service,
  Util\Util
};

/**
 * Cloud Server (virtual machine).
 */
class CloudServer extends Service {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = [
    'id' => 'service_id'
  ];

  /** {@inheritDoc} */
  const PROPERTY_COLLAPSED = [
    'cloud' => 'cloud_id',
    'package' => 'package_id'
  ];

  /** {@inheritDoc} */
  const PROPERTY_MODELS = ['cloud' => Cloud::class];

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
