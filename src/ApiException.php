<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use Nexcess\Sdk\Exception;

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
  const WRONG_MODEL_FOR_URI = 6;

  /** @var int No api token or other auth (i.e., 401 response). */
  const UNAUTHORIZED = 7;

  /** @var int User not authorized for action (i.e., 403 response). */
  const FORBIDDEN = 8;

  /** @var int Endpoint or action not found (i.e., 404 response). */
  const NOT_FOUND = 9;

  /** @var int Data submitted to endpoint has errors (i.e., 422 response). */
  const UNPROCESSABLE_ENTITY = 10;

  /** @var int Attempt to create/update on a read-only endpoint. */
  const ENDPOINT_NOT_WRITABLE = 11;

  /** @var int Invalid argument pass to __call(). */
  const WRONG_CALL_ARG = 12;

  /** @var int Tried to call a nonexistant endpoint. */
  const NO_SUCH_ENDPOINT = 13;

  /** @var int Bad API response to list query. */
  const GOT_MALFORMED_LIST = 14;

  /** @var int Attempt to create on a non-creatable endpoint. */
  const NOT_CREATABLE = 15;

  /** @var int Create request failed. */
  const CREATE_FAILED = 16;

  /** @var int Tried to decode an invalid or not-json response. */
  const NOT_DECODABLE = 17;

  /** {@inheritDoc} */
  const INFO = [
    self::CANNOT_CONNECT => ['message' => 'api.cannot_connect'],
    self::BAD_REQUEST => ['message' => 'api.bad_request'],
    self::SERVER_ERROR => ['message' => 'api.server_error'],
    self::REQUEST_FAILED => ['message' => 'api.request_failed'],
    self::MISSING_ID => ['message' => 'api.missing_id'],
    self::WRONG_MODEL_FOR_URI => ['message' => 'api.wrong_model_for_uri'],
    self::UNAUTHORIZED => ['message' => 'api.unauthorized'],
    self::FORBIDDEN => ['message' => 'api.forbidden'],
    self::NOT_FOUND => ['message' => 'api.not_found'],
    self::UNPROCESSABLE_ENTITY => ['message' => 'api.unprocessable_entity'],
    self::ENDPOINT_NOT_WRITABLE => ['message' => 'api.endpoint_not_writable'],
    self::WRONG_CALL_ARG => ['message' => 'api.wrong_call_arg'],
    self::NO_SUCH_ENDPOINT => ['message' => 'api.no_such_endpoint'],
    self::GOT_MALFORMED_LIST => ['message' => 'api.got_malformed_list'],
    self::NOT_CREATABLE => ['message' => 'api.not_creatable'],
    self::CREATE_FAILED => ['message' => 'api.create_failed'],
    self::NOT_DECODABLE => ['message' => 'api.not_decodable']
  ];
}
