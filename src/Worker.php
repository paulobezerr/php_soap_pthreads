<?php

namespace Soap;

use Worker;
use SoapClient;

/**
 * Create an empty worker for the sake of simplicity.
 */
class SoapWorker extends Worker
{
    private static $soap;

    private $soapUrl;
    private $soapConfig;

    public function __construct(string $soapUrl, array $soapConfig = array())
    {
        $this->soapUrl    = $soapUrl;
        $this->soapConfig = $soapConfig;
    }

    /**
     * You can put some code in here, which would be executed
     * before the Work's are started (the block of code in the `run` method of
     * your Work) by the Worker.
     */
    public function run()
    {
        try {
            $config = (array) $this->soapConfig;
            self::$soap = new SoapClient($this->soapUrl, $config);
        } catch (Exception $e) {
            printf("Error instanciate SOAP: %s\n", $e->getMessage());
        }
    }

    public function getSoap() :SoapClient
    {
        return self::$soap;
    }
}
