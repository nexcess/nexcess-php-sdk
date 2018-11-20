<?php

return [
  'directory_list' => ['src', 'vendor'],
  'exclude_analysis_directory_list' => ['vendor'],
  'suppress_issue_types' => [
    // https://github.com/phan/phan/issues/2123
    'PhanTypeInvalidDimOffset',
    // we're using "@internal" to mean "within this repo"
    // phan treats it as "within same namespace" for some reason
    'PhanAccessMethodInternal'
  ]
];
