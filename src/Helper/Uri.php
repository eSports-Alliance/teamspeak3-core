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

use ESportsAlliance\TeamSpeakCore\Exception\HelperException;

/**
 * Class Uri
 * @package ESportsAlliance\TeamSpeakCore\Helper
 * @class Uri
 * @brief Helper class for URI handling.
 */
class Uri
{
    /**
     * Stores the URI scheme.
     *
     * @var StringHelper
     */
    protected $scheme = null;

    /**
     * Stores the URI username
     *
     * @var StringHelper
     */
    protected $user = null;

    /**
     * Stores the URI password.
     *
     * @var StringHelper
     */
    protected $pass = null;

    /**
     * Stores the URI host.
     *
     * @var StringHelper
     */
    protected $host = null;

    /**
     * Stores the URI port.
     *
     * @var StringHelper
     */
    protected $port = null;

    /**
     * Stores the URI path.
     *
     * @var StringHelper
     */
    protected $path = null;

    /**
     * Stores the URI query string.
     *
     * @var StringHelper
     */
    protected $query = null;

    /**
     * Stores the URI fragment string.
     *
     * @var StringHelper
     */
    protected $fragment = null;

    /**
     * Stores grammar rules for validation via regex.
     *
     * @var array
     */
    protected $regex = [];

    /**
     * Uri constructor.
     *
     * @param string $uri
     * @throws HelperException
     */
    public function __construct($uri)
    {
        $uri = explode(":", strval($uri), 2);

        $this->scheme = strtolower($uri[0]);
        $uriString = isset($uri[1]) ? $uri[1] : "";

        if (!ctype_alnum($this->scheme)) {
            throw new HelperException("invalid URI scheme '" . $this->scheme . "' supplied");
        }

        /* grammar rules for validation */
        $this->regex["alphanum"] = "[^\W_]";
        $this->regex["escaped"] = "(?:%[\da-fA-F]{2})";
        $this->regex["mark"] = "[-_.!~*'()\[\]]";
        $this->regex["reserved"] = "[;\/?:@&=+$,]";
        $this->regex["unreserved"] = "(?:" . $this->regex["alphanum"] . "|" . $this->regex["mark"] . ")";
        $this->regex["segment"] = "(?:(?:" . $this->regex["unreserved"] . "|" . $this->regex["escaped"] . "|[:@&=+$,;])*)";
        $this->regex["path"] = "(?:\/" . $this->regex["segment"] . "?)+";
        $this->regex["uric"] = "(?:" . $this->regex["reserved"] . "|" . $this->regex["unreserved"] . "|" . $this->regex["escaped"] . ")";

        if (strlen($uriString) > 0) {
            $this->parseUri($uriString);
        }

        if (!$this->isValid()) {
            throw new HelperException("invalid URI supplied");
        }
    }

    /**
     * Parses the scheme-specific portion of the URI and place its parts into instance variables.
     *
     * @return void
     * @throws HelperException
     */
    protected function parseUri($uriString = '')
    {
        $status = @preg_match("~^((//)([^/?#]*))([^?#]*)(\?([^#]*))?(#(.*))?$~", $uriString, $matches);

        if ($status === false) {
            throw new HelperException("URI scheme-specific decomposition failed");
        }

        if (!$status) {
            return;
        }

        $this->path = (isset($matches[4])) ? $matches[4] : '';
        $this->query = (isset($matches[6])) ? $matches[6] : '';
        $this->fragment = (isset($matches[8])) ? $matches[8] : '';

        $status = @preg_match("~^(([^:@]*)(:([^@]*))?@)?((?(?=[[])[[][^]]+[]]|[^:]+))(:(.*))?$~", (isset($matches[3])) ? $matches[3] : "", $matches);

        if ($status === false) {
            throw new HelperException("URI scheme-specific authority decomposition failed");
        }

        if (!$status) {
            return;
        }

        $this->user = isset($matches[2]) ? $matches[2] : "";
        $this->pass = isset($matches[4]) ? $matches[4] : "";
        $this->host = isset($matches[5]) === true ? preg_replace('~^\[([^]]+)\]$~', '\1', $matches[5]) : "";
        $this->port = isset($matches[7]) ? $matches[7] : "";
    }

    /**
     * Validate the current URI from the instance variables.
     *
     * @return boolean
     * @throws HelperException
     */
    public function isValid()
    {
        return ($this->checkUser() && $this->checkPass() && $this->checkHost() && $this->checkPort() && $this->checkPath() && $this->checkQuery() && $this->checkFragment());
    }

    /**
     * Returns TRUE if a given URI is valid.
     *
     * @param StringHelper $uri
     * @return boolean
     * @throws HelperException
     */
    public static function check($uri)
    {
        try {
            $uri = new self(strval($uri));
        } catch (HelperException $e) {
            return false;
        }

        return $uri->isValid();
    }

    /**
     * Returns TRUE if the URI has a scheme.
     *
     * @return boolean
     */
    public function hasScheme()
    {
        return strlen($this->scheme) ? true : false;
    }

    /**
     * Returns the scheme.
     *
     * @param mixed default
     * @return StringHelper
     */
    public function getScheme($default = null)
    {
        return ($this->hasScheme()) ? new StringHelper($this->scheme) : $default;
    }

    /**
     * Returns TRUE if the username is valid.
     *
     * @param string $username
     * @return boolean
     * @throws HelperException
     */
    public function checkUser($username = null)
    {
        if ($username === null) {
            $username = $this->user;
        }

        if (strlen($username) == 0) {
            return true;
        }

        $pattern = "/^(" . $this->regex["alphanum"] . "|" . $this->regex["mark"] . "|" . $this->regex["escaped"] . "|[;:&=+$,])+$/";
        $status = @preg_match($pattern, $username);

        if ($status === false) {
            throw new HelperException("URI username validation failed");
        }

        return ($status == 1);
    }

    /**
     * Returns TRUE if the URI has a username.
     *
     * @return boolean
     */
    public function hasUser()
    {
        return strlen($this->user) ? true : false;
    }

    /**
     * Returns the username.
     *
     * @param mixed default
     * @return StringHelper
     */
    public function getUser($default = null)
    {
        return ($this->hasUser()) ? new StringHelper(urldecode($this->user)) : $default;
    }

    /**
     * Returns TRUE if the password is valid.
     *
     * @param StringHelper $password
     * @return boolean
     * @throws HelperException
     */
    public function checkPass($password = null)
    {
        if ($password === null) {
            $password = $this->pass;
        }

        if (strlen($password) == 0) {
            return true;
        }

        $pattern = "/^(" . $this->regex["alphanum"] . "|" . $this->regex["mark"] . "|" . $this->regex["escaped"] . "|[;:&=+$,])+$/";
        $status = @preg_match($pattern, $password);

        if ($status === false) {
            throw new HelperException("URI password validation failed");
        }

        return ($status == 1);
    }

    /**
     * Returns TRUE if the URI has a password.
     *
     * @return boolean
     */
    public function hasPass()
    {
        return strlen($this->pass) ? true : false;
    }

    /**
     * Returns the password.
     *
     * @param mixed default
     * @return StringHelper
     */
    public function getPass($default = null)
    {
        return ($this->hasPass()) ? new StringHelper(urldecode($this->pass)) : $default;
    }

    /**
     * Returns TRUE if the host is valid.
     * todo: Implement check for host URI segment
     *
     * @param string $host
     * @return boolean
     */
    public function checkHost($host = null)
    {
        if ($host === null) {
            $host = $this->host;
        }

        return true;
    }

    /**
     * Returns TRUE if the URI has a host.
     *
     * @return boolean
     */
    public function hasHost()
    {
        return strlen($this->host) ? true : false;
    }

    /**
     * Returns the host.
     *
     * @param mixed default
     * @return StringHelper
     */
    public function getHost($default = null)
    {
        return ($this->hasHost()) ? new StringHelper(rawurldecode($this->host)) : $default;
    }

    /**
     * Returns TRUE if the port is valid.
     * todo: Implement check for port URI segment
     *
     * @param integer $port
     * @return boolean
     */
    public function checkPort($port = null)
    {
        if ($port === null) {
            $port = $this->port;
        }

        return true;
    }

    /**
     * Returns TRUE if the URI has a port.
     *
     * @return boolean
     */
    public function hasPort()
    {
        return strlen($this->port) ? true : false;
    }

    /**
     * Returns the port.
     *
     * @param mixed default
     * @return integer
     */
    public function getPort($default = null)
    {
        return ($this->hasPort()) ? intval($this->port) : $default;
    }

    /**
     * Returns TRUE if the path is valid.
     *
     * @param string $path
     * @return boolean
     * @throws HelperException
     */
    public function checkPath($path = null)
    {
        if ($path === null) {
            $path = $this->path;
        }

        if (strlen($path) == 0) {
            return true;
        }

        $pattern = "/^" . $this->regex["path"] . "$/";
        $status = @preg_match($pattern, $path);

        if ($status === false) {
            throw new HelperException("URI path validation failed");
        }

        return ($status == 1);
    }

    /**
     * Returns TRUE if the URI has a path.
     *
     * @return boolean
     */
    public function hasPath()
    {
        return strlen($this->path) ? true : false;
    }

    /**
     * Returns the path.
     *
     * @param mixed default
     * @return StringHelper
     */
    public function getPath($default = null)
    {
        return ($this->hasPath()) ? new StringHelper(rawurldecode($this->path)) : $default;
    }

    /**
     * Returns TRUE if the query string is valid.
     *
     * @param string $query
     * @return boolean
     * @throws HelperException
     */
    public function checkQuery($query = null)
    {
        if ($query === null) {
            $query = $this->query;
        }

        if (strlen($query) == 0) {
            return true;
        }

        $pattern = "/^" . $this->regex["uric"] . "*$/";
        $status = @preg_match($pattern, $query);

        if ($status === false) {
            throw new HelperException("URI query string validation failed");
        }

        return ($status == 1);
    }

    /**
     * Returns TRUE if the URI has a query string.
     *
     * @return boolean
     */
    public function hasQuery()
    {
        return strlen($this->query) ? true : false;
    }

    /**
     * Returns an array containing the query string elements.
     *
     * @param mixed $default
     * @return array
     */
    public function getQuery($default = [])
    {
        if (!$this->hasQuery()) {
            return $default;
        }

        parse_str(rawurldecode($this->query), $queryArray);

        return $queryArray;
    }

    /**
     * Returns TRUE if the URI has a query variable.
     *
     * @return boolean
     */
    public function hasQueryVar($key)
    {
        if (!$this->hasQuery()) {
            return false;
        }

        parse_str($this->query, $queryArray);

        return array_key_exists($key, $queryArray) ? true : false;
    }

    /**
     * Returns a single variable from the query string.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getQueryVar($key, $default = null)
    {
        if (!$this->hasQuery()) {
            return $default;
        }

        parse_str(rawurldecode($this->query), $queryArray);

        if (array_key_exists($key, $queryArray)) {
            $val = $queryArray[$key];

            if (ctype_digit($val)) {
                return intval($val);
            } elseif (is_string($val)) {
                return new StringHelper($val);
            } else {
                return $val;
            }
        }

        return $default;
    }

    /**
     * Returns TRUE if the fragment string is valid.
     *
     * @param string $fragment
     * @return boolean
     * @throws HelperException
     */
    public function checkFragment($fragment = null)
    {
        if ($fragment === null) {
            $fragment = $this->fragment;
        }

        if (strlen($fragment) == 0) {
            return true;
        }

        $pattern = "/^" . $this->regex["uric"] . "*$/";
        $status = @preg_match($pattern, $fragment);

        if ($status === false) {
            throw new HelperException("URI fragment validation failed");
        }

        return ($status == 1);
    }

    /**
     * Returns TRUE if the URI has a fragment string.
     *
     * @return boolean
     */
    public function hasFragment()
    {
        return strlen($this->fragment) ? true : false;
    }

    /**
     * Returns the fragment.
     *
     * @param mixed default
     * @return StringHelper
     */
    public function getFragment($default = null)
    {
        return ($this->hasFragment()) ? new StringHelper(rawurldecode($this->fragment)) : $default;
    }

    /**
     * Returns a specified instance parameter from the $_REQUEST array.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getUserParam($key, $default = null)
    {
        return (array_key_exists($key, $_REQUEST) && !empty($_REQUEST[$key])) ? self::stripslashesRecursive($_REQUEST[$key]) : $default;
    }

    /**
     * Returns a specified environment parameter from the $_SERVER array.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getHostParam($key, $default = null)
    {
        return (array_key_exists($key, $_SERVER) && !empty($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }

    /**
     * Returns a specified session parameter from the $_SESSION array.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getSessParam($key, $default = null)
    {
        return (array_key_exists($key, $_SESSION) && !empty($_SESSION[$key])) ? $_SESSION[$key] : $default;
    }

    /**
     * Returns an array containing the three main parts of a FQDN (Fully Qualified Domain Name), including the
     * top-level domain, the second-level domains or hostname and the third-level domain.
     *
     * @param string $hostname
     * @return array
     */
    public static function getFQDNParts($hostname)
    {
        if (!preg_match("/^([a-z0-9][a-z0-9-]{0,62}\.)*([a-z0-9][a-z0-9-]{0,62}\.)+([a-z]{2,6})$/i", $hostname, $matches)) {
            return [];
        }

        $parts["tld"] = $matches[3];
        $parts["2nd"] = $matches[2];
        $parts["3rd"] = $matches[1];

        return $parts;
    }

    /**
     * Returns the applications host address.
     *
     * @return StringHelper
     */
    public static function getHostUri()
    {
        $sheme = (self::getHostParam("HTTPS") == "on") ? "https" : "http";

        $serverName = new StringHelper(self::getHostParam("HTTP_HOST"));
        $serverPort = self::getHostParam("SERVER_PORT");
        $serverPort = ($serverPort != 80 && $serverPort != 443) ? ":" . $serverPort : "";

        if ($serverName->endsWith($serverPort)) {
            $serverName = $serverName->replace($serverPort, "");
        }

        return new StringHelper($sheme . "://" . $serverName . $serverPort);
    }

    /**
     * Returns the applications base address.
     *
     * @return StringHelper
     */
    public static function getBaseUri()
    {
        $scriptPath = new StringHelper(dirname(self::getHostParam("SCRIPT_NAME")));

        return self::getHostUri()->append(($scriptPath == DIRECTORY_SEPARATOR ? "" : $scriptPath) . "/");
    }

    /**
     * Strips slashes from each element of an array using stripslashes().
     *
     * @param mixed $var
     * @return mixed
     */
    protected static function stripslashesRecursive($var)
    {
        if (!is_array($var)) {
            return stripslashes(strval($var));
        }

        foreach ($var as $key => $val) {
            $var[$key] = (is_array($val)) ? self::stripslashesRecursive($val) : stripslashes(strval($val));
        }

        return $var;
    }
}
