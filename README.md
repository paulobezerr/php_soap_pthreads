# SOAP threaded

This is a very simple project that facilitates the SOAP threaded requests.  
The code can be used as an example or a real implementation of SOAP requests in separated threads.

## Requirements

- PHP7+
- Pthreads 3.1.6

## Examples

Example of usage using pool:

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

$pool = new Pool($numberOfThreads, Soap\SoapWorker::class, $workerParams);

foreach ($elements as $item) {
    $pool->submit(new Soap\SoapThread(
        $item['SOAP_FUNCTION'],
        $item['SOAP_PARAMS']
    ));
}

$pool->shutdown();
```
