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
class App extends Model {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = ['id' => 'app_id'];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['app_id'];

  /** {@inheritDoc} */
  const READONLY_NAMES = [
    'name',
    'version',
    'type',
    'status'
  ];
}
