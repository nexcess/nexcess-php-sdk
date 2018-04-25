<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use ArrayAccess,
  Countable,
  Iterator,
  JsonSerializable,
  Throwable;

use Nexcess\Sdk\ {
  Exception\ModelException,
  Exception\SdkException,
  Model\Modelable as Model
};

/**
 * Interface for collections of models.
 */
interface Collector extends Countable, Iterator, JsonSerializable {

  /**
   * Adds a model to the collection.
   *
   * @param Model $model
   * @return Collection $this
   * @throws ModelException If the model is the wrong class for the collection
   */
  public function add(Model $model) : Collector;

  /**
   * Consts how many items are in this collection.
   *
   * @return int
   */
  public function count() : int;

  /**
   * Iterates over the collection, passing each model to a callback function.
   *
   * @param callable $function The callback to invoke
   * @return array List of return values from the callback function
   * @throws SdkException If the callback function throws
   */
  public function each(callable $function) : array;

  /**
   * Filters items and builds a new collection.
   *
   * Criteria can be a property:value map or a callback function.
   * Callback signature is like
   *  bool $filter(Model $item) Return true to take item, false to discard
   *
   * @param array|callable $filter Map of property:value|callback pairs
   * @return Collection A new collection of zero or more matching items
   * @throws SdkException If filter callback errors
   */
  public function filter($filter) : Collector;

  /**
   * Finds an item in this collection that meet given criteria.
   *
   * Criteria can be a property:value map or a callback function.
   * Callback signature is like
   *  bool $criteria(Model $item) Return true to match item; false to discard
   *
   * Note, this method returns zero or one items.
   * If you want a list of items, @see Collection::filter
   *
   * @param array|callable $criteria Map of property:value|callback pairs
   * @return Model|null A matching item, if any found; null otherwise
   * @throws SdkException If criteria callback errors
   */
  public function find($criteria);

  /**
   * Gets list of ids of items in this collection.
   *
   * @return int[]
   */
  public function getIds() : array;

  /**
   * Builds a new collection by applying a callback function to each item.
   *
   * Callback signature is like
   *  void $function(Model $item) Callback to apply to each item
   *
   * This method duplicates items.
   * If you want to operate on items "in place," @see Collection::each
   *
   * @param callable $function The callback to invoke
   * @return Collection
   * @throws SdkException If the callback function throws
   */
  public function map(callable $function) : Collector;

  /**
   * Removes a model from the collection.
   *
   * @param Model|int $id The Model or Model id to remove
   * @return Model The removed Model
   * @throws ModelException If the model does not exist in the collection
   */
  public function remove($model_or_id) : Model;

  /**
   * Sorts the collection by property value, optionally in descending order.
   *
   * @param string $property Name of property to sort by
   * @param bool $desc Sort in descending order?
   * @return Collection $this
   * @throws ModelException If property does not exist
   */
  public function sort(string $property, bool $desc = false) : Collector;

  /**
   * Gets the collection as an array.
   *
   * @return array
   */
  public function toArray() : array;
}
