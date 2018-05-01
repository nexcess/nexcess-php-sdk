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
  Model\Collector,
  Model\Modelable as Model
};

/**
 * Container for a collection of Models.
 */
class Collection implements Collector {

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
   * {@inheritDoc}
   */
  public function add(Model $model) : Collector {
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
   * @see https://php.net/Countable.count
   */
  public function count() : int {
    return count($this->_models);
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/Iterator.current
   */
  public function current() {
    // @todo should we check+sync() items when accessed?
    //  maybe an option to enable this? or a getGenerator() -like method?
    return current($this->_models);
  }

  /**
   * {@inheritDoc}
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
   * {@inheritDoc}
   */
  public function filter($filter) : Collector {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['class' => __CLASS__, 'method' => __FUNCTION__]
    );
  }

  /**
   * {@inheritDoc}
   */
  public function find($criteria) {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['class' => __CLASS__, 'method' => __FUNCTION__]
    );
  }

  /**
   * {@inheritDoc}
   */
  public function getIds() : array {
    return $this->each(function ($item) {
      return $item->getId();
    });
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/JsonSerializable.jsonSerialize
   */
  public function jsonSerialize() {
    return $this->toArray();
  }

  /**
   * {@inheritDoc}
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
   * {@inheritDoc}
   */
  public function map(callable $function) : Collector {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['class' => __CLASS__, 'method' => __FUNCTION__]
    );
  }

  /**
   * {@inheritDoc}
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
   * {@inheritDoc}
   */
  public function sort(string $property, bool $desc = false) : Collector {
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
   * {@inheritDoc}
   */
  public function toArray() : array {
    return $this->_models;
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/Iterator.valid
   */
  public function valid() {
    return key($this->_models) !== null;
  }
}
