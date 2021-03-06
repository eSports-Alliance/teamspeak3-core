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

namespace ESportsAlliance\TeamSpeakCore\Helper\Signal;

use ESportsAlliance\TeamSpeakCore\Adapter\Adapter;
use ESportsAlliance\TeamSpeakCore\Adapter\FileTransfer;
use ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery\Event;
use ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery\Reply;
use ESportsAlliance\TeamSpeakCore\Exception\SignalException;
use ESportsAlliance\TeamSpeakCore\Node\Host;
use ESportsAlliance\TeamSpeakCore\Node\Server;

/**
 * Interface SignalInterface
 * @package ESportsAlliance\TeamSpeakCore\Helper\Signal
 * @class SignalInterface
 * @brief Interface class describing the layout for ESportsAlliance\TeamSpeakCore\Helper\Signal callbacks.
 */
interface SignalInterface
{
    /**
     * Possible callback for '<adapter>Connected' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("serverqueryConnected", array($object, "onConnect"));
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferConnected", array($object, "onConnect"));
     *
     * @param  Adapter $adapter
     * @return void
     */
    public function onConnect(Adapter $adapter);

    /**
     * Possible callback for '<adapter>Disconnected' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("serverqueryDisconnected", array($object, "onDisconnect"));
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferDisconnected", array($object, "onDisconnect"));
     *
     * @return void
     */
    public function onDisconnect();

    /**
     * Possible callback for 'serverqueryCommandStarted' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("serverqueryCommandStarted", array($object, "onCommandStarted"));
     *
     * @param  string $cmd
     * @return void
     */
    public function onCommandStarted($cmd);

    /**
     * Possible callback for 'serverqueryCommandFinished' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("serverqueryCommandFinished", array($object, "onCommandFinished"));
     *
     * @param  string $cmd
     * @param  Reply $reply
     * @return void
     */
    public function onCommandFinished($cmd, Reply $reply);

    /**
     * Possible callback for 'notifyEvent' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyEvent", array($object, "onEvent"));
     *
     * @param  Event $event
     * @param  Host $host
     * @return void
     */
    public function onEvent(Event $event, Host $host);

    /**
     * Possible callback for 'notifyError' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyError", array($object, "onError"));
     *
     * @param  Reply $reply
     * @return void
     */
    public function onError(Reply $reply);

    /**
     * Possible callback for 'notifyServerselected' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyServerselected", array($object, "onServerselected"));
     *
     * @param  Host $host
     * @return void
     */
    public function onServerselected(Host $host);

    /**
     * Possible callback for 'notifyServercreated' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyServercreated", array($object, "onServercreated"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServercreated(Host $host, $sid);

    /**
     * Possible callback for 'notifyServerdeleted' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyServerdeleted", array($object, "onServerdeleted"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServerdeleted(Host $host, $sid);

    /**
     * Possible callback for 'notifyServerstarted' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyServerstarted", array($object, "onServerstarted"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServerstarted(Host $host, $sid);

    /**
     * Possible callback for 'notifyServerstopped' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyServerstopped", array($object, "onServerstopped"));
     *
     * @param  Host $host
     * @param  integer $sid
     * @return void
     */
    public function onServerstopped(Host $host, $sid);

    /**
     * Possible callback for 'notifyServershutdown' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyServershutdown", array($object, "onServershutdown"));
     *
     * @param  Host $host
     * @return void
     */
    public function onServershutdown(Host $host);

    /**
     * Possible callback for 'notifyLogin' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyLogin", array($object, "onLogin"));
     *
     * @param  Host $host
     * @return void
     */
    public function onLogin(Host $host);

    /**
     * Possible callback for 'notifyLogout' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyLogout", array($object, "onLogout"));
     *
     * @param  Host $host
     * @return void
     */
    public function onLogout(Host $host);

    /**
     * Possible callback for 'notifyTokencreated' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyTokencreated", array($object, "onTokencreated"));
     *
     * @param  Server $server
     * @param  string $token
     * @return void
     */
    public function onTokencreated(Server $server, $token);

    /**
     * Possible callback for 'filetransferHandshake' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferHandshake", array($object, "onFtHandshake"));
     *
     * @param  FileTransfer $adapter
     * @return void
     */
    public function onFtHandshake(FileTransfer $adapter);

    /**
     * Possible callback for 'filetransferUploadStarted' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferUploadStarted", array($object, "onFtUploadStarted"));
     *
     * @param  string  $ftkey
     * @param  integer $seek
     * @param  integer $size
     * @return void
     */
    public function onFtUploadStarted($ftkey, $seek, $size);

    /**
     * Possible callback for 'filetransferUploadProgress' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferUploadProgress", array($object, "onFtUploadProgress"));
     *
     * @param  string  $ftkey
     * @param  integer $seek
     * @param  integer $size
     * @return void
     */
    public function onFtUploadProgress($ftkey, $seek, $size);

    /**
     * Possible callback for 'filetransferUploadFinished' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferUploadFinished", array($object, "onFtUploadFinished"));
     *
     * @param  string  $ftkey
     * @param  integer $seek
     * @param  integer $size
     * @return void
     */
    public function onFtUploadFinished($ftkey, $seek, $size);

    /**
     * Possible callback for 'filetransferDownloadStarted' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferDownloadStarted", array($object, "onFtDownloadStarted"));
     *
     * @param  string  $ftkey
     * @param  integer $buff
     * @param  integer $size
     * @return void
     */
    public function onFtDownloadStarted($ftkey, $buff, $size);

    /**
     * Possible callback for 'filetransferDownloadProgress' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferDownloadProgress", array($object, "onFtDownloadProgress"));
     *
     * @param  string  $ftkey
     * @param  integer $buff
     * @param  integer $size
     * @return void
     */
    public function onFtDownloadProgress($ftkey, $buff, $size);

    /**
     * Possible callback for 'filetransferDownloadFinished' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferDownloadFinished", array($object, "onFtDownloadFinished"));
     *
     * @param  string  $ftkey
     * @param  integer $buff
     * @param  integer $size
     * @return void
     */
    public function onFtDownloadFinished($ftkey, $buff, $size);

    /**
     * Possible callback for '<adapter>DataRead' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("serverqueryDataRead", array($object, "onDebugDataRead"));
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferDataRead", array($object, "onDebugDataRead"));
     *
     * @param  string $data
     * @return void
     */
    public function onDebugDataRead($data);

    /**
     * Possible callback for '<adapter>DataSend' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("serverqueryDataSend", array($object, "onDebugDataSend"));
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferDataSend", array($object, "onDebugDataSend"));
     *
     * @param  string $data
     * @return void
     */
    public function onDebugDataSend($data);

    /**
     * Possible callback for '<adapter>WaitTimeout' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("serverqueryWaitTimeout", array($object, "onWaitTimeout"));
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("filetransferWaitTimeout", array($object, "onWaitTimeout"));
     *
     * @param  integer $time
     * @param  Adapter $adapter
     * @return void
     */
    public function onWaitTimeout($time, Adapter $adapter);

    /**
     * Possible callback for 'errorException' signals.
     *
     * === Examples ===
     *   - ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("errorException", array($object, "onException"));
     *
     * @param  SignalException $e
     * @return void
     */
    public function onException(SignalException $e);
}
