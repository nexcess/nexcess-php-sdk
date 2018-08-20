<?php
/**
 * @package Nexcess-SDK
 * @subpackage Sandbox
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Sandbox;

use Throwable;

use function GuzzleHttp\Promise\ {
  rejection_for as rejection,
  promise_for as resolution
};

use GuzzleHttp\ {
  Client as Guzzle,
  Exception\ClientException,
  Exception\ConnectException,
  Exception\RequestException,
  Exception\ServerException,
  HandlerStack as GuzzleHandlerStack,
  Promise\PromiseInterface as Promise,
  Psr7\Response as GuzzleResponse
};

use Nexcess\Sdk\ {
  Client,
  Exception\ApiException,
  Exception\SdkException,
  Sandbox\SandboxException,
  Util\Config,
  Util\Util
};

use Psr\Http\Message\RequestInterface as Request;

/**
 * Self-contained development environment for the Nexcess SDK.
 *
 * API requests made here are handled internally.
 * No HTTP requests will be made to the "live" API.
 *
 * Responses can be preconstructed and queued, routed from a datasource, etc..
 */
class Sandbox {

  /** @var string Fake API token (valid). */
  public const SANDBOX_TOKEN_VALID = 'sandbox-token-valid';

  /** @var string Fake API token (invalid). */
  public const SANDBOX_TOKEN_INVALID = 'sandbox-token-invalid';

  /** @var Config Base configuration. */
  protected $_config;

  /** @var callable Exception handler. */
  protected $_exception_handler;

  /** @var callable Delegated request handler. */
  protected $_handler;

  /** @var array[] Queued responses, sorted by method+endpoint. */
  protected $_response_queue = ['*' => []];

  /**
   * @param Config $config SDK configuration object
   * @param callable $request_handler Callback to handle requests
   * @param callable $exception_handler Callback to handle exceptions
   */
  public function __construct(
    Config $config,
    callable $request_handler = null,
    callable $exception_handler = null
  ) {
    $this->_config = clone $config;
    $this->_config->set('debug', true);
    $this->_config->set('sandboxed', true);
    $this->_config->set('api-token', self::SANDBOX_TOKEN_VALID);

    if ($request_handler) {
      $this->setRequestHandler($request_handler);
    }
    if ($exception_handler) {
      $this->setExceptionHandler($exception_handler);
    }
  }

  /**
   * Request handler.
   *
   * This returns queued responses, if any exist,
   * and invokes the delegated handler otherwise.
   * If no handler exists, a 503 response is returned.
   *
   * @todo Implement guzzle's $option handling and lifecycle events
   *
   * @param Request $request PSR-7 Request object
   * @param array $options Guzzle request options
   * @return Promise Response as a guzzle promise
   */
  public function handle(Request $request, array $options = []) : Promise {
    $request_key = "{$request->getMethod()} {$request->getUri()->getPath()}";

    $response = $this->_getResponseFor($request_key) ??
      $this->_handler ??
      new ServerException(
        '503 Service Unavailable',
        $request,
        new GuzzleResponse(503, [], 'Service Unavailable')
      );

    if (is_callable($response)) {
      $response = $response($request, $options);
    }

    $response = ($response instanceof Throwable) ?
      rejection($response) :
      resolution($response);

    return $response;
  }

  /**
   * Builds a new SDK client using the sandboxed config.
   *
   * @return Client Sandboxed SDK client
   */
  public function newClient() : Client {
    $config = clone $this->_config;

    $guzzle_defaults = [
      'handler' => GuzzleHandlerStack::create([$this, 'handle'])
    ];

    $config->set('guzzle_defaults', $guzzle_defaults, true);
    return new Client($config);
  }

  /**
   * Runs the given play with a new sandboxed API client.
   *
   * The callback signature is like
   *  mixed $play(Client $api, Sandbox $sandbox)
   *
   * @param callable $play The action to sandbox and run
   * @return mixed Return value of callback or exception handler
   */
  public function play(callable $play) {
    try {
      return $play($this->newClient(), $this);
    } catch (Throwable $e) {
      if ($this->_exception_handler) {
        return ($this->_exception_handler)($e);
      }

      throw $e;
    }
  }

  /**
   * Appends a response to the queue,
   * optionally restricted to a matching request.
   *
   * @param string $response_key "METHOD /path" or "*" for this response
   * @param GuzzleResponse|callable|Throwable Response to queue
   * @return Sandbox $this
   */
  public function queueResponse(string $response_key, $response) : Sandbox {
    if (
      ! $response instanceof GuzzleResponse &&
      ! is_callable($response) &&
      ! $response instanceof Throwable
    ) {
      throw new SdkException(
        SdkException::INVALID_RESPONSE,
        ['type' => Util::type($response)]
      );
    }

    $this->_response_queue[$response_key][] = $response;
    return $this;
  }

  /**
   * Creates a response and appends it to the response queue.
   *
   * @param string $response_key "METHOD /path" identifier for this response
   * @param int $code Http response status
   * @param array $data Response data (will be json-encoded)
   * @param array $headers Response headers (json content-type will be added)
   * @return Sandbox $this
   */
  public function makeResponse(
    string $response_key,
    int $code = 200,
    array $data = [],
    array $headers = []
  ) : Sandbox {
    $headers = ['Content-type' => 'application/json'] + $headers;
    $response = new GuzzleResponse($code, $headers, json_encode($data));
    return $this->queueResponse($response_key, $response);
  }

  /**
   * Sets exception handler for the sandbox.
   *
   * @param callable $handler
   * @return Sandbox $this
   */
  public function setExceptionHandler(callable $handler) : Sandbox {
    $this->_exception_handler = $handler;
    return $this;
  }

  /**
   * Sets request handler for the sandbox.
   *
   * @param callable $handler
   * @return Sandbox $this
   */
  public function setRequestHandler(callable $handler) : Sandbox {
    $this->_handler = $handler;
    return $this;
  }

  /**
   * Echos a summary of the given exception to stdout.
   *
   * @param Throwable $e The exception to dump
   */
  protected function _dumpException(Throwable $e) {
    echo "{$e->getMessage()}\n",
      "  {$e->getFile()}:{$e->getLine()}\n",
      "{$e->getTraceAsString()}\n\n";
  }

  /**
   * Finds a response for the given request.
   *
   * @param string $request_key
   * @return GuzzleResponse|callable|Throwable|null
   *  Matching response if any; null otherwise
   */
  protected function _getResponseFor(string $request_key) {
    if (isset($this->_response_queue[$request_key])) {
      return array_shift($this->_response_queue[$request_key]);
    }

    if (isset($this->_response_queue['*'])) {
      return array_shift($this->_response_queue['*']);
    }

    return null;
  }
}
