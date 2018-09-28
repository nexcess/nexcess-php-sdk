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
  Resource\Collector as Collection,
  Resource\Modelable as Model
};

/**
 * Interface for readable API endpoints for nexcess.net / thermo.io.
 *
 * Not all endpoints are editable.
 * This interface only defines read-oriented methods.
 * For write-oriented methods, @see Writable
 */
interface Readable {

  /**
   * Gets a new (empty) Model instance.
   *
   * @param string|null $name Model name (base name or fully qualified)
   * @return Model
   */
  public function getModel(string $name = null) : Model;

  /**
   * Fetches a paginated list of items from the API.
   *
   * @param array $filter Pagination and Model-specific filter options
   * @return Collection Models returned from the API
   * @throws ApiException If API request fails
   */
  public function list(array $filter = []) : Collection;

  /**
   * Fetches an item from the API.
   *
   * @param int $id Item id
   * @return Model A new model read from the API
   * @throws ApiException If the API request fails (e.g., item doesn't exist)
   */
  public function retrieve(int $id) : Model;

  /**
   * Syncs a Model with most recently fetched data from the API,
   * or re-fetches the item from the API.
   *
   * Note, this can OVERWRITE the model's state with the response from the API;
   * but it WILL NOT UPDATE the API with the model's current state.
   * To save changes to an updatable model, @see Writable::update()
   *
   * @param bool $hard Force hard sync with API?
   * @return Model The sync'd model
   */
  public function sync(Model $model, bool $hard = false) : Model;
}
