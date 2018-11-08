<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\CloudAccount;

use Nexcess\Sdk\ {
  Resource\CloudAccount\Endpoint,
  Resource\CloudAccount\Entity,
  Resource\CloudAccount\Backup,
  Resource\PromisedResource,
  Resource\Collection,
  Resource\Model,
  Tests\Resource\ModelTestCase,
  Util\Config
};

/**
 * Unit test for cloud accounts (virtual hosting).
 */
class BackupTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'cloud-account-1.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'cloud-account-1.toArray-shallow.php';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY =
    'cloud-account-1.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Backup::class;

 
  /**
   * @covers Backup::download
   */
  public function testDownloadBackup() {
    $path = '/tmp';
    $filename = 'filename.tgz';
    $force = false;

    $entity  = new Entity;
    $entity->sync(['id'=>1]);

    $backup = new Backup;
    $backup->sync(['filename'=>$filename]);
    $backup->setCloudAccount($entity);
    
    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->method('downloadBackup')
      ->with(
        $this->equalTo($entity),
        $this->equalTo($filename),
        $this->equalTo($path),
        $this->equalTo($force)
      );
    $backup->setApiEndpoint($endpoint);
    $backup->download($path);
    $this->assertTrue(true);
  }

  /**
   * @covers backup::equals
   */
  public function testEquals() {
    $model = $this->_getSubject()->sync(['filename'=>'filename.tgz']);
    $other = $this->_getSubject()->sync(['filename'=>'filename.tgz']);

    $this->assertTrue(
      $model->equals($other),
      'Models of same class with same id must compare equal'
    );

    $other->sync(['filename'=>'other_filename.tgz']);
    $this->assertFalse(
      $model->equals($other),
      'Models of same class with different ids must not compare equal'
    );

    $another = new class() extends Model {
      protected const _PROPERTY_NAMES = ['id'];
      public function __construct() {
        $this->set('id', 1);
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
    $backup = new Backup;
    $backup->sync(['filename'=>'filename.tgz']);
    $this->assertTrue($backup->isReal());
  }

  /**
   * @covers Backup::setCloudAccount
   */
  public function testSetCloudAccount() {
    $entity  = new Entity;
    $entity->sync(['id'=>1]);

    $backup = new Backup;
    $backup->sync(['filename'=>$filename]);
    $backup->setCloudAccount($entity);
    $this->assertEquals($entity, $backup->getCloudAccount());
    $this->assertTrue($backup->isReal());
  }


  /*
   * Stubs
   */
  public function testGetId() {
        $this->markTestSkipped( 'Not relevant to backup' );
  }


  public function testArray(
    array $data = [],
    array $expected = [],
    array $collapsed = []
  ) {
        $this->markTestSkipped( 'Not relevant to backup' );
    return;
  }

  public function testGetSet(
    Model $model = null,
    string $name = '',
    $expected = null,
    $set = null
  ) {
        $this->markTestSkipped( 'Not relevant to backup' );
  }

  public function testSync(array $from = [], array $expected = []) {
        $this->markTestSkipped( 'Not relevant to backup' );
  }
}
