<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\ApiToken;

use Nexcess\Sdk\ {
  Resource\ApiToken\ApiToken,
  Resource\WritableEndpoint
};

/**
 * API actions for portal Login.
 */
class Endpoint extends WritableEndpoint {

  /** {@inheritDoc} */
  protected const _URI = 'api-token';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = ApiToken::class;
}
