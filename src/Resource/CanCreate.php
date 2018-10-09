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
  Resource\Model
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
    $model = $this->getModel()->sync(
      $this->_client->request('POST', static::_URI . '/new', ['json' => $data])
    );

    $this->_wait($this->_waitUntilCreate($model));
    return $model;
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
