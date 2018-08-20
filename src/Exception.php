<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk;

use at\exceptable\Exception as Exceptable;

use Nexcess\Sdk\Util\Language;

abstract class Exception extends Exceptable {

  /**
   * {@inheritDoc}
   * Overridden to provide language translation support.
   */
  protected function _makeMessage(string $message = null, int $code) : string {
    $key = $message ?? static::get_info($code)['message'];
    return $this->_makeTrMessage(Language::get($key), $code) ?? $key;
  }
}
