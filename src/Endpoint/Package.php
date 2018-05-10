<?php
/**
 * @package Nexcess-SDK
 * @subpackage Cloud-Account
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\Model\Package as Model;

/**
 * API actions for service Packages.
 */
class Package extends Read {

  /** {@inheritDoc} */
  const ENDPOINT = 'package';

  /** {@inheritDoc} */
  const MODEL = Model::class;
}
