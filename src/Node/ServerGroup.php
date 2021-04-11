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

namespace ESportsAlliance\TeamSpeakCore\Node;

use ESportsAlliance\TeamSpeakCore\TeamSpeak;
use ESportsAlliance\TeamSpeakCore\Exception\NodeException;

/**
 * @class ServerGroup
 * @package ESportsAlliance\TeamSpeakCore\Node
 * @brief Class describing a TeamSpeak 3 server group and all it's parameters.
 */
class ServerGroup extends Group
{
    /**
     * The ServerGroup constructor.
     *
     * @param Server $server
     * @param array $info
     * @param string $index
     * @throws NodeException
     */
    public function __construct(Server $server, array $info, $index = "sgid")
    {
        $this->parent = $server;
        $this->nodeInfo = $info;

        if (!array_key_exists($index, $this->nodeInfo)) {
            throw new NodeException("invalid groupID", 0xA00);
        }

        $this->nodeId = $this->nodeInfo[$index];
    }

    /**
     * Renames the server group specified.
     *
     * @param string $name
     * @return void
     */
    public function rename($name)
    {
        $this->getParent()->serverGroupRename($this->getId(), $name);
    }

    /**
     * Deletes the server group. If $force is set to 1, the server group will be
     * deleted even if there are clients within.
     *
     * @param boolean $force
     * @return void
     */
    public function delete($force = false)
    {
        $this->getParent()->serverGroupDelete($this->getId(), $force);
    }

    /**
     * Creates a copy of the server group and returns the new groups ID.
     *
     * @param string $name
     * @param integer $tsgid
     * @param integer $type
     * @return integer
     */
    public function copy($name = null, $tsgid = 0, $type = TeamSpeak::GROUP_DBTYPE_REGULAR)
    {
        return $this->getParent()->serverGroupCopy($this->getId(), $name, $tsgid, $type);
    }

    /**
     * Returns a list of permissions assigned to the server group.
     *
     * @param boolean $permsid
     * @return array
     */
    public function permList($permsid = false)
    {
        return $this->getParent()->serverGroupPermList($this->getId(), $permsid);
    }

    /**
     * Adds a set of specified permissions to the server group. Multiple permissions
     * can be added by providing the four parameters of each permission in separate arrays.
     *
     * @param integer $permid
     * @param integer $permvalue
     * @param integer $permnegated
     * @param integer $permskip
     * @return void
     */
    public function permAssign($permid, $permvalue, $permnegated = 0, $permskip = 0)
    {
        $this->getParent()->serverGroupPermAssign($this->getId(), $permid, $permvalue, $permnegated, $permskip);
    }

    /**
     * Alias for permAssign().
     *
     * @deprecated
     */
    public function permAssignByName($permname, $permvalue, $permnegated = false, $permskip = false)
    {
        $this->permAssign($permname, $permvalue, $permnegated, $permskip);
    }

    /**
     * Removes a set of specified permissions from the server group. Multiple
     * permissions can be removed at once.
     *
     * @param integer $permid
     * @return void
     */
    public function permRemove($permid)
    {
        $this->getParent()->serverGroupPermRemove($this->getId(), $permid);
    }

    /**
     * Alias for permRemove().
     *
     * @deprecated
     */
    public function permRemoveByName($permname)
    {
        $this->permRemove($permname);
    }

    /**
     * Returns a list of clients assigned to the server group specified.
     *
     * @return array
     */
    public function clientList()
    {
        return $this->getParent()->serverGroupClientList($this->getId());
    }

    /**
     * Adds a client to the server group specified. Please note that a client cannot be
     * added to default groups or template groups.
     *
     * @param integer $cldbid
     * @return void
     */
    public function clientAdd($cldbid)
    {
        $this->getParent()->serverGroupClientAdd($this->getId(), $cldbid);
    }

    /**
     * Removes a client from the server group.
     *
     * @param integer $cldbid
     * @return void
     */
    public function clientDel($cldbid)
    {
        $this->getParent()->serverGroupClientDel($this->getId(), $cldbid);
    }

    /**
     * Alias for privilegeKeyCreate().
     *
     * @deprecated
     */
    public function tokenCreate($description = null, $customset = null)
    {
        return $this->privilegeKeyCreate($description, $customset);
    }

    /**
     * Creates a new privilege key (token) for the server group and returns the key.
     *
     * @param string $description
     * @param string $customset
     * @return string
     */
    public function privilegeKeyCreate($description = null, $customset = null)
    {
        return $this->getParent()
                    ->privilegeKeyCreate($this->getId(), TeamSpeak::TOKEN_SERVERGROUP, 0, $description, $customset);
    }

    /**
     * @ignore
     */
    protected function fetchNodeList()
    {
        $this->nodeList = [];

        foreach ($this->getParent()->clientList() as $client) {
            if (in_array($this->getId(), explode(",", $client["client_servergroups"]))) {
                $this->nodeList[] = $client;
            }
        }
    }

    /**
     * Returns a unique identifier for the node which can be used as a HTML property.
     *
     * @return string
     */
    public function getUniqueId()
    {
        return $this->getParent()->getUniqueId() . "_sg" . $this->getId();
    }

    /**
     * Returns the name of a possible icon to display the node object.
     *
     * @return string
     */
    public function getIcon()
    {
        return "group_server";
    }
}
