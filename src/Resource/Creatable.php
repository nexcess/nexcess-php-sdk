<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Resource;

/**
 * Interface for API endpoints which can create new resources on the API.
 *
 * Such endpoints MUST have a create() method,
 * though the method's signature will vary based on the inputs required.
 *
 * All endpoint create() methods MUST return a Modelable instance.
 */
interface Creatable extends Readable {}
