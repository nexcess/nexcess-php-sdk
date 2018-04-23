<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
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
  Client,
  Endpoint\Response,
  Exception\ApiException,
  Exception\SdkException,
  Model\Collection,
  Model\Model,
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
  protected $_client;

  /** @var Config Client configuration object. */
  protected $_config;

  /** @var array Map of last fetched property:value pairs. */
  protected $_retrieved = [];

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
   * Gets cached data retrieved from API.
   *
   * Note, this method can only be used when the "debug" options is set.
   *
   * @return array Map of last fetched property:value pairs
   * @throws SdkException If debug mode is not enabled
   */
  public function getRetrievedData() : array {
    if (! $this->_config->get('debug')) {
      throw new SdkException(
        SdkException::DEBUG_NOT_ENABLED,
        ['method' => __METHOD__]
      );
    }

    return $this->_retrieved;
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
    if ($id === null || empty($this->_retrieved[$id])) {
      return false;
    }

    return empty(
      $this->_diff(
        $model->toArray(),
        ($hard) ? $this->_retrieve($id) : $this->_retrieved[$id]
      )
    );
  }

  /**
   * Fetches a paginated list of items from the API.
   *
   * @param array $filter Pagination and Model-specific filter options
   * @return Collection Models returned from the API
   * @throws ApiException If API request fails
   */
  public function list(array $filter = []) : Collection {
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
   * @return Model A new model read from the API
   * @throws ApiException If the API request fails (e.g., item doesn't exist)
   */
  public function retrieve(int $id) : Model {
    $this->sync($this->getModel($id), true);
  }

  /**
   * Syncs a Model with most recently fetched data from the API,
   * or re-fetches the item from the API.
   *
   * Note, this OVERWRITES the model's state with the response from the API;
   * it *does not* update the API with the model's current state.
   * To save changes to an updatable model, @see CrudEndpoint::update
   *
   * @param bool $hard Force hard sync with API?
   * @return Endpoint $this
   */
  public function sync(Model $model, bool $hard = false) : Model {
    $id = $model->offsetGet('id');
    return $model->sync(
      ($hard || empty($this->_retrieved[$id])) ?
        $this->_retrieve($id) :
        $this->_retrieved[$id]
    );
  }

  /**
   * Builds a query string for list requests.
   *
   * @param array $filter Map of query string parameters
   * @return string A http query string
   */
  protected function _buildListQuery(array $filter) : string {
    $page_size = $this->_config->get('list.pageSize');
    if ($page_size) {
      $filter['pageSize'] = $filter['pageSize'] ?? $page_size;
    }

    return http_build_query($filter + static::BASE_LIST_FILTER);
  }

  /**
   * Checks that a provided model is of the correct type for this endpoint.
   *
   * @param Model $model The model to check
   * @throws ApiException If the model is of the wrong class
   */
  protected function _checkModelType(Model $model) {
    $fqcn = static::MODEL_NAME;
    if (! $model instanceof $fqcn) {
      throw new ApiException(
        ApiException::WRONG_MODEL_FOR_ENDPOINT,
        [
          'endpoint' => static::class,
          'model' => $fqcn,
          'type' => get_class($model)
        ]
      );
    }
  }

  /**
   * Reads an item from the API and returns its response data as an array.
   *
   * @param int $id Item id
   * @return array Data returned from API request
   * @throws ApiException If API request fails
   */
  protected function _retrieve(int $id) : array {
    $data = $this->_client
      ->request('GET', static::ENDPOINT . "/{$id}")
      ->toArray();

    $this->_retrieved[$id] = $data;
    return $data;
  }
}
