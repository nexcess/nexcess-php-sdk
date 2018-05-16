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
  Model\App,
  Model\Client,
  Model\Cloud,
  Model\Collector as Collection,
  Model\Package,
  Model\Service,
  Util\Util
};

/**
 * Cloud Account (virtual hosting).
 */
class CloudAccount extends Service {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = [
    'id' => 'service_id',
    'cloud_id' => 'cloud_account_id'
  ];

  /** {@inheritDoc} */
  const PROPERTY_COLLAPSED = [
    'cloud' => 'cloud_id',
    'package' => 'package_id'
  ];

  /** {@inheritDoc} */
  const PROPERTY_MODELS = [
    //'client' => Client::class,
    'cloud_account_app' => App::class,
    'location' => Cloud::class,
    'package' => Package::class,
  ];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['service_id'];

  /** {@inheritDoc} */
  const READONLY_NAMES = [
    'cloud',
    'cloud_account_app',
    'cloud_account_domain',
    'cloud_account_environment',
    'cloud_account_id',
    'cloud_account_ip',
    'cloud_account_status',
    'cloud_account_temp_domain',
    'description',
    'host',
    'identity',
    'location',
    'status',
    'type'
  ];

  public function sync(array $data, bool $hard = false) : Modelable {
    if (isset($data['cloud_account'])) {
      $cloud = $data['cloud_account'];
      unset($data['cloud_account']);
      foreach ($cloud as $key => $value) {
        $data["cloud_account_{$key}"] = $value;
      }
    }

    return parent::sync($data, $hard);
  }
}
