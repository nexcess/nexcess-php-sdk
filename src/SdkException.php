<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use Nexcess\Sdk\Exception;

class SdkException extends Exception {

  /** @var int Feature is not yet implemented. */
  const NOT_IMPLEMENTED = 1;

  /** @var int Missing api token. */
  const MISSING_API_TOKEN = 5;

  /** @var int Unknown error. */
  const UNKNOWN_ERROR = 6;

  /** @var int Error while invoking callback. */
  const CALLBACK_ERROR = 7;

  /** @var int Attempt to read request log when log is not enabled. */
  const REQUEST_LOG_NOT_ENABLED = 10;

  /** @var int Attempt to use a debug-only method when debug is not enabled. */
  const DEBUG_NOT_ENABLED = 11;

  /** @var int Request for endpoint that doesn't exist. */
  const NO_SUCH_ENDPOINT = 12;

  /** @var int Request for model that doesn't exist. */
  const NO_SUCH_MODEL = 13;

  /** {@inheritDoc} */
  const INFO = [
    self::NOT_IMPLEMENTED => ['message' => 'sdk.not_implemented'],
    self::MISSING_API_TOKEN => ['message' => 'sdk.missing_api_token'],
    self::UNKNOWN_ERROR => ['message' => 'sdk.unknown_error'],
    self::CALLBACK_ERROR => ['message' => 'sdk.callback_error'],
    self::REQUEST_LOG_NOT_ENABLED =>
      ['message' => 'sdk.request_log_not_enabled'],
    self::DEBUG_NOT_ENABLED => ['message' => 'sdk.debug_not_enabled'],
    self::NO_SUCH_MODEL => ['message' => 'sdk.no_such_model'],
    self::NO_SUCH_ENDPOINT => ['message' => 'sdk.no_such_endpoint']
  ];
}
