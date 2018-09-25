<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\Exception;

class ServiceException extends Exception {
  const NOT_CANCELLABLE = 1;

  const INFO = [
    self::NOT_CANCELLABLE => ['message' => 'service.not_cancelable'],
  ];
}
