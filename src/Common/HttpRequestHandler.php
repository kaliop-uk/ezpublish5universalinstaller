<?php

namespace Kaliop\eZP5UI\Common;

use Symfony\Component\Console\Output\OutputInterface;

class HttpRequestHandler extends YamlParsingHandler
{
    /**
     * @param OutputInterface $outputInterface
     */
    public function __construct(OutputInterface $outputInterface = null)
    {
        $this->setOutputInterface($outputInterface);
    }

    public function execute($env, $yamlKey, $yamlFile = 'ezpublish/config/parameters_{ENV}.yml')
    {
        $configFile = str_replace(array('{ENV}'), array($env), $yamlFile);
        $urls = $this->getKeyFromFile($yamlKey, $configFile);

        if (is_string($urls)) {
            $urls = array($urls);
        } else if (!is_array($urls)) {
            throw new \Exception("Yaml config '$yamlKey' in file '$configFile' is not an array");
        }

        foreach ($urls as $url) {
            $curl = curl_init($url);
            curl_exec($curl);
        }
    }
}
