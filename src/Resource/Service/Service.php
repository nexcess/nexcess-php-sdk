<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Service;

use Nexcess\Sdk\Resource\Model;

abstract class Service extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Service';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'service_id'];
}
