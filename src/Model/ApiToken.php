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
 * API Token.
 */
class ApiToken extends Model {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = ['id' => 'token_id'];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['token_id', 'name'];

  /** {@inheritDoc} */
  const READONLY_NAMES = ['token'];
}
