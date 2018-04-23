<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

/**
 * Config container for thermo.io SDK.
 */
class ThermoConfig extends Config {

  /** {@inheritDoc} */
  const DEFAULT_OPTIONS = ['base_uri' => 'https://core.thermo.io/'];
}
