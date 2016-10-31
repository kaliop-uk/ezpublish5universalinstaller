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

    /**
     * NB: assumes the standard eZPublish vcl by default
     *
     * @param string $env
     * @param string $yamlKey
     * @param string $yamlFile
     * @param string $banHeader
     * @throws \Exception
     */
    public function purge(
        $env,
        $yamlKey = 'ezpublish.system.default.http_cache.purge_servers',
        $yamlFile = 'ezpublish/config/ezpublish_{ENV}.yml',
        $banHeader = "X-Match: .*"
    )
    {
        $configFile = str_replace(array('{ENV}'), array($env), $yamlFile);

        $servers = $this->getKeyFromFile($yamlKey, $configFile);
        if (!is_array($servers)) {
            throw new \Exception("Yaml config '$yamlKey' in file '$configFile' is not an array");
        }

        foreach ($servers as $url) {
            $this->purgeServer($url, $banHeader);
        }
    }

    public function purgeServer($url, $banHeader = "X-Match: .*")
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "BAN");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($banHeader));

        curl_exec($curl);

        // depending on how varnish is set up, we might get different responses. So do not trigger exceptions

        /*if ($ok) {
            $this->writeln("Varnish server '$server' flushed");
        } else {
            throw new \Exception("Flush of varnishserver '$server' failed!");
        }*/
    }
}
