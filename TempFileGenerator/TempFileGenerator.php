<?php

namespace Doppy\UtilBundle\TempFileGenerator;

use Doppy\UtilBundle\Exception\TempFileGenerationException;
use Doppy\UtilBundle\Listener\TempFileCleanupListener;

class TempFileGenerator
{
    /**
     * @var TempFileCleanupListener
     */
    protected $cleanupListener;

    /**
     * InternetLocator constructor.
     *
     * @param TempFileCleanupListener $cleanupListener
     */
    public function __construct(TempFileCleanupListener $cleanupListener)
    {
        $this->cleanupListener = $cleanupListener;
    }

    /**
     * Returns the path for a tempfile. The system temp dir will be used as base location.
     *
     * @param string $prefix           prefix of the filename
     * @param bool   $removeOnShutDown set to true if the file is to be delete on shutdown
     *
     * @return string
     */
    public function getTempFileName($prefix = '', $removeOnShutDown = true)
    {
        // attempt to generate tempfile
        $tempFile = tempnam(sys_get_temp_dir(), $prefix);

        // check if that worked
        if ($tempFile === false) {
            throw new TempFileGenerationException('Unable to generate tempfile');
        }

        // maybe remove it on terminate
        if ($removeOnShutDown) {
            $this->cleanupListener->addFile($tempFile);
        }

        // return result
        return $tempFile;
    }
}