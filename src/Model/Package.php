<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use Nexcess\Sdk\Model\Model;

/**
 * Represents a service package.
 */
class Package extends Model {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = ['id' => 'package_id'];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['package_id'];

  /** {@inheritDoc} */
  const READONLY_NAMES = [
    'name',
    'bandwidth',
    'monthly_fee',
    'setup_fee',
    'overage_fee',
    'type',
    'cpu_used',
    'ip_num',
    'auto_renew',
    'virt_cpu',
    'virt_disk',
    'virt_ram',
    'user_concurrency',
    'user_autoscale_concurrency',
    'environment_type',
    'description',
    'hdd_count'
  ];
}
