<?php
/**
 * @package Nexcess-SDK
 * @subpackage User
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\User;

use Nexcess\Sdk\ {
  Resource\Endpoint as BaseEndpoint,
  Resource\User\Resource
};

/**
 * API actions for portal Users.
 */
class Endpoint extends BaseEndpoint {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'User';

  /** {@inheritDoc} */
  protected const _URI = 'user';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;
}
