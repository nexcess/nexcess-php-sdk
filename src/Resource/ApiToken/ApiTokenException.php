<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\ApiToken;

use Nexcess\Sdk\Resource\ResourceException;

/**
 * Error conditions involving ApiToken resources.
 */
class ApiTokenException extends ResourceException {

  /** @var int Api tokens can be viewed only when created. */
  const TOKEN_NOT_VIEWABLE = 1;

  /** {@inheritDoc} */
  const INFO = [
    self::TOKEN_NOT_VIEWABLE =>
      ['message' => 'resource.apitoken.token_not_viewable']
  ];
}
