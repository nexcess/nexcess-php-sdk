<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Ssl;

use Throwable;
use GuzzleHttp\ {
  Psr7\Response as GuzzleResponse
};
use Nexcess\Sdk\ {
  Resource\Ssl\Endpoint,
  Resource\Ssl\Entity,
  Resource\ResourceException,
  Tests\Resource\EndpointTestCase,
  Util\Language,
  Util\Util
};

class EndpointTest extends EndpointTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_INSTANCES = [];

  /** {@inheritDoc} */
  protected const _RESOURCE_LISTS = [];

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODULE = 'Package';

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [];
  }

  /**
   * @covers Ssl::retrieveByServiceId
   */
   public function testRetrieveByServiceId() {

   }

  /**
   * @covers Ssl::importCertificate
   */
   public function testImportCertificate() {

   }

  /**
   * @covers Ssl::createCertificateFromCsr
   */
   public function testCreateCertificateFromCsr() {

   }

  /**
   * @covers Ssl::createCertificate
   */
   public function testCreateCertificate() {

   }

}
