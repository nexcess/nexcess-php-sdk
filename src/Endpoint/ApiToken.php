<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\CrudEndpoint,
  Model\ApiToken as ApiTokenModel
};

/**
 * API actions for portal Login.
 */
class ApiToken extends CrudEndpoint {

  /** {@inheritDoc} */
  const PROPERTY_NAMES = ['service_id', 'name'];

  /** {@inheritDoc} */
  const ENDPOINT = 'api-token';

  /** {@inheritDoc} */
  const MODEL_NAME = ApiTokenModel::class;
}
