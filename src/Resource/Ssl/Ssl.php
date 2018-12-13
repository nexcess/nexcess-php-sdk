<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Client\Client,
  Resource\Model
};

/**
 * Certificates
 */
class Ssl extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Ssl';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'cert_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_COLLAPSED = [];

  /** {@inheritDoc} */
  protected const _PROPERTY_MODELS = ['client' => Client::class];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = [
    'approver_email',
    'cert_id',
    'chain',
    'common_name',
    'crt',
    'domain',
    'key',
    'months',
    'package_id'
  ];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'alt_names',
    'client_id',
    'identity',
    'is_expired',
    'is_installable',
    'is_multi_domain',
    'is_wildcard',
    'valid_from_date',
    'valid_to_date'
  ];

  /**
   * Creates a new Ssl Certificate.
   *
   * Be sure the following properties are set before invoking this method:
   *  - "domain"
   *  - "months"
   *  - "package_id"
   *  - "approver_email"
   *
   * @param array $distinguished_name Contains the following elements:
   *  - string "organization" Legal name of the org that owns the domain
   *  - string "email" Email address used to contact the organization
   *  - string "organizational_unit" Responsible department in organization
   *  - string "street" Organizations's street address
   *  - string "locality" City where the organization is located
   *  - string "state" State/region where the organization is located
   *  - string "country" Two-letter ISO-3166-2 country code
   *    where the organization is located
   * @return Ssl
   */
  public function create(array $distinguished_name) : Ssl {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->create(
      $this->get('domain'),
      $distinguished_name,
      $this->get('months'),
      $this->get('package_id'),
      $this->get('approver_email')
    );
  }

  /**
   * Creates a new Ssl from a CSR.
   *
   * Be sure the following properties are set before invoking this method:
   *  - "key"
   *  - "months"
   *  - "package_id"
   *  - "approver_email"
   *
   * @param string $csr a valid CSR
   * @return Ssl
   */
  public function createFromCsr(string $csr) : Ssl {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->createFromCsr(
      $csr,
      $this->get('key'),
      $this->get('months'),
      $this->get('package_id'),
      $this->get('approver_email')
    );
  }

  /**
   * Imports an existing certificate into your account.
   *
   * Be sure the following properties are set before invoking this method:
   *  - "key"
   *  - "crt"
   *  - "chain"
   *
   * @return Ssl
   */
  public function import() : Ssl {
    $endpoint = $this->_getEndpoint();
    assert($endpoint instanceof Endpoint);

    return $endpoint->import(
      $this->get('key'),
      $this->get('crt'),
      $this->get('chain')
    );
  }
}
