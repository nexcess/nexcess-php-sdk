<?php
/**
 * @package Nexcess-SDK
 * @subpackage User
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Endpoint;

use Nexcess\Sdk\ {
  Endpoint,
  Exception\ApiException,
  Response
};

/**
 * API actions for portal Login.
 */
class User extends Endpoint {

  /** @var string API endpoint. */
  const ENDPOINT = 'user';

  /**
   * Authenticate as the given username (email address).
   */
  public function login(string $username, string $password) : Response {
    return $this->_request(
      'POST',
      self::ENDPOINT . '/login',
      ['json' => ['username' => $username, 'password' => $password]]
    );
  }
}
