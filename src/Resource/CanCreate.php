<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\ {
  Client,
  Resource\Modelable,
  Util\Util
};

/**
 * Provides create() functionality for API endpoints.
 * Endpoints using this trait MUST implement Creatable.
 */
trait CanCreate {

  /** @var Client {@inheritDoc} @see Endpoint::$_client */
  protected $_client;

  /**
   * {@inheritDoc}
   * @see Endpoint::getModel
   */
  abstract public function getModel(string $name = null) : Modelable;

  /**
   * {@inheritDoc}
   * @see Endpoint::_getUri
   */
  abstract protected function _getUri() : string;

  /**
   * {@inheritDoc}
   * @see Endpoint::_validateParams
   */
  abstract protected function _validateParams(
    string $action,
    array $params
  ) : void;

  /**
   * {@inheritDoc}
   */
  public function create(array $data) : Modelable {
    $this->_validateParams(__FUNCTION__, $data);

    return $this->getModel()->sync(
      Util::decodeResponse(
        $this->_client->post("{$this->_getUri()}/new", ['json' => $data])
      )
    );
  }
}
