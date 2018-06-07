<?php

use Nexcess\Sdk\ {
  Resource\App\App,
  Resource\Cloud\Cloud,
  Resource\CloudAccount\CloudAccount,
  Resource\Collection,
  Resource\Order\Order,
  Resource\Package\Package
};

return [
  'service_id' => 1,
  'auto_renew' => 'yes',
  'bandwidth' => [
    'used' => [
      'total' => 0,
      'billable' => 0
    ],
    'projected' => [
      'total' => 0,
      'billable' => 0
    ],
    'previous' => [
      'total' => 0,
      'billable' => 0
    ],
    'given' => '100',
    'type' => 'GB',
    'overage_fee' => 0,
    'profile_id' => 2,
    'override' => 0
  ],
  'billing_term' => [
    'type' => 'monthly',
    'months' => 1,
    'years' => 0,
    'description' => 'Monthly',
    'amount' => 50
  ],
  'cancellable_override_expire_date' => 0,
  'can_change_root_password' => false,
  'child_cloud_accounts' => Collection::__set_state([
    '_models' => [],
    '_of' => 'Nexcess\\Sdk\\Resource\\CloudAccount\\CloudAccount'
  ]),
  'client_id' => 1,
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
          'version' => ''
        ],
      ]),
      'environment' => [
        'software' => [
          'php' => [
            'version' => '7.1',
            'path' => '/opt/remi/php71',
            'cli' => '/opt/remi/php71/root/usr/bin/php'
          ],
          'redis' => [
            'host' => '',
            'port' => '',
            'socket' => '/var/run/redis-multi-example.redis/redis.sock',
            'version' => '3.2'
          ]
        ],
        'options' => [
          'nxcache_nocache' => false,
          'nxcache_varnish' => false,
          'nxcache_varnish_static' => false,
          'nxcache_varnish_ttl' => 120,
          'autoscale_enabled' => true
        ]
      ]
    ]
  ]),
  'cloud_id' => 3,
  'description' => 'nc.xsmall-test',
  'dev_account_count' => 0,
  'discount_id' => 0,
  'environment_type' => 'production',
  'has_console' => false,
  'has_paypal_subscription' => false,
  'has_stored_password' => false,
  'host' => 'nexcess-cli.example.com',
  'identity' => 'nexcess-cli.example.com - nc.xsmall-test',
  'is_cancellable' => true,
  'is_rebootable' => false,
  'last_bill_date' => 1528689600,
  'location' => Cloud::__set_state([
    '_values' => [
      'cloud_id' => 3,
      'identity' => 'us-midwest-1',
      'type' => 'openstack',
      'status' => 'active',
      'location' => 'Southfield, MI',
      'location_code' => 'us-midwest-1',
      'country' => 'US'
    ],
  ]),
  'next_bill_date' => 1531281600,
  'nickname' => '',
  'order' => Order::__set_state([
    '_values' => [
      'order_id' => 55119,
      'identity' => '(1) Alice McAllison',
      'type' => 'virt-guest-cloud',
      'status' => 'completed'
    ]
  ]),
  'override_addons' => 'no',
  'override_percent' => 0,
  'override_price' => 50,
  'package' => Package::__set_state([
    '_values' => [
      'package_id' => 700,
      'identity' => 'nc.xsmall-test',
      'type' => 'virt-guest-cloud'
    ]
  ]),
  'parent_id' => 0,
  'paypal_subscribe_link' => 'https://www.sandbox.paypal.com',
  'pricing_type' => 'monthly',
  'settings' => [
    'extranet.disable-networking.enabled' => false
  ],
  'start_date' => 1526046171,
  'state' => 'stable',
  'status' => 'enabled',
  'term' => 1,
  'turnup_date' => 1526046171,
  'type' => 'virt-guest-cloud'
];
