<?php

namespace Soap;

use SoapClient;
use Pool;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * @see \Cron\Provider
 */
class Provider implements ServiceProviderInterface
{
    /**
     * All classes of this namespace must be registered here.
     *
     * @param Container $pimple
     *
     * @return VOID
     */
    public function register(Container $pimple)
    {
        $pimple['soap_pool'] = $pimple->factory(function ($pimple) {
            $numberOfThreads = $pimple['config']['wsdl']['threads'];
            $paramsToWorker = array(
                $pimple['config']['wsdl']['provisioning'],
                array('trace' => false, 'exceptions' => false)
            );

            return new Pool(
                $numberOfThreads,
                SoapWorker::class,
                $paramsToWorker
            );
        });

        $pimple['soap_thread'] = $pimple->factory(function ($pimple) {
            return function ($functionName, $params) {
                return new Thread($functionName, $params);
            };
        });
    }
}
