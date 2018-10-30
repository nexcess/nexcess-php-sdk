<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use GuzzleHttp\Promise\Promise;

use Nexcess\Sdk\ {
  ApiException,
  Resource\Modelable,
  Resource\ResourceException,
  SdkException,
  Util\Config
};

class PromisedResource extends Promise {

  /** @var int Default wait interval. */
  protected const _DEFAULT_WAIT_INTERVAL = 1;

  /** @var int Default timeout before waiting fails. */
  protected const _DEFAULT_WAIT_TIMEOUT = 30;

  /** @var int Time to abort. */
  protected $_deadline;

  /** @var callable The polling condition. */
  protected $_done;

  /** @var Modelable The resource to resolve to. */
  protected $_resource;

  /** @var int Maximum wait time. */
  protected $_timeout;

  /**
   * @param Config $config Config instance
   * @param Modelable $model The resource to resolve to
   */
  public function __construct(Config $config, Modelable $model) {
    $this->_config = $config;
    $this->_resource = $model;
    parent::__construct([$this, '_wait'], null);
  }

  /**
   * Sets the callback to determine when we're ready to resolve.
   *
   * The callback signature is like
   *  bool $done(Modelable $model) Returns true when done waiting for $model
   *
   * @param callable $done Waiting callback
   */
  public function waitUntil(callable $done) : PromisedResource {
    $this->_done = $done;
    return $this;
  }

  /**
   * Waits until we're ready, then resolves or rejects.
   */
  protected function _wait() {
    if ($this->_done === null) {
      $this->resolve($this->_resource);
      return;
    }

    try {
      $this->_waitPrep();

      while (($this->_done)($this->_resource) !== true) {
        $this->_tick();
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
   * Get ready to wait.
   */
  protected function _waitPrep() {
    $this->_tick = $this->_config->get('wait.tick_function');
    $this->_interval = $this->_config->get('wait.interval') ??
      self::_DEFAULT_WAIT_INTERVAL;
    $this->_timeout = $this->_config->get('wait.timeout') ??
      self::_DEFAULT_WAIT_TIMEOUT;
    $this->_deadline = time() + $this->_timeout;
  }

  /**
   * Waits for next poll, invoking the registered tick function if one exists.
   */
  protected function _tick() {
    if ($this->_tick) {
      ($this->_tick)();
    }

    if (time() > $this->_deadline) {
      throw new ResourceException(
        ResourceException::WAIT_TIMEOUT_EXCEEDED,
        ['timeout' => $this->_timeout]
      );
    }

    sleep($this->_interval);
  }
}
