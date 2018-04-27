<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use Throwable;

use Nexcess\Sdk\ {
  Exception\ModelException,
  Model\Collector as Collection,
  Model\Modelable
};

abstract class Model implements Modelable {

  /** @var string[] Map of property aliases:names. */
  const PROPERTY_ALIASES = [];

  /** @var string[] Map of model|collection property name:id|ids name. */
  const PROPERTY_COLLAPSED = [];

  /** @var string[] Map of property collection names:model classes. */
  const PROPERTY_COLLECTIONS = [];

  /** @var string[] Map of property model names:model classes. */
  const PROPERTY_MODELS = [];

  /** @var string[] List of property names. */
  const PROPERTY_NAMES = [];

  /** @var string[] List of readonly property names. */
  const READONLY_NAMES = [];

  /** @var array Default filter values for list(). */
  const BASE_LIST_FILTER = [];

  /** @var array Map of instance property:value pairs. */
  protected $_values = [];

  /**
   * {@inheritDoc}
   */
  public static function fromArray(array $data) : Modelable {
    $model = new static();

    foreach ($data as $property => $value) {
      $model->offsetSet($property, $value);
    }

    return $model;
  }

  /**
   * @param int|null $id Model id
   */
  public function __construct(int $id = null) {
    $this->sync([], true);
    if ($id) {
      $this->offsetSet('id', $id);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function equals(Modelable $other) : bool {
    return ($other instanceof $this) &&
      $other->offsetGet('id') === $this->offsetGet('id');
  }

  /**
   * {@inheritDoc}
   */
  public function getId() : int {
    return $this->offsetGet('id');
  }

  /**
   * {@inheritDoc}
   */
  public function isReal() : bool {
    return $this->offsetGet('id') !== null;
  }

  /**
   * @see https://php.net/JsonSerializable.jsonSerialize
   */
  public function jsonSerialize() {
    return $this->toArray();
  }

  /**
   * @see https://php.net/ArrayAccess.offsetExists
   * @param $include_readonly Include "read-only" properties from check?
   */
  public function offsetExists($name, $include_readonly = true) {
    $name = static::PROPERTY_ALIASES[$name] ?? $name;
    return in_array($name, static::PROPERTY_NAMES) ||
      ($include_readonly && in_array($name, static::READONLY_NAMES));
  }

  /**
   * @see https://php.net/ArrayAccess.offsetGet
   */
  public function offsetGet($name) {
    if (! $this->offsetExists($name)) {
      throw new ModelException(
        ModelException::NO_SUCH_PROPERTY,
        ['name' => $name, 'model' => static::class]
      );
    }
    $name = static::PROPERTY_ALIASES[$name] ?? $name;

    $getter = str_replace('_', '', "get{$name}");
    if (method_exists($this, $getter)) {
      return $this->$getter();
    }

    return $this->_values[$name] ?? null;
  }

  /**
   * @see https://php.net/ArrayAccess.offsetSet
   * @return Model $this
   */
  public function offsetSet($name, $value) {
    if (! $this->offsetExists($name, false)) {
      throw new ModelException(
        ModelException::NO_SUCH_WRITABLE_PROPERTY,
        ['name' => $name, 'model' => static::class]
      );
    }

    $name = static::PROPERTY_ALIASES[$name] ?? $name;
    $this->sync([$name => $value]);

    return $this;
  }

  /**
   * @see https://php.net/ArrayAccess.offsetUnset
   * @return Model $this
   */
  public function offsetUnset($name) {
    if (! $this->offsetExists($name, false)) {
      throw new ModelException(
        ModelException::NO_SUCH_WRITABLE_PROPERTY,
        ['name' => $name, 'model' => static::class]
      );
    }

    $name = static::PROPERTY_ALIASES[$name] ?? $name;
    $this->_values[$name] = null;

    return $this;
  }

  /**
   * Syncs model state with new data.
   *
   * This method is intended for use internally and by Endpoints,
   * and should generally not be used otherwise.
   * @see Endpoint::sync
   *
   * @param array $data Map of property:value pairs (e.g., from API response)
   * @param bool $hard Discard existing state?
   * @return Model $this
   * @throws ModelException If sync fails
   */
  public function sync(array $data, bool $hard = false) : Modelable {
    try {
      $prior = $this->_values;

      if ($hard) {
        $this->_values = array_fill_keys(self::PROPERTY_NAMES, null);
      }

      foreach ($data as $key => $value) {
        $key = static::PROPERTY_ALIASES[$key] ?? $key;
        if ($this->offsetExists($key)) {
          $setter = str_replace('_', '', "set{$key}");
          if (method_exists($this, $setter)) {
            $this->$setter($value);
            continue;
          }

          if (isset(static::PROPERTY_MODELS[$key])) {
            $value = $this->_buildPropertyModel(
              static::PROPERTY_MODELS[$key],
              $value
            );
          }

          if (isset(static::PROPERTY_COLLECTIONS[$key])) {
            $value = $this->_buildPropertyCollection(
              static::PROPERTY_COLLECTIONS[$key],
              $value
            );
          }

          $this->_values[$key] = $value;
        }
      }

      return $this;

    } catch (Throwable $e) {
      $this->_values = $prior;
      throw new ModelException(
        ModelException::SYNC_FAILED,
        $e,
        ['model' => static::class, 'id' => $data['id'] ?? $prior['id'] ?? 0]
      );
    }
  }

  /**
   * {@inheritDoc}
   */
  public function toArray(bool $collapse = false) : array {
    return ($collapse) ? $this->_collapse($this->_values) : $this->_values;
  }

  /**
   *
   */
  protected function _buildPropertyCollection(
    string $fqcn,
    array $values
  ) : Collection {
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
  protected function _buildPropertyModel(string $fqcn, $value) : Model {
    if (is_int($value)) {
      $value = ['id' => $value];
    }

    if (is_array($value)) {
      $model = new $fqcn;
      return $model->sync($value);
    }

    throw new ModelException(
      ModelException::UNMODELABLE,
      ['model' => $fqcn, 'type' => Util::type($value), 'data' => $value]
    );
  }

  /**
   * Reduces a property:value map to the set of writable properties,
   * replacing models/collections with model id/ids.
   *
   * This method normalizes Model::$_values as well as raw API data.
   *
   * @param array $values Raw values
   *
   */
  protected function _collapse(array $values) : array {
    $collapsed = array_fill_keys(self::PROPERTY_NAMES, null);

    foreach ($values as $property => $value) {
      $property = static::PROPERTY_ALIASES[$property] ?? $property;

      if (is_scalar($value) && in_array($property, static::PROPERTY_NAMES)) {
        $collapsed[$property] = $value;
        continue;
      }

      $collapsable = static::PROPERTY_COLLAPSED[$property] ?? null;
      if ($collapsable === null) {
        continue;
      }

      if ($value instanceof Collection) {
        $collapsed[static::PROPERTY_COLLAPSED[$property]] = $value->getIds();
        continue;
      }

      if ($value instanceof Model) {
        $collapsed[static::PROPERTY_COLLAPSED[$property]] = $value->getId();
        continue;
      }

      if (is_array($value)) {
        $collapsed[static::PROPERTY_COLLAPSED[$property]] = $value['id'] ??
          array_column($value, 'id');
        continue;
      }

      throw new ModelException(
        ModelException::UNCOLLAPSABLE,
        ['property' => $property, 'type' => Util::type($value)]
      );
    }

    return $collapsed;
  }
}
