<?php return array (
  'package' => 
  array (
    'type' => 'module',
    'name' => 'city',
    'version' => '4.0.0',
    'sku' => 'city',
    'path' => 'application/modules/City',
    'title' => 'City',
    'description' => '',
    'author' => '',
    'callback' => 
    array (
      'class' => 'Engine_Package_Installer_Module',
    ),
    'actions' => 
    array (
      0 => 'install',
      1 => 'upgrade',
      2 => 'refresh',
      3 => 'enable',
      4 => 'disable',
    ),
    'directories' => 
    array (
      0 => 'application/modules/City',
    ),
    'files' => 
    array (
      0 => 'application/languages/en/city.csv',
    ),
  ),
); ?>