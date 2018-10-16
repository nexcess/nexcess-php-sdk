<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use Nexcess\Sdk\ {
  Resource\App\Entity as App,
  Resource\Model,
  Resource\VirtGuestCloud\Entity as Service,
  Util\Util
};

/**
 * Cloud Account (virtual hosting).
 */
class Entity extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'CloudAccount';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = [
    'id' => 'account_id',
    'location' => 'service.location',
    'php_version' => 'environment.software.php.version',
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
    'environment.software.php.version',
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

  /**
   * Switches PHP version on this cloud account.
   *
   * @param string $version Target PHP version
   * @return Entity $this
   * @throws ResourceException If endpoint not available
   * @throws ApiException If request fails
   */
  public function setPhpVersion(string $version) : Entity {
    $this->_getEndpoint()->setPhpVersion($this, $version)->wait();
    return $this;
  }

  /**
   * Clear Nginx Cache
   *
   * @return Entity $this
   * @throws ResourceException If endpoint not available
   * @throws ApiException If request fails
   */
  public function clearNginxCache() : Entity {
    $this->_getEndpoint()->clearNginxCache()->wait();
    return $this;
  }

}
