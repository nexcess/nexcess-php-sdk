<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Order;

use Nexcess\Sdk\ {
  Resource\Endpoint as ReadableEndpoint,
  Resource\Order\Order
};

/**
 * API endpoint for orders.
 */
class Endpoint extends ReadableEndpoint {

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Order::class;

  /** {@inheritDoc} */
  protected const _URI = 'order';
}
