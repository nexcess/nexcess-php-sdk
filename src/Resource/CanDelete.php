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
  Resource\Modelable
};

/**
 * Provides delete() functionality for API endpoints.
 * Endpoints using this trait MUST implement Deletable.
 */
trait CanDelete {

  /** @var Client {@inheritDoc} @see Endpoint::$_client */
  protected $_client;

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
  public function delete(Modelable $model) : Modelable {
    $this->_checkModelType($model);
    $id = $model->getId();
    if (! is_int($id)) {
      throw new ApiException(
        ApiException::MISSING_ID,
        ['model' => static::class]
      );
    }

    $this->_client->delete("{$this->_getUri()}/{$id}");
    return $model;
  }
}
