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

namespace ESportsAlliance\TeamSpeakCore\Exception;

use Exception;
use ESportsAlliance\TeamSpeakCore\Helper\Signal;
use ESportsAlliance\TeamSpeakCore\Helper\StringHelper;

/**
 * Class TeamSpeakException
 * @package ESportsAlliance\TeamSpeakCore\Exception
 * @class TeamSpeakException
 * @brief Enhanced exception class for TeamSpeak objects.
 */
class TeamSpeakException extends Exception
{
    /**
     * Stores the original error code.
     *
     * @var integer
     */
    protected $raw_code = 0x00;

    /**
     * Stores the original error message.
     *
     * @var StringHelper
     */
    protected $raw_mesg = null;

    /**
     * Stores custom error messages.
     *
     * @var array
     */
    protected static $messages = [];

    /**
     * The TeamSpeakException constructor.
     *
     * @param  string  $mesg
     * @param  integer $code
     */
    public function __construct($mesg, $code = 0x00)
    {
        parent::__construct($mesg, $code);

        $this->raw_code = $code;
        $this->raw_mesg = $mesg;

        if (array_key_exists((int) $code, self::$messages)) {
            $this->message = $this->prepareCustomMessage(self::$messages[intval($code)]);
        }

        Signal::getInstance()->emit("errorException", $this);
    }

    /**
     * Prepares a custom error message by replacing pre-defined signs with given values.
     *
     * @param  StringHelper $mesg
     * @return string
     */
    protected function prepareCustomMessage(StringHelper $mesg)
    {
        $args = [
      "code" => $this->getCode(),
      "mesg" => $this->getMessage(),
      "line" => $this->getLine(),
      "file" => $this->getFile(),
    ];

        return $mesg->arg($args)->toString();
    }

    /**
     * Registers a custom error message to $code.
     *
     * @param  integer $code
     * @param  string  $mesg
     * @return void
     *@throws TeamSpeakException
     */
    public static function registerCustomMessage($code, $mesg)
    {
        if (array_key_exists((int) $code, self::$messages)) {
            throw new self("custom message for code 0x" . strtoupper(dechex($code)) . " is already registered");
        }

        if (!is_string($mesg)) {
            throw new self("custom message for code 0x" . strtoupper(dechex($code)) . " must be a string");
        }

        self::$messages[(int) $code] = new StringHelper($mesg);
    }

    /**
     * Unregisters a custom error message from $code.
     *
     * @param  integer $code
     * @return void
     * @throws TeamSpeakException
     */
    public static function unregisterCustomMessage($code)
    {
        if (!array_key_exists((int) $code, self::$messages)) {
            throw new self("custom message for code 0x" . strtoupper(dechex($code)) . " is not registered");
        }

        unset(self::$messages[(int) $code]);
    }

    /**
     * Returns the original error code.
     *
     * @return integer
     */
    public function getRawCode()
    {
        return $this->raw_code;
    }

    /**
     * Returns the original error message.
     *
     * @return integer
     */
    public function getRawMessage()
    {
        return $this->raw_mesg;
    }

    /**
     * Returns the class from which the exception was thrown.
     *
     * @return string
     */
    public function getSender()
    {
        $trace = $this->getTrace();

        return (isset($trace[0]["class"])) ? $trace[0]["class"] : "{main}";
    }
}
