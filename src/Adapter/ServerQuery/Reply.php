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

use ESportsAlliance\TeamSpeakCore\Exception\AdapterException;
use ESportsAlliance\TeamSpeakCore\Exception\ServerQueryException;
use ESportsAlliance\TeamSpeakCore\Helper\Signal;
use ESportsAlliance\TeamSpeakCore\Helper\StringHelper;
use ESportsAlliance\TeamSpeakCore\Node\Host;
use ESportsAlliance\TeamSpeakCore\TeamSpeak;

/**
 * Class Reply
 * @package ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery
 * @class Reply
 * @brief Provides methods to analyze and format a ServerQuery reply.
 */
class Reply
{
    /**
     * Stores the command used to get this reply.
     *
     * @var StringHelper
     */
    protected $cmd = null;

    /**
     * Stores the servers reply (if available).
     *
     * @var StringHelper
     */
    protected $rpl = null;

    /**
     * Stores connected ESportsAlliance\TeamSpeakCore\Node\Host object.
     *
     * @var Host
     */
    protected $con = null;

    /**
     * Stores an assoc array containing the error info for this reply.
     *
     * @var array
     */
    protected $err = [];

    /**
     * Sotres an array of events that occured before or during this reply.
     *
     * @var array
     */
    protected $evt = [];

    /**
     * Indicates whether exceptions should be thrown or not.
     *
     * @var boolean
     */
    protected $exp = true;

    /**
     * Creates a new ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery\Reply object.
     *
     * @param array $rpl
     * @param string $cmd
     * @param Host $con
     * @param boolean $exp
     * @throws AdapterException
     * @throws ServerQueryException
     */
    public function __construct(array $rpl, $cmd = null, Host $con = null, $exp = true)
    {
        $this->cmd = new StringHelper($cmd);
        $this->con = $con;
        $this->exp = (bool) $exp;

        $this->fetchError(array_pop($rpl));
        $this->fetchReply($rpl);
    }

    /**
     * Returns the reply as an ESportsAlliance\TeamSpeakCore\Helper\StringHelper object.
     *
     * @return StringHelper
     */
    public function toString()
    {
        return (!func_num_args()) ? $this->rpl->unescape() : $this->rpl;
    }

    /**
     * Returns the reply as a standard PHP array where each element represents one item.
     *
     * @return array
     */
    public function toLines()
    {
        if (!count($this->rpl)) {
            return [];
        }

        $list = $this->toString(0)->split(TeamSpeak::SEPARATOR_LIST);

        if (!func_num_args()) {
            for ($i = 0; $i < count($list); $i++) {
                $list[$i]->unescape();
            }
        }

        return $list;
    }

    /**
     * Returns the reply as a standard PHP array where each element represents one item in table format.
     *
     * @return array
     */
    public function toTable()
    {
        $table = [];

        foreach ($this->toLines(0) as $cells) {
            $pairs = $cells->split(TeamSpeak::SEPARATOR_CELL);

            if (!func_num_args()) {
                for ($i = 0; $i < count($pairs); $i++) {
                    $pairs[$i]->unescape();
                }
            }

            $table[] = $pairs;
        }

        return $table;
    }

    /**
     * Returns a multi-dimensional array containing the reply splitted in multiple rows and columns.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        $table = $this->toTable(1);

        for ($i = 0; $i < count($table); $i++) {
            foreach ($table[$i] as $pair) {
                if (!count($pair)) {
                    continue;
                }

                if (!$pair->contains(TeamSpeak::SEPARATOR_PAIR)) {
                    $array[$i][$pair->toString()] = null;
                } else {
                    list($ident, $value) = $pair->split(TeamSpeak::SEPARATOR_PAIR, 2);

                    $array[$i][$ident->toString()] = $value->isInt() ? $value->toInt() : (!func_num_args() ? $value->unescape() : $value);
                }
            }
        }

        return $array;
    }

    /**
     * Returns a multi-dimensional assoc array containing the reply splitted in multiple rows and columns.
     * The identifier specified by key will be used while indexing the array.
     *
     * @param  $ident
     * @return array
     * @throws ServerQueryException
     */
    public function toAssocArray($ident)
    {
        $nodes = (func_num_args() > 1) ? $this->toArray(1) : $this->toArray();
        $array = [];

        foreach ($nodes as $node) {
            if (isset($node[$ident])) {
                $array[(is_object($node[$ident])) ? $node[$ident]->toString() : $node[$ident]] = $node;
            } else {
                throw new ServerQueryException("invalid parameter", 0x602);
            }
        }

        return $array;
    }

    /**
     * Returns an array containing the reply splitted in multiple rows and columns.
     *
     * @return array
     */
    public function toList()
    {
        $array = func_num_args() ? $this->toArray(1) : $this->toArray();

        if (count($array) == 1) {
            return array_shift($array);
        }

        return $array;
    }

    /**
     * Returns an array containing stdClass objects.
     *
     * @return \ArrayObject|array
     */
    public function toObjectArray()
    {
        $array = (func_num_args() > 1) ? $this->toArray(1) : $this->toArray();

        for ($i = 0; $i < count($array); $i++) {
            $array[$i] = (object) $array[$i];
        }

        return $array;
    }

    /**
     * Returns the command used to get this reply.
     *
     * @return StringHelper
     */
    public function getCommandString()
    {
        return new StringHelper($this->cmd);
    }

    /**
     * Returns an array of events that occured before or during this reply.
     *
     * @return array
     */
    public function getNotifyEvents()
    {
        return $this->evt;
    }

    /**
     * Returns the value for a specified error property.
     *
     * @param  string $ident
     * @param  mixed  $default
     * @return mixed
     */
    public function getErrorProperty($ident, $default = null)
    {
        return (array_key_exists($ident, $this->err)) ? $this->err[$ident] : $default;
    }

    /**
     * Parses a ServerQuery error and throws a ESportsAlliance\TeamSpeakCore\Exception\ServerQueryException object if
     * there's an error.
     *
     * @param  StringHelper $err
     * @return void
     * @throws ServerQueryException
     */
    protected function fetchError(StringHelper $err)
    {
        $cells = $err->section(TeamSpeak::SEPARATOR_CELL, 1, 3);

        foreach ($cells->split(TeamSpeak::SEPARATOR_CELL) as $pair) {
            list($ident, $value) = $pair->split(TeamSpeak::SEPARATOR_PAIR);

            $this->err[$ident->toString()] = $value->isInt() ? $value->toInt() : $value->unescape();
        }

        Signal::getInstance()->emit("notifyError", $this);

        if ($this->getErrorProperty("id", 0x00) != 0x00 && $this->exp) {
            if ($permid = $this->getErrorProperty("failed_permid")) {
                if ($permsid = key($this->con->request("permget permid=" . $permid, false)->toAssocArray("permsid"))) {
                    $suffix = " (failed on " . $permsid . ")";
                } else {
                    $suffix = " (failed on " . $this->cmd->section(TeamSpeak::SEPARATOR_CELL) . " " . $permid . "/0x" . strtoupper(dechex($permid)) . ")";
                }
            } elseif ($details = $this->getErrorProperty("extra_msg")) {
                $suffix = " (" . trim($details) . ")";
            } else {
                $suffix = "";
            }

            throw new ServerQueryException($this->getErrorProperty("msg") . $suffix, $this->getErrorProperty("id"), $this->getErrorProperty("return_code"));
        }
    }

    /**
     * Parses a ServerQuery reply and creates a ESportsAlliance\TeamSpeakCore\Helper\StringHelper object.
     *
     * @param  array $rpl
     * @return void
     * @throws AdapterException
     */
    protected function fetchReply($rpl)
    {
        foreach ($rpl as $key => $val) {
            if ($val->startsWith(TeamSpeak::TS3_MOTD_PREFIX) || $val->startsWith(TeamSpeak::TEA_MOTD_PREFIX) || (defined("CUSTOM_MOTD_PREFIX") && $val->startsWith(CUSTOM_MOTD_PREFIX))) {
                unset($rpl[$key]);
            } elseif ($val->startsWith(TeamSpeak::EVENT)) {
                $this->evt[] = new Event($rpl[$key], $this->con);
                unset($rpl[$key]);
            }
        }

        $this->rpl = new StringHelper(implode(TeamSpeak::SEPARATOR_LIST, $rpl));
    }
}
