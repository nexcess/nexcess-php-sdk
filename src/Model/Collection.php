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
  Exception\ModelException,
  Exception\SdkException,
  Model\Model
};

/**
 * Container for a collection of Models.
 */
class Collection implements Iterable {

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
   * @see https://php.net/Iterator.current
   */
  public function current() {
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
   * @see https://php.net/Iterator.valid
   */
  public function valid() {
    return key($this->_models) !== null;
  }
}
