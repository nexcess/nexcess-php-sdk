<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\VirtGuestCloud;

use Nexcess\Sdk\ {
  Resource\VirtGuestCloud\Endpoint,
  Resource\VirtGuestCloud\Entity,
  Tests\Resource\ModelTestCase
};

/**
 * Unit test for cloud account services.
 */
class EntityTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'service-1.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'service-1.toArray-shallow.php';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY =
    'service-1.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Entity::class;

  /**
   * @covers Entity::getAvailablePhpVersions
   */
  public function testGetAvailablePhpVersions() {
    $versions = ['5.6', '7.0', '7.1', '7.2'];

    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('getAvailablePhpVersions')
      ->willReturn($versions);

    $entity = $this->_getSubject();
    $entity->setApiEndpoint($endpoint);

    $this->assertEquals(
      $versions,
      $entity->getAvailablePhpVersions(),
      'invokes and returns $endpoint->getAvailablePhpVersions()'
    );
  }
}
