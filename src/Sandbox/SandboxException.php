<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Sandbox;

use Nexcess\Sdk\Exception;

class SandboxException extends Exception {

  /** @var int An invalid response value was provided. */
  public const INVALID_RESPONSE = 14;

  /** {@inheritDoc} */
  public const INFO = [
    self::INVALID_RESPONSE => ['message' => 'util.exception.invalid_response']
  ];
}
