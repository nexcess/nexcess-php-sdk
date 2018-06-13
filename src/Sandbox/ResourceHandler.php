<?php
/**
 * @package Nexcess-SDK
 * @subpackage Sandbox
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Sandbox;

use RecursiveDirectoryIterator,
  RecursiveIteratorIterator,
  Throwable;

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
  Psr7\Request as GuzzleRequest,
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
 * Handles responses for the sandbox using file resources.
 *
 * Resource files should
 *  - be named like "METHOD urlencodedpath"
 *  - contain the response body text (files are not parsed).
 */
class ResourceHandler {

  /** @var string[] List of base paths to look for resources on. */
  protected $_paths = [];

  /**
   * @param string ...$paths Base paths to add
   */
  public function __construct(string ...$paths) {
    foreach ($paths as $path) {
      $this->addPath($path);
    }
  }

  /**
   * Adds a path to look up resources on.
   *
   * @param string $path The path to add (no trailing /)
   * @return ResourceHandler $this
   */
  public function addPath(string $path) : ResourceHandler {
    $this->_paths[] = $path;

    return $this;
  }

  /**
   * Request handler ({@see Sandbox::$_handler}).
   *
   * @param GuzzleRequest $request PSR-7 Request object
   * @param array $options Guzzle request options
   * @return GuzzleResponse|Throwable|null
   */
  public function handle(GuzzleRequest $request, array $options = []) {
    $uri = $request->getUri();
    $method = $request->getMethod();
    $path = $uri->getPath();
    $key_path = "{$method} " . urlencode($path);
    $key_full = "{$method} " . urlencode("{$path}?{$uri->getQuery()}");

    $partials = [];
    foreach ($this->_paths as $path) {
      $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path)
      );
      foreach ($iterator as $file => $info) {
        $basename = basename($file);
        // prefer full match
        if (strpos($basename, $key_full) === 0) {
          break 2;
        }
        // save partial matches for fallback
        if (strpos($basename, $key_path) === 0) {
          $partials[] = $file;
        }
        // clear
        $file = null;
      }
    }

    if (isset($file)) {
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        file_get_contents($file)
      );
    }

    if (! empty($partials)) {
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        file_get_contents(reset($partials))
      );
    }

    return null;
  }
}
