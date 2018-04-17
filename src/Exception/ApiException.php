<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use at\exceptable\Exception as Exceptable;

use Nexcess\Sdk\Util\I18n;

class ApiException extends Exceptable {

  /** @var int Networking/connectivity issue. */
  const CANNOT_CONNECT = 1;

  /** @var int Bad request (i.e., 4xx response). */
  const BAD_REQUEST = 2;

  /** @var int Server error (i.e., 5xx response). */
  const SERVER_ERROR = 3;

  /** @var int Unknown error. */
  const UNKNOWN_ERROR = 4;

  /** @var array[] {@inheritDoc} */
  const INFO = [
    self::CANNOT_CONNECT => ['message' => 'cannot_connect'],
    self::BAD_REQUEST => ['message' => 'bad_request'],
    self::SERVER_ERROR => ['message' => 'server_error'],
    self::UNKNOWN_ERROR => ['message' => 'unknown_error']
  ];
}
