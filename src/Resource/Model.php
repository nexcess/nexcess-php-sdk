<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use DateTimeImmutable as DateTime,
  Throwable;

use Nexcess\Sdk\ {
  Resource\Collection,
  Resource\Collector,
  Resource\Modelable,
  Resource\ResourceException,
  Util\Util
};

abstract class Model implements Modelable {

  /** @var string Formatting string for date properties. */
  protected const _DEFAULT_DATE_FORMAT = 'm/d/Y H:i:s e';

  /** @var string[] Map of property aliases:names. */
  protected const _PROPERTY_ALIASES = [];

  /** @var string[] Map of model|collection property name:id|ids name. */
  protected const _PROPERTY_COLLAPSED = [];

  /** @var string[] Map of property collection names:model classes. */
  protected const _PROPERTY_COLLECTIONS = [];

  /** @var string[] Map of property model names:model classes. */
  protected const _PROPERTY_MODELS = [];

  /** @var string[] List of property names. */
  protected const _PROPERTY_NAMES = [];

  /** @var string[] List of readonly property names. */
  protected const _READONLY_NAMES = [];

  /** @var array Map of instance property:value pairs. */
  protected $_values = [];

  /**
   * {@inheritDoc}
   */
  public static function fromArray(array $data) : Modelable {
    $model = new static();

    foreach ($data as $property => $value) {
      $model->set($property, $value);
    }

    return $model;
  }

  /**
   * @param int|null $id Model id
   */
  public function __construct(int $id = null) {
    $this->sync([], true);
    if ($id) {
      $this->set('id', $id);
    }
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/__set_state
   *
   * @internal
   * This method is meant for internal development/testing use only,
   * and should not be used otherwise.
   * Use of this method CAN result in a BROKEN object instance!
   */
  public static function __set_state($data) {
    $model = new static();
    $model->_values = $data['_values'] ?? [];
    return $model;
  }

  /**
   * {@inheritDoc}
   */
  public function equals(Modelable $other) : bool {
    return ($other instanceof $this) &&
      $other->getId() === $this->getId();
  }

  /**
   * {@inheritDoc}
   */
  public function exists(string $name, bool $include_readonly = true) : bool {
    $name = static::_PROPERTY_ALIASES[$name] ?? $name;
    return in_array($name, static::_PROPERTY_NAMES) ||
      ($include_readonly && in_array($name, static::_READONLY_NAMES));
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $name) {
    if (! $this->exists($name)) {
      throw new ResourceException(
        ResourceException::NO_SUCH_PROPERTY,
        ['name' => $name, 'model' => static::class]
      );
    }
    $name = static::_PROPERTY_ALIASES[$name] ?? $name;

    $getter = str_replace('_', '', "get{$name}");
    if (method_exists($this, $getter)) {
      return $this->$getter();
    }

    $value = Util::dig($this->_values, $name) ?? null;

    if (is_int($value) && $value > 0 && strpos($name, 'date') !== false) {
      $value = (new DateTime("@{$value}"))
        ->format(static::_DEFAULT_DATE_FORMAT);
    }

    return $value;
  }

  /**
   * {@inheritDoc}
   */
  public function getId() : ?int {
    return $this->get('id');
  }

  /**
   * {@inheritDoc}
   */
  public function isReal() : bool {
    return $this->getId() > 0;
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/JsonSerializable.jsonSerialize
   */
  public function jsonSerialize() {
    return $this->toArray(true);
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/ArrayAccess.offsetExists
   */
  public function offsetExists($name) {
    return $this->exists($name);
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/ArrayAccess.offsetGet
   */
  public function offsetGet($name) {
    return $this->get($name);
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/ArrayAccess.offsetSet
   */
  public function offsetSet($name, $value) {
    $this->set($name, $value);
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/ArrayAccess.unset
   */
  public function offsetUnset($name) {
    $this->unset($name);
  }

  /**
   * {@inheritDoc}
   */
  public function set(string $name, $value) : Modelable {
    if (! $this->exists($name, false)) {
      throw new ResourceException(
        ResourceException::NO_SUCH_WRITABLE_PROPERTY,
        ['name' => $name, 'model' => static::class]
      );
    }

    $name = static::_PROPERTY_ALIASES[$name] ?? $name;
    $this->sync([$name => $value]);

    return $this;
  }

  /**
   * Syncs model state with new data.
   *
   * @internal
   * This method is intended for use internally and by Endpoints,
   * and should generally not be used otherwise.
   *
   * @param array $data Map of property:value pairs (i.e., from API response)
   * @param bool $hard Discard existing state?
   * @return Model $this
   * @throws ResourceException If sync fails
   */
  public function sync(array $data, bool $hard = false) : Modelable {
    try {
      $prior = $this->_values;

      if ($hard) {
        $this->_values = array_fill_keys(self::_PROPERTY_NAMES, null);
      }

      foreach ($data as $key => $value) {
        $key = static::_PROPERTY_ALIASES[$key] ?? $key;
        // set property if exists (don't try to set nested properties)
        if ($this->exists($key) && strpos($key, '.') === false) {
          $setter = str_replace('_', '', "set{$key}");
          if (method_exists($this, $setter)) {
            $this->$setter($value);
            continue;
          }

          if (isset(static::_PROPERTY_MODELS[$key])) {
            $value = $this->_buildPropertyModel(
              static::_PROPERTY_MODELS[$key],
              $value
            );
          }

          if (isset(static::_PROPERTY_COLLECTIONS[$key])) {
            $value = $this->_buildPropertyCollection(
              static::_PROPERTY_COLLECTIONS[$key],
              $value
            );
          }

          $this->_values[$key] = $value;
        }
      }

      return $this;
    } catch (Throwable $e) {
      $this->_values = $prior;
      throw new ResourceException(
        ResourceException::SYNC_FAILED,
        $e,
        ['model' => static::class, 'id' => $data['id'] ?? $prior['id'] ?? 0]
      );
    }
  }

  /**
   * {@inheritDoc}
   */
  public function toArray(bool $recurse = true) : array {
    $properties = array_diff(
      array_merge(
        static::_PROPERTY_NAMES,
        static::_READONLY_NAMES,
        array_keys(static::_PROPERTY_ALIASES)
      ),
      static::_PROPERTY_ALIASES
    );
    usort(
      $properties,
      function ($a, $b) {
        if ($a === 'id') {
          return -1;
        }
        if ($b === 'id') {
          return 1;
        }
        return $a <=> $b;
      }
    );

    $array = [];
    foreach ($properties as $property) {
      $value = $this->get($property);
      if (is_array($value)) {
        Util::kSortRecursive($value);
      }
      if (
        $recurse &&
        ($value instanceof Modelable || $value instanceof Collector)
      ) {
        $value = $value->toArray($recurse);
      }
      $array[$property] = $value;
    }
    return $array;
  }

  /**
   * {@inheritDoc}
   */
  public function toCollapsedArray(array $values = null) : array {
    $collapsed = array_fill_keys(self::_PROPERTY_NAMES, null);

    foreach ($values ?? $this->_values as $property => $value) {
      $property = static::_PROPERTY_ALIASES[$property] ?? $property;

      if (is_scalar($value) && in_array($property, static::_PROPERTY_NAMES)) {
        $collapsed[$property] = $value;
        continue;
      }

      if (! isset(static::_PROPERTY_COLLAPSED[$property])) {
        continue;
      }

      if ($value instanceof Collection) {
        $collapsed[static::_PROPERTY_COLLAPSED[$property]] = $value->getIds();
        continue;
      }

      if ($value instanceof Model) {
        $collapsed[static::_PROPERTY_COLLAPSED[$property]] = $value->getId();
        continue;
      }

      if (is_array($value)) {
        $collapsed[static::_PROPERTY_COLLAPSED[$property]] = $value['id'] ??
          array_column($value, 'id');
        continue;
      }

      throw new ResourceException(
        ResourceException::UNCOLLAPSABLE,
        ['property' => $property, 'type' => Util::type($value)]
      );
    }

    ksort($collapsed);
    return $collapsed;
  }

  /**
   * {@inheritDoc}
   */
  public function unset(string $name) : Modelable {
    if (! $this->exists($name, false)) {
      throw new ResourceException(
        ResourceException::NO_SUCH_WRITABLE_PROPERTY,
        ['name' => $name, 'model' => static::class]
      );
    }

    $name = static::_PROPERTY_ALIASES[$name] ?? $name;
    $this->_values[$name] = null;

    return $this;
  }

  /**
   * Builds a Collection of Models given an array of ids or values.
   *
   * @param string $fqcn Fully qualified classname of model to build
   * @param int[]|array[] $value Values to build models from
   * @return Collector
   */
  protected function _buildPropertyCollection(
    string $fqcn,
    array $values
  ) : Collector {
    $collection = new Collection($fqcn);
    foreach ($values as $value) {
      $collection->add($this->_buildPropertyModel($fqcn, $value));
    }

    return $collection;
  }

  /**
   * Builds a Model given an id or array of values.
   *
   * @param string $fqcn Fully qualified classname of model to build
   * @param int|array $value Value(s) to build model from
   */
  protected function _buildPropertyModel(string $fqcn, $value) : Modelable {
    if (is_int($value)) {
      $value = ['id' => $value];
    }

    if (is_array($value)) {
      $model = new $fqcn;
      return $model->sync($value);
    }

    throw new ResourceException(
      ResourceException::UNMODELABLE,
      ['model' => $fqcn, 'type' => Util::type($value), 'data' => $value]
    );
  }
}
