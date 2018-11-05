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
  Resource\Model,
  Resource\Promise
};

/**
 * Provides delete() functionality for API endpoints.
 * Endpoints using this trait MUST implement Deletable.
 */
trait CanDelete {

  /**
   * {@inheritDoc}
   */
  public function delete(Model $model) : Model {
    $this->_checkModelType($model);
    $id = $model->getId();
    if (! is_int($id)) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::class]
      );
    }

    $this->_client->delete(static::_URI . "/{$id}");
    return $model;
  }
}
