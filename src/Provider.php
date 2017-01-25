<?php

namespace Soap;

use RuntimeException;
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
        $pimple['soap_pool_threads'] = 1;
        $pimple['soap_pool_url'] = '';
        $pimple['soap_pool_config'] = array();

        $pimple['soap_pool_factory'] = $pimple->factory(function ($pimple) {
            if (empty($pimple['soap_pool_url'])) {
                throw new RuntimeException('The WSDL URL must be set');
            }

            $mandatoryConfig = array('trace' => false, 'exceptions' => false);
            $config = $pimple['soap_pool_config'] + $mandatoryConfig;
            $paramsToWorker = array($pimple['soap_pool_url'], $config);

            return new Pool(
                $pimple['soap_pool_threads'],
                Worker::class,
                $paramsToWorker
            );
        });

        $pimple['soap_thread_factory'] = $pimple->factory(function ($pimple) {
            return function ($functionName, $params) {
                return new Thread($functionName, $params);
            };
        });
    }
}
