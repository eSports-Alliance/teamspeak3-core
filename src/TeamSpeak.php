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

namespace ESportsAlliance\TeamSpeakCore;

use ESportsAlliance\TeamSpeakCore\Node\Host;
use ESportsAlliance\TeamSpeakCore\Node\Node;
use ESportsAlliance\TeamSpeakCore\Helper\Uri;
use ESportsAlliance\TeamSpeakCore\Node\Server;
use ESportsAlliance\TeamSpeakCore\Adapter\Adapter;
use ESportsAlliance\TeamSpeakCore\Helper\Profiler;
use ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery;
use ESportsAlliance\TeamSpeakCore\Helper\StringHelper;
use ESportsAlliance\TeamSpeakCore\Exception\AdapterException;

/**
 * @class TeamSpeak
 * @brief Factory class all for TeamSpeak 3 PHP Framework objects.
 */
class TeamSpeak
{
    /**
     * TeamSpeak 3 protocol welcome message.
     */
    const TS3_PROTO_IDENT = "TS3";

    /**
     * TeamSpeak 3 protocol greeting message prefix.
     */
    const TS3_MOTD_PREFIX = "Welcome";

    /**
     * TeaSpeak protocol welcome message.
     */
    const TEA_PROTO_IDENT = "TeaSpeak";

    /**
     * TeaSpeak protocol greeting message prefix.
     */
    const TEA_MOTD_PREFIX = "Welcome";

    /**
     * TeamSpeak 3 protocol error message prefix.
     */
    const ERROR = "error";

    /**
     * TeamSpeak 3 protocol event message prefix.
     */
    const EVENT = "notify";

    /**
     * TeamSpeak 3 protocol server connection handler ID prefix.
     */
    const SCHID = "selected";

    /*@
     * TeamSpeak 3 protocol separators.
     */
    const SEPARATOR_LINE = "\n"; //!< protocol line separator
  const SEPARATOR_LIST = "|";  //!< protocol list separator
  const SEPARATOR_CELL = " ";  //!< protocol cell separator
  const SEPARATOR_PAIR = "=";  //!< protocol pair separator

  /*@
   * TeamSpeak 3 API key scopes.
   */
  const APIKEY_MANAGE = "manage";  //!< allow access to administrative calls
  const APIKEY_WRITE  = "write";   //!< allow access to read and write calls
  const APIKEY_READ   = "read";    //!< allow access to read-only calls

  /*@
   * TeamSpeak 3 log levels.
   */
    const LOGLEVEL_CRITICAL = 0x00; //!< 0: these messages stop the program
  const LOGLEVEL_ERROR    = 0x01; //!< 1: everything that is really bad
  const LOGLEVEL_WARNING  = 0x02; //!< 2: everything that might be bad
  const LOGLEVEL_DEBUG    = 0x03; //!< 3: output that might help find a problem
  const LOGLEVEL_INFO     = 0x04; //!< 4: informational output
  const LOGLEVEL_DEVEL    = 0x05; //!< 5: development output

  /*@
   * TeamSpeak 3 token types.
   */
    const TOKEN_SERVERGROUP  = 0x00; //!< 0: server group token  (id1={groupID} id2=0)
  const TOKEN_CHANNELGROUP = 0x01; //!< 1: channel group token (id1={groupID} id2={channelID})

  /*@
   * TeamSpeak 3 codec identifiers.
   */
    const CODEC_SPEEX_NARROWBAND    = 0x00; //!< 0: speex narrowband     (mono, 16bit, 8kHz)
  const CODEC_SPEEX_WIDEBAND      = 0x01; //!< 1: speex wideband       (mono, 16bit, 16kHz)
  const CODEC_SPEEX_ULTRAWIDEBAND = 0x02; //!< 2: speex ultra-wideband (mono, 16bit, 32kHz)
  const CODEC_CELT_MONO           = 0x03; //!< 3: celt mono            (mono, 16bit, 48kHz)
  const CODEC_OPUS_VOICE          = 0x04; //!< 3: opus voice           (interactive)
  const CODEC_OPUS_MUSIC          = 0x05; //!< 3: opus music           (interactive)

  /*@
   * TeamSpeak 3 codec encryption modes.
   */
    const CODEC_CRYPT_INDIVIDUAL = 0x00; //!< 0: configure per channel
  const CODEC_CRYPT_DISABLED   = 0x01; //!< 1: globally disabled
  const CODEC_CRYPT_ENABLED    = 0x02; //!< 2: globally enabled

  /*@
   * TeamSpeak 3 kick reason types.
   */
    const KICK_CHANNEL = 0x04; //!< 4: kick client from channel
  const KICK_SERVER  = 0x05; //!< 5: kick client from server

  /*@
   * TeamSpeak 3 text message target modes.
   */
    const TEXTMSG_CLIENT  = 0x01; //!< 1: target is a client
  const TEXTMSG_CHANNEL = 0x02; //!< 2: target is a channel
  const TEXTMSG_SERVER  = 0x03; //!< 3: target is a virtual server

  /*@
   * TeamSpeak 3 plugin command target modes.
   */
    const PLUGINCMD_CHANNEL            = 0x01; //!< 1: send plugincmd to all clients in current channel
  const PLUGINCMD_SERVER             = 0x02; //!< 2: send plugincmd to all clients on server
  const PLUGINCMD_CLIENT             = 0x03; //!< 3: send plugincmd to all given client ids
  const PLUGINCMD_CHANNEL_SUBSCRIBED = 0x04; //!< 4: send plugincmd to all subscribed clients in current channel

  /*@
   * TeamSpeak 3 host message modes.
   */
    const HOSTMSG_NONE      = 0x00; //!< 0: display no message
  const HOSTMSG_LOG       = 0x01; //!< 1: display message in chatlog
  const HOSTMSG_MODAL     = 0x02; //!< 2: display message in modal dialog
  const HOSTMSG_MODALQUIT = 0x03; //!< 3: display message in modal dialog and close connection

  /*@
   * TeamSpeak 3 host banner modes.
   */
    const HOSTBANNER_NO_ADJUST     = 0x00; //!< 0: do not adjust
  const HOSTBANNER_IGNORE_ASPECT = 0x01; //!< 1: adjust but ignore aspect ratio
  const HOSTBANNER_KEEP_ASPECT   = 0x02; //!< 2: adjust and keep aspect ratio

  /*@
   * TeamSpeak 3 client identification types.
   */
    const CLIENT_TYPE_REGULAR     = 0x00; //!< 0: regular client
  const CLIENT_TYPE_SERVERQUERY = 0x01; //!< 1: query client

  /*@
   * TeamSpeak 3 permission group database types.
   */
    const GROUP_DBTYPE_TEMPLATE    = 0x00; //!< 0: template group     (used for new virtual servers)
  const GROUP_DBTYPE_REGULAR     = 0x01; //!< 1: regular group      (used for regular clients)
  const GROUP_DBTYPE_SERVERQUERY = 0x02; //!< 2: global query group (used for ServerQuery clients)

  /*@
   * TeamSpeak 3 permission group name modes.
   */
    const GROUP_NAMEMODE_HIDDEN = 0x00; //!< 0: display no name
  const GROUP_NAMEMODE_BEFORE = 0x01; //!< 1: display name before client nickname
  const GROUP_NAMEMODE_BEHIND = 0x02; //!< 2: display name after client nickname

  /*@
   * TeamSpeak 3 permission group identification types.
   */
    const GROUP_IDENTIFIY_STRONGEST = 0x01; //!< 1: identify most powerful group
  const GROUP_IDENTIFIY_WEAKEST   = 0x02; //!< 2: identify weakest group

  /*@
   * TeamSpeak 3 permission types.
   */
    const PERM_TYPE_SERVERGROUP   = 0x00; //!< 0: server group permission
  const PERM_TYPE_CLIENT        = 0x01; //!< 1: client specific permission
  const PERM_TYPE_CHANNEL       = 0x02; //!< 2: channel specific permission
  const PERM_TYPE_CHANNELGROUP  = 0x03; //!< 3: channel group permission
  const PERM_TYPE_CHANNELCLIENT = 0x04; //!< 4: channel-client specific permission

  /*@
   * TeamSpeak 3 permission categories.
   */
    const PERM_CAT_GLOBAL              = 0x10; //!< 00010000: global permissions
  const PERM_CAT_GLOBAL_INFORMATION  = 0x11; //!< 00010001: global permissions -> global information
  const PERM_CAT_GLOBAL_SERVER_MGMT  = 0x12; //!< 00010010: global permissions -> virtual server management
  const PERM_CAT_GLOBAL_ADM_ACTIONS  = 0x13; //!< 00010011: global permissions -> global administrative actions
  const PERM_CAT_GLOBAL_SETTINGS     = 0x14; //!< 00010100: global permissions -> global settings
  const PERM_CAT_SERVER              = 0x20; //!< 00100000: virtual server permissions
  const PERM_CAT_SERVER_INFORMATION  = 0x21; //!< 00100001: virtual server permissions -> virtual server information
  const PERM_CAT_SERVER_ADM_ACTIONS  = 0x22; //!< 00100010: virtual server permissions -> virtual server administrative actions
  const PERM_CAT_SERVER_SETTINGS     = 0x23; //!< 00100011: virtual server permissions -> virtual server settings
  const PERM_CAT_CHANNEL             = 0x30; //!< 00110000: channel permissions
  const PERM_CAT_CHANNEL_INFORMATION = 0x31; //!< 00110001: channel permissions -> channel information
  const PERM_CAT_CHANNEL_CREATE      = 0x32; //!< 00110010: channel permissions -> create channels
  const PERM_CAT_CHANNEL_MODIFY      = 0x33; //!< 00110011: channel permissions -> edit channels
  const PERM_CAT_CHANNEL_DELETE      = 0x34; //!< 00110100: channel permissions -> delete channels
  const PERM_CAT_CHANNEL_ACCESS      = 0x35; //!< 00110101: channel permissions -> access channels
  const PERM_CAT_GROUP               = 0x40; //!< 01000000: group permissions
  const PERM_CAT_GROUP_INFORMATION   = 0x41; //!< 01000001: group permissions -> group information
  const PERM_CAT_GROUP_CREATE        = 0x42; //!< 01000010: group permissions -> create groups
  const PERM_CAT_GROUP_MODIFY        = 0x43; //!< 01000011: group permissions -> edit groups
  const PERM_CAT_GROUP_DELETE        = 0x44; //!< 01000100: group permissions -> delete groups
  const PERM_CAT_CLIENT              = 0x50; //!< 01010000: client permissions
  const PERM_CAT_CLIENT_INFORMATION  = 0x51; //!< 01010001: client permissions -> client information
  const PERM_CAT_CLIENT_ADM_ACTIONS  = 0x52; //!< 01010010: client permissions -> client administrative actions
  const PERM_CAT_CLIENT_BASICS       = 0x53; //!< 01010011: client permissions -> client basic communication
  const PERM_CAT_CLIENT_MODIFY       = 0x54; //!< 01010100: client permissions -> edit clients
  const PERM_CAT_FILETRANSFER        = 0x60; //!< 01100000: file transfer permissions
  const PERM_CAT_NEEDED_MODIFY_POWER = 0xFF; //!< 11111111: needed permission modify power (grant) permissions

  /*@
   * TeamSpeak 3 file types.
   */
    const FILE_TYPE_DIRECTORY = 0x00; //!< 0: file is directory
  const FILE_TYPE_REGULAR   = 0x01; //!< 1: file is regular

  /*@
   * TeamSpeak 3 server snapshot types.
   */
    const SNAPSHOT_STRING = 0x00; //!< 0: default string
  const SNAPSHOT_BASE64 = 0x01; //!< 1: base64 string
  const SNAPSHOT_HEXDEC = 0x02; //!< 2: hexadecimal string

  /*@
   * TeamSpeak 3 channel spacer types.
   */
    const SPACER_SOLIDLINE      = 0x00; //!< 0: solid line
  const SPACER_DASHLINE       = 0x01; //!< 1: dash line
  const SPACER_DOTLINE        = 0x02; //!< 2: dot line
  const SPACER_DASHDOTLINE    = 0x03; //!< 3: dash dot line
  const SPACER_DASHDOTDOTLINE = 0x04; //!< 4: dash dot dot line
  const SPACER_CUSTOM         = 0x05; //!< 5: custom format

  /*@
   * TeamSpeak 3 channel spacer alignments.
   */
    const SPACER_ALIGN_LEFT   = 0x00; //!< 0: alignment left
  const SPACER_ALIGN_RIGHT  = 0x01; //!< 1: alignment right
  const SPACER_ALIGN_CENTER = 0x02; //!< 2: alignment center
  const SPACER_ALIGN_REPEAT = 0x03; //!< 3: repeat until the whole line is filled

  /*@
   * TeamSpeak 3 reason identifiers.
   */
    const REASON_NONE                = 0x00; //!<  0: no reason
  const REASON_MOVE                = 0x01; //!<  1: channel switched or moved
  const REASON_SUBSCRIPTION        = 0x02; //!<  2: subscription added or removed
  const REASON_TIMEOUT             = 0x03; //!<  3: client connection timed out
  const REASON_CHANNEL_KICK        = 0x04; //!<  4: client kicked from channel
  const REASON_SERVER_KICK         = 0x05; //!<  5: client kicked from server
  const REASON_SERVER_BAN          = 0x06; //!<  6: client banned from server
  const REASON_SERVER_STOP         = 0x07; //!<  7: server stopped
  const REASON_DISCONNECT          = 0x08; //!<  8: client disconnected
  const REASON_CHANNEL_UPDATE      = 0x09; //!<  9: channel information updated
  const REASON_CHANNEL_EDIT        = 0x0A; //!< 10: channel information edited
  const REASON_DISCONNECT_SHUTDOWN = 0x0B; //!< 11: client disconnected on server shutdown

  /**
   * Stores an array containing various chars which need to be escaped while communicating
   * with a TeamSpeak 3 Server.
   *
   * @var array
   */
    protected static $escape_patterns = [
    "\\" => "\\\\", // backslash
    "/"  => "\\/",  // slash
    " "  => "\\s",  // whitespace
    "|"  => "\\p",  // pipe
    ";"  => "\\;",  // semicolon
    "\a" => "\\a",  // bell
    "\b" => "\\b",  // backspace
    "\f" => "\\f",  // formfeed
    "\n" => "\\n",  // newline
    "\r" => "\\r",  // carriage return
    "\t" => "\\t",  // horizontal tab
    "\v" => "\\v"   // vertical tab
  ];

    /**
     * Factory for ESportsAlliance\TeamSpeakCore\Adapter\Adapter classes. $uri must be formatted as
     * "<adapter>://<user>:<pass>@<host>:<port>/<options>#<flags>". All parameters
     * except adapter, host and port are optional.
     *
     * === Supported Options ===
     *   - timeout
     *   - blocking
     *   - tls (TeaSpeak only)
     *   - ssh (TeamSpeak only)
     *   - nickname
     *   - no_query_clients
     *   - use_offline_as_virtual
     *   - clients_before_channels
     *   - server_id|server_uid|server_port|server_name
     *   - channel_id|channel_name
     *   - client_id|client_uid|client_name
     *
     * === Supported Flags (only one per $uri) ===
     *   - no_query_clients
     *   - use_offline_as_virtual
     *   - clients_before_channels
     *
     * === URI Examples ===
     *   - serverquery://127.0.0.1:10011/
     *   - serverquery://127.0.0.1:10022/?ssh=1 (TeamSpeak ONLY)
     *   - serverquery://127.0.0.1:10011/?tls=1 (TeaSpeak ONLY)
     *   - serverquery://127.0.0.1:10022/?ssh=1&server_port=9987
     *   - serverquery://127.0.0.1:10011/?server_port=9987&channel_id=1
     *   - serverquery://127.0.0.1:10011/?server_port=9987&channel_id=1#no_query_clients
     *   - serverquery://127.0.0.1:10011/?server_port=9987&client_name=ScP
     *   - filetransfer://127.0.0.1:30011/
     *
     * @param  string $uri
     * @return Adapter
     * @return Node
     * @return Host
     * @return Server
     */
    public static function factory($uri)
    {
        self::init();

        $uri = new Uri($uri);

        $adapter = self::getAdapterName($uri->getScheme());
        $options = ["host" => $uri->getHost(), "port" => $uri->getPort(), "timeout" => (int) $uri->getQueryVar("timeout", 10), "blocking" => (int) $uri->getQueryVar("blocking", 1), "tls" => (int) $uri->getQueryVar("tls", 0), "ssh" => (int) $uri->getQueryVar("ssh", 0)];

        self::loadClass($adapter);

        if ($options["ssh"]) {
            $options["username"] = $uri->getUser();
            $options["password"] = $uri->getPass();
        }

        $object = new $adapter($options);

        try {if ($object instanceof ServerQuery) {
            $node = $object->getHost();

            if ($uri->hasUser() && $uri->hasPass()) {
                $node->login($uri->getUser(), $uri->getPass());
            }

            if ($uri->hasQueryVar("nickname")) {
                $node->setPredefinedQueryName($uri->getQueryVar("nickname"));
            }

            if ($uri->getFragment() == "use_offline_as_virtual") {
                $node->setUseOfflineAsVirtual(true);
            } elseif ($uri->hasQueryVar("use_offline_as_virtual")) {
                $node->setUseOfflineAsVirtual($uri->getQueryVar("use_offline_as_virtual") ? true : false);
            }

            if ($uri->getFragment() == "clients_before_channels") {
                $node->setLoadClientlistFirst(true);
            } elseif ($uri->hasQueryVar("clients_before_channels")) {
                $node->setLoadClientlistFirst($uri->getQueryVar("clients_before_channels") ? true : false);
            }

            if ($uri->getFragment() == "no_query_clients") {
                $node->setExcludeQueryClients(true);
            } elseif ($uri->hasQueryVar("no_query_clients")) {
                $node->setExcludeQueryClients($uri->getQueryVar("no_query_clients") ? true : false);
            }

            if ($uri->hasQueryVar("server_id")) {
                $node = $node->serverGetById($uri->getQueryVar("server_id"));
            } elseif ($uri->hasQueryVar("server_uid")) {
                $node = $node->serverGetByUid($uri->getQueryVar("server_uid"));
            } elseif ($uri->hasQueryVar("server_port")) {
                $node = $node->serverGetByPort($uri->getQueryVar("server_port"));
            } elseif ($uri->hasQueryVar("server_name")) {
                $node = $node->serverGetByName($uri->getQueryVar("server_name"));
            }

            if ($node instanceof Server) {
                if ($uri->hasQueryVar("channel_id")) {
                    $node = $node->channelGetById($uri->getQueryVar("channel_id"));
                } elseif ($uri->hasQueryVar("channel_name")) {
                    $node = $node->channelGetByName($uri->getQueryVar("channel_name"));
                }

                if ($uri->hasQueryVar("client_id")) {
                    $node = $node->clientGetById($uri->getQueryVar("client_id"));
                }
                if ($uri->hasQueryVar("client_uid")) {
                    $node = $node->clientGetByUid($uri->getQueryVar("client_uid"));
                } elseif ($uri->hasQueryVar("client_name")) {
                    $node = $node->clientGetByName($uri->getQueryVar("client_name"));
                }
            }

            return $node;
        }}
    catch (Exception $e) {
      $object->__destruct();
      throw $e;
    }

        return $object;
    }

    /**
     * Returns the name of an adapter class by $name.
     *
     * @param string $name
     * @param string $namespace
     * @return string
     * @throws AdapterException
     */
    protected static function getAdapterName($name, $namespace = "TeamSpeak3_Adapter_")
    {
        $path = self::getFilePath($namespace);
        $scan = scandir($path);

        foreach($scan as $node)
        {
            $file = StringHelper::factory($node)->toLower();

            if($file->startsWith($name) && $file->endsWith(".php"))
            {
                return $namespace . str_replace(".php", "", $node);
            }
        }

        throw new AdapterException("adapter '" . $name . "' does not exist");
    }

    /**
     * Loads a class from a PHP file. The filename must be formatted as "$class.php".
     *
     * include() is not prefixed with the @ operator because if the file is loaded and
     * contains a parse error, execution will halt silently and this is difficult to debug.
     *
     * @param  string $class
     * @return boolean
     *@throws \Exception
     */
    protected static function loadClass($class)
    {
        if (class_exists($class, false) || interface_exists($class, false)) {
            return;
        }

        if (preg_match("/[^a-z0-9\\/\\\\_.-]/i", $class)) {
            throw new \Exception("illegal characters in classname '" . $class . "'");
        }

        $file = self::getFilePath($class) . ".php";

        if (!file_exists($file) || !is_readable($file)) {
            throw new \Exception("file '" . $file . "' does not exist or is not readable");
        }

        if (class_exists($class, false) || interface_exists($class, false)) {
            throw new \Exception("class '" . $class . "' does not exist");
        }

        return include_once($file);
    }

    /**
     * Generates a possible file path for $name.
     *
     * @param  string $name
     * @return string
     */
    protected static function getFilePath($name)
    {
        $path = str_replace("_", DIRECTORY_SEPARATOR, $name);
        $path = str_replace(__CLASS__, dirname(__FILE__), $path);

        return $path;
    }

    /**
     * Checks for required PHP features, enables autoloading and starts a default profiler.
     *
     * @throws \Exception
     * @return void
     */
    public static function init()
    {
        if (version_compare(phpversion(), "7.0.0") == -1) {
            throw new \Exception("this particular software cannot be used with the installed version of PHP");
        }

        if (!function_exists("stream_socket_client")) {
            throw new \Exception("network functions are not available in this PHP installation");
        }

        if (!function_exists("spl_autoload_register")) {
            throw new \Exception("autoload functions are not available in this PHP installation");
        }

        Profiler::start();
    }

    /**
     * Returns an assoc array containing all escape patterns available on a TeamSpeak 3
     * Server.
     *
     * @return array
     */
    public static function getEscapePatterns()
    {
        return self::$escape_patterns;
    }

    /**
     * Debug helper function. This is a wrapper for var_dump() that adds the pre-format tags,
     * cleans up newlines and indents, and runs htmlentities() before output.
     *
     * @param  mixed  $var
     * @param  bool   $echo
     * @return string
     */
    public static function dump($var, $echo = true)
    {
        ob_start();
        var_dump($var);

        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", ob_get_clean());

        if (PHP_SAPI == "cli") {
            $output = PHP_EOL . PHP_EOL . $output . PHP_EOL;
        } else {
            $output = "<pre>" . htmlspecialchars($output, ENT_QUOTES, "utf-8") . "</pre>";
        }

        if ($echo) {
            echo($output);
        }

        return $output;
    }
}

/*!
 * \mainpage API Documentation
 *
 * \section welcome_sec Introduction
 *
 * \subsection welcome1 What is the TS3 PHP Framework?
 * Initially released in January 2010, the TS3 PHP Framework is a powerful, open source, object-oriented framework
 * implemented in PHP 5 and licensed under the GNU General Public License. It's based on simplicity and a rigorously
 * tested agile codebase. Extend the functionality of your servers with scripts or create powerful web applications
 * to manage all features of your TeamSpeak 3 Server instances.
 *
 * Tested. Thoroughly. Enterprise-ready and built with agile methods, the TS3 PHP Framework has been unit-tested from
 * the start to ensure that all code remains stable and easy for you to extend, re-test with your extensions, and
 * further maintain.
 *
 * \subsection welcome2 Why should I use the TS3 PHP Framework rather than other PHP libraries?
 * The TS3 PHP Framework is a is a modern use-at-will framework that provides individual components to communicate
 * with the TeamSpeak 3 Server.
 *
 * There are lots of arguments for the TS3 PHP Framework in comparison with other PHP based libraries. It is the most
 * dynamic and feature-rich piece of software in its class. In addition, it's always up-to-date and 100% compatible to
 * almost any TeamSpeak 3 Server version available.
 *
 * \section sysreqs_sec Requirements
 * The TS3 PHP Framework currently supports PHP 5.2.1 or later, but we strongly recommend the most current release of
 * PHP for critical security and performance enhancements. If you want to create a web application using the TS3 PHP
 * Framework, you need a PHP 5 interpreter with a web server configured to handle PHP scripts correctly.
 *
 * Note that the majority of TS3 PHP Framework development and deployment is done on nginx, so there is more community
 * experience and testing performed on nginx than on other web servers.
 *
 * \section feature_sec Features
 * Features of the TS3 PHP Framework include:
 *
 *   - Fully object-oriented PHP 5 and E_STRICT compliant components
 *   - Access to all TeamSpeak 3 Server features via ServerQuery
 *   - Integrated full featured and customizable TSViewer interfaces
 *   - Full support for file transfers to up- and /or download custom icons and other stuff
 *   - Powerful error handling capablities using exceptions and customizable error messages
 *   - Dynamic signal slots for event based scripting
 *   - ...
 *
 * \section example_sec Usage Examples
 *
 * \subsection example1 1. Kick a single Client from a Virtual Server
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // kick the client with ID 123 from the server
 *   $ts3_VirtualServer->clientKick(123, TeamSpeak::KICK_SERVER, "evil kick XD");
 *
 *   // spawn an object for the client by unique identifier and do the kick
 *   $ts3_VirtualServer->clientGetByUid("FPMPSC6MXqXq751dX7BKV0JniSo=")->kick(TeamSpeak::KICK_SERVER, "evil kick XD");
 *
 *   // spawn an object for the client by current nickname and do the kick
 *   $ts3_VirtualServer->clientGetByName("ScP")->kick(TeamSpeak::KICK_SERVER, "evil kick XD");
 * @endcode
 *
 * \subsection example2 2. Kick all Clients from a Virtual Server
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // query clientlist from virtual server
 *   $arr_ClientList = $ts3_VirtualServer->clientList();
 *
 *   // kick all clients online with a single command
 *   $ts3_VirtualServer->clientKick($arr_ClientList, TeamSpeak::KICK_SERVER, "evil kick XD");
 * @endcode
 *
 * \subsection example3 3. Print the Nicknames of connected Android Clients
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // query clientlist from virtual server and filter by platform
 *   $arr_ClientList = $ts3_VirtualServer->clientList(array("client_platform" => "Android"));
 *
 *   // walk through list of clients
 *   foreach($arr_ClientList as $ts3_Client)
 *   {
 *     echo $ts3_Client . " is using " . $ts3_Client["client_platform"] . "<br />\n";
 *   }
 * @endcode
 *
 * \subsection example4 4. Modify the Settings of each Virtual Server
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the server instance
 *   $ts3_ServerInstance = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/");
 *
 *   // walk through list of virtual servers
 *   foreach($ts3_ServerInstance as $ts3_VirtualServer)
 *   {
 *     // modify the virtual servers hostbanner URL only using the ArrayAccess interface
 *     $ts3_VirtualServer["virtualserver_hostbanner_gfx_url"] = "http://www.example.com/banners/banner01_468x60.jpg";
 *
 *     // modify the virtual servers hostbanner URL only using property overloading
 *     $ts3_VirtualServer->virtualserver_hostbanner_gfx_url = "http://www.example.com/banners/banner01_468x60.jpg";
 *
 *     // modify multiple virtual server properties at once
 *     $ts3_VirtualServer->modify(array(
 *       "virtualserver_hostbutton_tooltip" => "My Company",
 *       "virtualserver_hostbutton_url"     => "http://www.example.com",
 *       "virtualserver_hostbutton_gfx_url" => "http://www.example.com/buttons/button01_24x24.jpg",
 *     ));
 *   }
 * @endcode
 *
 * \subsection example5 5. Create a Privilege Key for a Server Group
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // spawn an object for the group using a specified name
 *   $arr_ServerGroup = $ts3_VirtualServer->serverGroupGetByName("Admins");
 *
 *   // create the privilege key
 *   $ts3_PrivilegeKey = $arr_ServerGroup->privilegeKeyCreate();
 * @endcode
 *
 * \subsection example6 6. Modify the Permissions of Admins on each Virtual Server
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the server instance
 *   $ts3_ServerInstance = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/");
 *
 *   // walk through list of virtual servers
 *   foreach($ts3_ServerInstance as $ts3_VirtualServer)
 *   {
 *     // identify the most powerful group on the virtual server
 *     $ts3_ServerGroup = $ts3_VirtualServer->serverGroupIdentify();
 *
 *     // assign a new permission
 *     $ts3_ServerGroup->permAssign("b_virtualserver_modify_hostbanner", TRUE);
 *
 *     // revoke an existing permission
 *     $ts3_ServerGroup->permRemove("b_virtualserver_modify_maxclients");
 *   }
 * @endcode
 *
 * \subsection example7 7. Create a new Virtual Server
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the server instance
 *   $ts3_ServerInstance = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/");
 *
 *   // create a virtual server and get its ID
 *   $new_sid = $ts3_ServerInstance->serverCreate(array(
 *     "virtualserver_name"               => "My TeamSpeak 3 Server",
 *     "virtualserver_maxclients"         => 64,
 *     "virtualserver_hostbutton_tooltip" => "My Company",
 *     "virtualserver_hostbutton_url"     => "http://www.example.com",
 *     "virtualserver_hostbutton_gfx_url" => "http://www.example.com/buttons/button01_24x24.jpg",
 *   ));
 * @endcode
 *
 * \subsection example8 8. Create a hierarchical Channel Stucture
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // create a top-level channel and get its ID
 *   $top_cid = $ts3_VirtualServer->channelCreate(array(
 *     "channel_name"           => "My Channel",
 *     "channel_topic"          => "This is a top-level channel",
 *     "channel_codec"          => TeamSpeak::CODEC_SPEEX_WIDEBAND,
 *     "channel_flag_permanent" => TRUE,
 *   ));
 *
 *   // create a sub-level channel and get its ID
 *   $sub_cid = $ts3_VirtualServer->channelCreate(array(
 *     "channel_name"           => "My Sub-Channel",
 *     "channel_topic"          => "This is a sub-level channel",
 *     "channel_codec"          => TeamSpeak::CODEC_SPEEX_NARROWBAND,
 *     "channel_flag_permanent" => TRUE,
 *     "cpid"                   => $top_cid,
 *   ));
 * @endcode
 *
 * \subsection example9 9. Create a simple TSViewer for your Website
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // build and display HTML treeview using custom image paths (remote icons will be embedded using data URI sheme)
 *   echo $ts3_VirtualServer->getViewer(new ESportsAlliance\TeamSpeakCore\Viewer\Html("images/viewericons/", "images/countryflags/", "data:image"));
 * @endcode
 *
 * \subsection example10 10. Update all outdated Audio Codecs to their Opus equivalent
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // walk through list of chanels
 *   foreach($ts3_VirtualServer->channelList() as $ts3_Channel)
 *   {
 *     if($ts3_Channel["channel_codec"] == TeamSpeak::CODEC_CELT_MONO)
 *     {
 *       $ts3_Channel["channel_codec"] = TeamSpeak::CODEC_OPUS_MUSIC;
 *     }
 *     elseif($ts3_Channel["channel_codec"] != TeamSpeak::CODEC_OPUS_MUSIC)
 *     {
 *       $ts3_Channel["channel_codec"] = TeamSpeak::CODEC_OPUS_VOICE;
 *     }
 *   }
 * @endcode
 *
 * \subsection example11 11. Display the Avatar of a connected User
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // spawn an object for the client using a specified nickname
 *   $ts3_Client = $ts3_VirtualServer->clientGetByName("John Doe");
 *
 *   // download the clients avatar file
 *   $avatar = $ts3_Client->avatarDownload();
 *
 *   // send header and display image
 *   header("Content-Type: " . ESportsAlliance\TeamSpeakCore\Helper\Convert::imageMimeType($avatar));
 *   echo $avatar;
 * @endcode
 *
 * \subsection example12 12. Create a Simple Bot waiting for Events
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // connect to local server in non-blocking mode, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987&blocking=0");
 *
 *   // get notified on incoming private messages
 *   $ts3_VirtualServer->notifyRegister("textprivate");
 *
 *   // register a callback for notifyTextmessage events
 *   ESportsAlliance\TeamSpeakCore\Helper\Signal::getInstance()->subscribe("notifyTextmessage", "onTextmessage");
 *
 *   // wait for events
 *   while(1) $ts3_VirtualServer->getAdapter()->wait();
 *
 *   // define a callback function
 *   function onTextmessage(ESportsAlliance\TeamSpeakCore\Adapter\ServerQuery_Event $event, TeamSpeak3_Node_Host $host)
 *   {
 *     echo "Client " . $event["invokername"] . " sent textmessage: " . $event["msg"];
 *   }
 * @endcode
 *
 * \subsection example13 13. Handle Errors using Exceptions and Custom Error Messages
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // register custom error message (supported placeholders are: %file, %line, %code and %mesg)
 *   ESportsAlliance\TeamSpeakCore\TeamSpeakException::registerCustomMessage(0x300, "The specified channel does not exist; server said: %mesg");
 *
 *   try
 *   {
 *     // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *     $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *     // spawn an object for the channel using a specified name
 *     $ts3_Channel = $ts3_VirtualServer->channelGetByName("I do not exist");
 *   }
 *   catch(ESportsAlliance\TeamSpeakCore\TeamSpeakException $e)
 *   {
 *     // print the error message returned by the server
 *     echo "Error " . $e->getCode() . ": " . $e->getMessage();
 *   }
 * @endcode
 *
 * \subsection example14 14. Save Connection State in Persistent Session Variable
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // start a PHP session
 *   session_start();
 *
 *   // connect to local server, authenticate and spawn an object for the virtual server on port 9987
 *   $ts3_VirtualServer = TeamSpeak::factory("serverquery://username:password@127.0.0.1:10011/?server_port=9987");
 *
 *   // save connection state (including login and selected virtual server)
 *   $_SESSION["_TS3"] = serialize($ts3_VirtualServer);
 * @endcode
 *
 * \subsection example15 15. Restore Connection State from Persistent Session Variable
 * @code
 *   // load framework files
 *   require_once("libraries/TeamSpeak/TeamSpeak.php");
 *
 *   // start a PHP session
 *   session_start();
 *
 *   // restore connection state
 *   $ts3_VirtualServer = unserialize($_SESSION["_TS3"]);
 *
 *   // send a text message to the server
 *   $ts3_VirtualServer->message("Hello World!");
 * @endcode
 *
 * Speed up new development and reduce maintenance costs by using the TS3 PHP Framework!
 */
