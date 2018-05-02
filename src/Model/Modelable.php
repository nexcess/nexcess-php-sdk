<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use ArrayAccess,
  JsonSerializable;

use Nexcess\Sdk\Exception\ModelException;

/**
 * Interface for API item models.
 */
interface Modelable extends ArrayAccess, JsonSerializable {

  /** @var string Base namespace for model classes. */
  const NAMESPACE = __NAMESPACE__;

  /**
   * Makes a new Model instance and populates it with given data.
   *
   * @param array $data Map of property:value pairs to assign
   * @return Modelable On success
   * @throws ModelException On error
   */
  public static function fromArray(array $data) : Modelable;

  /**
   * Checks whether this Model instance represents the same item as another.
   *
   * Note, this compares item identity (id), and does NOT compare values!
   *
   * @param Modelable $other The model to compare to this model.
   */
  public function equals(Modelable $other) : bool;

  /**
   * Checks whether a property exists on this model,
   * optionally excluding properties which are "readonly."
   *
   * @param string $name Name of the property to check
   * @param bool $include_readonly Include "read-only" properties from check?
   * @return bool True if property exists; false otherwise
   */
  public function exists(string $name, bool $include_readonly = true) : bool;

  /**
   * Gets a property value.
   *
   * @param string $name Name of the property to get
   * @return mixed The property value on success
   * @throws ModelException If the named property does not exist
   */
  public function get(string $name);

  /**
   * Gets the model id.
   *
   * @return int
   */
  public function getId() : int;

  /**
   * Does this model represent an item which exists on the API?
   *
   * @return bool
   */
  public function isReal() : bool;

  /**
   * Sets a value on a property.
   *
   * @param string $name Name of the property to set
   * @param mixed $value The value to set
   * @return Model $this
   * @throws ModelException If the named property does not exist or is readonly
   */
  public function set(string $name, $value) : Model;

  /**
   * Gets model state as an array.
   *
   * This method is intended for use by Endpoints,
   * and should generally not be used otherwise.
   *
   * @param bool $collapse Replace nested models/collections with their id/ids?
   * @return array
   */
  public function toArray(bool $collapse = false) : array;

  /**
   * Unsets a property.
   *
   * @param string $name Name of the property to unset
   * @return Model $this
   * @throws ModelException If the named property does not exist or is readonly
   */
  public function unset(string $name) : Model;
}
