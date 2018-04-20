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
abstract class CrudEndpoint extends Endpoint {

  /** @var array Map of field names:values for add() action. */
  const ADD_VALUE_MAP = [];

  /** @var array Default filter values for list(). */
  const BASE_LIST_FILTER = [];

  /** @var array Map of field names:values for edit() action. */
  const EDIT_VALUE_MAP = [];

  /**
   * Creates a new item.
   *
   * Implementing class must define ADD_VALUE_MAP as a name:default value map.
   *
   * @param array $values Map of values for new item
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function add(array $values) : Response {
    $params = [
      'json' => array_intersect_key($values, static::ADD_VALUE_MAP) +
        static::ADD_VALUE_MAP
    ];
    return $this->_request('POST', static::ENDPOINT, $params);
  }

  /**
   * Deletes an existing item.
   *
   * @param int $id Item id
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function delete(int $id) : Response {
    return $this->_request('DELETE', static::ENDPOINT . "/{$id}");
  }

  /**
   * Updates an existing item.
   *
   * Implementing class must define EDIT_VALUE_MAP as a name:default value map.
   *
   * @param int $id Item id
   * @param array $update Property:value map of changes to make
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function edit(int $id, array $values) : Response {
    $arams = [
      'json' => array_intersect_key($values, static::EDIT_VALUE_MAP) +
        static::EDIT_VALUE_MAP
    ];
    return $this->_request('PATCH', self::ENDPOINT . "/{$id}/edit", $params);
  }

  /**
   * Gets a paginated list of existing items.
   *
   * @param array $filter @see DoesEndpointCrud::_buildListQuery $filter
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function list(array $filter = []) : Response {
    return $this->_request(
      'GET',
      static::ENDPOINT . "?{$this->_buildListQuery($filter)}"
    );
  }

  /**
   * Gets information about an existing item.
   *
   * @param int $id Item id
   * @return array API response data
   * @throws ApiException If request fails
   */
  public function show(int $id) : Response {
    return $this->_request('GET', static::ENDPOINT . "/{$id}");
  }

  /**
   * Builds a query string for list requests.
   *
   * @param array $filter Map of query string parameters
   * @return string A http query string
   */
  protected function _buildListQuery(array $filter) : string {
    return http_build_query($filter + static::BASE_LIST_FILTER);
  }
}
