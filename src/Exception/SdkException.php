<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use Nexcess\Sdk\Exception\Exception;

class SdkException extends Exception {

  /** @var int Unknown endpoint. */
  const NO_SUCH_ENDPOINT = 1;

  /** @var array[] {@inheritDoc} */
  const INFO = [
    self::NO_SUCH_ENDPOINT => ['message' => 'no_such_endpoint']
  ];
}
