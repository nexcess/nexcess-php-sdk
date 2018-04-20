<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use ArrayAccess,
  Throwable;

use Nexcess\Sdk\ {
  Client,
  Exception\ModelException,
  Util\Config
};

abstract class Model implements ArrayAccess {

  /** @var string[] Map of property aliases:names. */
  const PROPERTY_ALIASES = [];

  /** @var string[] List of property names. */
  const PROPERTY_NAMES = [];

  /** @var string[] List of readonly property names. */
  const READONLY_NAMES = [];

  /** @var array Default filter values for list(). */
  const BASE_LIST_FILTER = [];

  /** @var int Some Model properties are not set. */
  const STATE_INCOMPLETE = 1;

  /** @var int All Model properties are set. */
  const STATE_COMPLETE = 2;

  /** @var array Map of instance property:value pairs. */
  protected $_values = [];

  /**
   * Makes a new Model instance and populates it with given data.
   *
   * @param array $data Map of property:value pairs to assign
   * @return Model On success
   * @throws ModelException On error
   */
  public static function fromArray(array $data) : Model {
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
    $this->_client = $client;
    $this->_config = $config;
    $this->sync([], true);

    if ($id) {
      $this->offsetSet('id', $id);
    }
  }

  /**
   * Checks whether this Model instance represents the same item as another.
   *
   * Note, this compares item identity.
   * It does NOT compare values!
   *
   * @param Model $other The model to compare to this model.
   */
  public function equals(Model $other) : bool {
    return ($other instanceof $this) &&
      $other->offsetGet('id') === $this->offsetGet('id');
  }

  /**
   * Does this model represent an item which exists on the API?
   *
   * @return bool
   */
  public function isReal() : bool {
    return $this->offsetGet('id') !== null;
  }

  /**
   * @see https://php.net/ArrayAccess.offsetExists
   */
  public function offsetExists($name) {
    $name = static::PROPERTY_ALIASES[$name] ?? $name;
    return in_array($name, static::PROPERTY_NAMES);
  }

  /**
   * @see https://php.net/ArrayAccess.offsetGet
   */
  public function offsetGet($name) {
    if (! $this->offsetExists($name)) {
      throw new ModelException(
        ModelException::NO_SUCH_PROPERTY,
        ['name' => $name, 'model' => static::NAME]
      );
    }

    $name = static::PROPERTY_ALIASES[$name] ?? $name;

    return method_exists($this, "get{$name}") ?
      $this->{"get{$name}"}() :
      ($this->_values[$name] ?? null);
  }

  /**
   * @see https://php.net/ArrayAccess.offsetSet
   */
  public function offsetSet($name, $value) {
    if (! $this->offsetExists($name)) {
      throw new ModelException(
        ModelException::NO_SUCH_PROPERTY,
        ['name' => $name, 'model' => static::NAME]
      );
    }

    $name = static::PROPERTY_ALIASES[$name] ?? $name;

    if (in_array($name, static::READONLY_NAMES)) {
      throw new ModelException(
        ModelException::READONLY_PROPERTY,
        ['name' => $name, 'model' => static::NAME]
      );
    }

    if (method_exists($this, "set{$name}")) {
      $this->{"set{$name}"}($value);
      return;
    }

    $this->_values[$name] = $value;
  }

  /**
   * @see https://php.net/ArrayAccess.offsetUnset
   */
  public function offsetUnset($name) {
    if (! $this->offsetExists($name)) {
      throw new ModelException(
        ModelException::NO_SUCH_PROPERTY,
        ['name' => $name, 'model' => static::NAME]
      );
    }

    $name = static::PROPERTY_ALIASES[$name] ?? $name;

    if (in_array($name, static::READONLY_NAMES)) {
      throw new ModelException(
        ModelException::READONLY_PROPERTY,
        ['name' => $name, 'model' => static::NAME]
      );
    }

    $this->_values[$name] = null;
  }

  /**
   * Syncs model state with new data.
   *
   * This method is intended for use by Endpoints,
   * and should generally not be used otherwise.
   * @see Endpoint::sync
   *
   * @param array $data Map of property:value pairs from API response
   * @param bool $hard Discard existing state?
   * @throws ModelException If sync fails
   */
  public function sync(array $data, bool $force = false) {
    try {
      $prior = $this->_values;

      if ($force) {
        $this->_values = array_fill_keys(self::NAMES, null);
      }

      foreach ($data as $key => $value) {
        if ($this->offsetExists($key)) {
          $this->_values[$key] = $value;
        }
      }

    } catch (Throwable $e) {
      $this->_values = $prior;
      throw new ModelException(
        ModelException::SYNC_FAILED,
        ['model' => static::NAME, 'id' => $data['id'] ?? $prior['id'] ?? 0],
        $e
      );
    }
  }

  /**
   * Gets model state as an array.
   *
   * This method is intended for use by Endpoints,
   * and should generally not be used otherwise.
   *
   * @return array
   */
  public function toArray() : array {
    return $this->_values;
  }
}
