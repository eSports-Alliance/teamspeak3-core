<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   TeamSpeak
 * @author    Sven 'ScP' Paulsen
 * @copyright Copyright (c) Planet TeamSpeak. All rights reserved.
 */

namespace ESportsAlliance\TeamSpeakCore\Helper\Profiler;

/**
 * Class Timer
 * @package ESportsAlliance\TeamSpeakCore\Helper\Profiler
 * @class Timer
 * @brief Helper class providing profiler timers.
 */
class Timer
{
    /**
     * Indicates wether the timer is running or not.
     *
     * @var boolean
     */
    protected $running = false;

    /**
     * Stores the timestamp when the timer was last started.
     *
     * @var integer
     */
    protected $started = 0;

    /**
     * Stores the timer name.
     *
     * @var string
     */
    protected $name = null;

    /**
     * Stores various information about the server environment.
     *
     * @var array
     */
    protected $data = [];

    /**
     * Timer constructor.
     *
     * @param  string $name
     */
    public function __construct($name)
    {
        $this->name = (string) $name;

        $this->data["runtime"] = 0;
        $this->data["realmem"] = 0;
        $this->data["emalloc"] = 0;

        $this->start();
    }

    /**
     * Starts the timer.
     *
     * @return void
     */
    public function start()
    {
        if ($this->isRunning()) {
            return;
        }

        $this->data["realmem_start"] = memory_get_usage(true);
        $this->data["emalloc_start"] = memory_get_usage();

        $this->started = microtime(true);
        $this->running = true;
    }

    /**
     * Stops the timer.
     *
     * @return void
     */
    public function stop()
    {
        if (!$this->isRunning()) {
            return;
        }

        $this->data["runtime"] += microtime(true) - $this->started;
        $this->data["realmem"] += memory_get_usage(true) - $this->data["realmem_start"];
        $this->data["emalloc"] += memory_get_usage() - $this->data["emalloc_start"];

        $this->started = 0;
        $this->running = false;
    }

    /**
     * Return the timer runtime.
     *
     * @return mixed
     */
    public function getRuntime()
    {
        if ($this->isRunning()) {
            $this->stop();
            $this->start();
        }

        return $this->data["runtime"];
    }

    /**
     * Returns the amount of memory allocated to PHP in bytes.
     *
     * @param  boolean $realmem
     * @return integer
     */
    public function getMemUsage($realmem = false)
    {
        if ($this->isRunning()) {
            $this->stop();
            $this->start();
        }

        return ($realmem !== false) ? $this->data["realmem"] : $this->data["emalloc"];
    }

    /**
     * Returns TRUE if the timer is running.
     *
     * @return boolean
     */
    public function isRunning()
    {
        return $this->running;
    }
}
