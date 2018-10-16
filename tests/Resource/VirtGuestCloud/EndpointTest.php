<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\VirtGuestCloud;

use GuzzleHttp\ {
  Psr7\Response as GuzzleResponse
};
use Nexcess\Sdk\ {
  Resource\VirtGuestCloud\Endpoint,
  Resource\VirtGuestCloud\Entity,
  Tests\Resource\EndpointTestCase,
};

class EndpointTest extends EndpointTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODULE = 'VirtGuestCloud';

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [];
  }

  /**
   * @covers Endpoint::getAvailablePhpVersions
   */
  public function testGetAvailablePhpVersions() {
    $handler = function ($request, $options) {
      $this->assertEquals(
        'service/1/get-php-versions',
        $request->getUri()->getPath()
      );

      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        '["5.6", "7.0", "7.1", "7.2"]'
      );
    };
    $this->_getSandbox(null, $handler)->play(function ($api, $sandbox) {
      $entity = $api->getModel(static::_SUBJECT_MODULE)->set('id', 1);
      $this->assertEquals(
        ['5.6', '7.0', '7.1', '7.2'],
        $api->getEndpoint(static::_SUBJECT_MODULE)
          ->getAvailablePhpVersions($entity)
      );
    });
  }
}
