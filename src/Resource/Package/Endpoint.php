<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Package;

use Nexcess\Sdk\ {
  Resource\Endpoint as BaseEndpoint,
  Resource\Collection,
  Resource\Package\Entity,
  Resource\Package\PackageException,
  Util\Util
};

/**
 * API actions for service Packages.
 */
class Endpoint extends BaseEndpoint {

  /** @var string Production cloud account packages. */
  public const ENV_TYPE_PRODUCTION = 'production';

  /** @var string Development cloud account packages. */
  public const ENV_TYPE_DEVELOPMENT = 'development';

  /** @var string App packages. */
  public const TYPE_APP = 'app';

  /** @var string Cloud account (managed hosting) packages. */
  public const TYPE_CLOUDACCOUNT = 'virt-guest-cloud';

  /** @var string Cloud server (VPS) packages. */
  public const TYPE_CLOUDSERVER = 'virt-guest';

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Package';

  /** {@inheritDoc} */
  protected const _URI = 'package';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _PARAMS = [
    'list' => [
      'type' => [Util::TYPE_STRING],
      'environment_type' => [Util::TYPE_STRING, false]
    ]
  ];

  /**
   * Lists production cloud account service packages.
   *
   * @param string $env_type One of the self::CLOUDACCOUNT_ENV_* constants
   * @return Collection List of packages
   */
  public function listCloudAccountPackages(
    string $env_type = self::ENV_TYPE_PRODUCTION
  ) : Collection {
    return $this->list([
      'type' => self::TYPE_CLOUDACCOUNT,
      'environment_type' => $env_type
    ]);
  }

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

    return parent::_buildListQuery($filter);
  }
}
