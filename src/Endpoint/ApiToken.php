<?php
/**
 * @package Nexcess-SDK
 * @subpackage User
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\CrudEndpoint,
  Response
};

/**
 * API actions for portal Login.
 */
class ApiToken extends CrudEndpoint {

  /** {@inheritDoc} */
  const ADD_VALUE_MAP = ['name' => ''];

  /** {@inheritDoc} */
  const EDIT_VALUE_MAP = ['name' => ''];

  /** {@inheritDoc} */
  const ENDPOINT = 'api-token';
}
