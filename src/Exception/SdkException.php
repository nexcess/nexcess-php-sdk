<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Exception;

use Nexcess\Sdk\Exception\Exception;

class SdkException extends Exception {

  /** @var int Feature is not yet implemented. */
  const NOT_IMPLEMENTED = 1;

  /** @var int Unknown endpoint. */
  const NO_SUCH_ENDPOINT = 2;

  /** @var int Error from json_decode. */
  const JSON_DECODE_FAILURE = 3;

  /** @var int Invalid translation map or value. */
  const INVALID_LANGUAGE_MAP = 4;

  /** @var int Missing api token. */
  const MISSING_API_TOKEN = 5;

  /** @var int Unknown error. */
  const UNKNOWN_ERROR = 6;

  /** @var int Error while invoking callback. */
  const CALLBACK_ERROR = 7;

  /** @var int Invalid arguemnt for create/update action. */
  const INVALID_CREATE_OR_UPDATE = 8;

  /** @var int Invalid argument for retrieve action. */
  const INVALID_RETRIEVE = 9;

  /** @var int Attempt to read request log when log is not enabled. */
  const REQUEST_LOG_NOT_ENABLED = 10;

  /** @var int Attempt to use a debug-only method when debug is not enabled. */
  const DEBUG_NOT_ENABLED = 11;

  /** {@inheritDoc} */
  const INFO = [
    self::NOT_IMPLEMENTED => ['message' => 'not_implemented'],
    self::NO_SUCH_ENDPOINT => ['message' => 'no_such_endpoint'],
    self::JSON_DECODE_FAILURE => ['message' => 'json_decode_failure'],
    self::INVALID_LANGUAGE_MAP => ['message' => 'invalid_language_map'],
    self::MISSING_API_TOKEN => ['message' => 'missing_api_token'],
    self::UNKNOWN_ERROR => ['message' => 'unknown_error'],
    self::CALLBACK_ERROR => ['message' => 'callback_error'],
    self::INVALID_CREATE_OR_UPDATE =>
      ['message' => 'invalid_create_or_update'],
    self::INVALID_RETRIEVE => ['message' => 'invalid_retrieve'],
    self::REQUEST_LOG_NOT_ENABLED => ['message' => 'request_log_not_enabled'],
    self::DEBUG_NOT_ENABLED => ['message' => 'debug_not_enabled']
  ];
}
