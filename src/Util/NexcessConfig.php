<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Util;

/**
 * Config container for SDK.
 */
class NexcessConfig extends Config {

  /** {@inheritDoc} */
  protected const _DEFAULT_OPTIONS =
    ['base_uri' => 'https://portal.nexcess.net/'];
}
