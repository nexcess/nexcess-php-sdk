<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Package;

use Nexcess\Sdk\ {
  Resource\Endpoint as ReadableEndpoint,
  Resource\Package\Resource,
  Resource\Package\PackageException
};

/**
 * API actions for service Packages.
 */
class Endpoint extends ReadableEndpoint {

  /** {@inheritDoc} */
  protected const _URI = 'package';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;

  /**
   * {@inheritDoc}
   * Overridden to verify package type on list queries.
   *
   * @throws PackageException If package type is omitted from filter
   */
  protected function _buildListQuery(array $filter) : string {
    if (empty($filter['type'])) {
      throw new PackageException(PackageException::PACKAGE_TYPE_REQUIRED);
    }

    return parent::_buildListQuery(
      $filter + ['environment_type' => 'production']
    );
  }
}
