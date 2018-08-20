<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Invoice\Tests;

use Nexcess\Sdk\ {
  Resource\Invoice\Invoice,
  Resource\Tests\ModelTestCase
};

/**
 * Unit test for cloud accounts (virtual hosting).
 */
class InvoiceTest extends ModelTestCase {

  /** {@inheritDoc} */
  protected const _RESOURCE_PATH = __DIR__ . '/resources';

  /** {@inheritDoc} */
  protected const _RESOURCE_FROMARRAY = 'invoice-1.fromArray.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOARRAY = 'invoice-1.toArray-shallow.json';

  /** {@inheritDoc} */
  protected const _RESOURCE_TOCOLLAPSEDARRAY =
    'invoice-1.toCollapsedArray.json';

  /** {@inheritDoc} */
  protected const _SUBJECT_FQCN = Invoice::class;
}
