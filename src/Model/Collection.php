<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use ArrayAccess,
  Iterator,
  JsonSerializable,
  Throwable;

use Nexcess\Sdk\ {
  Exception\ModelException,
  Exception\SdkException,
  Model\Model
};

/**
 * Container for a collection of Models.
 */
class Collection implements Iterator, JsonSerializable {

  /** @var Model[] List of models. */
  protected $_models = [];

  /**
   * @param string $of Fully qualified Model classname this collection holds
   * @param Model[] $models List of models to add to the collection
   */
  public function __construct(string $of, array $models = []) {
    $this->_of = $of;
    foreach ($models as $model) {
      $this->add($model);
    }
  }

  /**
   * Adds a model to the collection.
   *
   * @param Model $model
   * @return Collection $this
   * @throws ModelException If the model is the wrong class for the collection
   */
  public function add(Model $model) : Collection {
    if (! $model instanceof $this->_of) {
      throw new ModelException(
        ModelException::WRONG_MODEL_FOR_COLLECTION,
        ['collection' => $this->_of, 'model' => get_class($model)]
      );
    }

    $this->_models[$model->offsetGet('id')] = $model;

    return $this;
  }

  /**
   * Consts how many items are in this collection.
   *
   * @return int
   */
  public function count() : int {
    return count($this->_models);
  }

  /**
   * @see https://php.net/Iterator.current
   */
  public function current() {
    // @todo should we check+sync() items when accessed?
    //  maybe an option to enable this? or a getGenerator() -like method?
    return current($this->_models);
  }

  /**
   * Iterates over the collection, passing each model to a callback function.
   *
   * @param callable $function The callback to invoke
   * @return array List of return values from the callback function
   * @throws SdkException If the callback function throws
   */
  public function each(callable $function) : array {
    $results = [];

    try {
      foreach ($this as $model) {
        $results[] = $function($model);
      }
    } catch (Throwable $e) {
      throw new SdkException(SdkException::CALLBACK_ERROR, $e);
    }

    return $results;
  }

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
  public function filter($filter) {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['class' => __CLASS__, 'method' => __FUNCTION__]
    );
  }

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
  public function find($criteria) {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['class' => __CLASS__, 'method' => __FUNCTION__]
    );
  }

  /**
   * Gets list of ids of items in this collection.
   *
   * @return int[]
   */
  public function getIds() : array {
    return $this->each(function ($item) { return $item->offsetGet('id'); });
  }

  /**
   * @see https://php.net/JsonSerializable.jsonSerialize
   */
  public function jsonSerialize() {
    return $this->toArray();
  }

  /**
   * @see https://php.net/Iterator.key
   */
  public function key() {
    return key($this->_models);
  }

  /**
   * @see https://php.net/Iterator.next
   */
  public function next() {
    next($this->_models);
  }

  /**
   * Builds a new collection by applying a callback function to each item.
   *
   * This method duplicates items.
   * If you want to operate on items "in place," @see Collection::each
   *
   * @param callable $function The callback to invoke
   * @return Collection
   * @throws SdkException If the callback function throws
   */
  public function map(callable $function) : Collection {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['class' => __CLASS__, 'method' => __FUNCTION__]
    );
  }

  /**
   * Removes a model from the collection.
   *
   * @param Model|int $id The Model or Model id to remove
   * @return Model The removed Model
   * @throws ModelException If the model does not exist in the collection
   */
  public function remove($model_or_id) : Model {
    $id = $model_or_id;
    if ($model_or_id instanceof $this->_of) {
      $id = $model_or_id->offsetGet('id');
    }
    if (! is_int($id) || ! isset($this->_models[$id])) {
      throw new ModelException(
        ModelException::MODEL_NOT_FOUND,
        ['model' => $this->_of, 'id' => $id]
      );
    }

    $model = $this->_models[$id];
    unset($this->_models[$id]);
    return $model;
  }

  /**
   * @see https://php.net/Iterator.rewind
   */
  public function rewind() {
    reset($this->_models);
  }

  /**
   * Sorts the collection by property value, optionally in descending order.
   *
   * @param string $property Name of property to sort by
   * @param bool $desc Sort in descending order?
   * @return Collection $this
   * @throws ModelException If property does not exist
   */
  public function sort(string $property, bool $desc = false) : Collection {
    uasort(
      $this->_models,
      function ($a, $b) use ($desc) {
        return ($desc) ?
          $b->offsetGet($property) <=> $a->offsetGet($property) :
          $a->offsetGet($property) <=> $b->offsetGet($property);
      }
    );

    return $this;
  }

  /**
   * Gets the collection as an array.
   *
   * @return array
   */
  public function toArray() : array {
    return $this->_models;
  }

  /**
   * @see https://php.net/Iterator.valid
   */
  public function valid() {
    return key($this->_models) !== null;
  }
}
