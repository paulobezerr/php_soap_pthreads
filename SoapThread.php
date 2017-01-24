<?php

namespace Soap;

use Thread;

/**
 * This is the *Work* which would be ran by the worker.
 * The work which you'd want to do in your worker.
 * This class needs to extend the \Threaded or \Collectable or \Thread class.
 */
class SoapThread extends Thread
{
    /**
     * The SOAP function name that will be called
     * @var string
     */
    private $soapFunction;

    /**
     * The SOAP function params to be injected in calling time
     * @var array
     */
    private $soapParams;

    /**
     * Execution result
     * @var stdClass
     */
    private $soapResult;

    /**
     * Error code and message when occours
     * @var array
     */
    private $soapFault;

    /**
     * The block of code in the constructor of your work,
     * would be executed when a work is submitted to your pool.
     *
     * @param string $functionName SOAP function name
     * @param array  $params       Params to be passed to SOAP function
     */
    public function __construct(string $functionName, array $params = array())
    {
        $this->soapFunction = $functionName;
        $this->soapParams   = $params;
    }

    /**
     * This block of code in, the method, run
     * would be called by your worker.
     * All the code in this method will be executed in another thread.
     */
    public function run()
    {
        try {
            $soapClient = $this->worker->getSoap();
            $soapResult = $soapClient->__soapCall(
                $this->soapFunction,
                $this->soapParams
            );
            $this->setSoapResult($soapResult);
        } catch (Exception $e) {
            printf("\tError in execute function: %s", $e->getMessage());
            return;
        }
    }

    private function setSoapResult($result)
    {
        if ($result instanceof SoapFault) {
            $result = array(
                'error' => $result->faultcode,
                'message' => $result->faultstring
            );
        }

        $this->soapResult = $result;
    }

    public function getSoapResult()
    {
        if (!is_null($this->soapFault)) {
            return $this->soapFault;
        }
        return $this->soapResult;
    }
}
