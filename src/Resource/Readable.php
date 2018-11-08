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
 */
interface Readable {

  /**
   * Gets the module name this model belongs to.
   *
   * @return string Module name
   */
  public static function moduleName() : string;

  /**
   * Gets a new (empty) Model instance.
   *
   * @param string|null $name Model name (base name or fully qualified)
   * @return Model
   */
  public function getModel(string $name = null) : Model;

  /**
   * Gets a list of parameters and their descriptions for an action.
   *
   * @param string $action The subject action
   * @return array A parameter name: [type, required, description] map
   */
  public function getParams(string $action) : array;

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
   * Re-fetches a resource from the API.
   *
   * Note, this can OVERWRITE the model's state with the response from the API;
   * but it WILL NOT UPDATE the API with the model's current state.
   * To save changes to an updatable model, @see Updatable::update
   *
   * @param Model $model The Model to sync
   * @return Model The sync'd model
   */
  public function sync(Model $model) : Model;
}
