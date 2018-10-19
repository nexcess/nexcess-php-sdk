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

  public function download(string $path) : bool {
    if (! $this->isReal()) {
      throw new Exception('##LG_INVALID_FILENAME##');
    }
    return $this->_getEndpoint()->downloadBackup($this->get('filename'), $path);
  }

  public function delete() : bool {
    if (! $this->isReal()) {
      throw new Exception('##LG_INVALID_FILENAME##');
    }
    return $this->_getEndpoint()->deleteBackup($this->get('filename'));
  }

  public function equals(Modelable $other) : bool {
    return ($other instanceof $this) &&
      ($other->get('filename') === $this->get('filename'));
  }

  public function isReal() : bool {
    return ! empty($this->$this->get('filename'));
  }

}
