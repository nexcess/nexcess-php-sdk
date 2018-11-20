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
  Client,
  Resource\Modelable,
  Util\Util
};

/**
 * Provides update() functionality for API endpoints.
 * Endpoints using this trait MUST implement Updatable.
 */
trait CanUpdate {

  /** @var Client {@inheritDoc} @see Endpoint::$_client */
  protected $_client;

  /** @var array[] {@inheritDoc} @see Endpoint::$_retrieved */
  protected $_retrieved = [];

  /**
   * {@inheritDoc}
   * @see Endpoint::_checkModelType
   */
  abstract protected function _checkModelType(Modelable $model) : void;

  /**
   * {@inheritDoc}
   * @see Endpoint::_getUri
   */
  abstract protected function _getUri() : string;

  /**
   * {@inheritDoc}
   */
  public function update(Modelable $model) : Modelable {
    $this->_checkModelType($model);
    $id = $model->getId();
    if (! $id) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => get_class($model)]
      );
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
        Util::decodeResponse(
          $this->_client->patch("{$this->_getUri()}/{$id}", $update)
        ),
        true
      );
    }

    return $model;
  }
}
