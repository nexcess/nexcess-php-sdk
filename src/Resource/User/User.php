<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\User;

use Nexcess\Sdk\Resource\Model;

/**
 * Represents a portal User.
 */
class User extends Model {

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'user_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = [
    'user_id'
  ];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    // @todo
  ];
}
