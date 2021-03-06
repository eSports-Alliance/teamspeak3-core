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

namespace ESportsAlliance\TeamSpeakCore\Helper;

use ESportsAlliance\TeamSpeakCore\TeamSpeak;

/**
 * Class Convert
 * @package ESportsAlliance\TeamSpeakCore\Helper
 * @class Convert
 * @brief Helper class for data conversion.
 */
class Convert
{
    /**
     * Converts bytes to a human readable value.
     *
     * @param integer $bytes
     * @return string
     */
    public static function bytes($bytes)
    {
        // @todo: Fix precision lost from multiple rounding
        $kbytes = sprintf("%.02f", $bytes / 1024);
        $mbytes = sprintf("%.02f", $kbytes / 1024);
        $gbytes = sprintf("%.02f", $mbytes / 1024);
        $tbytes = sprintf("%.02f", $gbytes / 1024);

        // @todo: Fix assuming non-negative $bytes value, without validation
        // Recommend something like: if( (float)$xbytes != 0 )
        if ($tbytes >= 1) {
            return $tbytes . " TB";
        }
        if ($gbytes >= 1) {
            return $gbytes . " GB";
        }
        if ($mbytes >= 1) {
            return $mbytes . " MB";
        }
        if ($kbytes >= 1) {
            return $kbytes . " KB";
        }

        return $bytes . " B";
    }

    /**
     * Converts seconds/milliseconds to a human readable value.
     *
     * Note: Assumes non-negative integer, but no validation
     * @param integer $seconds
     * @param boolean $is_ms
     * @param string $format
     * @return string
     * @todo: Handle negative integer $seconds, or invalidate
     *
     */
    public static function seconds($seconds, $is_ms = false, $format = "%dD %02d:%02d:%02d")
    {
        if ($is_ms) {
            $seconds = $seconds / 1000;
        }

        return sprintf($format, $seconds / 60 / 60 / 24, ($seconds / 60 / 60) % 24, ($seconds / 60) % 60, $seconds % 60);
    }

    /**
     * Converts a given codec ID to a human readable name.
     *
     * @param integer $codec
     * @return string
     */
    public static function codec($codec)
    {
        if ($codec == TeamSpeak::CODEC_SPEEX_NARROWBAND) {
            return "Speex Narrowband";
        }
        if ($codec == TeamSpeak::CODEC_SPEEX_WIDEBAND) {
            return "Speex Wideband";
        }
        if ($codec == TeamSpeak::CODEC_SPEEX_ULTRAWIDEBAND) {
            return "Speex Ultra-Wideband";
        }
        if ($codec == TeamSpeak::CODEC_CELT_MONO) {
            return "CELT Mono";
        }
        if ($codec == TeamSpeak::CODEC_OPUS_VOICE) {
            return "Opus Voice";
        }
        if ($codec == TeamSpeak::CODEC_OPUS_MUSIC) {
            return "Opus Music";
        }

        return "Unknown";
    }

    /**
     * Converts a given group type ID to a human readable name.
     *
     * @param integer $type
     * @return string
     */
    public static function groupType($type)
    {
        if ($type == TeamSpeak::GROUP_DBTYPE_TEMPLATE) {
            return "Template";
        }
        if ($type == TeamSpeak::GROUP_DBTYPE_REGULAR) {
            return "Regular";
        }
        if ($type == TeamSpeak::GROUP_DBTYPE_SERVERQUERY) {
            return "ServerQuery";
        }

        return "Unknown";
    }

    /**
     * Converts a given permission type ID to a human readable name.
     *
     * @param integer $type
     * @return string
     */
    public static function permissionType($type)
    {
        if ($type == TeamSpeak::PERM_TYPE_SERVERGROUP) {
            return "Server Group";
        }
        if ($type == TeamSpeak::PERM_TYPE_CLIENT) {
            return "Client";
        }
        if ($type == TeamSpeak::PERM_TYPE_CHANNEL) {
            return "Channel";
        }
        if ($type == TeamSpeak::PERM_TYPE_CHANNELGROUP) {
            return "Channel Group";
        }
        if ($type == TeamSpeak::PERM_TYPE_CHANNELCLIENT) {
            return "Channel Client";
        }

        return "Unknown";
    }

    /**
     * Converts a given permission category value to a human readable name.
     *
     * @param integer $pcat
     * @return string
     */
    public static function permissionCategory($pcat)
    {
        if ($pcat == TeamSpeak::PERM_CAT_GLOBAL) {
            return "Global";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GLOBAL_INFORMATION) {
            return "Global / Information";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GLOBAL_SERVER_MGMT) {
            return "Global / Virtual Server Management";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GLOBAL_ADM_ACTIONS) {
            return "Global / Administration";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GLOBAL_SETTINGS) {
            return "Global / Settings";
        }
        if ($pcat == TeamSpeak::PERM_CAT_SERVER) {
            return "Virtual Server";
        }
        if ($pcat == TeamSpeak::PERM_CAT_SERVER_INFORMATION) {
            return "Virtual Server / Information";
        }
        if ($pcat == TeamSpeak::PERM_CAT_SERVER_ADM_ACTIONS) {
            return "Virtual Server / Administration";
        }
        if ($pcat == TeamSpeak::PERM_CAT_SERVER_SETTINGS) {
            return "Virtual Server / Settings";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CHANNEL) {
            return "Channel";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CHANNEL_INFORMATION) {
            return "Channel / Information";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CHANNEL_CREATE) {
            return "Channel / Create";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CHANNEL_MODIFY) {
            return "Channel / Modify";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CHANNEL_DELETE) {
            return "Channel / Delete";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CHANNEL_ACCESS) {
            return "Channel / Access";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GROUP) {
            return "Group";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GROUP_INFORMATION) {
            return "Group / Information";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GROUP_CREATE) {
            return "Group / Create";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GROUP_MODIFY) {
            return "Group / Modify";
        }
        if ($pcat == TeamSpeak::PERM_CAT_GROUP_DELETE) {
            return "Group / Delete";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CLIENT) {
            return "Client";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CLIENT_INFORMATION) {
            return "Client / Information";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CLIENT_ADM_ACTIONS) {
            return "Client / Admin";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CLIENT_BASICS) {
            return "Client / Basics";
        }
        if ($pcat == TeamSpeak::PERM_CAT_CLIENT_MODIFY) {
            return "Client / Modify";
        }
        if ($pcat == TeamSpeak::PERM_CAT_FILETRANSFER) {
            return "File Transfer";
        }
        if ($pcat == TeamSpeak::PERM_CAT_NEEDED_MODIFY_POWER) {
            return "Grant";
        }

        return "Unknown";
    }

    /**
     * Converts a given log level ID to a human readable name and vice versa.
     *
     * @param mixed $level
     * @return string
     */
    public static function logLevel($level)
    {
        if (is_numeric($level)) {
            if ($level == TeamSpeak::LOGLEVEL_CRITICAL) {
                return "CRITICAL";
            }
            if ($level == TeamSpeak::LOGLEVEL_ERROR) {
                return "ERROR";
            }
            if ($level == TeamSpeak::LOGLEVEL_DEBUG) {
                return "DEBUG";
            }
            if ($level == TeamSpeak::LOGLEVEL_WARNING) {
                return "WARNING";
            }
            if ($level == TeamSpeak::LOGLEVEL_INFO) {
                return "INFO";
            }

            return "DEVELOP";
        } else {
            if (strtoupper($level) == "CRITICAL") {
                return TeamSpeak::LOGLEVEL_CRITICAL;
            }
            if (strtoupper($level) == "ERROR") {
                return TeamSpeak::LOGLEVEL_ERROR;
            }
            if (strtoupper($level) == "DEBUG") {
                return TeamSpeak::LOGLEVEL_DEBUG;
            }
            if (strtoupper($level) == "WARNING") {
                return TeamSpeak::LOGLEVEL_WARNING;
            }
            if (strtoupper($level) == "INFO") {
                return TeamSpeak::LOGLEVEL_INFO;
            }

            return TeamSpeak::LOGLEVEL_DEVEL;
        }
    }

    /**
     * Converts a specified log entry string into an array containing the data.
     *
     * @param string $entry
     * @return array
     */
    public static function logEntry($entry)
    {
        $parts = explode("|", $entry, 5);
        $array = [];

        if (count($parts) != 5) {
            $array["timestamp"] = 0;
            $array["level"] = TeamSpeak::LOGLEVEL_ERROR;
            $array["channel"] = "ParamParser";
            $array["server_id"] = "";
            $array["msg"] = StringHelper::factory("convert error (" . trim($entry) . ")");
            $array["msg_plain"] = $entry;
            $array["malformed"] = true;
        } else {
            $array["timestamp"] = strtotime(trim($parts[0]) . " UTC");
            $array["level"] = self::logLevel(trim($parts[1]));
            $array["channel"] = trim($parts[2]);
            $array["server_id"] = trim($parts[3]);
            $array["msg"] = StringHelper::factory(trim($parts[4]));
            $array["msg_plain"] = $entry;
            $array["malformed"] = false;
        }

        return $array;
    }

    /**
     * Converts a specified 32-bit unsigned integer value to a signed 32-bit integer value.
     *
     * @param integer $unsigned
     * @return integer
     */
    public static function iconId($unsigned)
    {
        $signed = (int)$unsigned;

        if (PHP_INT_SIZE > 4) { // 64-bit
            if ($signed & 0x80000000) {
                return $signed - 0x100000000;
            }
        }

        return $signed;
    }

    /**
     * Converts a given string to a ServerQuery password hash.
     *
     * @param string $plain
     * @return string
     */
    public static function password($plain)
    {
        return base64_encode(sha1($plain, true));
    }

    /**
     * Returns a client-like formatted version of the TeamSpeak 3 version string.
     *
     * @param string $version
     * @param string $format
     * @return StringHelper
     */
    public static function version($version, $format = "Y-m-d h:i:s")
    {
        if (!$version instanceof StringHelper) {
            $version = new StringHelper($version);
        }

        $buildno = $version->section("[", 1)->filterDigits()->toInt();

        return ($buildno <= 15001) ? $version : $version->section("[")->append("(" . date($format, $buildno) . ")");
    }

    /**
     * Returns a client-like short-formatted version of the TeamSpeak 3 version string.
     *
     * @param string $version
     * @return StringHelper
     */
    public static function versionShort($version)
    {
        if (!$version instanceof StringHelper) {
            $version = new StringHelper($version);
        }

        return $version->section(" ", 0);
    }

    /**
     * Tries to detect the type of an image by a given string and returns it.
     *
     * @param string $binary
     * @return string
     */
    public static function imageMimeType($binary)
    {
        if (!preg_match('/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/', $binary, $matches)) {
            return "image/svg+xml";
        }

        $type = [
            1 => "image/jpeg",
            2 => "image/gif",
            3 => "image/png",
            4 => "image/x-windows-bmp",
            5 => "image/tiff",
            6 => "image/x-ilbm",
        ];

        return $type[count($matches) - 1];
    }
}
