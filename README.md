# SOAP threaded

This is a very simple project that facilitates the SOAP threaded requests.  
The code can be used as an example or a real implementation of SOAP requests in separated threads.

## Requirements

- PHP7+
- Pthreads 3.1.6

## Examples

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
        if ($this->isGarbage()) {
            $soapResults[] = $thread->soapResult;
        }
        return $this->isGarbage();
    })) {
    continue;
};

$pool->shutdown();

var_dump($soapResults);
```
