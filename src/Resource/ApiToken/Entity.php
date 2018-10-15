<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\ApiToken;

use Nexcess\Sdk\Resource\Model;

/**
 * API Token.
 */
class Entity extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'ApiToken';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'token_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['token_id', 'name'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = ['token', 'identity'];
}
