<?php

return [
  'directory_list' => ['src', 'vendor'],
  'exclude_analysis_directory_list' => ['vendor'],
  'suppress_issue_types' => [
    //// https://github.com/phan/phan/issues/1143
    //'PhanUnanalyzable',

    // https://github.com/phan/phan/issues/2123
    'PhanTypeInvalidDimOffset',
    // we're using "@internal" to mean "within this repo"
    // phan treats it as "within same namespace" for some reason
    'PhanAccessMethodInternal',

    // @todo WTF
    // "$response is \Psr\Http\Message\MessageInterface|ResponseInterface"
    //  // NO, it's not
    // "but Util::decodeResponse() takes \GuzzleHttp\Psr7\Response"
    //  // YES, and that's *exactly* what $response is
    'PhanTypeMismatchArgument',
  ]
];
