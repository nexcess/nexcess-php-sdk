<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use Throwable;

use GuzzleHttp\ {
  Client as Guzzle,
  Exception\ClientException,
  Exception\ConnectException,
  Exception\RequestException,
  Exception\ServerException,
  HandlerStack as GuzzleHandlerStack,
  Middleware as GuzzleMiddleware
};

use function GuzzleHttp\default_user_agent as guzzle_user_agent;

use Nexcess\Sdk\ {
  ApiException,
  Resource\Creatable,
  Resource\Modelable as Model,
  Resource\Readable as Endpoint,
  Resource\Response,
  Resource\Updatable,
  SdkException,
  Util\Config,
  Util\Language,
  Util\Util
};

/**
 * API client for nexcess.net / thermo.io
 *
 * API Endpoints. These are created on first access.
 *
 * @property Endpoint $ApiToken
 * @property Endpoint $CloudAccount
 * @property Endpoint $CloudServer
 *
 * Proxies for List|Create|Retrieve|Update actions on an API Endpoint.
 * Argument may be one of:
 *  - array: Create a new Model from given key:value map.
 *  - int: Id for a Model to retrieve from the API.
 *  - Model: A Model with modified properties to submit to the API.
 *  - void: Omit the argument to get a list of Models from the API.
 *
 * @method Model|Collection ApiToken(array|int|Model|null $arg)
 * @method Model|Collection CouldAccount(array|int|Model|null $arg)
 * @method Model|Collection CloudServer(array|int|Model|null $arg)
 */
class Client {

  /** @var string Api version. */
  public const API_VERSION = '0';

  /** @var string SDK root directory. */
  public const DIR = __DIR__;

  /** @var string Base namespace for resource modules. */
  public const RESOURCE_NAMESPACE = __NAMESPACE__ . '\\Resource';

  /** @var string Sdk version. */
  public const SDK_VERSION = '0.1-alpha';

  /** @var Config Client configuration object. */
  protected $_config;

  /** @var Guzzle Http client. */
  protected $_client;

  /** @var Endpoint[] Cache of Endpoint instances. */
  protected $_endpoints = [];

  /** @var Language Language object. */
  protected $_language;

  /** @var array API request log. */
  protected $_request_log = [];

  /**
   * @param Config $config Client configuration object
   */
  public function __construct(Config $config) {
    $this->_config = $config;
    $this->_client = $this->_newGuzzleClient();
    $this->_setLanguageHandler();
  }

  /**
   * {@inheritDoc}
   * @see https://php.net/__call
   * Allows create/read/update actions to be accessed as a method call.
   *
   * @example <?php
   *  // get a list of existing items
   *  $tokens = $Client->ApiToken();
   *
   *  // create a new item
   *  $token = $Client->ApiToken(['name' => 'foo']);
   *
   *  // read an existing item
   *  $token = $Client->ApiToken(1);
   *
   *  // update an existing item
   *  $token['name'] = 'bar';
   *  $Client->ApiToken($token);
   *
   * @param string $name Endpoint classname (short name or fully qualified)
   * @param array|Model|mixed $args Arguments for create/read/update action
   * @return Model On success
   * @throws SdkException If Endpoint is unknown
   * @throws ApiException If API request fails
   * @throws ModelException If Model cannot be created/updated
   */
  public function __call($name, $args) {
    $endpoint = $this->getEndpoint($name);
    $arg = array_shift($args);

    if ($arg === null) {
      return $endpoint->list();
    }

    if (is_int($arg)) {
      return $endpoint->retrieve($arg);
    }

    if (is_array($arg)) {
      if (! $endpoint instanceof Creatable) {
        throw new ApiException(
          ApiException::ENDPOINT_NOT_WRITABLE,
          ['endpoint' => $name]
        );
      }

      return $endpoint->create($arg);
    }

    if ($arg instanceof Model) {
      if (! $endpoint instanceof Updatable) {
        throw new ApiException(
          ApiException::ENDPOINT_NOT_WRITABLE,
          ['endpoint' => $name]
        );
      }

      return $endpoint->update($arg);
    }

    throw new SdkException(
      SdkException::WRONG_CALL_ARG,
      [
        'class' => __CLASS__,
        'expected' => 'null|int|array|Model',
        'type' => Util::type($arg)
      ]
    );
  }

  /**
   * @see https://php.net/__get
   * Allows endpoints to be accessed as properties.
   *
   * @example <?php
   *  // get the "api-token" endpoint
   *  $endpoint = $Client->ApiToken;
   *
   * @param string $name Endpoint classname (short name or fully qualified)
   * @return Endpoint on success
   * @throws SdkException If Endpoint is unknown
   */
  public function __get(string $name) : Endpoint {
    return $this->getEndpoint($name);
  }

  /**
   * Gets the client config object.
   *
   * @return Config
   */
  public function getConfig() : Config {
    return $this->_config;
  }

  /**
   * Gets an API endpoint.
   *
   * For convenience, endpoints can also be accessed as Client properties.
   * @see Client::__get
   *
   * @param string $name Endpoint FQCN or module name
   * @return Endpoint
   * @throws SdkException If the endpoint is unknown
   */
  public function getEndpoint(string $name) : Endpoint {
    if (is_a($name, Endpoint::class, true)) {
      $endpoint = $name;
    } else {
      $endpoint = static::RESOURCE_NAMESPACE . "\\{$name}\\Endpoint";

      if (! is_a($endpoint, Endpoint::class, true)) {
        throw new SdkException(
          SdkException::NO_SUCH_ENDPOINT,
          ['name' => $name]
        );
      }
    }

    $module = $endpoint::moduleName();
    if (empty($this->_endpoints[$module])) {
      $this->_endpoints[$module] =
        new Endpoint($this, $this->_config);
    }

    return $this->_endpoints[$module];
  }

  /**
   * Gets an API resource.
   *
   * This method should *always* be used to build models
   * (as opposed to using `new`),
   * as it will associate them with their correct endpoint(s) automatically.
   *
   * @param string $name Resource FQCN or module name
   * @return Model
   * @throws SdkException If the model is unknown
   */
  public function getModel(string $name) : Model {
    if (is_a($name, Model::class, true)) {
      $model = $name;
    } else {
      $model = static::RESOURCE_NAMESPACE . "\\{$name}\\Resource";

      if (! is_a($model, Model::class, true)) {
        throw new SdkException(SdkException::NO_SUCH_MODEL, ['name' => $name]);
      }
    }

    return new $model($this->getEndpoint($model::moduleName()));
  }

  /**
   * Gets a log of API requests performed by this client.
   *
   * @return array[] Info about API request, categorized by endpoint
   * @throws If request logging is disabled
   */
  public function getRequestLog() : array {
    $config = $this->_config;
    if (! $config->get('request.log')) {
      throw new SdkException(SdkException::REQUEST_LOG_NOT_ENABLED);
    }

    return $this->_request_log;
  }

  /**
   * Perform an API request.
   *
   * This is intended for use by Endpoints,
   * and should generally not be used otherwise.
   *
   * Requests and responses are logged
   * if the "debug" or "request.log" config options are true.
   *
   * @param string $method The http method to use
   * @param string $endpoint The API endpoint to request
   * @param array $params Http client parameters
   * @return array Response data
   * @throws ApiException On http error (4xx, 5xx, network issues, etc.)
   * @throws SdkException On any other error
   */
  public function request(
    string $method,
    string $endpoint,
    array $params = []
  ) : array {
    try {
      $params['headers'] =
        ($params['headers'] ?? []) + $this->_getDefaultHeaders();

      $guzzle_response = $this->_client->request($method, $endpoint, $params);
      $content_type = $guzzle_response->getHeader('Content-type');
      $body = (string) $guzzle_response->getBody();

      return (reset($content_type) === 'application/json') ?
        Util::jsonDecode($body) :
        ['response' => $body];
    } catch (ConnectException $e) {
      throw new ApiException(ApiException::CANNOT_CONNECT, $e);
    } catch (ClientException $e) {
      switch ($e->getResponse()->getStatusCode()) {
        case 401:
          $code = ApiException::UNAUTHORIZED;
          break;
        case 403:
          $code = ApiException::FORBIDDEN;
          break;
        case 404:
          $code = ApiException::NOT_FOUND;
          break;
        case 422:
          $code = ApiException::UNPROCESSABLE_ENTITY;
          break;
        default:
          $code = ApiException::BAD_REQUEST;
          break;
      }
      throw new ApiException(
        $code,
        $e,
        ['method' => $method, 'endpoint' => $endpoint]
      );
    } catch (ServerException $e) {
      throw new ApiException(ApiException::SERVER_ERROR, $e);
    } catch (RequestException $e) {
      throw new ApiException(ApiException::REQUEST_FAILED, $e);
    } catch (Throwable $e) {
      throw new SdkException(SdkException::UNKNOWN_ERROR, $e);
    }
  }

  /**
   * Perform self-update.
   *
   * @return array Information about the update.
   * @throws SdkException If update fails
   */
  public function selfUpdate() : array {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['method' => __METHOD__]
    );
  }

  /**
   * Does the SDK need to be updated?
   *
   * @return bool True if a newer SDK version is available; false otherwise
   */
  public function shouldUpdate() : bool {
    throw new SdkException(
      SdkException::NOT_IMPLEMENTED,
      ['method' => __METHOD__]
    );
  }

  /**
   * Gets default headers for API requests.
   *
   * @return array Map of http headers
   */
  protected function _getDefaultHeaders() : array {
    $headers = [
      'Accept' => 'application/json',
      'Accept-language' => $this->_config->get('language'),
      'Api-version' => static::API_VERSION,
      'User-agent' => 'Nexcess-PHP-SDK/' . static::SDK_VERSION .
        ' (' . guzzle_user_agent() . ')'
    ];
    $api_token = $this->_config->get('api_token');
    if ($api_token) {
      $headers['Authorization'] = "Bearer {$api_token}";
    }

    return $headers;
  }

  /**
   * Creates a new Guzzle client based on current config.
   *
   * @return Guzzle
   */
  protected function _newGuzzleClient() : Guzzle {
    $config = $this->_config;
    $defaults = $config->get('guzzle_defaults') ?? [];

    $handler = $defaults['handler'] ?? GuzzleHandlerStack::create();
    if ($config->get('debug') || $config->get('request.log')) {
      $handler->push(GuzzleMiddleware::history($this->_request_log));
    }

    $options = [
      'base_uri' => $config->get('base_uri'),
      'handler' => $handler
    ];

    return new Guzzle(Util::extendRecursive($options, $defaults));
  }

  /**
   * Sets up preferred language options, if configured.
   */
  protected function _setLanguageHandler() {
    $this->_language = Language::getInstance();
    $language = $this->_config->get('language');
    if (! empty($language['language'])) {
      $this->_language->setLanguage($language['language']);
    }
    if (! empty($language['paths'])) {
      $this->_language->addPaths(...$language['paths']);
    }
  }
}
