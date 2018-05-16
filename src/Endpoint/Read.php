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
  Endpoint\Readable as Endpoint,
  Endpoint\Response,
  Exception\ApiException,
  Exception\SdkException,
  Model\Collection,
  Model\Collector,
  Model\Modelable as Model,
  Util\Config,
  Util\Util
};

/**
 * Represents a readable API endpoint for nexcess.net / thermo.io.
 */
abstract class Read implements Endpoint {

  /** @var array Default filter values for list(). */
  const BASE_LIST_FILTER = [];

  /** @var string API endpoint. */
  const ENDPOINT = '';

  /** @var string Fully qualified classname of the Model for this endpoint. */
  const MODEL = '';

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
   * {@inheritDoc}
   */
  public function getModel(int $id = null) : Model {
    $fqcn = static::MODEL;
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
    if (false && ! $this->_config->get('debug')) {
      throw new SdkException(
        SdkException::DEBUG_NOT_ENABLED,
        ['method' => __METHOD__]
      );
    }

    return $this->_retrieved;
  }

  /**
   * {@inheritDoc}
   */
  public function isInSync(Model $model, bool $hard = false) : bool {
    $data = $model->toArray();
    $id = $model->getId();
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
   * {@inheritDoc}
   */
  public function list(array $filter = []) : Collector {
    $response = $this->_client->request(
      'GET',
      static::ENDPOINT . "?{$this->_buildListQuery($filter)}"
    );

    $collection = new Collection(static::MODEL);
    foreach ($response->toArray() as $data) {
      $item = $this->getModel();
      $item->sync($data);
      $collection->add($item);
    }

    // this might end up being redundant,
    // but is needed since not all endpoints support filters on all properties.
    return $filter ? $collection->filter($filter) : $collection;
  }

  /**
   * {@inheritDoc}
   */
  public function retrieve(int $id) : Model {
    return $this->sync($this->getModel($id), true);
  }

  /**
   * {@inheritDoc}
   */
  public function sync(Model $model, bool $hard = false) : Model {
    $id = $model->getId();
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
    $fqcn = static::MODEL;
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
