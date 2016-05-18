<?php

namespace Kaliop\eZP5UI\Common;

use Symfony\Component\Console\Output\OutputInterface;

class VarnishHandler extends YamlParsingHandler
{
    /**
     * @param OutputInterface $outputInterface
     */
    public function __construct(OutputInterface $outputInterface = null)
    {
        $this->setOutputInterface($outputInterface);
    }

    public function purge(
        $env,
        $yamlKey = 'ezpublish.system.default.http_cache.purge_servers',
        $yamlFile = 'ezpublish/config/ezpublish_{ENV}.yml'
    )
    {
        $configFile = str_replace(array('{ENV}'), array($env), $yamlFile);

        $servers = $this->getKeyFromFile($yamlKey, $configFile);
        if (!is_array($servers)) {
            throw new \Exception("Yaml config '$yamlKey' in file '$configFile' is not an array");
        }

        foreach ($servers as $server) {

            $curl = curl_init($server);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "BAN");
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Location-Id: *"));

            curl_exec($curl);

            // depending on how varnish is set up, we might get different responses. So do not trigger exceptions

            /*if ($ok) {
                $this->writeln("Varnish server '$server' flushed");
            } else {
                throw new \Exception("Flush of varnishserver '$server' failed!");
            }*/
        }
    }
}
