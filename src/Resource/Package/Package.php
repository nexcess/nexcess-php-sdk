<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Package;

use Nexcess\Sdk\Resource\Model;

/**
 * Represents a service package.
 */
class Package extends Model {

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'package_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['package_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'auto_renew',
    'bandwidth',
    'cpu_used',
    'description',
    'environment_type',
    'identity',
    'hdd_count',
    'ip_num',
    'monthly_fee',
    'name',
    'overage_fee',
    'setup_fee',
    'type',
    'user_autoscale_concurrency',
    'user_concurrency',
    'virt_cpu',
    'virt_disk',
    'virt_ram'
  ];
}
