# SOAP threaded

[![Average time to resolve an issue](http://isitmaintained.com/badge/resolution/paulobezerr/php_soap_pthreads.svg)](http://isitmaintained.com/project/paulobezerr/php_soap_pthreads "Average time to resolve an issue")
[![Percentage of issues still open](http://isitmaintained.com/badge/open/paulobezerr/php_soap_pthreads.svg)](http://isitmaintained.com/project/paulobezerr/php_soap_pthreads "Percentage of issues still open")

This is a very simple library that facilitates the SOAP threaded requests.  
The code can be used as an example of pthread usage or a real implementation of SOAP requests in separated threads.

## Requirements

- PHP7+
- Pthreads 3.1.6

## Examples

### With pimple and Pool

```php
<?php

$pimple = new Pimple\Container();
$pimple->register(new Soap\Provider);

// Configure and get instance of Pool.
$pimple['soap_pool_threads'] = 2;
$pimple['soap_pool_url'] = 'http://SOAP_URL?wsdl';
$soapPool = $pimple['soap_pool_factory'];

$elements = array(
    array(
        'SOAP_FUNCTION' => 'soap_fuction_name',
        'SOAP_PARAMS' => array('soap', 'function', 'params')
    ),
    array(
        'SOAP_FUNCTION' => 'soap_fuction_name',
        'SOAP_PARAMS' => array('soap', 'function', 'params')
    )
);

foreach ($elements as $item) {
    $soapThreadFunc = $pimple['soap_thread_factory'];
    $soapPool->submit(
        $soapThreadFunc($item['SOAP_FUNCTION'], $item['SOAP_PARAMS'])
    );
}

$workerCount = count($elements);

// Here we collect all soap results and put in array
$soapResults = array();
while ($soapPool->collect(function(Soap\Thread $thread) use (&$soapResults) {
        if ($thread->isGarbage()) {
            $soapResults[] = $thread->soapResult;
        }
        return $thread->isGarbage();
    }) || count($soapResults) < $workerCount) {
    continue;
};

$soapPool->shutdown();

var_dump($soapResults);
```

### Without pimple with Pool

```php
<?php

use Soap;
use Pool;

$numberOfThreads = 2;
$workerParams = array(
    'http://SOAP_URL?wsdl',
    array('trace' => false, 'exceptions' => false) // Or anything that you need
);

$elements = array(
    array(
        'SOAP_FUNCTION' => 'soap_fuction_name',
        'SOAP_PARAMS' => array('soap', 'function', 'params')
    ),
    array(
        'SOAP_FUNCTION' => 'soap_fuction_name',
        'SOAP_PARAMS' => array('soap', 'function', 'params')
    )
);

$pool = new Pool($numberOfThreads, Worker::class, $workerParams);

foreach ($elements as $item) {
    $pool->submit(new Thread(
        $item['SOAP_FUNCTION'],
        $item['SOAP_PARAMS']
    ));
}

$workerCount = count($elements);

// Here we collect all soap results and put in array
$soapResults = array();
while ($pool->collect(function($thread) use (&$soapResults) {
        if ($thread->isGarbage()) {
            $soapResults[] = $thread->soapResult;
        }
        return $thread->isGarbage();
    }) || count($soapResults) < $workerCount) {
    continue;
};

$pool->shutdown();

var_dump($soapResults);
```
