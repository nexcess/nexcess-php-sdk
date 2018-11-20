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
  protected const _RESOURCE_GET = 'GET-%2Fssl-cert%2F.json';

  /** @var string Resource sll cert by service_id */
  protected const _RESOURCE_GET_1 = 'ssl-by-service-id.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_IMPORT = 'POST-%2Fssl-cert%2F.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_INSTANCES = [
    'ssl-cert-1.fromArray.json' => 'ssl-cert-1.toArray-shallow.php'
  ];

  /** {@inheritDoc} */
  protected const _RESOURCE_LISTS = [
    'GET-%2Fssl-cert%2F.json' => []
  ];

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Endpoint::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODEL_FQCN = Entity::class;

  /** {@inheritDoc} */
  protected const _SUBJECT_MODULE = 'Ssl';

  /**
   * {@inheritDoc}
   */
  public function getParamsProvider() : array {
    return [
      [
        'retrieveByServiceId',
        [
          'service_id' => [
              Util::TYPE_INT,
              true,
              'service_id (integer): Required. ' .
                Language::get('resource.Ssl.retrieveByServiceId.service_id')
            ]
        ]
      ],
      ['importCertificate',
        [
          'key' => [
            Util::TYPE_STRING,
            true,
              'key (string): Required. ' .
                Language::get('resource.Ssl.importCertificate.key')
          ],
          'crt' => [
            Util::TYPE_STRING,
            true,
              'crt (string): Required. ' .
                Language::get('resource.Ssl.importCertificate.crt')
          ],
          'chain' => [
            Util::TYPE_STRING,
            true,
              'chain (string): Required. ' .
                Language::get('resource.Ssl.importCertificate.chain')
          ]

        ]
      ],
      ['createCertificateFromCsr',
        [
          'csr' => [
            Util::TYPE_STRING,
            true,
            'csr (string): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.csr')
          ],
          'key' => [
            Util::TYPE_STRING,
            true,
            'key (string): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.key')
          ],
          'months' => [
            Util::TYPE_INT,
            true,
            'months (integer): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.months')
          ],
          'package_id' => [
            Util::TYPE_INT,
            true,
            'package_id (integer): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.package_id')
          ],
          'approver_email' => [
            Util::TYPE_ARRAY,
            true,
            'approver_email (array): Required. ' .
            Language::get('resource.Ssl.createCertificateFromCsr.approver_email')
          ],
        ]
      ],
      ['createCertificate',
        [
          'domain' => [
            Util::TYPE_STRING,
            true,
            'domain (string): Required. ' .
            Language::get('resource.Ssl.createCertificate.domain')
          ],
          'distinguished_name' => [
            Util::TYPE_ARRAY,
            true,
            'distinguished_name (array): Required. ' .
            Language::get('resource.Ssl.createCertificate.distinguished_name')
          ],
          'months' => [
            Util::TYPE_INT,
            true,
            'months (integer): Required. ' .
            Language::get('resource.Ssl.createCertificate.months')
          ],
          'package_id' => [
            Util::TYPE_INT,
            true,
            'package_id (integer): Required. ' .
            Language::get('resource.Ssl.createCertificate.package_id')
          ],
          'approver_email' => [
            Util::TYPE_ARRAY,
            true,
            'approver_email (array): Required. ' .
            Language::get('resource.Ssl.createCertificate.approver_email')
          ]

        ]
      ]
    ];
  }

  /**
   * @covers Ssl::retrieveByServiceId
   */
  public function testRetrieveByServiceId() {
    // custom request handler for sandbox
    $handler = function ($request, $options) {
      // check request path
      $this->assertEquals('ssl-cert', $request->getUri()->getPath());
      $this->assertEquals('filter[service_id]=58887', urldecode($request->getUri()->getQuery()));

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
        $results = $api->getEndpoint(static::_SUBJECT_MODULE)
          ->retrieveByServiceId(58887);
        $this->assertEquals(123, $results->get('cert_id'));
        $this->assertEquals('admin@example1.com', $results->get('approver_email'));
      });

  }

  /**
   * @covers Ssl::importCertificate
   */
   public function testImportCertificate() {
     $this->markTestIncomplete('This test has not been implemented yet.');
   }

  /**
   * @covers Ssl::createCertificateFromCsr
   */
   public function testCreateCertificateFromCsr() {
     $this->markTestIncomplete('This test has not been implemented yet.');
   }

  /**
   * @covers Ssl::createCertificate
   */
   public function testCreateCertificateByCSR() {
     $this->markTestIncomplete('This test has not been implemented yet.');
   }

  /**
   * @covers Ssl::createCertificate
   */
   public function testCreateCertificateByData() {
     $this->markTestIncomplete('This test has not been implemented yet.');
   }

}
