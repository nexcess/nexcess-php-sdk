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
   * Note, this most compare item identity (id), and MUST NOT compare values!
   *
   * @param Modelable $other The model to compare to this model.
   */
  public function equals(Modelable $other) : bool;

  /**
   * Does this model represent an item which exists on the API?
   *
   * @return bool
   */
  public function isReal() : bool;

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
}
