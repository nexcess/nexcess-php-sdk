<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests;

use ReflectionObject,
  Throwable;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Nexcess\Sdk\ {
  Sandbox\Sandbox,
  Tests\TestException,
  Util\Config,
  Util\Util
};

/**
 * Base unit test for Nexcess-SDK tests.
 */
abstract class TestCase extends PHPUnitTestCase {

  /** @var string Filesystem path to test resources directory. */
  protected const _RESOURCE_PATH = '';

  /** @var string Fully qualified classname of the class under test. */
  protected const _SUBJECT_FQCN = '';

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

  // phpcs:disable -- @todo typehint "object $object" once we drop 7.1 support
  /**
   * Gets the value of a nonpublic property of an object under test.
   *
   * @param object $object The object to inspect
   * @param string $property The property to access
   * @return mixed|null The property's value if exists; null otherwise
   */
  protected function _getNonpublicProperty($object, string $property) {
    // phpcs:enable
    $ro = new ReflectionObject($object);
    if (! $ro->hasProperty($property)) {
      return null;
    }

    $rp = $ro->getProperty($property);
    $rp->setAccessible(true);
    return $rp->getValue($object);
  }

  /**
   * Gets a test resource.
   *
   * Supports plain text, json, and php resources
   * (php files MUST return a value, and MUST NOT produce output).
   *
   * @param string $name Filename of resource to get
   * @param bool $parse Parse contents?
   * @return mixed Contents of resource on success
   * @throws TestException On failure
   */
  protected function _getResource(string $name, bool $parse = true) {
    $resource = static::_RESOURCE_PATH . "/{$name}";
    if (! is_readable($resource)) {
      throw new TestException(
        TestException::UNREADABLE_RESOURCE,
        ['name' => $name, 'path' => static::_RESOURCE_PATH]
      );
    }

    $type = explode('.', $name);
    $type = end($type);

    if ($type === 'txt' || ! $parse) {
      return file_get_contents($resource);
    }

    if ($type === 'json') {
      return Util::jsonDecode(file_get_contents($resource));
    }

    if ($type === 'php') {
      return require $resource;
    }

    throw new TestException(
      TestException::UNSUPPORTED_RESOURCE_TYPE,
      ['name' => $name, 'type' => $type, 'types' => 'txt|json|php']
    );
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

  // phpcs:disable -- @todo typehint ": object" once we drop 7.1 support
  /**
   * Gets an instance of the class under test.
   *
   * @param mixed ...$constructor_args Arguments for subject's constructor
   * @return object
   */
  protected function _getSubject(...$constructor_args) {
    // phpcs:enable
    $fqcn = static::_SUBJECT_FQCN;
    return new $fqcn(...$constructor_args);
  }

  // phpcs:disable -- @todo typehint "object $object" once we drop 7.1 support
  /**
   * Invokes a nonpublic method on an object under test.
   *
   * @param object $object The object under test
   * @param string $method The method to invoke
   * @param mixed ...$args Argument(s) to use for invocation
   * @return mixed The return value from the method invocation
   */
  protected function _invokeNonpublicMethod(
    $object,
    string $method,
    ...$args
  ) {
    // phpcs:enable
    $ro = new ReflectionObject($object);
    if (! $ro->hasMethod($method)) {
      return;
    }

    $rm = $ro->getMethod($method);
    $rm->setAccessible(true);
    return $rm->invoke($object, ...$args);
  }

  // phpcs:disable -- @todo typehint "object $object" once we drop 7.1 support
  /**
   * Sets the value of a nonpublic property of an object under test.
   *
   * NOTE it's very easy to break everything using this method
   *
   * @param object $object The object to modufy
   * @param string $property The property to set
   * @param mixed $value The new value
   * @return void
   */
  protected function _setNonpublicProperty(
    $object,
    string $property,
    $value
  ) : void {
    // phpcs:enable
    $ro = new ReflectionObject($object);
    if (! $ro->hasProperty($property)) {
      return;
    }

    $rp = $ro->getProperty($property);
    $rp->setAccessible(true);
    $rp->setValue($object, $value);
  }
}
