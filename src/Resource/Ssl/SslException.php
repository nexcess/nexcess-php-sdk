<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Ssl;

use Nexcess\Sdk\Exception;

/**
 * Error conditions for Ssl resources.
 */
class SslException extends Exception {
  /** @var int No certificates were found to match the given filter. */
  const NO_MATCHING_CERTS = 1;

  /** {@inheritDoc} */
  const INFO = [
    self::NO_MATCHING_CERTS =>
      ['message' => 'resource.Ssl.Exception.no_matching_certs'],
  ];
}
