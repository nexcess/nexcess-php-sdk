<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
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
  const TYPE = '';

  /**
   * Requests a service cancellation.
   *
   * @param int $id Service id to cancel
   * @param array $survey Cancellation survey
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function cancel(int $id, array $survey) : Response {
    throw new SdkException(SdkException::NOT_IMPLEMENTED);
  }

  /**
   * {@inheritDoc}
   * Overridden to set service type on list queries.
   */
  protected function _buildListQuery(array $filter) : string {
    return parent::_buildListQuery(['type' => static::TYPE] + $filter);
  }
}
