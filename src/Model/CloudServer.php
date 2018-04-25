<?php
/**
 * @package Nexcess-SDK
 * @license https://opensource.org/licenses/MIT
 * @copyright 2018 Nexcess.net, LLC
 */

declare(strict_types  = 1);

namespace Nexcess\Sdk\Model;

use Nexcess\Sdk\ {
  Model\Collector as Collection,
  Model\ServiceModel,
  Model\SshKey
};

/**
 * Cloud Server (virtual machine).
 */
class CloudServer extends ServiceModel {

  const PROPERTY_COLLAPSED = [
    'cloud' => 'cloud_id',
    'package' => 'package_id',
    'ssh_keys' => 'ssh_key_ids',
    'template' => 'template_id'
  ];

  /** {@inheritDoc} */
  const PROPERTY_NAMES = [
    'service_id',
    'cloud',
    'hostname',
    'package',
    'ssh_keys',
    'template'
  ];
}
