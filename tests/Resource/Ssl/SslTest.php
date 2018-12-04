<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Tests\Resource\Ssl;

use Nexcess\Sdk\ {
  Resource\Ssl\Ssl,
  Tests\Resource\ModelTestCase
};

/**
 * Unit test for Ssl.
 */
class SslTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'GET-%2Fssl-cert%2F1.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'ssl-cert-1.toArray.php';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY =
    'ssl-cert-1.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Ssl::class;
}
