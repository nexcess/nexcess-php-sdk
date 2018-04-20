<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use Nexcess\Sdk\ {
  Endpoint,
  Response
};

/**
 * Represents an API endpoint for items with create/read/update/delete actions.
 */
abstract class ServiceEndpoint extends CrudEndpoint {

  /** {@inheritDoc} */
  const ENDPOINT = 'service';

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
   * Overridden to use service/ endpoint.
   * The implementing class must define BASE_LIST_FILTER["type"].
   *
   * @see DoesEndpointCrud::list()
   */
  public function list(array $filter = []) : Response {
    return $this->_request('GET', "service?{$this->_buildListQuery($filter)}");
  }
}
