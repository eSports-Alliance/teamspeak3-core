<?php


namespace ESportsAlliance\TeamSpeakCore\Adapter;


use ESportsAlliance\TeamSpeakCore\Exception\AdapterException;
use ESportsAlliance\TeamSpeakCore\Helper\Profiler;
use ESportsAlliance\TeamSpeakCore\Helper\Signal;
use ESportsAlliance\TeamSpeakCore\TeamSpeak;
use ESportsAlliance\TeamSpeakCore\Transport\MockTCP;

class MockServerQuery extends ServerQuery
{

    /**
     * Connects the Transport object and performs initial actions on the remote
     * server.
     *
     * @throws AdapterException
     * @return void
     */
    protected function syn()
    {
        $this->initTransport($this->options, MockTCP::class);
        $this->transport->setAdapter($this);

        Profiler::init(spl_object_hash($this));

        $rdy = $this->getTransport()->readLine();

        if (!$rdy->startsWith(TeamSpeak::TS3_PROTO_IDENT) && !$rdy->startsWith(TeamSpeak::TEA_PROTO_IDENT) && !(defined("CUSTOM_PROTO_IDENT") && $rdy->startsWith(CUSTOM_PROTO_IDENT))) {
            throw new AdapterException("invalid reply from the server (" . $rdy . ")");
        }

        Signal::getInstance()->emit("serverqueryConnected", $this);
    }
}
