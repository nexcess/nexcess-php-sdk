<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Creatable,
  Resource\Endpoint as ReadableEndpoint,
  Resource\Ssl\Ssl,
  Resource\Ssl\SslException,
  Util\Util
};

/**
 * API endpoint for Ssl.
 */
class Endpoint extends ReadableEndpoint implements Creatable {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Ssl';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Ssl::class;

  /** {@inheritDoc} */
  protected const _URI = 'ssl-cert';

  /** {@inheritDoc} */
  protected const _PARAMS = [
    'retrieveByServiceId' => [
      'service_id' => [Util::TYPE_INT]
    ],
    'importCertificate' => [
      'key' => [Util::TYPE_STRING],
      'crt' => [Util::TYPE_STRING],
      'chain' => [Util::TYPE_STRING]
    ],
    'createFromCsr' => [
      'csr' => [Util::TYPE_STRING],
      'key' => [Util::TYPE_STRING],
      'months' => [Util::TYPE_INT],
      'package_id' => [Util::TYPE_INT],
      'approver_email' => [Util::TYPE_ARRAY]
    ],
    'create' => [
      'domain' => [Util::TYPE_STRING],
      'distinguished_name' => [Util::TYPE_ARRAY],
      'months' => [Util::TYPE_INT],
      'package_id' => [Util::TYPE_INT],
      'approver_email' => [Util::TYPE_ARRAY]
    ],
    'decodeCsr' => [
      'csr' => [Util::TYPE_STRING],
      'approver_email' => [Util::TYPE_ARRAY]
    ],
    'getCsrDetails' => [
      'domain' => [Util::TYPE_STRING],
      'distinguished_name' => [Util::TYPE_ARRAY],
      'package_id' => [Util::TYPE_INT]
    ]
  ];

  /**
   * Retrieves a certificate by looking up it's service_id.
   *
   * @param int $service_id A valid service_id for a certificate
   * @return Ssl
   */
  public function retrieveByServiceId(int $service_id) : Ssl {
    $certs = $this->list(['filter' => ['service_id' => $service_id]]);
    if (count($certs) === 0) {
      throw new SslException(SslException::NO_MATCHING_CERTS);
    }
    return $certs->current();
  }

  /**
   * Imports an existing certificate.
   *
   * @param string $key The key to the crt
   * @param string $crt The crt
   * @param string $chain The chain certificate
   * @return Ssl
   */
  public function importCertificate(
    string $key,
    string $crt,
    string $chain = ''
  ) : Ssl {
    return $this->getModel()->sync(
      Util::decodeResponse(
        $this->_client->post(
          self::_URI,
          ['json' => ['key' => $key, 'crt' => $crt, 'chain' => $chain]]
        )
      )
    );
  }

 /**
   * Creates a new certificate from a Certificate Signing Request.
   *
   * @param string $csr A valid csr
   * @param string $key The key for the csr
   * @param int $months The number of months to make this certificate valid for
   * @param int $package_id The SSL package purchased
   * @param array $approver_email Format: [domain.name => approver@domain.name]
   *  Must be one of the approved 'approver emails'
   * @return Ssl
   */
  public function createFromCsr(
    string $csr,
    string $key,
    int $months,
    int $package_id,
    array $approver_email
  ) : Ssl {
    $response = $this->_client->post(
      self::_URI,
      [
        'json' => [
          'key' => $key,
          'csr' => $csr,
          'months' => $months,
          'package_id' => $package_id,
          'approver_email' => $approver_email
        ]
      ]
    );
    return $this->retrieveByServiceId(
      Util::decodeResponse($response)['service_id']
    );
  }

/**
   * Creates a new certificate.
   *
   * @param string $domain the domain this certificate is for
   * @param array $distinguished_name Contains the following elements:
   *  - string "organization" Legal name of the org that owns the domain
   *  - string "email" Email address used to contact the organization
   *  - string "organizational_unit" Responsible department in organization
   *  - string "street" Organizations's street address
   *  - string "locality" City where the organization is located
   *  - string "state" State/region where the organization is located
   *  - string "country" Two-letter ISO-3166-2 country code
   *    where the organization is located
   * @param array $approver_email Format: [domain.name => approver@domain.name]
   *  Must be one of the approved 'approver emails'
   * @return Ssl
   */
  public function create(
    string $domain,
    array $distinguished_name,
    int $months,
    int $package_id,
    array $approver_email
  ) : Ssl {
    $response = $this->_client->post(
      self::_URI,
      [
        'json' => [
          'domain' => $domain,
          'months' => $months,
          'package_id' => $package_id,
          'approver_email' => $approver_email,
          'distinguished_name' => $distinguished_name
        ]
      ]
    );
    return $this->retrieveByServiceId(
      Util::decodeResponse($response)['service_id']
    );
  }

  /**
   * Decodes an existing CSR and compares it to the package_id.
   *
   * @param string $csr The CSR to decode
   * @param int $package_id Valid package_id for the type of csr
   * @return array Decoded CSR details
   */
  public function decodeCsr(string $csr, int $package_id) : array {
    return Util::decodeResponse(
      $this->_client->post(
        self::_URI . '/decode-csr',
        ['json' => ['csr' => $csr, 'package_id' => $package_id]]
      )
    );
  }

  /**
   * Creates a CSR, making sure the type matches the package type.
   *
   * @param string $domain Primary hostname for this certificate
   * @param array $distinguished_name Contains the following elements:
   *  - string "organization" Legal name of the org that owns the domain
   *  - string "email" Email address used to contact the organization
   *  - string "organizational_unit" Responsible department in organization
   *  - string "street" Organizations's street address
   *  - string "locality" City where the organization is located
   *  - string "state" State/region where the organization is located
   *  - string "country" Two-letter ISO-3166-2 country code
   *    where the organization is located
   * @param int $package_id Valid package_id for the type of CSR
   * @return array Details of the new CSR
   */
  public function getCsrDetails(
    string $domain,
    array $distinguished_name,
    int $package_id
  ) : array {
    return Util::decodeResponse(
      $this->_client->post(
        self::_URI . '/get-csr-details',
        [
          'json' => [
            'domains' => $domain,
            'package_id' => $package_id,
            'distinguished_name' => $distinguished_name
          ]
        ]
      )
    );
  }
}
