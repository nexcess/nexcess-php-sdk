<?php
/**
 * @package Nexcess-SDK
 * @subpackage Cloud-Account
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\Model\App as Model;

/**
 * API actions for Clouds (virtual hosting clusters).
 */
class App extends Read {

  /** {@inheritDoc} */
  const ENDPOINT = 'app';

  /** {@inheritDoc} */
  const MODEL = Model::class;
}
