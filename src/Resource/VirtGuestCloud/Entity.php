<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\VirtGuestCloud;

use Nexcess\Sdk\ {
  Resource\Cloud\Entity as Cloud,
  Resource\CloudAccount\Entity as CloudAccount,
  Resource\Modelable,
  Resource\Package\Entity as Package,
  Resource\Order\Entity as Order,
  Resource\Service\Entity as Service
};

/**
 * Cloud Account (virtual hosting) service object.
 */
class Entity extends Service {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'VirtGuestCloud';

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
   * Gets php versions available for a given cloud account to use.
   *
   * @param Entity $entity The subject cloud account
   * @return string[] List of available php major.minor versions
   */
  public function getAvailablePhpVersions() : array {
    return $this->_getEndpoint()->getAvailablePhpVersions($this);
  }

  /**
   * Switches PHP versions active on this service's primary cloud account.
   *
   * @param string $version Desired PHP version
   * @return Entity $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(Entity $entity, string $version) : Endpoint {
    $this->_getEndpoint()->setPhpVersion($this, $version)->wait();
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function sync(array $data, bool $hard = false) : Modelable {
    // remove "bandwidth" pre/suffixes
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
