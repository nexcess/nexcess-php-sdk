<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\ApiToken;

use Nexcess\Sdk\ {
  Resource\ApiToken\ApiTokenException,
  Resource\Model,
};

/**
 * API Token.
 */
class ApiToken extends Model {

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'token_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['token_id', 'name'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = ['token', 'identity'];

  /**
   * Api tokens can be viewed only when created.
   *
   * @return string The new API token
   * @throws ApiTokenException If token has already been viewed
   */
  public function getToken() : string {
    if (isset($this->_values['token'])) {
      return $this->_values['token'];
    }

    throw new ApiTokenException(ApiTokenException::TOKEN_NOT_VIEWABLE);
  }
}
