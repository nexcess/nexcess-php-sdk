<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Cloud\Tests;

use Nexcess\Sdk\ {
  Resource\Cloud\Cloud,
  Resource\Tests\ModelTestCase
};

/**
 * Unit test for clouds (virt-cloud hosts).
 */
class CloudTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'cloud-3.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'cloud-3.toArray-shallow.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY = 'cloud-3.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Cloud::class;
}
