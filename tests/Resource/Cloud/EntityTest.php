<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Cloud;

use Nexcess\Sdk\ {
  Resource\Cloud\Entity,
  Tests\Resource\ModelTestCase
};

/**
 * Unit test for clouds (virt-cloud hosts).
 */
class EntityTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'cloud-3.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'cloud-3.toArray-shallow.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY = 'cloud-3.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Entity::class;
}
