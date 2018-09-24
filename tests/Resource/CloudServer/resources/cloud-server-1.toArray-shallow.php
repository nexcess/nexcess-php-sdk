<?php

use Nexcess\Sdk\ {
  Resource\Cloud\Resource as Cloud,
  Resource\Package\Resource as Package
};

return [
  'id' => 1,
  'bandwidth' => [
    'used' => [
      'total' => 0,
      'billable' => 0,
    ],
    'projected' => [
      'total' => 0,
      'billable' => 0,
    ],
    'previous' => [
      'total' => 0,
      'billable' => 0,
    ],
    'given' => '2048',
    'type' => 'GB',
  ],
  'billing_term' => [
    'type' => 'monthly',
    'months' => 1,
    'years' => 0,
    'description' => 'Monthly',
    'amount' => 10,
  ],
  'client' => null,
  'description' => 'nex1.small',
  'host' => 'nexcess-cli.example.com',
  'identity' => 'nexcess-cli.example.com - 203.0.113.1',
  'last_bill_date' => 0,
  'cloud' => Cloud::__set_state([
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
  'network' => null,
  'next_bill_date' => '09/05/2017 04:00:00 +00:00',
  'os' => [
    'id' => 32,
    'identity' => 'ubuntu 17.04 (x86_64)',
    'is_real' => true,
    'meta' => [
      'scope' => 'operating-system',
    ],
    'distribution' => 'ubuntu',
    'version' => '17.04',
    'arch' => 'x86_64',
  ],
  'package' => Package::__set_state([
    '_values' => [
      'package_id' => 680,
      'identity' => 'n5s.small',
      'type' => 'virt-guest',
      'bandwidth' => 2048,
    ],
  ]),
  'power_status' => 'on',
  'start_date' => '08/05/2017 13:02:15 +00:00',
  'state' => 'stable',
  'status' => 'enabled',
];
