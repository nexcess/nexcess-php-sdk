<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use Nexcess\Sdk\ {
  Model\Model
};

abstract class ServiceModel extends Model {

  /** {@inheritDoc} */
  const PROPERTY_ALIASES = ['id' => 'service_id'];
}
