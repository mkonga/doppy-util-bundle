<?php

namespace Doppy\UtilBundle\Stopwatch;

use Symfony\Component\Stopwatch\Stopwatch;

class NullStopwatch extends Stopwatch {

    public function openSection($id = null)
    {
        // no action
    }

    public function stopSection($id)
    {
        // no action
    }

    public function start($name, $category = null)
    {
        // no action
    }

    public function isStarted($name)
    {
        return false;
    }

    public function stop($name)
    {
        // no action
    }

    public function lap($name)
    {
        // no action
    }

    public function getEvent($name)
    {
        return null;
    }

    public function getSectionEvents($id)
    {
        return array();
    }
}
