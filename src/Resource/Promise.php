<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use GuzzleHttp\Promise\Promise as GuzzlePromise;

use Nexcess\Sdk\ {
  ApiException,
  Resource\Modelable,
  Resource\ResourceException,
  SdkException
};

/**
 * A Promise which polls a callback and resolves when it returns true.
 *
 * The polling callback determines whether the promise is ready to be resolved
 * (that is, whether polling should stop or continue).
 * Its signature is like
 *  bool $done(Modelable $resource) Returns true if polling should stop
 *
 * Each polling interval is described as a "tick."
 * A tick function may be provided to perform arbitrary tasks.
 * It will be invoked once per tick, between polling calls.
 * Its signature is like
 *  void $tick_fn(int $iteration_count) Does something
 */
class Promise extends GuzzlePromise {

  /** @var int Option key for polling interval. */
  public const OPT_INTERVAL = 0;

  /** @var int Option key for tick function. */
  public const OPT_TICK_FN = 1;

  /** @var int Option key for polling timeout. */
  public const OPT_TIMEOUT = 2;

  /** @var int Default polling interval. */
  protected const _DEFAULT_INTERVAL = 1;

  /** @var int Default timeout before polling fails. */
  protected const _DEFAULT_TIMEOUT = 30;

  /** @var int Time to abort. */
  protected $_deadline;

  /** @var callable The polling function. */
  protected $_done;

  /** @var int The polling interval. */
  protected $_interval;

  /** @var Modelable The resource to resolve to. */
  protected $_resource;

  /** @var callable The tick function. */
  protected $_tick_fn;

  /** @var int Maximum wait time. */
  protected $_timeout;

  /**
   * @param Modelable $resource The resource to resolve to
   * @param callable $done The callback to poll
   * @param array $options Polling settings:
   *  - int self::OPT_INTERVAL Time (in seconds) to wait between ticks
   *  - callable self::TICK_FN Callback to invoke on each tick
   *  - int self::OPT_TIMEOUT Total time (in seconds) to poll before aborting
   */
  public function __construct(
    Modelable $resource,
    callable $done,
    array $options = []
  ) {
    $this->_resource = $resource;
    $this->_done = $done;

    $this->_interval = $options[self::OPT_INTERVAL] ?? self::_DEFAULT_INTERVAL;
    $this->_tick_fn = $options[self::OPT_TICK_FN] ?? null;
    $this->_timeout = $options[self::OPT_TIMEOUT] ?? self::_DEFAULT_TIMEOUT;

    parent::__construct([$this, '_poll'], null);
  }

  /**
   * Waits until we're ready, then resolves or rejects.
   */
  protected function _poll() {
    try {
      if ($this->_timeout > 0) {
        $this->_deadline = time() + $this->_timeout;
      }

      $i = 0;
      while (($this->_done)($this->_resource) !== true) {
        $this->_tick(++$i);
      }

      $this->resolve($this->_resource);
    } catch (Throwable $e) {
      if (! $e instanceof ApiException && ! $e instanceof SdkException) {
        $e = new SdkException(SdkException::CALLBACK_ERROR, $e);
      }

      $this->reject($e);
    }
  }

  /**
   * Waits for next poll, invoking the registered tick function if it exists.
   */
  protected function _tick(int $i) {
    if ($this->_tick_fn) {
      ($this->_tick_fn)($i);
    }

    if ($this->_deadline && (time() > $this->_deadline)) {
      throw new ResourceException(
        ResourceException::WAIT_TIMEOUT_EXCEEDED,
        ['timeout' => $this->_timeout]
      );
    }

    sleep($this->_interval);
  }
}
