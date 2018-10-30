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
  Resource\CloudAccount\CloudAccountException,
  Resource\Model,
  Resource\Modelable,
  Util\Util
};

/**
 * Backup Entity
 */
class Backup extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'CloudAccount';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = [];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'filepath',
    'filename',
    'filesize',
    'filesize_bytes',
    'type',
    'download_url',
    'filedate',
    'complete'
  ];

  /**
   * Download this backup
   *
   * @param string $path Where to save the file to
   * @throws CloudAccountException
   * @return Promise A Guzzle Promise
   */
  public function download(string $path) : bool {
    if (! $this->isReal()) {
      throw new CloudAccountException(
        CloudAccountException::INVALID_BACKUP,
        ['action' => __METHOD__]
      );
    }
    return $this->_getEndpoint()->downloadBackup($this->get('filename'), $path)->wait();
  }

  /**
   * Delete this backup
   *
   * @throws CloudAccountException
   * @return Promise A Guzzle Promise
   */
  public function delete() : bool {
    if (! $this->isReal()) {
      throw new CloudAccountException(
        CloudAccountException::INVALID_BACKUP,
        ['action' => __METHOD__]
      );
    }
    return $this->_getEndpoint()->deleteBackup($this->get('filename'))->wait();
  }

  /**
   * Compare a Backup object to this one
   *
   * @param Modelable $other The other object to compare
   * @return bool true if the file names match
   */
  public function equals(Modelable $other) : bool {
    return ($other instanceof $this) &&
      ($other->get('filename') === $this->get('filename'));
  }

  /**
   * Check to see if this is a complete object
   *
   * @return bool true if it has a non-empty file name
   */
  public function isReal() : bool {
    return ! empty($this->_values['filename']);
  }

}
