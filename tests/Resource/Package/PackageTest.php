<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Package;

use Nexcess\Sdk\ {
  Resource\Package\Package,
  Tests\Resource\ModelTestCase
};

/**
 * Unit test for cloud accounts (virtual hosting).
 */
class PackageTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'package-717.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'package-717.toArray-shallow.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY =
    'package-717.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Package::class;
}
