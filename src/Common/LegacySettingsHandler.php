<?php

namespace Kaliop\eZP5UI\Common;

use Symfony\Component\Console\Output\OutputInterface;

class LegacySettingsHandler extends Handler
{
    protected $baseDir;
    protected $targetDir;

    /**
     * @param string $baseDir the folder where settings are stored.
     *                        Structure: <baseDir>
     *                                   |- common
     *                                   | |- override
     *                                   |   |- *.ini
     *                                   | |- siteaccess
     *                                   |   |- <name>
     *                                   |     |- *.ini
     *                                   |- <env>
     *                                     |- override
     *                                     | |- *.ini
     *                                     |- siteaccess
     *                                       |- <name>
     *                                         |- *.ini
     *
     * @param string $targetDir the 'settings' folder, as in .../ezpublish_legacy/settings
     * @param OutputInterface $outputInterface
     */
    public function __construct($baseDir, $targetDir, OutputInterface $outputInterface = null)
    {
        $this->baseDir = $baseDir;
        $this->targetDir = $targetDir;

        $this->setOutputInterface($outputInterface);
    }

    public function install($env, $doCleanup = false, $relative = false)
    {

        if ($doCleanup) {
            $this->cleanUpTarget();
        }

        // 'common' settings will be overtaken by per-env ones if they do exist
        foreach(array('common', $env) as $env) {
            $baseDir = $this->baseDir . '/' . $env;

            if (is_dir($baseDir . '/override')) {
                $this->symLinkSettingsInDir($baseDir . '/override', 'override', false, $relative);
            }

            foreach(glob($baseDir . '/siteaccess/*',  GLOB_ONLYDIR) as $siteaccess) {
                $this->symLinkSettingsInDir($siteaccess, 'siteaccess', false, $relative);
            }
        }
    }

    public function cleanUpTarget()
    {
        $fs = new Filesystem();
        $this->writeln("Cleaning dir '".$this->targetDir . "/override/'");
        $fs->remove(glob($this->targetDir . '/override/*'));
        $this->writeln("Cleaning dir '".$this->targetDir . "/siteaccess/'");
        $fs->remove(glob($this->targetDir . '/siteaccess/*'));
    }

    /**
     * @param string $dir
     * @param string $type 'override' or other
     * @param bool $doOverwrite
     * @param bool $relative
     * @throws IOException
     *
     * @todo check if this works in case the target exists and is not a symlink
     */
    protected function symLinkSettingsInDir($dir, $type, $doOverwrite = false, $relative = false)
    {
        $fs = new Filesystem();

        foreach(array_filter(glob($dir . '/*'), 'is_file') as $file) {
            if ($type == 'override') {
                $target = $this->targetDir . '/override/' . basename($file);
            } else {
                $target = $this->targetDir . '/siteaccess/' . basename(dirname($file)) . '/' . basename($file);
            }

            // this is all handled by $fs->atomicSymlink()...
            /*if(!is_dir(dirname($target))) {
                $fs->mkdir(dirname($target));
            } else if(is_file($target)) {
                $this->info("Removing file: '$file'");
                $fs->remove($target);
            }*/

            $source = realpath($file);

            if ($relative) {
                $relativeDir = $fs->makePathRelative(dirname($source), dirname($target));
                $basename = basename($source);
                $source = $relativeDir . $basename;
            }

            $this->writeln("Symlinking '$source' to '$target'");

            $fs->atomicSymlink($source, $target, true, $doOverwrite);
        }
    }
}