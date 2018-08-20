<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests;

use Nexcess\Sdk\Exception;

/**
 * Error cases involving resources (models).
 */
class TestException extends Exception {

  /** @var int Unable to read resource file. */
  const UNREADABLE_RESOURCE = 1;

  /** @var int Resource type is not supported. */
  const UNSUPPORTED_RESOURCE_TYPE = 2;

  /** @var int Use the sandbox to get an endpoint subject instance. */
  const USE_SANDBOX_INSTEAD = 3;

  /** {@inheritDoc} */
  const INFO = [
    self::UNREADABLE_RESOURCE =>
      ['message' => 'sdk.tests.unreadable_resource'],
    self::UNSUPPORTED_RESOURCE_TYPE =>
      ['message' => 'sdk.tests.unsupported_resource_type'],
    self::USE_SANDBOX_INSTEAD => ['message' => 'sdk.tests.use_sandbox_instead']
  ];
}
