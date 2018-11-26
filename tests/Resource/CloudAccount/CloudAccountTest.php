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
  Resource\CloudAccount\CloudAccount,
  Resource\CloudAccount\Backup,
  Resource\PromisedResource,
  Resource\Collection,
  Tests\Resource\ModelTestCase,
  Util\Config
};

/**
 * Unit test for cloud accounts (virtual hosting).
 */
class CloudAccountTest extends ModelTestCase {

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
  protected const _SUBJECT_FQCN = CloudAccount::class;

  /**
   * @covers CloudAccount::getAvailablePhpVersions
   */
  public function testGetAvailablePhpVersions() {
    $versions = ['5.6', '7.0', '7.1', '7.2'];

    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('getAvailablePhpVersions')
      ->willReturn($versions);

    $cloudaccount = CloudAccount::__set_state(['_endpoint' => $endpoint]);
    $this->assertEquals(
      $versions,
      $cloudaccount->getAvailablePhpVersions(),
      'invokes and returns $endpoint->getAvailablePhpVersions()'
    );
  }

  /**
   * @covers CloudAccount::setPhpVersion
   */
  public function testSetPhpVersion() {
    $cloudaccount = $this->_getSubject();

    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->expects($this->once())
      ->method('setPhpVersion')
      ->with($this->equalTo($cloudaccount), $this->equalTo('7.2'))
      ->willReturn($cloudaccount);

    $cloudaccount->setApiEndpoint($endpoint);

    $this->assertEquals(
      $cloudaccount,
      $cloudaccount->setPhpVersion('7.2'),
      'invokes $endpoint->setPhpversion($cloudaccount, 7.2)'
    );
  }

  /**
   * @covers CloudAccount::backup
   */
  public function testCreateBackup() {
    $cloudaccount = $this->_getSubject();

    $backup = new Backup();
    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->method('createBackup')
      ->with($this->equalTo($cloudaccount))
      ->willReturn($backup);

    $cloudaccount->setApiEndpoint($endpoint);

    $this->assertEquals(
      $backup,
      $cloudaccount->backup(),
      'invokes $endpoint->createBackup($cloudaccount)'
    );
  }

  /**
   * @covers CloudAccount::listBackups
   */
  public function testListBackups() {
    $cloudaccount = $this->_getSubject();

    $collection = new Collection(Backup::class);
    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->method('listBackups')
      ->with($this->equalTo($cloudaccount))
      ->willReturn($collection);

    $cloudaccount->setApiEndpoint($endpoint);

    $this->assertEquals(
      $collection,
      $cloudaccount->listBackups(),
      'invokes $endpoint->listBackups($cloudaccount)'
    );
  }

  /**
   * @covers CloudAccount::retrieveBackup
   */
  public function testRetrieveBackup() {
    $cloudaccount = $this->_getSubject();
    $filename = 'filename.tgz';

    $backup = $this->createMock(Backup::class);
    $backup->method('get')
      ->with('filename')
      ->willReturn($filename);

    $endpoint = $this->createMock(Endpoint::class);
    $endpoint->method('retrieveBackup')
      ->with($this->equalTo($cloudaccount), $this->equalTo($filename))
      ->willReturn($backup);

    $cloudaccount->setApiEndpoint($endpoint);

    $this->assertEquals(
      $filename,
      $cloudaccount->retrieveBackup($filename)->get('filename'),
      'invokes $endpoint->listBackups($cloudaccount)'
    );
  }
}
