<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\Endpoint,
  Response
};

/**
 * Represents an API endpoint for items with create/read/update/delete actions.
 */
abstract class ServiceEndpoint extends CrudEndpoint {

  /** {@inheritDoc} */
  const ENDPOINT = 'service';

  /** @var string Service type (must be overridden by implementing class). */
  const SERVICE_TYPE = '';

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = ['id' => 'service_id'];

  /**
   * Requests a service cancellation.
   *
   * @param Model $model Service model to cancel
   * @param array $survey Cancellation survey
   * @return Model
   * @throws ApiException If request fails
   */
  public function cancel(Model $model, $survey) : Model {
    $this->_checkModelType($model);
    throw new SdkException(SdkException::NOT_IMPLEMENTED);
  }

  /**
   * {@inheritDoc}
   * Overridden to set service type on list queries.
   */
  protected function _buildListQuery(array $filter) : string {
    return parent::_buildListQuery(['type' => static::SERVICE_TYPE] + $filter);
  }
}
