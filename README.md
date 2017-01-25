# SOAP threaded

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

// Here we collect all soap results and put in array
$soapResults = array();
while ($soapPool->collect(function(Soap\Thread $thread) use (&$soapResults) {
        if ($thread->isGarbage()) {
            $soapResults[] = $thread->soapResult;
        }
        return $thread->isGarbage();
    })) {
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

// Here we collect all soap results and put in array
$soapResults = array();
while ($pool->collect(function($thread) use (&$soapResults) {
        if ($thread->isGarbage()) {
            $soapResults[] = $thread->soapResult;
        }
        return $thread->isGarbage();
    })) {
    continue;
};

$pool->shutdown();

var_dump($soapResults);
```
