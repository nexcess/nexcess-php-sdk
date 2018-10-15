<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource\Invoice;

use Nexcess\Sdk\Resource\Model;

/**
 * Represents an Invoice.
 */
class Entity extends Model {

  /** {@inheritDoc} */
  public const MODULE_NAME = 'Invoice';

  /** {@inheritDoc} */
  protected const _PROPERTY_ALIASES = ['id' => 'invoice_id'];

  /** {@inheritDoc} */
  protected const _PROPERTY_NAMES = ['invoice_id'];

  /** {@inheritDoc} */
  protected const _READONLY_NAMES = [
    'description',
    'full_id',
    'identity',
    'invoice_id',
    'status',
    'total',
    'type'
  ];
}
