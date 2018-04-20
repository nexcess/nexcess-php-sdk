<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Throwable;

use GuzzleHttp\ {
  Client as Guzzle,
  Exception\ClientException,
  Exception\ConnectException,
  Exception\RequestException,
  Exception\ServerException,
  Exception\TransferException
};

use Nexcess\Sdk\ {
  Exception\ApiException,
  Exception\SdkException,
  Model\Collection,
  Model\Model,
  Response,
  Util\Config,
  Util\Util
};

/**
 * Represents an API endpoint for nexcess.net / thermo.io
 */
abstract class Endpoint {

  /** @var array Default filter values for list(). */
  const BASE_LIST_FILTER = [];

  /** @var string Fully qualified classname of the Model for this endpoint. */
  const MODEL_NAME = '';

  /** @var Guzzle The Guzzle http client. */
  private $_client;

  /** @var Config Client configuration object. */
  protected $_config;

  /** @var array Map of last fetched property:value pairs. */
  protected $_stored;

  public function __construct(Client $client, Config $config) {
    $this->_client = $client;
    $this->_config = $config;
  }

  /**
   * Gets a new Model instance.
   *
   * @param int|null $id Model id
   * @return Model
   */
  public function getModel(int $id = null) : Model {
    $fqcn = static::MODEL_NAME;
    return new $fqcn($id);
  }

  /**
   * Checks whether given model is in sync with stored data, or with the API.
   *
   * @param Model $model The model to check
   * @param bool $hard Force hard check with API?
   * @return bool True if data is in sync; false otherwise
   * @throws ApiException If API request fails
   */
  public function isInSync(Model $model, bool $hard = false) : bool {
    $data = $model->toArray();
    $id = $model->offsetGet('id');
    if ($id === null || empty($this->_stored[$id])) {
      return false;
    }

    return empty(
      $this->_diff(
        $model->toArray(),
        ($hard) ? $this->_read($id) : $this->_stored[$id]
      )
    );
  }

  /**
   * Fetches a paginated list of items from the API.
   *
   * @param array $filter Pagination and Model-specific filter options
   * @return array API response data
   * @throws ApiException If API request fails
   */
  public function list(array $filter = []) : array {
    $response = $this->_client->request(
      'GET',
      static::ENDPOINT . "?{$this->_buildListQuery($filter)}"
    );

    $collection = new Collection(static::MODEL_NAME);
    foreach ($response->toArray() as $data) {
      $item = $this->getModel();
      $item->sync($data);
      $collection->add($item);
    }

    return $collection;
  }

  /**
   * Fetches an item from the API.
   *
   * @param int $id Item id
   * @return Model The model read from the API
   * @throws ModelException If the id is invalid
   * @throws ApiException If the API request fails (e.g., item doesn't exist)
   */
  public function read(int $id) : Model {
    $this->sync($this->getModel($id), true);
  }

  /**
   * Syncs a Model with most recently fetched data from the API,
   * or re-fetches the item from the API.
   *
   * @param bool $hard Force hard sync with API?
   * @return Endpoint $this
   */
  public function sync(Model $model, bool $hard = false) : Model {
    $id = $model->getOffset('id');

    if ($hard || empty($this->_stored[$id])) {
      $this->_stored[$id] = $this->_read($id);
    }

    $model->sync($this->_stored[$id], true);
    return $model;
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

  /**
   * Reads an item from the API and returns its response data as an array.
   *
   * @param int $id Item id
   * @return array API response data
   * @throws ApiException If API request fails
   */
  protected function _read(int $id) : array {
    return $this->_client
        ->request('GET', static::ENDPOINT . "/{$id}")
        ->toArray();
}
