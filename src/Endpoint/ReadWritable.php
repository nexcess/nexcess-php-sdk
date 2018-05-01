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
interface ReadWritable extends Readable {

  /** @var int Key for wait() $opts interval. */
  const OPT_WAIT_INTERVAL = 0;

  /** @var int Key for wait() $opts timeout. */
  const OPT_WAIT_TIMEOUT = 1;

  /** @var int Default wait interval. */
  const DEFAULT_WAIT_INTERVAL = 1;

  /** @var int Default timeout before waiting fails. */
  const DEFAULT_WAIT_TIMEOUT = 30;

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
   * @return ReadWritable $this
   * @throws ApiException If request fails
   */
  public function delete($model_or_id) : ReadWritable;

  /**
   * Updates an existing item.
   *
   * @param int $id Item id
   * @param array|null $data Map of properties:values to set before update
   * @return ReadWritable $this
   * @throws ApiException If request fails
   */
  public function update(Model $model, array $data = []) : ReadWritable;

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
   *  bool $until(ReadWritable $endpoint) Returns true when done waiting
   *
   * If no callback is provided,
   * this will use a callback provided by the most recent action,
   * or return immediately if none exists.
   *
   * @param callable|null $until The callback to wait for
   * @param array $opts Wait interval/timeout options
   * @return ReadWritable $this
   * @throws ApiException If callback throws an ApiException
   * @throws SdkException If callback throws any other exception
   */
  public function wait(
    callable $until = null,
    array $opts = []
  ) : ReadWritable;
}
