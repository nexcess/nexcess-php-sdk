<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Package;

use Nexcess\Sdk\Resource\ResourceException;

/**
 * Error conditions involving Package resources.
 */
class PackageException extends ResourceException {

  /** @var int Must provide a type when listing packages. */
  const PACKAGE_TYPE_REQUIRED = 1;

  /** {@inheritDoc} */
  const INFO = [
    self::PACKAGE_TYPE_REQUIRED =>
      ['message' => 'resource.package.package_type_required']
  ];
}
