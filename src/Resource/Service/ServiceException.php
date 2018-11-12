<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Service;

use Nexcess\Sdk\Exception;

/**
 * Error conditions involving Service resources.
 */
class ServiceException extends Exception {

  /** @var int Attempt to cancel a service that is not cancellable. */
  const NOT_CANCELLABLE = 1;

  /** @var int Invalid service type. */
  const NO_SUCH_SERVICE_MODEL = 2;

  /** {@inheritDoc} */
  const INFO = [
    self::NOT_CANCELLABLE => ['message' => 'service.not_cancelable'],
    self::NO_SUCH_SERVICE_MODEL =>
      ['message' => 'service.no_such_service_model']
  ];
}
