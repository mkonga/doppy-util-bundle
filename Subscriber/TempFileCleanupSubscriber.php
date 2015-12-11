<?php

namespace Doppy\UtilBundle\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TempFileCleanupSubscriber implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    protected $files = [];

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::TERMINATE => 'onKernelTerminate'
        );
    }

    /**
     * Adds a temporary file to be deleted at the end of the request
     *
     * @param string|string[] $filename
     */
    public function addFile($filename)
    {
        $this->files[] = $filename;
    }

    /**
     * Removes all temp files at the end of the request
     *
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        if (count($this->files) > 0) {
            $this->removeFiles($this->files);
        }
    }

    /**
     * Removes files that are no longer needed
     *
     * @param string[] $files
     */
    protected function removeFiles($files)
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($files));
        foreach ($iterator as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}