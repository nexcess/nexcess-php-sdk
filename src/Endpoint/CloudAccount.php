<?php
/**
 * @package Nexcess-SDK
 * @subpackage Cloud-Account
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint\Service,
  Exception\ApiException,
  Model\Modelable as Model,
  Response
};

/**
 * API actions for Cloud Accounts (virtual hosting).
 */
class CloudAccount extends Service {

  /** {@inheritDoc} */
  const SERVICE_TYPE = 'virt-guest-cloud';

  /**
   * Switches PHP versions active on an existing cloud server.
   *
   * @param int $id Cloud server id
   * @param string $version Desired PHP version
   * @return array Response data
   * @throws ApiException If request fails
   */
  public function setPhpVersion(Model $model, string $version) : Model {
    $this->_request(
      'POST',
      self::ENDPOINT . "/{$model->offsetGet('id')}",
      ['json' => ['_action' => 'set-php-version', 'php_version' => $version]]
    );

    return $model;
  }
}
