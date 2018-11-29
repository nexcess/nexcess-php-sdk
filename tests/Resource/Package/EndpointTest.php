<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Package;

use Throwable;
use GuzzleHttp\ {
  Psr7\Response as GuzzleResponse
};
use Nexcess\Sdk\ {
  Resource\Package\Endpoint,
  Resource\Package\Package,
  Resource\ResourceException,
  Tests\Resource\EndpointTestCase,
  Util\Language,
  Util\Util
};

class EndpointTest extends EndpointTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_INSTANCES = [
    'package-717.fromArray.json' => 'package-717.toArray-shallow.json'
  ];

  /** {@inheritDoc} */
  protected const _RESOURCE_LISTS = [
    'GET-%2fpackage%3Ftype%3Dapp.json' => ['type' => 'app'],
    'GET-%2fpackage%3Ftype%3Dvirt-guest.json' => ['type' => 'virt-guest'],
    'GET-%2fpackage%3Ftype%3Dvirt-guest-cloud.json' =>
      ['type' => 'virt-guest-cloud']
  ];

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Package::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODULE = 'Package';

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [
      [
        'list',
        [
          'type' => [
            Util::TYPE_STRING,
            true,
            'type (string): Required. ' .
              Language::get('resource.Package.list.type')
          ],
          'environment_type' => [
            Util::TYPE_STRING,
            false,
            'environment_type (string): Optional. ' .
              Language::get('resource.Package.list.environment_type')
          ]
        ]
      ]
    ];
  }

  /**
   * @covers Endpoint::listCloudAccountPackages
   * @dataProvider listCloudAccountPackagesProvider
   *
   * @param string|null $env_type
   */
  public function testListCloudAccountPackages(?string $env_type) {
    $handler = function ($request, $options) use ($env_type) {
      parse_str($request->getUri()->getQuery(), $query);
      $this->assertArrayHasKey('type', $query);
      $this->assertEquals(
        'virt-guest-cloud',
        $query['type'],
        'restricts to cloud account packages'
      );
      $this->assertArrayHasKey('environment_type', $query);
      $this->assertEquals(
        $env_type ?? Endpoint::ENV_TYPE_PRODUCTION,
        $query['environment_type'],
        'passes desired env type and defaults to "production" if omitted'
      );

      // empty response is fine
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        '[]'
      );
    };

    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) use ($env_type) {
        if ($env_type === null) {
          $api->getEndpoint(static::_SUBJECT_MODULE)
            ->listCloudAccountPackages();
          return;
        }

        $api->getEndpoint(static::_SUBJECT_MODULE)
          ->listCloudAccountPackages($env_type);
      });
  }

  /**
   * @return array List of testcases
   */
  public function listCloudAccountPackagesProvider() : array {
    return [
      [Endpoint::ENV_TYPE_PRODUCTION],
      [Endpoint::ENV_TYPE_DEVELOPMENT],
      [null]
    ];
  }
}
