<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\Exception;

/**
 * Error conditions involving Package resources.
 */
class ServiceException extends Exception {

  /** @var int Attempt to cancel a service that is not cancellable. */
  const NOT_CANCELLABLE = 1;

  /** {@inheritDoc} */
  const INFO = [
    self::NOT_CANCELLABLE => ['message' => 'service.not_cancelable'],
  ];
}
