<?php
/**
 * @package Nexcess-SDK
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

/**
 * Config container for thermo.io SDK.
 */
class ThermoConfig extends Config {

  /** @var string Base URL for Thermo API. */
  const DEFAULT_BASE_URI = 'https://core.thermo.io/';
}
