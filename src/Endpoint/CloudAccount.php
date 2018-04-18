<?php
/**
 * @package Nexcess-SDK
 * @subpackage Cloud-Account
 * @license TBD
 * @copyright 2018 Nexcess.net
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint,
  Exception\ApiException
};

/**
 * API actions for Cloud Accounts (virtual hosting).
 */
class CloudAccount extends Endpoint {

  /** @var string API endpoint. */
  const ENDPOINT = 'cloud-account';

  /**
   * Creates a new cloud account.
   *
   * @param int $app_id Application environment id
   * @param int $cloud_id Cloud (location) id
   * @param string $domain Desired domain name
   * @param int $package_id Service package id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function add(
    int $app_id,
    int $cloud_id,
    string $domain,
    int $package_id
  ) : array {
    return $this->_request(
      'POST',
      self::ENDPOINT,
      [
        "json" => [
          'app_id' => $app_id,
          'cloud_id' => $cloud_id,
          'domain' => $domain,
          'package_id' => $package_id
        ]
      ]
    );
  }

  /**
   * Gets information about an existing cloud account.
   *
   * @param int $cloud_account_id Service id
   * @return array API response data
   * @throws ApiException On failure
   */
  public function view(int $cloud_account_id) : array {
    return $this->_request('GET', self::ENDPOINT . "/{$cloud_account_id}");
  }
}
