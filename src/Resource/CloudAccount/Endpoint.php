<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use Closure,
  Throwable;

use GuzzleHttp\Cookie\CookieJar;

use Nexcess\Sdk\ {
  ApiException,
  Resource\CanCreate,
  Resource\CloudAccount\Backup,
  Resource\CloudAccount\CloudAccountException,
  Resource\CloudAccount\Entity,
  Resource\Creatable,
  Resource\Collection,
  Resource\Endpoint as BaseEndpoint,
  Resource\Modelable,
  Resource\PromisedResource,
  Util\Util
};

/**
 * API endpoint for Cloud Accounts (virtual hosting).
 */
class Endpoint extends BaseEndpoint implements Creatable {
  use CanCreate;

  /** {@inheritDoc} */
  public const MODULE_NAME = 'CloudAccount';

  /** {@inheritDoc} */
  protected const _URI = 'cloud-account';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _PARAMS = [
    'clearNginxCache' => [],
    'create' => [
      'app_id' => [Util::TYPE_INT],
      'cloud_id' => [Util::TYPE_INT],
      'domain' => [Util::TYPE_STRING],
      'install_app' => [Util::TYPE_BOOL, false],
      'package_id' => [Util::TYPE_INT]
    ],
    'createDevAccount' => [
      'copy_account' => [Util::TYPE_BOOL],
      'domain' => [Util::TYPE_STRING],
      'package_id' => [Util::TYPE_INT],
      'ref_cloud_account_id' => [Util::TYPE_INT],
      'ref_service_id' => [Util::TYPE_INT],
      'ref_type' => [Util::TYPE_STRING],
      'scrub_account' => [Util::TYPE_BOOL]
    ],
    'setPhpVersion' => ['version' => [Util::TYPE_STRING]]
  ];

  /**
   * Requests cancellation of the service associated with a cloud account.
   *
   * Note, this creates a cancellation request,
   * and does not delete the cloud account directly.
   * Use this method to cancel a primary cloud account, not a dev account.
   *
   * @param Entity $entity Cloud Server instance
   * @param array $survey Cancellation survey
   * @return PromisedResource
   */
  public function cancel(Entity $entity, array $survey) : PromisedResource {
    return $entity->get('service')->cancel($survey);
  }

  /**
   * Creates a development-mode CloudAccount based on given CloudAccount.
   * Note, the given CloudAccount MUST NOT be a development account itself.
   *
   * @param Entity $entity CloudAccount instance
   * @return PromisedResource Promise resolving to new dev account
   * @throws ApiException On failure
   */
  public function createDevAccount(
    Entity $entity,
    array $data
  ) : PromisedResource {
    $data = [
      'domain' => ($data['domain'] ?? 'dev') . ".{$entity->get('domain')}",
      'ref_cloud_account_id' => $entity->getId(),
      'ref_service_id' => $entity->get('service')->getId(),
      'ref_type' => 'development'
    ] + $data
      + ['copy_account' => true, 'scrub_account' => true];
    $this->_validateParams(__FUNCTION__, $data);

    // sync; no waiting
    return $this->_buildPromise(
      $this->getModel()->sync(
        Util::decodeResponse($this->_post(static::_URI, ['json' => $data]))
      )
    );
  }

  /**
   * Gets php versions available for a given cloud account to use.
   *
   * @param Entity $entity The subject cloud account
   * @return string[] List of available php major.minor versions
   */
  public function getAvailablePhpVersions(Entity $entity) : array {
    return $entity->get('service')->getAvailablePhpVersions();
  }

  /**
   * Switches PHP versions active on an existing cloud account.
   *
   * @param Entity $entity Cloud server instance
   * @param string $version Desired PHP version
   * @return PromisedResource A promise resolving to the updated entity
   * @throws ApiException If request fails
   */
  public function setPhpVersion(
    Entity $entity,
    string $version
  ) : PromisedResource {
    $r = $this->_post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'set-php-version', 'php_version' => $version]]
    );

    // sync; no waiting
    return $this->_buildPromise($entity);
  }

  /**
   * {@inheritDoc}
   * Overridden to handle both Entity and secondary Entities (Backup).
   */
  public function sync(Modelable $model) : Modelable {
    if ($model instanceof Backup) {
      return $model->sync(
        $this->getBackup($model->getCloudAccount(), $model->get('filename'))
          ->wait()
          ->toArray()
      );
    }

    return parent::sync($model);
  }

  /**
   * Create a backup
   *
   * @param Entity An instance of cloud account entity.
   * @return PromisedResource Resolves to Backup
   * @throws ApiException If request fails
   */
  public function createBackup(Entity $entity) : PromisedResource {
    return $this->_buildPromise(
      $this->getModel(Backup::class)
        ->setCloudAccount($entity)
        ->sync(
          Util::decodeResponse(
            $this->_post(self::_URI . "/{$entity->getId()}/backup")
          )
        )
    )->waitUntil($this->_waitUntilBackupComplete());
  }

  /**
   * Waits for a backup to be complete.
   *
   * @return Closure
   */
  protected function _waitUntilBackupComplete() : Closure {
    return function ($backup) {
      $this->sync($backup);
      return $backup->get('complete');
    };
  }

  /**
   * Return a list of backups
   *
   * @return Collection Of Backups
   * @throws ApiException If request fails
   */
  public function getBackups(Entity $entity) : Collection {
    $collection = new Collection(Backup::class);

    foreach ($this->_fetchBackupList($entity) as $backup) {
      $collection->add(
        $this->getModel(Backup::class)->setCloudAccount($entity)->sync($backup)
      );
    }

    return $collection;
  }

  /**
   * Return a specific backup
   *
   * @param string $file_name The unique file name for the backup to retrieve.
   * @return PromisedResource
   * @throws ApiException If request fails
   */
  public function getBackup(
    Entity $entity,
    string $file_name
  ) : PromisedResource {
    return $this->_buildPromise($this->_findBackup($entity, $file_name));
  }

  /**
   * Download a specific backup
   *
   * @param string $file_name The unique file name for the backup to retrieve.
   * @param string $path the directory to store the download in.
   * @throws ApiException If request fails
   * @throws Exception
   */
  public function downloadBackup(
    Entity $entity,
    string $file_name,
    string $path
  ) {

    if (! file_exists($path) || ! is_dir($path)) {
      throw new CloudAccountException(
        CloudAccountException::INVALID_PATH,
        ['filepath' => $path]
      );
    }

    $path = trim($path);
    if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
      $path .= DIRECTORY_SEPARATOR;
    }

    $save_to = $path . $file_name;
    if (file_exists($save_to)) {
      throw new CloudAccountException(
        CloudAccountException::FILE_EXISTS,
        ['filename' => $save_to]
      );
    }

    $stream = @fopen($save_to, 'w');
    if (! is_resource($stream)) {
      throw new CloudAccountException(
        CloudAccountException::INVALID_STREAM,
        ['filename' => $save_to]
      );
    }

    try {
      $this->_get(
        $this->_findBackup($entity, $file_name)->get('download_url'),
        [
          'cookies' => (new CookieJar()),
          'sink' => $stream,
          'verify' => false
        ]
      );
    } catch (Throwable $e) {
      fclose($stream);
      unlink($save_to);
      throw $e;
    }
  }

  /**
   * Delete a specific backup
   *
   * @param Entity $entity The entity representing the backup
   * @param string $file_name The unique file name for the backup to retrieve.
   * @throws ApiException If request fails
   */
  public function deleteBackup(Entity $entity, string $file_name)  {
    $this->_delete(self::_URI . "/{$entity->getId()}/backup/{$file_name}");
  }

  /**
   * Find a specific backup from the list.
   *
   * @param string $file_name The unique file name for the backup to retrieve.
   *
   * @return Backup
   * @throws ApiException If request fails
   * @throws CloudAccountException If backup not found
   */
  protected function _findBackup(Entity $entity, string $file_name) : Backup {
    foreach ($this->_fetchBackupList($entity) as $backup) {
      if ($backup['filename'] === $file_name) {
        return $this->getModel(Backup::class)
          ->setCloudAccount($entity)
          ->sync($backup);
      }
    }

    throw new CloudAccountException(
      CloudAccountException::BACKUP_NOT_FOUND,
      ['name' => $file_name]
    );
  }

  /**
   * Fetch the list of backups for a given cloud account
   *
   * @return array
   * @throws ApiException If request fails
   */
  protected function _fetchBackupList(Entity $entity) : array {
    return Util::decodeResponse(
      $this->_get(self::_URI . "/{$entity->getId()}/backup")
    );
  }

  /**
   * Clear Nginx Cache
   *
   * @return PromisedResource Promise resolving to the given entity
   * @throws ResourceException If endpoint not available
   * @throws ApiException If request fails
   */
  public function clearNginxCache(Entity $entity) : PromisedResource {
    $this->_post(
      self::_URI . "/{$entity->getId()}",
      ['json' => ['_action' => 'purge-cache']]
    );

    // sync; no waiting
    return $this->_buildPromise($entity);
  }
}
