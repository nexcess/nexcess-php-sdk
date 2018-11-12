<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Countable,
  Iterator,
  JsonSerializable;

use Nexcess\Sdk\ {
  SdkException,
  Resource\Modelable,
  Resource\ResourceException
};

/**
 * Interface for collections of models.
 */
interface Collector extends Countable, Iterator, JsonSerializable {

  /**
   * Adds a model to the collection.
   *
   * @param Modelable $model
   * @return Collection $this
   * @throws ResourceException If the model is wrong class for the collection
   */
  public function add(Modelable $model) : Collector;

  /**
   * Counts how many items are in this collection.
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
   * Checks whether another collection is the same as this one.
   *
   * @param Collector $other The collection to compare to
   * @return bool True if collections are of the same type,
   *  and contain the same items
   */
  public function equals(Collector $other) : bool;

  /**
   * Filters items and builds a new collection.
   *
   * Criteria can be a property:value map or a callback function.
   * Callback signature is like
   *  bool $filter(Modelable $item) Return true to take item, false to discard
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
   *  bool $criteria(Modelable $item) Return true to match item; false to discard
   *
   * Note, this method returns zero or one items.
   * If you want a list of items, @see Collection::filter
   *
   * @param array|callable $criteria Map of property:value|callback pairs
   * @return Modelable|null A matching item, if any found; null otherwise
   * @throws SdkException If criteria callback errors
   */
  public function find($criteria) : ?Modelable;

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
   *  void $function(Modelable $item) Callback to apply to each item
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
   * Gets the fully qualified classname of the model this collector collects.
   *
   * @return string
   */
  public function of() : string;

  /**
   * Removes a model from the collection.
   *
   * @param Modelable|int $model_or_id The Modelable or Modelable id to remove
   * @return Modelable The removed Modelable
   * @throws ResourceException If the model does not exist in the collection
   */
  public function remove($model_or_id) : Modelable;

  /**
   * Sorts the collection by property value, optionally in descending order.
   *
   * @param string|null $prop Property to sort by (sorts by id if omitted)
   * @param bool $desc Sort in descending order?
   * @return Collection $this
   * @throws ResourceException If property does not exist
   */
  public function sort(string $prop = null, bool $desc = false) : Collector;

  /**
   * Gets the collection as an array.
   *
   * @param bool $recurse Convert models to arrays also?
   * @return array
   */
  public function toArray(bool $recurse = true) : array;
}
