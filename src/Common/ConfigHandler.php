<?php

namespace Kaliop\eZP5UI\Common;

use Symfony\Component\Console\Output\OutputInterface;
use Memcached;

class ConfigHandler extends YamlParsingHandler
{
    public function get($env, $key, $fileName, $delimiter='.')
    {
        $configFile = str_replace(array('{ENV}'), array($env), $fileName);

        return $this->getKeyFromFile($key, $configFile, $delimiter);
    }
}