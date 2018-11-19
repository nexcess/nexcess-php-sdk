<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Ssl\Entity,
  Tests\Resource\ModelTestCase
};

/**
 * Unit test for Ssl.
 */
class EntityTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = '';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = '';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY = '';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Entity::class;
}
