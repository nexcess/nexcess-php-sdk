<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use Nexcess\Sdk\Model\Model;

/**
 * Represents a cloud (e.g., servers that do cloud stuff).
 */
class Cloud extends Model {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = [
    'id' => 'cloud_id',
    'identity' => 'location_code'
  ];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['cloud_id'];

  /** {@inheritDoc} */
  const READONLY_NAMES = [
    'country',
    'location',
    'location_code',
    'status',
    'type'
  ];
}
