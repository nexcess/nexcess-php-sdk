<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\ {
  ApiException,
  Resource\Model,
  Resource\ResourceException,
  Util\Util
};

/**
 * Provides create() functionality for API endpoints.
 * Endpoints using this trait MUST implement Creatable.
 */
trait CanCreate {

  /**
   * {@inheritDoc}
   */
  public function create(array $data) : Model {
    $this->_validateCreateData($data);

    $model = $this->getModel()->sync(
      $this->_client->request('POST', static::_URI . '/new', ['json' => $data])
    );

    $this->_wait($this->_waitUntilCreate($model));
    return $model;
  }

  /**
   * Throws if the given value is not json-encodable.
   * @see Util::isJsonable
   *
   * @param mixed $value The value to check
   * @throws ResourceException If value cannot be json-encoded
   */
  protected function _assertJsonable($value) : void {
    if (! Util::isJsonable($value)) {
      throw new ResourceException(
        ResourceException::NOT_JSONABLE,
        ['type' => Util::type($value)]
      );
    }
  }

  /**
   * Performs basic validation of data proivded to create().
   *
   * Extend this method to provide endpoint-specific validation.
   * It is recommend that child classes call this parent method first,
   * before doing their own validation.
   *
   * NOTE, authoritative validation is performed by the API;
   * to prevent conflicts/confusion, validation here should remain minimal:
   * mainly limited to checking data names and types.
   *
   * @param array $data The provided data
   * @throws ResourceException If data is missing/incorrect
   */
  protected function _validateCreateData(array $data) : void {
    array_walk_recursive($data, [$this, '_assertJsonable']);

    if (defined('static::_CREATE_DATA')) {
      $diff = array_diff_key($data, static::_CREATE_DATA);
      if (! empty($diff)) {
        throw new ResourceException(
          ResourceException::INVALID_CREATE_DATA_NAMES,
          ['names' => array_keys($diff)]
        );
      }

      foreach (static::_CREATE_DATA as $name => $info) {
        $this->_assertMatchesSchema($data[$name] ?? null, $info);
      }
    }
  }

  /**
   * Checks for a CREATE to finish and then syncs the associated Model.
   *
   * By default, assumes creation is already complete and simply syncs model.
   * Override this method to provide custom checks if needed.
   *
   * @param Model $model
   * @return callable @see wait() $until
   */
  protected function _waitUntilCreate(Model $model) : callable {
    return function ($endpoint) use ($model) {
      try {
        $endpoint->sync($model, true);
        return $model->isReal();
      } catch (ApiException $e) {
        if ($e->getCode() === ApiException::NOT_FOUND) {
          throw new ApiException(ApiException::CREATE_FAILED);
        }

        throw $e;
      }
    };
  }
}
