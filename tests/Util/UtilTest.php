<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Util;

use ArrayObject,
  StdClass;

use Nexcess\Sdk\ {
  Sandbox\Sandbox,
  Tests\TestCase,
  Util\Util
};

/**
 * Tests for Utility functions.
 */
class UtilTest extends TestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** @var string Filepath to abc.json test file. */
  protected const _TEST_JSON_FILE = self::_RESOURCE_PATH . '/test.json';

  /**
   * @covers Util::dig
   * @dataProvider digProvider
   *
   * @param array|ArrayAccess $subject The subject
   * @param string $path Dot-delimited path of keys to follow
   * @param mixed $expected The value to be found
   */
  public function testDig($subject, string $path, $expected) {
    $this->assertEquals($expected, Util::dig($subject, $path));
  }

  /**
   * @return array[] @see UtilTest::testDig
   */
  public function digProvider() : array {
    $array = [
      'a' => 'A',
      'b' => 'B',
      'c' => [
        'x' => 1,
        'y' => 2,
        'z' => [
          'foo',
          'bar',
          'baz'
        ]
      ]
    ];
    $arrayaccess = new ArrayObject($array);

    $tests = [];
    foreach ([$array, $arrayaccess] as $subject) {
      $tests[] = [$subject, 'c.z.2', $subject['c']['z'][2]];
      $tests[] = [$subject, 'c.z', $subject['c']['z']];
      $tests[] = [$subject, 'c.x', $subject['c']['x']];
      $tests[] = [$subject, 'a', $subject['a']];
      $tests[] = [$subject, 'a.b.c', null];
    }

    return $tests;
  }

  /**
   * @covers Util::extendRecursive
   * @dataProvider extendRecursiveProvider
   *
   * @param array $expected The expected extended array
   * @param array $subject The subject array
   * @param array $extenders The arrays to extend the subject array
   */
  public function testExtendRecursive(
    array $expected,
    array $subject,
    array ...$extenders
  ) {
    $this->assertEquals(
      $expected,
      Util::extendRecursive($subject, ...$extenders)
    );
  }

  /**
   * @return array[] @see UtilTest::testType
   */
  public function extendRecursiveProvider() : array {
    return [
      [
        ['a' => '∀', 'b' => 'B'],
        ['a' => 'A', 'b' => 'B'],
        ['a' => '∀']
      ],
      [
        ['a' => 'A', 'b' => [1, 2, 3, 4]],
        ['a' => 'A', 'b' => [1, 2, 3]],
        ['b' => [4]]
      ],
      [
        ['a' => 'A', 'b' => 'ᗺ'],
        ['a' => 'A', 'b' => [1, 2, 3]],
        ['b' => 'ᗺ']
      ],
      [
        ['a' => '∀', 'b' => 'ᗺ'],
        ['a' => 'A', 'b' => 'B'],
        ['a' => '∀'],
        ['b' => 'ᗺ']
      ]
    ];
  }

  /**
   * @covers Util::readJsonFile
   */
  public function testReadJsonFile() {
    $this->assertEquals(
      ['a' => 'A', 'b' => 'B', 'c' => 'C'],
      Util::readJsonFile(self::_TEST_JSON_FILE)
    );
  }

  /**
   * @covers Util::type
   * @dataProvider typeProvider
   *
   * @param mixed $value The value to check
   * @param string $expected Expected type
   */
  public function testType($value, string $expected) {
    $this->assertEquals($expected, Util::type($value));
  }

  /**
   * @return array[] @see UtilTest::testType
   */
  public function typeProvider() : array {
    return [
      [[], 'array'],
      [true, 'boolean'],
      [1.2, 'double'],
      [123, 'integer'],
      [null, 'null'],
      ['abc', 'string'],
      [new StdClass, 'stdClass'],
      [$this->_getSandbox(), Sandbox::class]
    ];
  }
}
