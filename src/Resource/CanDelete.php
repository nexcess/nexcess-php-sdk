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
  Resource\Deletable,
  Resource\Model
};

/**
 * Provides delete() functionality for API endpoints.
 * Endpoints using this trait MUST implement Deletable.
 */
trait CanDelete {

  /**
   * {@inheritDoc}
   */
  public function delete($model_or_id) : Deletable {
    $model = is_int($model_or_id) ?
      $this->getModel($model_or_id) :
      $model_or_id;
    // throws if wrong model
    $this->_checkModelType($model);

    // can't delete if doesn't exist yet
    $id = $model->getId();
    if (! is_int($id)) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::class]
      );
    }

    $this->_client->request('DELETE', static::_URI . "/{$id}");
    $this->_wait($this->_waitUntilDelete($model));

    return $this;
  }

  /**
   * Checks for a DELETE to finish and then syncs the associated Model.
   *
   * @param Model $model
   * @return callable @see wait() $until
   */
  protected function _waitUntilDelete(Model $model) : callable {
    return function ($endpoint) use ($model) {
      try {
        $endpoint->retrieve($model->getId());
      } catch (ApiException $e) {
        if ($e->getCode() === ApiException::NOT_FOUND) {
          $model->unset('id');
          return true;
        }

        throw $e;
      }
    };
  }
}
