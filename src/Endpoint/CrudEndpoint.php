<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\Endpoint,
  Response
};

/**
 * Represents an API endpoint for items with create/read/update/delete actions.
 */
abstract class CrudEndpoint extends Endpoint {

  /**
   * Creates a new item.
   *
   * @param array $data Map of values for new item
   * @return Model
   * @throws ApiException If request fails
   */
  public function create(array $data) : Model {
    $model = static::MODEL_NAME;

    return $this->sync(
      new $model(),
      $this->_client
        ->request('POST', static::ENDPOINT, ['json' => $data])
        ->toArray()
    );
  }

  /**
   * Deletes an existing item.
   *
   * @param Model|int $id Model or item id to delete
   * @throws ApiException If request fails
   */
  public function delete($model_or_id) {
    $fqcn = static::MODEL_NAME;
    $id = ($model_or_id instanceof $fqcn) ?
      $model_or_id->offsetGet('id') :
      $model_or_id;
    if (! is_int($id)) {
      throw new ApiException(ApiException::MISSING_ID, ['model' => $fqcn]);
    }

    $this->_request('DELETE', static::ENDPOINT . "/{$id}");
  }

  /**
   * Updates an existing item.
   *
   * Implementing class must define EDIT_VALUE_MAP as a name:default value map.
   *
   * @param int $id Item id
   * @param array|null $data Map of properties:values to set before update
   * @return Model The updated Model
   * @throws ApiException If request fails
   */
  public function update(Model $model, array $data = null) : Model {
    $id = $model->offsetGet('id');
    if (! $id) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::NAME]
      );
    }

    foreach ($data as $key => $value) {
      $model->offsetSet($key, $value);
    }

    $update = empty($this->_stored[$id]) ?
      $model->toArray() :
      array_udiff_assoc(
        $model->toArray(),
        $this->_stored[$id],
        function ($value, $stored) { return ($value === $stored) ? 0 : 1; }
      );

    if (! empty($update)) {
      return $this->_sync(
        $this->_client
          ->request('PATCH', static::ENDPOINT . "/{$id}/edit", $update)
          ->toArray()
      );
    }
    return $model;
  }
}
