<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Invoice;

use Nexcess\Sdk\ {
  Resource\Endpoint as ReadableEndpoint,
  Resource\Invoice\Resource
};

/**
 * API endpoint for invoices.
 */
class Endpoint extends ReadableEndpoint {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Invoice';

  /** {@inheritDoc} */
  protected const _MODEL_FQCN = Resource::class;

  /** {@inheritDoc} */
  protected const _URI = 'invoice';
}
