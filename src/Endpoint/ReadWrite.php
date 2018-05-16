<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\Read,
  Endpoint\ReadWritable,
  Exception\ApiException,
  Exception\SdkException,
  Model\Modelable as Model,
  Response
};

/**
 * Represents a writable API endpoint.
 */
abstract class ReadWrite extends Read implements ReadWritable {

  /** @var string|null API endpoint url for create action (if different). */
  const ENDPOINT_CREATE = null;

  /** @var callable Queued callback for wait(). */
  protected $_wait_until;

  /**
   * {@inheritDoc}
   */
  public function create(array $data) : Model {
    $response = $this->_client
      ->request(
        'POST',
        static::ENDPOINT_CREATE ?? static::ENDPOINT,
        ['json' => $data]
      );

    $model = $this->getModel()->sync($response->toArray());

    $this->_wait($this->_waitUntilCreate($model));
    return $model;
  }

  /**
   * {@inheritDoc}
   */
  public function delete($model_or_id) : ReadWritable {
    $model = is_int($model_or_id) ?
      $this->getModel($model_or_id) :
      $model_or_id;
    $this->_checkModelType($model);

    $id = $model->getId();
    if (! is_int($id)) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::class]
      );
    }

    $this->_client->request('DELETE', static::ENDPOINT . "/{$id}");
    $this->_wait($this->_waitUntilDelete($model));

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function update(Model $model, array $data = []) : ReadWritable {
    $this->_checkModelType($model);

    $id = $model->get('id');
    if (! $id) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::MODEL]
      );
    }

    foreach ($data as $key => $value) {
      $model->set($key, $value);
    }

    $update = empty($this->_stored[$id]) ?
      $model->toArray() :
      array_udiff_assoc(
        $model->toArray(true),
        $this->_stored[$id],
        function ($value, $stored) { return ($value === $stored) ? 0 : 1; }
      );

    $this->_wait(null);

    return (! empty($update)) ?
      $this :
      $this->_sync(
        $this->_client
          ->request('PATCH', static::ENDPOINT . "/{$id}/edit", $update)
          ->toArray()
      );
  }

  /**
   * {@inheritDoc}
   */
  public function wait(
    callable $until = null,
    array $opts = []
  ) : ReadWritable {
    $config = $this->_config;

    $until = $until ??
      $this->_wait_until ??
      function () {
        return true;
      };
    $this->_wait_until = null;

    $tick = $config->get('wait.tick_function');

    $wait = $opts[self::OPT_WAIT_INTERVAL] ??
      $config->get('wait.interval') ??
      self::DEFAULT_WAIT_INTERVAL;

    $timeout = $opts[self::OPT_WAIT_TIMEOUT] ??
      $config->get('wait.timeout') ??
      self::DEFAULT_WAIT_TIMEOUT;
    $deadline = time() + $timeout;

    try {
      while ($until($this) !== true) {
        if (time() > $deadline) {
          throw new SdkException(
            SdkException::WAIT_TIMEOUT_EXCEEDED,
            ['timeout' => $timeout]
          );
        }

        if ($tick) {
          $tick($this);
        }
        sleep($wait);
      }

      return $this;
    } catch (ApiException $e) {
      throw $e;
    } catch (Throwable $e) {
      throw new SdkException(SdkException::CALLBACK_ERROR, $e);
    }
  }

  /**
   * Checks for a CREATE to finsih and then syncs the associated Model.
   *
   * By default, assumes creation is already complete.
   * Override this method to provide custom checks if needed.
   *
   * @param Model $model
   * @return callable @see wait() $until
   */
  protected function _waitUntilCreate(Model $model) : callable {
    return function ($endpoint) use ($model) {
      try {
        $model->sync($endpoint->retrieve($model->getId())->toArray());
        return true;
      } catch (ApiException $e) {
        if ($e->getCode() === ApiException::NOT_FOUND) {
          throw new ApiException(ApiException::CREATE_FAILED);
        }

        throw $e;
      }
    };
  }

  /**
   * Checks for a DELETE to finish and then syncs the associated Model.
   *
   * @param Model $model
   * @return callable @see wait() $until
   */
  protected function _waitUntilDelete(Model $model) : callable {
    return function ($endpoint) use ($model) {
      try {
        $endpoint->retrieve($model->getId());
      } catch (ApiException $e) {
        if ($e->getCode() === ApiException::NOT_FOUND) {
          $model->unset('id');
          return true;
        }

        throw $e;
      }
    };
  }

  /**
   * Queues or invokes a wait callback based on config options.
   *
   * @param callable|null $until
   */
  protected function _wait(callable $until = null) {
    if ($until === null) {
      $this->_wait_until = null;
      return;
    }

    if ($this->_config->get('wait.always')) {
      $this->wait($until);
      return;
    }

    $this->_wait_until = $until;
  }
}
