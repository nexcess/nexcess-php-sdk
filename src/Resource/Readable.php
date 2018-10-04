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
   * Gets a new Model instance, and sets the model id if provided.
   *
   * @param int|null $id Model id
   * @return Model
   */
  public function getModel(int $id = null) : Model;

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
   * To save changes to an updatable model, @see Updatable::update
   *
   * @param bool $hard Force hard sync with API?
   * @return Model The sync'd model
   */
  public function sync(Model $model, bool $hard = false) : Model;

  /**
   * Blocks until callback returns true.
   *
   * Some actions are queued by the API,
   * and so may not be complete at the time the API response is received.
   * This method is used to wait for a queued action to complete
   * (though it is possible, of course, to use it to wait for anything).
   *
   * @example <?php
   *  // suppose this code may experience a race condition,
   *  //  where the $model object is in the wrong state for the second call:
   *  $endpoint->someQueuedAction($model)
   *    ->someOtherAction($model);
   *
   *  // to prevent this, wait() for the queued action to complete first:
   *  $endpoint->someQueuedAction($model)
   *    ->wait()
   *    ->someOtherAction($model);
   *
   * The callback signature is like
   *  bool $until(Readable $endpoint) Returns true when done waiting
   *
   * If no callback is provided,
   * this will use a callback provided by the most recent action,
   * or return immediately if none exists.
   *
   * @param callable|null $until The callback to wait for
   * @param array $opts Wait interval/timeout options
   * @return Readable $this
   * @throws ApiException If callback throws an ApiException
   * @throws SdkException If callback throws any other exception
   */
  public function wait(callable $until = null, array $opts = []) : Readable;
}
