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
  Resource\Modelable,
  Resource\PromisedResource,
  Resource\ResourceException,
  Util\Util
};

/**
 * Provides create() functionality for API endpoints.
 * Endpoints using this trait MUST implement Creatable.
 */
trait CanCreate {

  /**
   * {@inheritDoc}
   */
  public function create(array $data) : Modelable {
    $this->_validateParams(__FUNCTION__, $data);

    $model = $this->getModel()->sync(
      Util::decodeResponse(
        $this->_post(static::_URI . '/new', ['json' => $data])
      )
    );

    return $this->_buildPromise($model)
      ->waitUntil($this->_waitUntilCreate());
  }

  /**
   * Checks for a CREATE to finish and then syncs the associated Model.
   *
   * Creation is usually already complete;
   * this simply syncs model if there's no id.
   * Override this method to provide custom checks if needed.
   *
   * @return callable @see PromisedResource::waitUntil() $done
   */
  protected function _waitUntilCreate() : callable {
    return function ($model) {
      try {
        if ($model->isReal()) {
          return true;
        }

        $this->sync($model);
        return $model->isReal();
      } catch (ApiException $e) {
        if ($e->getCode() === ApiException::NOT_FOUND) {
          throw new ApiException(ApiException::CREATE_FAILED, $e);
        }

        throw $e;
      }
    };
  }
}
