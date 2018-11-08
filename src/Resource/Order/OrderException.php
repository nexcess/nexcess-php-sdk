<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Order;

use Nexcess\Sdk\Exception;

/**
 * Error conditions for order resources.
 */
class OrderException extends Exception {

  /** @var int Can't parse a service from given data. */
  const INVALID_SERVICE_DATA = 1;

  /** {@inheritDoc} */
  const INFO = [
    self::INVALID_SERVICE_DATA =>
      ['message' => 'resource.Order.Exception.invalid_service_data']
  ];
}
