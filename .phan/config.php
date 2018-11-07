<?php

return [
  'directory_list' => ['src', 'vendor'],
  'exclude_analysis_directory_list' => ['vendor'],
  'suppress_issue_types' => [
    //// https://github.com/phan/phan/issues/1143
    //'PhanUnanalyzable',
    //// https://github.com/phan/phan/issues/2123
    //'PhanTypeInvalidDimOffset'


    'PhanAccessMethodInternal',
    'PhanCommentParamOnEmptyParamList',
    'PhanCommentParamWithoutRealParam',
    'PhanParamTooMany',
    'PhanTypeInvalidDimOffset',
    'PhanTypeMagicVoidWithReturn',
    'PhanTypeMismatchArgument',
    'PhanTypeMismatchDeclaredParam',
    'PhanTypeMismatchDeclaredParamNullable',
    'PhanTypeMismatchDeclaredReturn',
    'PhanTypeMismatchProperty',
    'PhanTypeMismatchReturn',
    'PhanUndeclaredClass',
    'PhanUndeclaredClassCatch',
    'PhanUndeclaredClassConstant',
    'PhanUndeclaredClassMethod',
    'PhanUndeclaredConstant',
    'PhanUndeclaredMethod',
    'PhanUndeclaredProperty',
    'PhanUndeclaredTypeParameter',
    'PhanUndeclaredTypeReturnType',
    'PhanUndeclaredTypeThrowsType',
    'PhanUndeclaredVariable',
    'PhanUnextractableAnnotationElementName',
    'PhanUnreferencedUseFunction',
    'PhanUnreferencedUseNormal'
  ]
];
