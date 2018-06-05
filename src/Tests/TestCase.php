<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests;

use Throwable;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

use Nexcess\Sdk\ {
  Sandbox\Sandbox,
  Util\Config
};

/**
 * Base unit test for Nexcess-SDK tests.
 */
abstract class TestCase extends PHPUnitTestCase {

  /** @var string Filesystem path to test resources directory. */
  public const TEST_RESOURCE_PATH = __DIR__ . '/resources';

  /**
   * Sets phpunit's expectExcpetion*() methods from an example.
   *
   * @param Throwable $expected Exception the test expects to be thrown
   */
  public function setExpectedException(Throwable $expected) {
    parent::expectException(get_class($expected));

    $code = $expected->getCode();
    if (! empty($code)) {
      $this->expectExceptionCode($code);
    }
  }

  /**
   * Gets a sandbox for a test.
   *
   * @param Config $config SDK configuration object
   * @param callable $request_handler Callback to handle requests
   * @param callable $exception_handler Callback to handle exceptions
   * @return Sandbox
   */
  protected function _getSandbox(
    Config $config = null,
    callable $request_handler = null,
    callable $exception_handler = null
  ) : Sandbox {
    $config = $config ?? new Config([]);
    return new Sandbox($config, $request_handler, $exception_handler);
  }
}
