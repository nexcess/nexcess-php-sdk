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
  Endpoint\ServiceEndpoint,
  Exception\ApiException,
  Response
};

/**
 * API actions for Cloud Accounts (virtual hosting).
 */
class CloudAccount extends ServiceEndpoint {

  /** {@inheritDoc} */
  const TYPE = 'virt-guest-cloud';

  /**
   * {@inheritDoc}
   *
   * - int "app_id": Application environment id
   * - int "cloud_id": Cloud (location) id
   * - string "domain": Desired domain name
   * - int "package_id": Service package id
   */
  const ADD_VALUE_MAP = [
    'app_id' => 0,
    'cloud_id' => 0,
    'domain' => '',
    'package_id' => 0
  ];

  /**
   * Switches PHP versions active on an existing cloud server.
   *
   * @param int $id Cloud server id
   * @param string $version Desired PHP version
   * @return array Response data
   * @throws ApiException If request fails
   */
  public function setPhpVersion(int $id, string $version) : Response {
    $this->_request(
      'POST',
      self::ENDPOINT . "/{$id}",
      ['json' => ['_action' => 'set-php-version', 'php_version' => $version]]
    );
  }
}
