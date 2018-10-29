<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

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
  ApiException,
  Client,
  SdkException,
  Resource\Collection,
  Resource\Collector,
  Resource\Modelable as Model,
  Resource\Readable,
  Util\Config,
  Util\Language,
  Util\Util
};

/**
 * Represents a readable API endpoint for nexcess.net / thermo.io.
 */
abstract class Endpoint implements Readable {

  /** @var string Name of resource module this endpoint belongs to. */
  public const MODULE_NAME = '';

  /** @var int Key for parameter type. */
  public const PARAM_TYPE = 0;

  /** @var int Key for parameter required. */
  public const PARAM_REQUIRED = 1;

  /** @var int Key for parameter description. */
  public const PARAM_DESCRIPTION = 2;

  /** @var array Default filter values for list(). */
  protected const _BASE_LIST_FILTER = [];

  /** @var string Fully qualified classname of associated Model. */
  protected const _MODEL_FQCN = '';

  /** @var string API endpoint. */
  protected const _URI = '';

  /** @var array[] Map of action name:parameter info pairs. */
  protected const _PARAMS = [];

  /** @var int Key for wait() $opts interval. */
  public const OPT_WAIT_INTERVAL = 0;

  /** @var int Key for wait() $opts timeout. */
  public const OPT_WAIT_TIMEOUT = 1;

  /** @var int Default wait interval. */
  protected const _DEFAULT_WAIT_INTERVAL = 1;

  /** @var int Default timeout before waiting fails. */
  protected const _DEFAULT_WAIT_TIMEOUT = 30;

  /** @var Guzzle The Sdk Client. */
  protected $_client;

  /** @var Config Client configuration object. */
  protected $_config;

  /** @var array Map of last fetched property:value pairs. */
  protected $_retrieved = [];

  /** @var callable Queued callback for wait(). */
  protected $_wait_until;

  /**
   * {@inheritDoc}
   */
  public static function moduleName() : string {
    return static::MODULE_NAME;
  }

  /**
   * @param Client $client Api Client instance
   * @param Config $config Api Config object
   */
  public function __construct(Client $client, Config $config) {
    $this->_client = $client;
    $this->_config = $config;
  }

  /**
   * {@inheritDoc}
   */
  public function getModel(string $name = null) : Model {
    return $this->_client->getModel($name ?? static::_MODEL_FQCN);
  }

  /**
   * {@inheritDoc}
   */
  public function getParams(string $action) : array {
    $params = static::_PARAMS[$action] ?? [];
    foreach ($params as $param => $info) {
      if (! isset($info[self::PARAM_TYPE])) {
        $params[$param][self::PARAM_TYPE] = Util::TYPE_STRING;
      }
      if (! isset($info[self::PARAM_REQUIRED])) {
        $params[$param][self::PARAM_REQUIRED] = true;
      }
      $params[$param][self::PARAM_DESCRIPTION] =
        "{$param} ({$info[self::PARAM_TYPE]}): " .
        Language::get(
          $params[$param][self::PARAM_REQUIRED] ? 'required' : 'optional'
        ) . '. ' .
        Language::get(
          'resource.' . static::MODULE_NAME . ".{$action}.{$param}"
        );
    }

    return $params;
  }

  /**
   * {@inheritDoc}
   */
  public function list(array $filter = []) : Collector {
    $response = $this->_client->request(
      'GET',
      static::_URI . "?{$this->_buildListQuery($filter)}"
    );
    $collection = new Collection(static::_MODEL_FQCN);
    foreach ($response as $data) {
      if (! is_array($data)) {
        throw new ApiException(
          ApiException::GOT_MALFORMED_LIST,
          ['uri' => static::_URI . "?{$this->_buildListQuery($filter)}"]
        );
      }

      $collection->add($this->getModel()->sync($data));
    }

    // this might end up being redundant,
    // but is needed since not all endpoints support filters on all properties.
    return empty($filter) ? $collection : $collection->filter($filter);
  }

  /**
   * {@inheritDoc}
   */
  public function retrieve(int $id) : Model {
    return $this->sync($this->getModel()->set('id', $id), true);
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
   * {@inheritDoc}
   */
  public function wait(callable $until = null, array $opts = []) : Readable {
    $config = $this->_config;

    $until = $until ??
      $this->_wait_until ??
      function () {
        return true;
      };
    $this->_wait_until = null;

    $tick = $config->get('wait.tick_function');

    $wait = $opts[self::OPT_WAIT_INTERVAL] ??
      $config->get('wait.interval') ??
      self::_DEFAULT_WAIT_INTERVAL;

    $timeout = $opts[self::OPT_WAIT_TIMEOUT] ??
      $config->get('wait.timeout') ??
      self::_DEFAULT_WAIT_TIMEOUT;
    $deadline = time() + $timeout;

    try {
      while ($until($this) !== true) {
        if (time() > $deadline) {
          throw new SdkException(
            SdkException::WAIT_TIMEOUT_EXCEEDED,
            ['timeout' => $timeout]
          );
        }

        if ($tick) {
          $tick($this);
        }
        sleep($wait);
      }

      return $this;
    } catch (ApiException $e) {
      throw $e;
    } catch (Throwable $e) {
      throw new SdkException(SdkException::CALLBACK_ERROR, $e);
    }
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

    return http_build_query($filter + static::_BASE_LIST_FILTER);
  }

  /**
   * Checks that a provided model is of the correct type for this endpoint.
   *
   * @param Model $model The model to check
   * @throws ApiException If the model is of the wrong class
   */
  protected function _checkModelType(Model $model) {
    $fqcn = static::_MODEL_FQCN;
    if (! $model instanceof $fqcn) {
      throw new ApiException(
        ApiException::WRONG_MODEL_FOR_URI,
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
    $this->_retrieved[$id] =
      $this->_client->request('GET', static::_URI . "/{$id}");

    return $this->_retrieved[$id];
  }

  /**
   * Inspects params passed for given API action and throws if invalid.
   *
   * NOTE, authoritative validation is performed by the API;
   * to prevent conflicts/confusion, validation here should remain minimal:
   * mainly limited to checking data names and types.
   *
   * @param array $data The provided data
   * @throws ResourceException If data is missing/incorrect
   */
  protected function _validateParams(string $action, array $params) : void {
    foreach ($this->getParams($action) as $param => $info) {
      if (! isset($params[$param])) {
        if ($info[self::PARAM_REQUIRED]) {
          throw new ResourceException(
            ResourceException::MISSING_PARAM,
            [
              'param' => $param,
              'module' => static::MODULE_NAME,
              'action' => $action,
              'description' => $info[self::PARAM_DESCRIPTION]
            ]
          );
        }

        continue;
      }

      if (Util::type($params[$param]) !== $info[self::PARAM_TYPE]) {
        throw new ResourceException(
          ResourceException::WRONG_PARAM,
          [
            'param' => $param,
            'module' => static::MODULE_NAME,
            'action' => $action,
            'type' => $info[self::PARAM_TYPE],
            'actual' => Util::type($params[$param]),
            'description' => $info[self::PARAM_DESCRIPTION]
          ]
        );
      }
    }
  }

  /**
   * Queues or invokes a wait callback based on config options.
   *
   * @param callable|null $until
   */
  protected function _wait(callable $until = null) {
    $this->_wait_until = $until;
    if ($until !== null && $this->_config->get('wait.always')) {
      $this->wait($until);
      return;
    }
  }
}
