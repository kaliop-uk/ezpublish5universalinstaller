<?php

namespace Kaliop\eZP5UI\Common;

use Symfony\Component\Console\Output\OutputInterface;
use Memcached;

class MemcacheHandler extends YamlParsingHandler
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
        $yamlKey = 'stash.caches.default.Memcache.servers',
        $yamlFile = 'ezpublish/config/ezpublish_{ENV}.yml'
    )
    {
        $configFile = str_replace(array('{ENV}'), array($env), $yamlFile);

        $servers = $this->getKeyFromFile($yamlKey, $configFile);
        if (!is_array($servers)) {
            throw new \Exception("Yaml config '$yamlKey' in file '$configFile' is not an array");
        }

        foreach ($servers as $server) {

            if (!isset($server['server'])) {
                throw new \Exception("Memcache 'server' config missing");
            }

            $serverName = $server['server'];
            $serverPort = isset($server['port']) ? $server['port'] : 11211;

            $memcache_obj = new Memcached;
            $memcache_obj->addserver($serverName, $serverPort);
            $ok = $memcache_obj->flush();

            if ($ok) {
                $this->writeln("Memcache server '$serverName:$serverPort' flushed");
            } else {
                throw new \Exception("Flush of memcache server '$serverName:$serverPort' failed!");
            }
        }
    }
}
