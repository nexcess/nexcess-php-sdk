<?php

use Nexcess\Sdk\ {
  Resource\App\App,
  Resource\Cloud\Cloud,
  Resource\CloudAccount\CloudAccount,
  Resource\Collection,
  Resource\Invoice\Invoice,
  Resource\Package\Package,
  Resource\Order\Order,
  Resource\VirtGuestCloud\VirtGuestCloud
};

return [
  'id' => 1,
  'client_id' => 1,
  'cloud_id' => 3,
  'identity' => '#1 nc.xsmall-test',
  'invoice' => Invoice::__set_state([
    '_values' => [
      'invoice_id' => 1,
      'identity' => 'Alice McAllison - #1',
      'type' => 'pre-billed',
      'status' => 'paid',
      'description' => 'nc.xsmall-test',
      'full_id' => '1-P',
      'total' => 50,
    ]
  ]),
  'order_date' => 1526046167,
  'package' => Package::__set_state([
    '_values' => [
      'package_id' => 700,
      'identity' => 'nc.xsmall-test',
      'type' => 'virt-guest-cloud',
    ],
  ]),
  'recurring_total' => 50,
  'service' => VirtGuestCloud::__set_state([
    '_values' => [
      'service_id' => 1,
      'identity' => 'nexcess-cli.example.com - nc.xsmall-test',
      'type' => 'virt-guest-cloud',
      'status' => 'enabled',
      'description' => 'nc.xsmall-test',
      'nickname' => '',
      'host' => 'nexcess-cli.example.com',
      'cloud_account' => CloudAccount::__set_state([
        '_values' => [
          'account_id' => 1,
          'identity' => 'nexcess-cli.example.com',
          'ip' => '203.0.113.1',
          'domain' => 'nexcess-cli.example.com',
          'is_dev_account' => false,
          'temp_domain' => 'example.nxcli.net',
          'unix_username' => 'abcd1234',
          'app' => App::__set_state([
            '_values' => [
              'app_id' => 13,
              'identity' => 'Flexible ',
              'type' => 'generic',
              'status' => 'active',
              'name' => 'Flexible',
              'version' => '',
            ],
          ]),
          'environment' => [
            'software' => [
              'php' => [
                'version' => '7.1',
                'path' => '/opt/remi/php71',
                'cli' => '/opt/remi/php71/root/usr/bin/php',
              ],
              'redis' => [
                'host' => '',
                'port' => '',
                'socket' => '/var/run/redis-multi-example.redis/redis.sock',
                'version' => '3.2',
              ],
            ],
            'options' => [
              'nxcache_nocache' => false,
              'nxcache_varnish' => false,
              'nxcache_varnish_static' => false,
              'nxcache_varnish_ttl' => 120,
              'autoscale_enabled' => true,
            ],
          ],
        ],
      ]),
      'location' => Cloud::__set_state([
        '_values' => [
          'cloud_id' => 3,
          'identity' => 'us-midwest-1',
          'type' => 'openstack',
          'status' => 'active',
          'location' => 'Southfield, MI',
          'location_code' => 'us-midwest-1',
          'country' => 'US',
        ],
      ]),
      'order' => Order::__set_state([
        '_values' => [
          'order_id' => 1,
          'identity' => '(1) Alice McAllison',
          'type' => 'virt-guest-cloud',
          'status' => 'completed',
        ],
      ]),
      'dev_account_count' => 0,
      'state' => 'stable',
      'child_cloud_accounts' => Collection::__set_state([
        '_models' => [],
        '_of' => CloudAccount::class,
      ]),
    ],
  ]),
  'setup_fee' => 0,
  'status' => 'completed',
  'total' => 50,
  'type' => 'virt-guest-cloud',
];
