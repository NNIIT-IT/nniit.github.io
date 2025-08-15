<?php
return array (
  'cache' => 
  array (
    'value' => 
    array (
      'type' => 
      array (
        'class_name' => '\\Bitrix\\Main\\Data\\CacheEngineRedis',
        'extension' => 'redis',
      ),
      'redis' => 
      array (
        'host' => '127.0.0.1',
        'port' => '6379',
      ),
      'sid' => '/var/www/html#ho',
    ),
  ),
  'cookies' => 
  array (
    'value' => 
    array (
      'secure' => false,
      'http_only' => true,
    ),
    'readonly' => false,
  ),
  'exception_handling' => 
  array (
    'value' => 
    array (
      'debug' => true,
      'handled_errors_types' => 29687,
      'exception_errors_types' => 20853,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => 256,
      'log' => 
      array (
        'settings' => 
        array (
          'file' => 'bitrix/modules/error.log',
          'log_size' => 1000000,
        ),
      ),
    ),
    'readonly' => false,
  ),
  'connections' => 
  array (
    'value' => 
    array (
      'default' => 
      array (
        'host' => 'localhost',
        'database' => '******',
        'login' => '******',
        'password' => '******',
        'options' => 2.0,
        'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
      ),
    ),
    'readonly' => true,
  ),
  'crypto' => 
  array (
    'value' => 
    array (
      'crypto_key' => 'a6248eec47a2ac9ab795489cebe17363',
    ),
    'readonly' => true,
  ),
);
