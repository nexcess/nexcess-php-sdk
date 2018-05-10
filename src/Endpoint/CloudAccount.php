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
  Model\CloudAccount as Model
};

/**
 * API actions for Cloud Accounts (virtual hosting).
 */
class CloudAccount extends Service {

  /** {@inheritDoc} */
  const ENDPOINT_CREATE = 'cloud-account';

  /** {@inheritDoc} */
  const SERVICE_TYPE = 'virt-guest-cloud';

  /** {@inheritDoc} */
  const MODEL = Model::class;

  /**
   * Switches PHP versions active on an existing cloud server.
   *
   * @param int $id Cloud server id
   * @param string $version Desired PHP version
   * @return CloudAccount $this
   * @throws ApiException If request fails
   */
  public function setPhpVersion(Model $model, string $version) : CloudAccount {
    $this->_request(
      'POST',
      self::ENDPOINT . "/{$model->getId()}",
      ['json' => ['_action' => 'set-php-version', 'php_version' => $version]]
    );

    $this->_wait($this->_waitUntilVersion($model, $version));
    return $this;
  }

  /**
   * Checks for php version to be updated and then syncs the associated Model.
   *
   * @param Model $model
   * @param string $version
   * @return callable @see wait() $until
   */
  protected function _waitUntilVersion(
    Model $model,
    string $version
  ) : callable {
    return function ($endpoint) use ($model, $version) {
      if ($endpoint->retrieve($model)->get('php_version') === $version) {
        $model->set('version', $version);
        return true;
      }
    };
  }
}
