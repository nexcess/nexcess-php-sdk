<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\CloudAccount;

use Nexcess\Sdk\ {
  Resource\Collection,
  Resource\CloudAccount\Backup,
  Resource\CloudAccount\CloudAccountException,
  Resource\CloudAccount\Endpoint,
  Resource\CloudAccount\Entity as CloudAccount,
  Resource\PromisedResource,
  Resource\Promise,
  Resource\Model,
  Tests\Resource\ModelTestCase,
  Util\Config
};

/**
 * Unit test for Backups.
 */
class BackupTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'backup.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'backup.toArray-shallow.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY = 'backup.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Backup::class;

  /**
   * @covers Backup::download
   */
  public function testDownload() {
    $path = '/tmp';
    $filename = 'filename.tgz';
    $force = false;

    $entity  = new CloudAccount();
    $entity->sync(['id' => 1]);

    $backup = new Backup();
    $backup->setCloudAccount($entity);

    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('downloadBackup')
      ->with(
        $this->equalTo($backup),
        $this->equalTo($path),
        $this->equalTo($force)
      );
    $backup->setApiEndpoint($endpoint);
    $backup->sync(['filename' => $filename]);
    $backup->download($path);
  }

  /**
   * @covers Backup::download
   */
  public function testDelete() {
    $filename = 'filename.tgz';

    $entity  = new CloudAccount();
    $entity->sync(['id' => 1]);

    $backup = new Backup();
    $backup->setCloudAccount($entity);

    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('deleteBackup')
      ->with($this->equalTo($backup));
    $backup->setApiEndpoint($endpoint);
    $backup->sync(['filename' => $filename]);
    $backup->delete();
  }

  /**
   * @covers backup::equals
   */
  public function testEquals() {
    $model = $this->_getSubject()->sync(['filename' => 'filename.tgz']);
    $other = $this->_getSubject()->sync(['filename' => 'filename.tgz']);

    $this->assertTrue(
      $model->equals($other),
      'Models of same class with same id must compare equal'
    );

    $other->sync(['filename' => 'other_filename.tgz']);
    $this->assertFalse(
      $model->equals($other),
      'Models of same class with different ids must not compare equal'
    );

    $another = new class() extends Model {
      protected const _PROPERTY_NAMES = ['filename'];
      public function __construct() {
        $this->set('filename', 'filename.tgz');
      }
    };

    $this->assertFalse(
      $model->equals($another),
      'Models of different classes must not compare equal'
    );
  }

  /**
   * @covers Backup::isReal
   */
  public function testIsReal() {
    $backup = new Backup();
    $backup->sync(['filename' => 'filename.tgz']);
    $this->assertTrue($backup->isReal());
  }

  /**
   * @covers Backup::setCloudAccount
   * @covers Backup::getCloudAccount
   */
  public function testGetSetCloudAccount() {
    $entity  = new CloudAccount();
    $entity->sync(['id' => 1]);

    $backup = new Backup;
    $backup->setCloudAccount($entity);
    $this->assertEquals($entity, $backup->getCloudAccount());
  }


  /**
   * @covers Backup::setCloudAccount
   */
  public function testWhenComplete() {
    $backup = $this->_getSubject()->setCloudAccount(new CloudAccount());
    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('whenBackupComplete')
      ->willReturn(new Promise($backup, function () {}));
    $backup->setApiEndpoint($endpoint);
    $backup->whenComplete();
  }

  /**
   * @covers Backup::getId()
   */
  public function testGetId() {
    $this->assertNull(
      $this->_getSubject()->getId(),
      'Backups do not have numeric IDs'
    );
  }
}
