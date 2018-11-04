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
  Resource\Promise,
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
  public function update(Model $model) : Model {
    $this->_checkModelType($model);
    $id = $model->getId();
    if (! $id) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::_MODEL_FQCN]
      );
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
        $this->_client->patch(static::_URI . "/{$id}", $update),
        true
      );
    }

    return $model;
  }
}
