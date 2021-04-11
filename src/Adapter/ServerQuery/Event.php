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

namespace ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery;

use ArrayAccess;
use ESportsAlliance\TeamSpeakCore\Exception\AdapterException;
use ESportsAlliance\TeamSpeakCore\Exception\NodeException;
use ESportsAlliance\TeamSpeakCore\Exception\ServerQueryException;
use ESportsAlliance\TeamSpeakCore\Helper\Signal;
use ESportsAlliance\TeamSpeakCore\Helper\StringHelper;
use ESportsAlliance\TeamSpeakCore\Node\Host;
use ESportsAlliance\TeamSpeakCore\TeamSpeak;
/**
 * Class Event
 * @package ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery
 * @class Event
 * @brief Provides methods to analyze and format a ServerQuery event.
 */
class Event implements ArrayAccess
{
    /**
     * Stores the event type.
     *
     * @var StringHelper
     */
    protected $type = null;

    /**
     * Stores the event data.
     *
     * @var array
     */
    protected $data = null;

    /**
     * Stores the event data as an unparsed string.
     *
     * @var StringHelper
     */
    protected $mesg = null;

    /**
     * Creates a new ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery\Event object.
     *
     * @param  StringHelper $evt
     * @param  Host     $con
     * @throws AdapterException
     */
    public function __construct(StringHelper $evt, Host $con = null)
    {
        if (!$evt->startsWith(TeamSpeak::EVENT)) {
            throw new AdapterException("invalid notification event format");
        }

        list($type, $data) = $evt->split(TeamSpeak::SEPARATOR_CELL, 2);

        if (empty($data)) {
            throw new AdapterException("invalid notification event data");
        }

        $fake = new StringHelper(TeamSpeak::ERROR . TeamSpeak::SEPARATOR_CELL . "id" . TeamSpeak::SEPARATOR_PAIR . 0 . TeamSpeak::SEPARATOR_CELL . "msg" . TeamSpeak::SEPARATOR_PAIR . "ok");
        $repl = new Reply([$data, $fake], $type);

        $this->type = $type->substr(strlen(TeamSpeak::EVENT));
        $this->data = $repl->toList();
        $this->mesg = $data;

        Signal::getInstance()->emit("notifyEvent", $this, $con);
        Signal::getInstance()->emit("notify" . ucfirst($this->type), $this, $con);
    }

    /**
     * Returns the event type string.
     *
     * @return StringHelper
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the event data array.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the event data as an unparsed string.
     *
     * @return StringHelper
     */
    public function getMessage()
    {
        return $this->mesg;
    }

    /**
     * @ignore
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->data) ? true : false;
    }

    /**
     * @throws ServerQueryException
     * @ignore
     */
    public function offsetGet($offset)
    {
        if (!$this->offsetExists($offset)) {
            throw new ServerQueryException("invalid parameter", 0x602);
        }

        return $this->data[$offset];
    }

    /**
     * @throws NodeException
     * @ignore
     */
    public function offsetSet($offset, $value)
    {
        throw new NodeException("event '" . $this->getType() . "' is read only");
    }

    /**
     * @ignore
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * @throws ServerQueryException
     * @ignore
     */
    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    /**
     * @throws NodeException
     * @ignore
     */
    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }
}
