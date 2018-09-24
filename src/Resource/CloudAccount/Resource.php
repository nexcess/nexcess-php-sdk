<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use Nexcess\Sdk\ {
  Resource\App\Resource as App,
  Resource\Model,
  Resource\VirtGuestCloud\Resource as Service,
  Util\Util
};

/**
 * Cloud Account (virtual hosting).
 */
class Resource extends Model {

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = [
    'id' => 'account_id',
    'location' => 'service.location',
    'software' => 'environment.software',
    'status' => 'service.status'
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [
    'app' => 'app_id',
    'parent_account' => 'parent_account_id',
    'service' => 'service_id'
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [
    'app' => App::class,
    'parent_account' => self::class,
    'service' => Service::class,
  ];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['account_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'app',
    'deploy_date',
    'domain',
    'environment',
    'environment.software',
    'identity',
    'ip',
    'is_dev_account',
    'options',
    'parent_account',
    'service',
    'service.location',
    'service.status',
    'state',
    'status',
    'temp_domain',
    'unix_username'
  ];
}
