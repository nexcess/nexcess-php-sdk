<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\CloudServer;

use Nexcess\Sdk\ {
  Resource\CloudServer\Resource,
  Tests\Resource\ModelTestCase
};

/**
 * Unit test for cloud accounts (virtual hosting).
 */
class ResourceTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'cloud-server-1.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'cloud-server-1.toArray-shallow.php';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY =
    'cloud-server-1.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Resource::class;
}
