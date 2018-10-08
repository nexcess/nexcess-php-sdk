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
  Resource\Updatable
};

/**
 * Provides update() functionality for API endpoints.
 * Endpoints using this trait MUST implement Updatable.
 */
trait CanUpdate {

  /**
   * {@inheritDoc}
   */
  public function update(Model $model, array $data = []) : Updatable {
    $this->_checkModelType($model);

    $id = $model->getId();
    if (! $id) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::_MODEL_FQCN]
      );
    }

    foreach ($data as $key => $value) {
      $model->set($key, $value);
    }

    $update = isset($this->_retrieved[$id]) ?
      array_udiff_assoc(
        $model->toCollapsedArray(),
        $model->toCollapsedArray($this->_retrieved[$id]),
        function ($value, $retrieved) {
          return ($value === $retrieved) ? 0 : 1;
        }
      ) :
      $model->toCollapsedArray();


    if (! empty($update)) {
      $model->sync(
        $this->_client
          ->request('PATCH', static::_URI . "/{$id}/edit", $update),
        true
      );
    }
    $this->_wait(null);
    return $this;
  }
}
