<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Exception;

use Nexcess\Sdk\Exception\Exception;

use Nexcess\Sdk\Util\I18n;

class ApiException extends Exception {

  /** @var int Networking/connectivity issue. */
  const CANNOT_CONNECT = 1;

  /** @var int Bad request (i.e., 4xx response). */
  const BAD_REQUEST = 2;

  /** @var int Server error (i.e., 5xx response). */
  const SERVER_ERROR = 3;

  /** @var int Other guzzle problem. */
  const REQUEST_FAILED = 4;

  /** @var int API failed due to missing item id. */
  const MISSING_ID = 5;

  /** @var int Wrong model subclass passed to endpoint. */
  const WRONG_MODEL_FOR_ENDPOINT = 6;

  /** @var int No api token or other auth (i.e., 401 response). */
  const UNAUTHORIZED = 7;

  /** @var int User not authorized for action (i.e., 403 response). */
  const FORBIDDEN = 8;

  /** @var int Endpoint or action not found (i.e., 404 response). */
  const NOT_FOUND = 9;

  /** @var int Data submitted to endpoint has errors (i.e., 422 response). */
  const UNPROCESSABLE_ENTITY = 10;

  /** {@inheritDoc} */
  const INFO = [
    self::CANNOT_CONNECT => ['message' => 'cannot_connect'],
    self::BAD_REQUEST => ['message' => 'bad_request'],
    self::SERVER_ERROR => ['message' => 'server_error'],
    self::REQUEST_FAILED => ['message' => 'request_failed'],
    self::MISSING_ID => ['message' => 'missing_id'],
    self::WRONG_MODEL_FOR_ENDPOINT =>
      ['message' => 'wrong_model_for_endpoint'],
    self::UNAUTHORIZED => ['message' => 'unauthorized'],
    self::FORBIDDEN => ['message' => 'forbidden'],
    self::NOT_FOUND => ['message' => 'not_found'],
    self::UNPROCESSABLE_ENTITY => ['message' => 'unprocessable_entity']
  ];
}
