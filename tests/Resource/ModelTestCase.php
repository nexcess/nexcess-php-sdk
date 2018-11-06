<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource;

use DateTimeInterface as DateTime,
  Throwable;
use Nexcess\Sdk\ {
  Resource\Collection,
  Resource\Endpoint,
  Resource\Model,
  Resource\ResourceException,
  Sandbox\Sandbox,
  Tests\TestCase,
  Util\Config
};

/**
 * Base unit testcase for models.
 */
abstract class ModelTestCase extends TestCase {

  /** @var string Resource name for data for fromArray(). */
  protected const _RESOURCE_FROMARRAY = '';

  /** @var string Resource name for return value of toArray(). */
  protected const _RESOURCE_TOARRAY = '';

  /** @var string Resource name for return value of toCollapsedArray(). */
  protected const _RESOURCE_TOCOLLAPSEDARRAY = '';

  /**
   * @covers Modelable::toArray
   * @covers Modelable::toCollapsedArray
   * @dataProvider toArrayProvider
   *
   * @param array $data Raw data to hydrate model
   * @param array $expected Expected toArray(false) result
   * @param array $collapsed Expected toCollapsedArray() result
   */
  public function testArray(array $data, array $expected, array $collapsed) {
    $model = $this->_getSubject()->sync($data);

    // expected "shallow" array result
    $this->assertEquals($expected, $model->toArray(false));

    // expected recursive array result
    foreach ($expected as $key => $value) {
      if ($value instanceof Model) {
        $expected[$key] = $value->toCollapsedArray() +
          ['identity' => $value->get('identity')];
      } elseif ($value instanceof Collection) {
        $expected[$key] = $value->toArray(true);
      } elseif ($value instanceof DateTime) {
        $expected[$key] = $value->format('U');
      }
    }
    $this->assertEquals($expected, $model->toArray(true));

    // expected collapsed array result
    $this->assertEquals($collapsed, $model->toCollapsedArray());
  }

  /**
   * @return array[] Testcases
   */
  public function toArrayProvider() : array {
    return [
      [
        $this->_getResource(static::_RESOURCE_FROMARRAY),
        $this->_getResource(static::_RESOURCE_TOARRAY),
        $this->_getResource(static::_RESOURCE_TOCOLLAPSEDARRAY)
      ]
    ];
  }

  /**
   * @covers Modelable::equals
   */
  public function testEquals() {
    $model = $this->_getSubject(1);
    $other = $this->_getSubject(1);

    $this->assertTrue(
      $model->equals($other),
      'Models of same class with same id must compare equal'
    );

    $other->set('id', 2);
    $this->assertFalse(
      $model->equals($other),
      'Models of same class with different ids must not compare equal'
    );

    $another = new class() extends Model {
      protected const _PROPERTY_NAMES = ['id'];
      public function __construct() {
        $this->set('id', 1);
      }
    };
    $this->assertFalse(
      $model->equals($another),
      'Models of different classes must not compare equal'
    );
  }

  /**
   * @covers Modelable::exists
   * @covers Modelable::offsetExists
   * @dataProvider existsProvider
   *
   * @param string $name Name of the property to check
   * @param bool $readonly Is readonly property?
   * @param bool $expected Is the property expected to exist?
   */
  public function exists(string $name, bool $readonly, bool $expected) {
    $model = $this->_getSubject(1);

    if ($readonly) {
      $this->assertFalse(
        $model->exists($name, false),
        'Read-only properties must be considered non-existent' .
          ' when $include_readonly is false'
      );
    }

    $this->assertEquals($expected, $model->exists($name, true));

    $this->assertEquals(
      $expected,
      $model->exists($name),
      '$include_readonly must be true by default'
    );

    $this->assertEquals(
      $expected,
      isset($model[$name]),
      'isset() and exists() must return the same result'
    );
  }

  /**
   * @return array[] Testcases
   */
  public function existsProvider() : array {
    $model = $this->_getSubject(1);
    [$aliases, $names, $readonlys] = (function () {
      return [
        self::_PROPERTY_ALIASES,
        self::_PROPERTY_NAMES,
        self::_READONLY_NAMES
      ];
    })->bindTo($model, $model)();

    // assuming these won't exist on any real model
    $testcases = [
      ['foo', false, false],
      ['bar', true, false]
    ];

    // aliases
    foreach ($aliases as $alias => $name) {
      $testcases[] = [$alias, in_array($name, $readonlys), true];
    }
    // writable names
    foreach ($names as $name) {
      $testcases[] = [$name, false, true];
    }
    // readonly names
    foreach ($readonlys as $name) {
      $testcases[] = [$name, true, true];
    }

    return $testcases;
  }

  /**
   * @covers Modelable::getId
   */
  public function testGetId() {
    $this->assertEquals(1, $this->_getSubject(1)->getId());
    $this->assertEquals(101, $this->_getSubject(101)->getId());
  }

  /**
   * @covers Modelable::get
   * @covers Modelable::offsetGet
   * @covers Modelable::offsetSet
   * @covers Modelable::offsetUnset
   * @covers Modelable::set
   * @covers Modelable::unset
   * @dataProvider getSetProvider
   *
   * @param string $name Name of the property to access
   * @param mixed $expected Value to expect; or a Throwable if failure expected
   * @param mixed|null $set Value to set (omit if same as $expected)
   */
  public function testGetSet(
    Model $model,
    string $name,
    $expected,
    $set = null
  ) {
    if (! $model->exists($name, true)) {
      $this->setExpectedException(
        new ResourceException(ResourceException::NO_SUCH_PROPERTY)
      );
    }

    $this->assertEquals($expected, $model->get($name));
    $this->assertEquals(
      $expected,
      $model[$name],
      'ArrayAccess must return expected value'
    );

    if (! $model->exists($name, false)) {
      $this->setExpectedException(
        new ResourceException(ResourceException::NO_SUCH_WRITABLE_PROPERTY)
      );
    }

    $model->unset($name);
    $this->assertNull($model->get($name));

    $model->set($name, $set ?? $expected);
    $this->assertEquals($expected, $model->get($name));

    unset($model[$name]);
    $this->assertNull($model->get($name));

    $model[$name] = $set ?? $expected;
    $this->assertEquals($expected, $model->get($name));
  }

  /**
   * @return array[] Testcases
   */
  public function getSetProvider() : array {
    $set = $this->_getResource(static::_RESOURCE_FROMARRAY);
    $expect = $this->_getResource(static::_RESOURCE_TOARRAY);

    $model = $this->_getSubject()->sync($set);

    // assuming these won't exist on any real model
    $testcases = [
      [$model, 'foo', null, null],
      [$model, 'bar', null, null]
    ];
    foreach ($expect as $name => $value) {
      $set = (isset($set[$name]) && $set[$name] !== $value) ?
        $set[$name] :
        null;
      $testcases[] = [$model, $name, $value, $set];
    }

    return $testcases;
  }

  /**
   * @covers Modelable::isReal
   */
  public function testIsReal() {
    $this->assertTrue($this->_getSubject(1)->isReal());
    $this->assertFalse($this->_getSubject(0)->isReal());
  }

  /**
   * @covers Modelable::sync
   * @dataProvider syncProvider
   */
  public function testSync(array $from, array $expected) {
    $this->assertEquals(
      $expected,
      $this->_getSubject()->sync($from)->toArray(false)
    );
  }

  /**
   * @return array[] Testcases
   */
  public function syncProvider() : array {
    return [[
      $this->_getResource(static::_RESOURCE_FROMARRAY),
      $this->_getResource(static::_RESOURCE_TOARRAY)
    ]];
  }

  /**
   * {@inheritDoc}
   */
  protected function _getSubject(...$constructor_args) {
    if (! reset($constructor_args) instanceof Endpoint) {
      // skip endpoint arg if none provided
      array_unshift($constructor_args, null);
    }

    return parent::_getSubject(...$constructor_args);
  }
}
