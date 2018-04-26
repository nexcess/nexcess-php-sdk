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
  const PROPERTY_ALIASES = ['id' => 'token_id', 'identity' => 'name'];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['token_id', 'name'];

  /** {@inheritDoc} */
  const READONLY_NAMES = ['token'];

  /**
   * Api tokens can be viewed only when created.
   *
   * @return string The new API token
   * @throws ModelException If token has already been viewed
   */
  public function getToken() : string {
    if (isset($this->_values['token'])) {
      return $this->_values['token'];
    }

    throw new ModelException(ModelException::API_TOKEN_NOT_VIEWABLE);
  }
}
