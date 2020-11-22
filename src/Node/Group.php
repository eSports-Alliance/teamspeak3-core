<?php


namespace PlanetTeamSpeak\TeamSpeak3Framework\Node;

use PlanetTeamSpeak\TeamSpeak3Framework\Exception\ServerQueryException;
use PlanetTeamSpeak\TeamSpeak3Framework\Helper\StringHelper;
use PlanetTeamSpeak\TeamSpeak3Framework\TeamSpeak3;

abstract class Group extends Node
{

    /**
     * Sends a text message to all clients residing in the channel group on the virtual server.
     *
     * @param  string $msg
     * @return void
     * @throws ServerQueryException
     */
    public function message($msg)
    {
        foreach ($this as $client) {
            try {
                $this->execute("sendtextmessage", ["msg" => $msg, "target" => $client, "targetmode" => TeamSpeak3::TEXTMSG_CLIENT]);
            } catch (ServerQueryException $e) {
                /* ERROR_client_invalid_id */
                if ($e->getCode() != 0x0200) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Downloads and returns the channel groups icon file content.
     *
     * @return StringHelper|void
     */
    public function iconDownload()
    {
        if ($this->iconIsLocal("iconid") || $this["iconid"] == 0) {
            return;
        }

        $download = $this->getParent()->transferInitDownload(rand(0x0000, 0xFFFF), 0, $this->iconGetName("iconid"));
        $transfer = TeamSpeak3::factory("filetransfer://" . (strstr($download["host"], ":") !== false ? "[" . $download["host"] . "]" : $download["host"]) . ":" . $download["port"]);

        return $transfer->download($download["ftkey"], $download["size"]);
    }

    /**
     * Returns a symbol representing the node.
     *
     * @return string
     */
    public function getSymbol()
    {
        return "%";
    }

    /**
     * Returns a string representation of this node.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this["name"];
    }
}