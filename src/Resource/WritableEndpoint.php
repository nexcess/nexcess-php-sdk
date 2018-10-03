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
  SdkException,
  Resource\Endpoint,
  Resource\Modelable as Model,
  Resource\Writable
};

/**
 * Represents a writable API endpoint.
 */
abstract class WritableEndpoint extends Endpoint implements Writable {

  /** @var bool Is this endpoint creatable? */
  public const CAN_CREATE = true;

  /** @var bool Is this endpoint deletable? */
  public const CAN_DELETE = true;

  /** @var bool Is this endpoint updatable? */
  public const CAN_UPDATE = true;

  /** @var int Key for wait() $opts interval. */
  public const OPT_WAIT_INTERVAL = 0;

  /** @var int Key for wait() $opts timeout. */
  public const OPT_WAIT_TIMEOUT = 1;

  /** @var int Default wait interval. */
  protected const _DEFAULT_WAIT_INTERVAL = 1;

  /** @var int Default timeout before waiting fails. */
  protected const _DEFAULT_WAIT_TIMEOUT = 30;

  /** @var string|null API endpoint url for create action (if different). */
  protected const _URI_CREATE = null;

  /** @var callable Queued callback for wait(). */
  protected $_wait_until;

  /**
   * {@inheritDoc}
   */
  public function create(array $data) : Model {
    if (! static::CAN_CREATE) {
      throw new ApiException(
        ApiException::CANNOT_CREATE,
        ['endpoint' => basename(static::_MODEL_FQCN)]
      );
    }

    $model = $this->getModel()->sync(
      $this->_client->request(
        'POST',
        static::_URI_CREATE ?? static::_URI,
        ['json' => $data]
      )
    );

    $this->_wait($this->_waitUntilCreate($model));
    return $model;
  }

  /**
   * {@inheritDoc}
   */
  public function delete($model_or_id) : Writable {
    if (! static::CAN_DELETE) {
      throw new ApiException(
        ApiException::CANNOT_DELETE,
        ['endpoint' => static::class]
      );
    }

    if ($model_or_id instanceof Model) {
      $this->_checkModelType($model_or_id);
      $id = $model_or_id->getId();
    } else {
      $id = $model_or_id;
    }

    if (! is_int($model_or_id)) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::class]
      );
    }

    $this->_client->request('DELETE', static::_URI . "/{$id}");
    $this->_wait($this->_waitUntilDelete($model));

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function update(Model $model, array $data = []) : Writable {
    if (! static::CAN_UPDATE) {
      throw new ApiException(
        ApiException::CANNOT_UPDATE,
        ['endpoint' => basename(static::_MODEL_FQCN)]
      );
    }

    $this->_checkModelType($model);

    $id = $model->getId();
    if (! $id) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::_MODEL_FQCN]
      );
    }

    foreach ($data as $key => $value) {
      $model->set($key, $value);
    }

    $update = isset($this->_retrieved[$id]) ?
      array_udiff_assoc(
        $model->toCollapsedArray(),
        $model->toCollapsedArray($this->_retrieved[$id]),
        function ($value, $retrieved) {
          return ($value === $retrieved) ? 0 : 1;
        }
      ) :
      $model->toCollapsedArray();


    if (! empty($update)) {
      $model->sync(
        $this->_client
          ->request('PATCH', static::_URI . "/{$id}/edit", $update),
        true
      );
    }
    $this->_wait(null);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function wait(callable $until = null, array $opts = []) : Writable {
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
      self::_DEFAULT_WAIT_INTERVAL;

    $timeout = $opts[self::OPT_WAIT_TIMEOUT] ??
      $config->get('wait.timeout') ??
      self::_DEFAULT_WAIT_TIMEOUT;
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
        $endpoint->sync($model, true);
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
    $this->_wait_until = $until;
    if ($until !== null && $this->_config->get('wait.always')) {
      $this->wait($until);
      return;
    }
  }
}
