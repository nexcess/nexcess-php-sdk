<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\CloudAccount;

use Throwable;
use GuzzleHttp\ {
  Psr7\Response as GuzzleResponse
};

use org\bovigo\vfs\vfsStream;

use Nexcess\Sdk\ {
  Resource\CloudAccount\Endpoint,
  Resource\CloudAccount\Entity,
  Resource\CloudAccount\Backup,
  Resource\ResourceException,
  Resource\VirtGuestCloud\Entity as Service,
  Tests\Resource\EndpointTestCase,
  Util\Language,
  Util\Util
};

class EndpointTest extends EndpointTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** @var string Resource name for new dev account responses. */
  protected const _RESOURCE_NEW_DEV = 'POST-%2Fcloud-account.json';

  /** @var string Resource name for cloud account #1 json payload. */
  protected const _RESOURCE_GET_1 = 'GET-%2Fcloud-account%2F1.json';

  /** @var string Resource name for cloud account instance data. */
  protected const _RESOURCE_CLOUD = 'cloud-account-1.toArray-shallow.php';

  /** @var string Resource name for new backup. */
  protected const _RESOURCE_NEW_BACKUP = 'POST-%2Fcloud-account%2F1%2Fbackup.json';

  /** @var string Resource name for list of backups. */
  protected const _RESOURCE_BACKUPS = 'GET-%2Fcloud-account%2F1%2Fbackup.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_INSTANCES = [
    'cloud-account-1.fromArray.json' => 'cloud-account-1.toArray-shallow.php'
  ];

  /** {@inheritDoc} */
  protected const _RESOURCE_LISTS = [
    'GET-%2Fcloud-account%3F.json' => []
  ];

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODULE = 'CloudAccount';

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [
      [
        'create',
        [
          'app_id' => [
            Util::TYPE_INT,
            true,
            'app_id (integer): Required. ' .
              Language::get('resource.CloudAccount.create.app_id')
          ],
          'cloud_id' => [
            Util::TYPE_INT,
            true,
            'cloud_id (integer): Required. ' .
              Language::get('resource.CloudAccount.create.cloud_id')
          ],
          'domain' => [
            Util::TYPE_STRING,
            true,
            'domain (string): Required. ' .
              Language::get('resource.CloudAccount.create.domain')
          ],
          'install_app' => [
            Util::TYPE_BOOL,
            false,
            'install_app (boolean): Optional. ' .
              Language::get('resource.CloudAccount.create.install_app')
          ],
          'package_id' => [
            Util::TYPE_INT,
            true,
            'package_id (integer): Required. ' .
              Language::get('resource.CloudAccount.create.package_id')
          ]
        ]
      ],
      [
        'createDevAccount',
        [
          'copy_account' => [
            Util::TYPE_BOOL,
            true,
            'copy_account (boolean): Required. ' .
              Language::get('resource.CloudAccount.createDevAccount.copy_account')
          ],
          'domain' => [
            Util::TYPE_STRING,
            true,
            'domain (string): Required. ' .
              Language::get('resource.CloudAccount.createDevAccount.domain')
          ],
          'package_id' => [
            Util::TYPE_INT,
            true,
            'package_id (integer): Required. ' .
              Language::get('resource.CloudAccount.createDevAccount.package_id')
          ],
          'ref_cloud_account_id' => [
            Util::TYPE_INT,
            true,
            'ref_cloud_account_id (integer): Required. ' .
              Language::get(
                'resource.CloudAccount.createDevAccount.ref_cloud_account_id'
              )
          ],
          'ref_service_id' => [
            Util::TYPE_INT,
            true,
            'ref_service_id (integer): Required. ' .
              Language::get(
                'resource.CloudAccount.createDevAccount.ref_service_id'
              )
          ],
          'ref_type' => [
            Util::TYPE_STRING,
            true,
            'ref_type (string): Required. ' .
              Language::get('resource.CloudAccount.createDevAccount.ref_type')
          ],
          'scrub_account' => [
            Util::TYPE_BOOL,
            true,
            'scrub_account (boolean): Required. ' .
              Language::get(
                'resource.CloudAccount.createDevAccount.scrub_account'
              )
          ]
        ]
      ],
      [
        'setPhpVersion',
        [
          'version'=> [
            Util::TYPE_STRING,
            true,
            'version (string): Required. ' .
              Language::get('resource.CloudAccount.setPhpVersion.version')
          ]
        ]
      ],
      [
        'clearNginxCache', []
      ],
      [
        'createBackup', []
      ]
    ];
  }

  /**
   * @covers Endpoint::createDevAccount
   * @dataProvider createDevAccountProvider
   *
   * @param Entity $cloud Parent cloud account
   * @param array $params Map of test input parameters
   * @param array|Throwable $expected Expected request payload;
   *  or an Exception if input is invalid
   * @param GuzzleResponse|callable|Throwable|null $response Response to queue
   */
  public function testCreateDevAccount(
    Entity $cloud,
    array $params,
    $expected,
    $response = null
  ) {
    if ($expected instanceof Throwable) {
      $this->setExpectedException($expected);
    }

    $handler = function ($request, $options) use ($expected, $response) {
      $actual = Util::jsonDecode((string) $request->getBody());
      foreach ($expected as $param => $expect) {
        $this->assertArrayHasKey($param, $actual);
        $this->assertEquals($expect, $actual[$param]);
      }

      return $response;
    };
    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) use ($cloud, $params) {
        $api->getEndpoint(static::_SUBJECT_FQCN)
          ->createDevAccount($cloud, $params);
      });
  }


  /**
   * @covers Endpoint::clearNginxCache
   */
  public function testClearNginxCache(){
    $handler = function ($request, $options) {
      $actual = Util::jsonDecode((string) $request->getBody());

      $this->assertArrayHasKey('_action', $actual);
      $this->assertEquals($actual['_action'], 'purge-cache');

      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_GET_1, false)
      );
    };

    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) {
        $endpoint = $api->getEndpoint(static::_SUBJECT_FQCN);
        $entity = $endpoint->getModel()->set('id', 1);
        $endpoint->clearNginxCache($entity);
      });

  }

  /**
   * @return array[] List of testcases
   */
  public function createDevAccountProvider() : array {
    $fqcn = static::_SUBJECT_MODEL_FQCN;
    $cloud = Entity::__set_state([
      '_values' => $this->_getResource(static::_RESOURCE_CLOUD) +
        ['account_id' => 1]
    ]);
    $expected = function ($input) use ($cloud) {
      return [
        'domain' => ($input['domain'] ?? 'dev') . ".{$cloud->get('domain')}",
        'ref_cloud_account_id' => 1,
        'ref_service_id' => 1,
        'ref_type' => 'development'
      ] + $input
      + ['copy_account' => true, 'scrub_account' => true];
    };
    $response = new GuzzleResponse(
      200,
      ['Content-type' => 'application/json'],
      $this->_getResource(static::_RESOURCE_NEW_DEV, false)
    );

    return [
      [$cloud, ['package_id' => 1], $expected(['package_id' => 1]), $response],
      [
        $cloud,
        ['package_id' => 1, 'domain' => 'test'],
        $expected(['package_id' => 1, 'domain' => 'test']),
        $response
      ],
      [
        $cloud,
        ['package_id' => 1, 'scrub_account' => false],
        $expected(['package_id' => 1, 'scrub_account' => false]),
        $response
      ],

      [$cloud, [], new ResourceException(ResourceException::MISSING_PARAM)],
      [
        $cloud,
        ['package_id' => 'foo'],
        new ResourceException(ResourceException::WRONG_PARAM)
      ]
    ];
  }

  /**
   * @covers Endpoint::getAvailablePhpVersions
   */
  public function testGetAvailablePhpVersions() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $versions = ['5.6', '7.0', '7.1', '7.2'];

      $service = $this->createMock(Service::class);
      $service->expects($this->once())
        ->method('getAvailablePhpVersions')
        ->willReturn($versions);

      $entity = Entity::__set_state([
        '_values' => ['account_id' => 1, 'service' => $service]
      ]);
      $this->assertEquals(
        $versions,
        $api->getEndpoint(static::_SUBJECT_MODULE)
          ->getAvailablePhpVersions($entity),
        'invokes and returns $entity->get(service)->getAvailablePhpVersions()'
      );
    });
  }

  /**
   * @covers Endpoint::setPhpVersion
   */
  public function testSetPhpVersion() {
    // custom request handler for sandbox
    $handler = function ($request, $options) {
      // check request path
      $this->assertEquals('cloud-account/1', $request->getUri()->getPath());

      // check request parameters
      $actual = Util::jsonDecode((string) $request->getBody());
      $this->assertArrayHasKey('_action', $actual);
      $this->assertEquals('set-php-version', $actual['_action']);
      $this->assertArrayHasKey('php_version', $actual);
      $this->assertEquals('7.2', $actual['php_version']);

      // assertions passed; return 200 response
      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_GET_1, false)
      );
    };

    // kick off
    $this->_getSandbox(null, $handler)
      ->play(function ($api, $sandbox) {
        $entity = $api->getModel(static::_SUBJECT_MODULE)->set('id', 1);
        $api->getEndpoint(static::_SUBJECT_MODULE)
          ->setPhpVersion($entity, '7.2');
      });
  }

  /**
   * @covers Endpoint::createBackup
   */
  public function testCreateBackup() {
    // custom request handler for sandbox
    $request_handler = function ($request, $options) {
      // check request path
      $this->assertEquals('cloud-account/1/backup', $request->getUri()->getPath());

      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_NEW_BACKUP, false)
      );
    };

    // kick off
    $this->_getSandbox(null, $request_handler)
      ->play(function ($api, $sandbox) {
        $entity = $api->getModel(static::_SUBJECT_MODEL_FQCN)->set('id', 1);
        $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
        $results = $endpoint->createBackup($entity);
        $this->assertEquals('filename.tgz', $results->get('filename'));
        $this->assertEquals("123 MB", $results->get('filesize'));
        $this->assertEquals(456, $results->get('filesize_bytes'));
      });
  }

  /**
   * @covers Endpoint::downloadBackup
   */
  public function testDownloadBackup() {
    $assertionCounter = 0;
    $request_handler = function ($request, $options) use (&$assertionCounter) {
      // check request path

      switch (++$assertionCounter) {
        case 1:
          $this->assertEquals('cloud-account/1/backup', $request->getUri()->getPath());
          break;

        case 2:
          $this->assertEquals('/siteworx/index', $request->getUri()->getPath());
          break;

        default:
          $this->fail("unexpected request: {$request->getUri()->getPath()}");
      }

      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        $this->_getResource(static::_RESOURCE_BACKUPS, false)
      );
    };

    // kick off
    $this->_getSandbox(null, $request_handler)
      ->play(function ($api, $sandbox) {
        $vfs = vfsStream::setup('backupDownloadTest');

        $filename = 'filename.tgz';
        $path = $vfs->url();

        $entity = $api->getModel(static::_SUBJECT_MODEL_FQCN)->set('id', 1);

        $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
        $endpoint->downloadBackup($entity, $filename, $path);

        $path = trim($path);

        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
          $path .= DIRECTORY_SEPARATOR;
        }

        $full_filename = $path . $filename;
        $this->assertTrue(file_exists($full_filename));
      });
  }

  /**
   * @covers Endpoint::downloadBackup
   */
  public function testDeleteBackup() {

    $request_handler = function ($request, $options)  {
      $this->assertEquals('DELETE', $request->getMethod());
      $this->assertEquals('cloud-account/1/backup/filename.tgz', $request->getUri()->getPath());

      return new GuzzleResponse(
        200,
        ['Content-type' => 'application/json'],
        '[]'
      );
    };

    // kick off
    $this->_getSandbox(null, $request_handler)
      ->play(function ($api, $sandbox) {
        $filename = 'filename.tgz';
        $entity = $api->getModel(static::_SUBJECT_MODEL_FQCN)->set('id', 1);
        $api->getEndpoint(static::_SUBJECT_MODULE)
          ->deleteBackup($entity, $filename);
      });
  }
}
