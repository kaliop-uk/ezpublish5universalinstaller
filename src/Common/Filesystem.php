<?php

namespace Kaliop\eZP5UI\Common;

use Symfony\Component\Filesystem\Filesystem as BaseFilesystem;
use Symfony\Component\Filesystem\Exception\IOException;

class Filesystem extends BaseFilesystem
{
    /**
     * Extends the parent's class Symlink call to make file operations atomic via usage of 'symlink+rename' in case
     * the target file/directory is already a symlink or even a file (original one will be deleted!).
     *
     * NB: will not re
     *
     * @todo extend the atomicity also to the copyOnwindows case
     *
     * @param $originDir existing dir/file
     * @param $targetDir name of the symlink to create
     * @param bool $copyOnWindows
     * @param bool $replaceFiles when true, existing target files will be replaced. By default, an existing target file
     *                           (not symlink) will abort the process
     */
    public function atomicSymlink($originDir, $targetDir, $copyOnWindows = false, $replaceFiles = false)
    {
        if ('\\' === DIRECTORY_SEPARATOR && $copyOnWindows) {
            $this->mirror($originDir, $targetDir);

            return;
        }

        $this->mkdir(dirname($targetDir));

        $ok = false;
        $toFinish = false;
        if (is_link($targetDir) || ($replaceFiles && is_file($targetDir))) {
            if (is_link($targetDir) && readlink($targetDir) == $originDir) {
                $ok = true;
            } else {
                $realTargetDir = $targetDir;
                $targetDir = $targetDir . '.' . str_replace(' ', '_', microtime());
                $toFinish = true;
            }
        }

        if (!$ok && true !== @symlink($originDir, $targetDir)) {
            $report = error_get_last();
            if (is_array($report)) {
                if ('\\' === DIRECTORY_SEPARATOR && false !== strpos($report['message'], 'error code(1314)')) {
                    throw new IOException('Unable to create symlink due to error code 1314: \'A required privilege is not held by the client\'. Do you have the required Administrator-rights?');
                }
                throw new IOException(sprintf('Failed to create symbolic link from "%s" to "%s".', $originDir, $targetDir), 0, null, $targetDir);
            }
            throw new IOException(sprintf('Failed to create symbolic link from %s to %s', $originDir, $targetDir));
        }

        if ($toFinish) {
            $this->remove($realTargetDir);
            $this->rename($targetDir, $realTargetDir);
        }
    }
}