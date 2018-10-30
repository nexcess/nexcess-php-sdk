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
   * Creates a development-mode account based on this cloud account.
   *
   * Note, will fail if this cloud account is not a "primary" account.
   *
   * @param array $data Map of settings for new dev account:
   *  - bool "copy_account" (optional, defaults to true) Copy settings/data?
   *  - int "package_id" (required) Service package id
   *  - bool "scrub_account" (optional, defaults to true) Obfuscate PII?
   *  - string "subdomain" (optional, defaults to "dev.") Dev subdomain
   * @return Entity The new dev account on success
   * @throws ResourceException If endpoint not available
   * @throws ApiException If request fails
   */
  public function createDevAccount(array $data) : Entity {
    $endpoint = $this->_getEndpoint();
    $dev = $endpoint->createDevAccount($this, $data);
    $endpoint->wait();
    return $dev;
  }

  /**
   * Gets php versions available for this cloud account to use.
   *
   * @return string[] List of available php major.minor versions
   */
  public function getAvailablePhpVersions() : array {
    return $this->_getEndpoint()->getAvailablePhpVersions($this);
  }

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
    $this->_getEndpoint()->clearNginxCache($this);
    return $this;
  }

  /**
   * Get a list of backups for a cloud account
   *
   * @return Collection
   */
  public function getBackups() : Collection {
    return $this->_getEndpoint()->getBackups($this)->wait();
  }

  /**
   * Get a list of backups for a cloud account
   *
   * @return Backup
   */
  public function getBackup(string $filename) : Backup {
    return $this->_getEndpoint()->getBackup($this, $filename)->wait();
  }

  /**
   * Download a backup
   *
   * @return Backup
   */
  public function downloadBackup(string $filename, string $path)  {
    $this->_getEndpoint()->downloadBackup($this, $filename, $path)->wait();
  }

  /**
   * Delete a backup
   *
   * @return Backup
   */
  public function deleteBackup(string $filename) {
    $this->_getEndpoint()->downloadBackup($this, $filename)->wait();
  }
  
}
