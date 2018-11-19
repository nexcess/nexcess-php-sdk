<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\CloudAccount;

use Nexcess\Sdk\Exception;

/**
 * Error conditions for cloud account resources.
 */
class CloudAccountException extends Exception {

  /** @var int Invalid download target for a cloud account backup. */
  const INVALID_PATH = 1;

  /** @var int Target download filename already exists. */
  const FILE_EXISTS = 2;

  /** @var int Requested cloud account backup was not found. */
  const BACKUP_NOT_FOUND = 3;

  /** @var int Attempt to access/operate on an invalid backup instance. */
  const INVALID_BACKUP = 4;

  /** @var int Backup file failed to open. */
  const INVALID_STREAM = 5;

  /** @var int No cloud account associated with backup instance. */
  const OWNER_UNKNOWN = 6;

  /** @var int Attempt to download an incomplete backup. */
  const INCOMPLETE_BACKUP = 7;

  /** {@inheritDoc} */
  const INFO = [
    self::INVALID_PATH =>
      ['message' => 'resource.CloudAccount.Exception.invalid_path'],
    self::FILE_EXISTS =>
      ['message' => 'resource.CloudAccount.Exception.file_exists'],
    self::BACKUP_NOT_FOUND =>
      ['message' => 'resource.CloudAccount.Exception.backup_not_found'],
    self::INVALID_BACKUP =>
      ['message' => 'resource.CloudAccount.Exception.invalid_backup'],
    self::INVALID_STREAM =>
      ['message' => 'resource.CloudAccount.Exception.invalid_stream'],
    self::OWNER_UNKNOWN =>
      ['message' => 'resource.CloudAccount.Exception.owner_unknown'],
    self::INCOMPLETE_BACKUP =>
      ['message' => 'resource.CloudAccount.Exception.incomplete_backup']
  ];
}
