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

namespace ESportsAlliance\TeamSpeakCore\Adapter;

use ESportsAlliance\TeamSpeakCore\Exception\AdapterException;
use ESportsAlliance\TeamSpeakCore\Helper\Profiler;
use ESportsAlliance\TeamSpeakCore\Helper\Profiler\Timer;
use ESportsAlliance\TeamSpeakCore\Transport\TCP;
use ESportsAlliance\TeamSpeakCore\Transport\Transport;

/**
 * @class ESportsAlliance\TeamSpeakCore\Adapter\Adapter
 * @brief Provides low-level methods for concrete adapters to communicate with a TeamSpeak 3 Server.
 */
abstract class Adapter
{
    /**
     * Stores user-provided options.
     *
     * @var array
     */
    protected $options = null;

    /**
     * Stores an ESportsAlliance\TeamSpeakCore\Transport\Transport object.
     *
     * @var Transport
     */
    protected $transport = null;

    /**
     * The ESportsAlliance\TeamSpeakCore\Adapter\Adapter constructor.
     *
     * @param  array $options
     * @throws AdapterException
     */
    public function __construct(array $options)
    {
        $this->options = $options;

        if ($this->transport === null) {
            $this->syn();
        }
    }

    /**
     * The ESportsAlliance\TeamSpeakCore\Adapter\Adapter destructor.
     *
     * @return void
     */
    abstract public function __destruct();

    /**
     * Connects the ESportsAlliance\TeamSpeakCore\Transport\Transport object and performs initial actions on the remote
     * server.
     *
     * @throws AdapterException
     * @return void
     */
    abstract protected function syn();

    /**
     * Commit pending data.
     *
     * @return array
     */
    public function __sleep()
    {
        return ["options"];
    }

    /**
     * Reconnects to the remote server.
     *
     * @return void
     * @throws AdapterException
     */
    public function __wakeup()
    {
        $this->syn();
    }

    /**
     * Returns the profiler timer used for this connection adapter.
     *
     * @return Timer
     */
    public function getProfiler()
    {
        return Profiler::get(spl_object_hash($this));
    }

    /**
     * Returns the transport object used for this connection adapter.
     *
     * @return Transport
     */
    public function getTransport()
    {
        return $this->transport;
    }

    /**
     * Loads the transport object object used for the connection adapter and passes a given set
     * of options.
     *
     * @param  array  $options
     * @param  string $transport
     * @throws AdapterException
     * @return void
     */
    protected function initTransport($options, $transport = TCP::class)
    {
        if (!is_array($options)) {
            throw new AdapterException("transport parameters must provided in an array");
        }

        $this->transport = new $transport($options);
    }

    /**
     * Returns the hostname or IPv4 address the underlying ESportsAlliance\TeamSpeakCore\Transport\Transport object
     * is connected to.
     *
     * @return string
     */
    public function getTransportHost()
    {
        return $this->getTransport()->getConfig("host", "0.0.0.0");
    }

    /**
     * Returns the port number of the server the underlying ESportsAlliance\TeamSpeakCore\Transport\Transport object
     * is connected to.
     *
     * @return string
     */
    public function getTransportPort()
    {
        return $this->getTransport()->getConfig("port", "0");
    }
}
