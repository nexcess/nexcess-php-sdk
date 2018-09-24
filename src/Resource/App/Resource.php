<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\App;

use Nexcess\Sdk\Resource\Model;

/**
 * Represents an App Environment for a cloud account.
 */
class Resource extends Model {

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'app_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['app_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'identity',
    'name',
    'version',
    'type',
    'status'
  ];
}
