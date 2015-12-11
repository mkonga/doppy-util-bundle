<?php

namespace Doppy\UtilBundle\TempFileGenerator;

use Doppy\UtilBundle\Exception\TempFileGenerationException;
use Doppy\UtilBundle\Subscriber\TempFileCleanupSubscriber;

class TempFileGenerator
{
    /**
     * @var TempFileCleanupSubscriber
     */
    protected $cleanupSubscriber;

    /**
     * InternetLocator constructor.
     *
     * @param TempFileCleanupSubscriber $cleanupSubscriber
     */
    public function __construct(TempFileCleanupSubscriber $cleanupSubscriber)
    {
        $this->cleanupSubscriber = $cleanupSubscriber;
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
            $this->cleanupSubscriber->addFile($tempFile);
        }

        // return result
        return $tempFile;
    }
}