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
  Resource\Model,
  Resource\Promise,
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
  public function create(array $data) : Model {
    $this->_validateParams(__FUNCTION__, $data);

    return $this->getModel()->sync(
      Util::decodeResponse(
        $this->_client->post(static::_URI . '/new', ['json' => $data])
      )
    );
  }
}
