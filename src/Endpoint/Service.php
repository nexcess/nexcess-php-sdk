<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\ReadWrite,
  Exception\ApiException,
  Model\Modelable as Model
};

/**
 * Represents an API endpoint for client services.
 */
abstract class Service extends ReadWrite {

  /** {@inheritDoc} */
  const ENDPOINT = 'service';

  /** @var string Service type. */
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
  public function cancel(Model $model, array $survey) : Model {
    $this->_checkModelType($model);
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['class' => static::class, 'method' => __FUNCTION__]
    );
  }

  /**
   * {@inheritDoc}
   * Overridden to set service type on list queries.
   */
  protected function _buildListQuery(array $filter) : string {
    return parent::_buildListQuery(['type' => static::SERVICE_TYPE] + $filter);
  }
}
