<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Exception\ApiException,
  Model\Collector as Collection,
  Model\Modelable as Model
};

/**
 * Interface for readable API endpoints for nexcess.net / thermo.io.
 *
 * Not all endpoints are editable.
 * This interface only defines read-oriented methods.
 * For write-oriented methods, @see ReadWriter
 */
interface Readable {

  /** @var string Base namespace for endpoint classes. */
  const NAMESPACE = __NAMESPACE__;

  /**
   * Gets a new Model instance, and sets the model id if provided.
   *
   * @param int|null $id Model id
   * @return Model
   */
  public function getModel(int $id = null) : Model;

  /**
   * Checks whether given model is in sync with stored data, or with the API.
   *
   * @param Model $model The model to check
   * @param bool $hard Force hard check with API?
   * @return bool True if data is in sync; false otherwise
   * @throws ApiException If API request fails
   */
  public function isInSync(Model $model, bool $hard = false) : bool;

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
   * To save changes to an updatable model, @see ReadWriter::update
   *
   * @param bool $hard Force hard sync with API?
   * @return Model The sync'd model
   */
  public function sync(Model $model, bool $hard = false) : Model;
}
