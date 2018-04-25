<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\Readable,
  Exception\ApiException,
  Model\Modelable as Model
};

/**
 * Interface for writable API endpoints.
 */
Interface ReadWritable extends Readable {

  /**
   * Creates a new item.
   *
   * @param array $data Map of values for new item
   * @return Model
   * @throws ApiException If request fails
   */
  public function create(array $data) : Model;

  /**
   * Deletes an existing item.
   *
   * @param Model|int $id Model or item id to delete
   * @throws ApiException If request fails
   */
  public function delete($model_or_id);

  /**
   * Updates an existing item.
   *
   * @param int $id Item id
   * @param array|null $data Map of properties:values to set before update
   * @return Model The updated Model
   * @throws ApiException If request fails
   */
  public function update(Model $model, array $data = []) : Model;
}
