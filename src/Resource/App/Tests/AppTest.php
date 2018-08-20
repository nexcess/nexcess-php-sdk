<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\App\Tests;

use Nexcess\Sdk\ {
  Resource\App\App,
  Resource\Tests\ModelTestCase
};

/**
 * Unit test for app environments.
 */
class AppTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'app-13.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'app-13.toArray-shallow.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY = 'app-13.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = App::class;
}
