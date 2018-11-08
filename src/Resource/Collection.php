<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Throwable;

use Nexcess\Sdk\ {
  Resource\Collector,
  Resource\Modelable,
  Resource\ResourceException,
  SdkException,
  Util\Util
};

/**
 * Container for a collection of Modelables.
 */
class Collection implements Collector {

  /** @var Modelable[] List of models. */
  protected $_models = [];

  /** @var string FQCN of models this collection holds. */
  protected $_of;

  /**
   * @param string $of Fully qualified Model classname this collection holds
   * @param Modelable[] $models List of models to add to the collection
   */
  public function __construct(string $of, array $models = []) {
    $this->_of = $of;
    foreach ($models as $model) {
      $this->add($model);
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
    $collection = new static($data['_of']);
    $collection->_models = $data['_models'] ?? [];
    return $collection;
  }

  /**
   * {@inheritDoc}
   */
  public function add(Modelable $model) : Collector {
    if (! $model instanceof $this->_of) {
      throw new ResourceException(
        ResourceException::WRONG_MODEL_FOR_COLLECTION,
        ['collection' => $this->_of, 'model' => get_class($model)]
      );
    }

    $this->_models[] = $model;

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
  public function equals(Collector $other) : bool {
    if ($other->of() !== $this->of()) {
      return false;
    }

    $ids = $this->getIds();
    $otherids = $other->getIds();
    sort($ids);
    sort($otherids);
    return ($ids === $otherids);
  }

  /**
   * {@inheritDoc}
   */
  public function filter($filter) : Collector {
    if (! is_callable($filter)) {
      if (is_array($filter)) {
        $filter = function ($model) use ($filter) {
          foreach ($filter as $property => $value) {
            if ($model->get($property) !== $value) {
              return false;
            }
          }

          return true;
        };
      } else {
        throw new ResourceException(
          ResourceException::INVALID_FILTER,
          ['method' => __METHOD__, 'type' => Util::type($filter)]
        );
      }
    }

    return new static($this->_of, array_filter($this->_models, $filter));
  }

  /**
   * {@inheritDoc}
   */
  public function find($criteria) : ?Modelable {
    return $this->filter($criteria)->current();
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
    return $this->toArray(true);
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/Iterator.key
   */
  public function key() {
    return key($this->_models);
  }

  /**
   * {@inheritDoc}
   */
  public function map(callable $function) : Collector {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['method' => __METHOD__]
    );
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
  public function of() : string {
    return $this->_of;
  }

  /**
   * {@inheritDoc}
   */
  public function remove($model_or_id) : Modelable {
    foreach ($this->_models as $i => $model) {
      if ($model->equals($model_or_id) || $model->getId() === $model_or_id) {
        unset($this->_models[$i]);
        return $model;
      }
    }

    throw new ResourceException(
      ResourceException::MODEL_NOT_FOUND,
      ['model' => $this->_of, 'id' => $model_or_id]
    );
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
  public function sort(string $prop = null, bool $desc = false) : Collector {
    $prop = $prop ?? 'id';

    uasort(
      $this->_models,
      function ($a, $b) use ($prop, $desc) {
        return ($desc) ?
          $b->get($prop) <=> $a->get($prop) :
          $a->get($prop) <=> $b->get($prop);
      }
    );

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function toArray(bool $recurse = true) : array {
    $models = $this->_models;
    uasort(
      $models,
      function ($a, $b) {
        return $a->getId() <=> $b->getId();
      }
    );

    return $recurse ?
      array_map(
        function ($model) use ($recurse) {
          return $model->toArray($recurse);
        },
        $models
      ) :
      $models;
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/Iterator.valid
   */
  public function valid() {
    return key($this->_models) !== null;
  }
}
