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
  Resource\Modelable as Model,
  Resource\Readable
};

/**
 * Interface for writable API endpoints.
 */
interface Writable extends Readable {

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
   * @param Model|int $model_or_id Model or item id to delete
   * @return Writable $this
   * @throws ApiException If request fails
   */
  public function delete($model_or_id) : Writable;

  /**
   * Updates an existing item.
   *
   * @param int $id Item id
   * @param array|null $data Map of properties:values to set before update
   * @return Writable $this
   * @throws ApiException If request fails
   */
  public function update(Model $model, array $data = []) : Writable;

  /**
   * Blocks until callback returns true.
   *
   * Some actions are queued by the API,
   * and so may not be complete at the time the API response is received.
   * This method is used to wait for a queued action to complete
   * (though it is possible, of course, use it to wait for anything).
   *
   * @example <?php
   *  // this code may experience a race condition,
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
   *  bool $until(Writable $endpoint) Returns true when done waiting
   *
   * If no callback is provided,
   * this will use a callback provided by the most recent action,
   * or return immediately if none exists.
   *
   * @param callable|null $until The callback to wait for
   * @param array $opts Wait interval/timeout options
   * @return Writable $this
   * @throws ApiException If callback throws an ApiException
   * @throws SdkException If callback throws any other exception
   */
  public function wait(callable $until = null, array $opts = []) : Writable;
}
