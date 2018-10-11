<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

use Nexcess\Sdk\Exception;

/**
 * Error cases involving resources (models).
 */
class ResourceException extends Exception {

  /** @var int Attempt to access a non-existant model property. */
  const NO_SUCH_PROPERTY = 1;

  /** @var int Attempt to assign to a non-existant or readonly property. */
  const NO_SUCH_WRITABLE_PROPERTY = 2;

  /** @var int Syncing model properties failed. */
  const SYNC_FAILED = 3;

  /** @var int Attempt to access a model that doesn't exist. */
  const MODEL_NOT_FOUND = 4;

  /** @var int Attempt to create/update/delete a readonly API endpoint. */
  const READONLY_MODEL = 5;

  /** @var int Unknown model name. */
  const NO_SUCH_MODEL = 6;

  /** @var int Wrong model class provided to a Collection. */
  const WRONG_MODEL_FOR_COLLECTION = 7;

  /** @var int A wait() callback took too long to complete. */
  const WAIT_TIMEOUT_EXCEEDED = 8;

  /** @var int Invalid filter callback. */
  const INVALID_FILTER = 9;

  /** @var int Attempt to use an api method when no endpoint is available. */
  const NO_ENDPOINT_AVAILABLE = 10;

  /** @var int Attempt to build a collection from an invalid value. */
  const UNCOLLECTABLE = 11;

  /** @var int Attempt to build a datetime from an invalid value. */
  const UNDATETIMEABLE = 12;

  /** @var int Attempt to build a model from an invalid value. */
  const UNMODELABLE = 13;

  /** {@inheritDoc} */
  const INFO = [
    self::NO_SUCH_PROPERTY => ['message' => 'resource.no_such_property'],
    self::NO_SUCH_WRITABLE_PROPERTY =>
      ['message' => 'resource.no_such_writable_property'],
    self::SYNC_FAILED => ['message' => 'resource.sync_failed'],
    self::MODEL_NOT_FOUND => ['message' => 'resource.model_not_found'],
    self::READONLY_MODEL => ['message' => 'resource.readonly_model'],
    self::NO_SUCH_MODEL => ['message' => 'resource.no_such_model'],
    self::WRONG_MODEL_FOR_COLLECTION =>
      ['message' => 'resource.wrong_model_for_collection'],
    self::WAIT_TIMEOUT_EXCEEDED =>
      ['message' => 'resource.wait_timeout_exceeded'],
    self::INVALID_FILTER => ['message' => 'resource.invalid_filter'],
    self::NO_ENDPOINT_AVAILABLE =>
      ['message' => 'resource.no_endpoint_available'],
    self::UNCOLLECTABLE => ['message' => 'resource.uncollectable'],
    self::UNDATETIMEABLE => ['message' => 'resource.undatetimeable'],
    self::UNMODELABLE => ['message' => 'resource.unmodelable']
  ];
}
