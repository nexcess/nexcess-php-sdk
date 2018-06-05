<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\VirtGuestCloud;

use Nexcess\Sdk\ {
  Resource\Cloud\Cloud,
  Resource\CloudAccount\CloudAccount,
  Resource\Modelable,
  Resource\Package\Package,
  Resource\Order\Order,
  Resource\Service
};

/**
 * Cloud Account (virtual hosting) service object.
 */
class VirtGuestCloud extends Service {

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['cloud' => 'location'] +
    Service::_PROPERTY_ALIASES;

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [
    'cloud_account' => 'cloud_account_id',
    'package' => 'package_id'
  ];

  protected const _PROPERTY_COLLECTIONS = [
    'child_cloud_accounts' => CloudAccount::class
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [
    'cloud_account' => CloudAccount::class,
    'location' => Cloud::class,
    'order' => Order::class,
    'package' => Package::class
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['service_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'auto_renew',
    'bandwidth',
    'billing_term',
    'cancellable_override_expire_date',
    'can_change_root_password',
    'child_cloud_accounts',
    'client_id',
    'cloud_account',
    'cloud_id',
    'description',
    'dev_account_count',
    'discount_id',
    'environment_type',
    'has_console',
    'has_paypal_subscription',
    'has_stored_password',
    'host',
    'identity',
    'is_cancellable',
    'is_rebootable',
    'last_bill_date',
    'location',
    'next_bill_date',
    'nickname',
    'order',
    'override_addons',
    'override_percent',
    'override_price',
    'package',
    'parent_id',
    'paypal_subscribe_link',
    'pricing_type',
    'service_id',
    'settings',
    'start_date',
    'state',
    'status',
    'term',
    'turnup_date',
    'type'
  ];

  /**
   * {@inheritDoc}
   */
  public function sync(array $data, bool $hard = false) : Modelable {
    $bandwidth_keys = [
      'bandwidth_overage_fee',
      'bandwidth_profile_id',
      'override_bandwidth'
    ];
    foreach ($bandwidth_keys as $key) {
      if (isset($data[$key])) {
        $bandwidth_key = strtr($key, ['bandwidth_' => '', '_bandwidth' => '']);
        $data['bandwidth'][$bandwidth_key] = $data[$key];
      }
    }

    return parent::sync($data, $hard);
  }
}
