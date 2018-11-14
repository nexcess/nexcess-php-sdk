<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Cloud;

use Nexcess\Sdk\Resource\Model;

/**
 * Represents a cloud (e.g., servers that do cloud stuff).
 */
class Cloud extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Cloud';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'cloud_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['cloud_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'country',
    'identity',
    'location',
    'location_code',
    'status',
    'type'
  ];

  /**
   * Gets a nicer cloud "identity."
   *
   * @return string Cloud identity
   */
  public function getIdentity() : string {
    return "{$this->_values['location']} ({$this->_values['identity']})";
  }
}
