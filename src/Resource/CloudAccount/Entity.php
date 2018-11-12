<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use Nexcess\Sdk\ {
  ApiException,
  Resource\App\Entity as App,
  Resource\CloudAccount\Endpoint,
  Resource\Collection,
  Resource\Model,
  Resource\VirtGuestCloud\Entity as Service,
  Resource\ResourceException
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
    'software' => 'environment.software'
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
    assert($endpoint instanceof Endpoint);

    return $endpoint->createDevAccount($this, $data);
  }

  /**
   * Gets php versions available for this cloud account to use.
   *
   * @return string[] List of available php major.minor versions
   * @throws ResourceException If endpoint not available
   * @throws ApiException If request fails
   */
  public function getAvailablePhpVersions() : array {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->getAvailablePhpVersions($this);
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
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->setPhpVersion($this, $version);
  }

  /**
   * Clear Nginx Cache
   *
   * @return Entity $this
   * @throws ResourceException If endpoint not available
   * @throws ApiException If request fails
   */
  public function clearNginxCache() : Entity {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->clearNginxCache($this);
  }

  /**
   * Creates a backup of this cloud account.
   *
   * @return Backup
   */
  public function backup() : Backup {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->createBackup($this);
  }

  /**
   * Get a list of backups for a cloud account
   *
   * @return Collection
   */
  public function getBackups() : Collection {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->getBackups($this);
  }

  /**
   * Get an individual backup
   *
   * @return Backup
   */
  public function getBackup(string $filename) : Backup {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->getBackup($this, $filename);
  }
}
