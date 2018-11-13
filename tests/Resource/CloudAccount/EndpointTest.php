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
  Resource\CloudAccount\Backup,
  Resource\CloudAccount\CloudAccountException,
  Resource\CloudAccount\Endpoint,
  Resource\CloudAccount\Entity as CloudAccount,
  Resource\Promise,
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
  protected const _RESOURCE_NEW_BACKUP =
    'POST-%2Fcloud-account%2F1%2Fbackup.json';

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
  protected const _SUBJECT_MODEL_FQCN = CloudAccount::class;

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
            'copy_account (boolean): Required. ' . Language::get(
              'resource.CloudAccount.createDevAccount.copy_account'
            )
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
            'package_id (integer): Required. ' . Language::get(
              'resource.CloudAccount.createDevAccount.package_id'
            )
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
          'version' => [
            Util::TYPE_STRING,
            true,
            'version (string): Required. ' .
              Language::get('resource.CloudAccount.setPhpVersion.version')
          ]
        ]
      ],
      ['clearNginxCache', []],
      ['createBackup', []]
    ];
  }

  /**
   * @covers Endpoint::createDevAccount
   * @dataProvider createDevAccountProvider
   *
   * @param CloudAccount $cloud Parent cloud account
   * @param array $params Map of test input parameters
   * @param array|Throwable $expected Expected request payload;
   *  or an Exception if input is invalid
   * @param GuzzleResponse|callable|Throwable|null $response Response to queue
   */
  public function testCreateDevAccount(
    CloudAccount $cloud,
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
    $cloud = CloudAccount::__set_state([
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

      $entity = CloudAccount::__set_state([
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
      $this->assertEquals(
        'cloud-account/1/backup',
        $request->getUri()->getPath()
      );

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
        $this->assertEquals('123 MB', $results->get('filesize'));
        $this->assertEquals(456, $results->get('filesize_bytes'));
      });
  }

  /**
   * @covers Endpoint::downloadBackup
   */
  public function testDownloadBackup() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $sandbox->makeResponse(
        'GET /siteworx/index',
        200,
        $this->_getResource(static::_RESOURCE_BACKUPS)
      );

      $vfs = vfsStream::setup('backupDownloadTest');
      $path = $vfs->url();

      $backup = Backup::__set_state([
        '_values' => $this->_getResource(static::_RESOURCE_BACKUPS)[0]
      ]);
      $endpoint = $api->getEndpoint(static::_SUBJECT_MODULE);
      $endpoint->downloadBackup($backup, $path);

      if ($path[-1] !== DIRECTORY_SEPARATOR) {
        $path .= DIRECTORY_SEPARATOR;
      }
      $this->assertTrue(file_exists($path . $backup->get('filename')));

      // forcibly overwrite existing local file
      $sandbox->makeResponse(
        'GET /siteworx/index',
        200,
        $this->_getResource(static::_RESOURCE_BACKUPS)
      );

      $endpoint->downloadBackup($backup, $path, true);

      // failure case: don't forcibly overwrite
      $this->setExpectedException(
        new CloudAccountException(CloudAccountException::FILE_EXISTS)
      );

      $endpoint->downloadBackup($backup, $path);
    });
  }

  /**
   * @covers Endpoint::downloadBackup
   */
  public function testDownloadIncompleteBackup() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $this->setExpectedException(
        new CloudAccountException(CloudAccountException::INCOMPLETE_BACKUP)
      );

      // valid backup, but not complete (no download_url)
      $backup = Backup::__set_state([
        '_values' => ['filename' => 'filename.tgz']
      ]);
      $api->getEndpoint(static::_SUBJECT_MODULE)
        ->downloadBackup($backup, 'some/path');
    });
  }

  /**
   * @covers Endpoint::downloadBackup
   */
  public function testDownloadInvalidBackup() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $this->setExpectedException(
        new CloudAccountException(CloudAccountException::INVALID_BACKUP)
      );

      // invalid backup (no filename)
      $api->getEndpoint(static::_SUBJECT_MODULE)
        ->downloadBackup(new Backup(), 'some/path');
    });
  }

  /**
   * @covers Endpoint::downloadBackup
   */
  public function testDownloadUnwritableBackup() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $this->setExpectedException(
        new CloudAccountException(CloudAccountException::INVALID_STREAM)
      );

      // make target download location unwritable
      $vfs = vfsStream::setup('backupDownloadTest', 0400);
      $path = $vfs->url();

      $backup = Backup::__set_state([
        '_values' => $this->_getResource(static::_RESOURCE_BACKUPS)[0]
      ]);
      $api->getEndpoint(static::_SUBJECT_MODULE)
        ->downloadBackup($backup, $path);
    });
  }

  /**
   * @covers Endpoint::downloadBackup
   */
  public function testDeleteBackup() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      $backup = Backup::__set_state([
        '_values' => $this->_getResource(static::_RESOURCE_BACKUPS)[0]
      ])->setCloudAccount(
        CloudAccount::__set_state(['_values' => ['account_id' => 1]])
      );

      $sandbox->makeResponse('DELETE cloud-account/1/backup/filename.tgz');
      $api->getEndpoint(static::_SUBJECT_MODULE)->deleteBackup($backup);

      // failure case: invalid backup (no filename)
      $this->setExpectedException(
        new CloudAccountException(CloudAccountException::INVALID_BACKUP)
      );
      $api->getEndpoint(static::_SUBJECT_MODULE)->deleteBackup(new Backup());
    });
  }

  /**
   * @covers Endpoint::whenBackupComplete
   */
  public function testWhenBackupComplete() {
    $this->_getSandbox()->play(function ($api, $sandbox) {
      // all values must exist to prevent extraneous _tryToHydrate() calls
      $incomplete = $this->_getResource(self::_RESOURCE_NEW_BACKUP) +
        ['download_url' => ''];
      $complete = ['complete' => true] + $incomplete;

      $backup = $api->getModel(Backup::class)
        ->sync($incomplete)
        ->setCloudAccount(new CloudAccount());

      $promise = $api->getEndpoint(static::_SUBJECT_MODULE)
        ->whenBackupComplete($backup, [Promise::OPT_INTERVAL => 0]);
      $this->assertInstanceOf(Promise::class, $promise);

      $sandbox->makeResponse('*', 200, [$incomplete]);
      $sandbox->makeResponse('*', 200, [$complete]);
      $resolved = $promise->wait();
      $this->assertInstanceOf(Backup::class, $resolved);
      $this->assertTrue($resolved->equals($backup));
      $this->assertTrue($resolved->get('complete'));
    });
  }
}
