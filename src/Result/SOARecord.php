<?php

namespace Whoisdoma\DNSParser\Result;

class SOARecord extends AbstractResult {

    /**
     * @var $nameserver
     */
    protected $nameserver;

    /**
     * @var $email
     */
    protected $email;

    /**
     * @var $serial
     */
    protected $serial;

    /**
     * @var $refresh
     */
    protected $refresh;

    /**
     * @var $retry;
     */
    protected $retry;

    /**
     * @var $expiry;
     */
    protected $expiry;

    /**
     * @var $minimum;
     */
    protected $minimum;

    /**
     * Serialize properties
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->toArray());
    }

    /**
     * Convert properties to xml by using SimpleXMLElement
     *
     * @return string
     */
    public function toXml()
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" standalone="yes"?><parser></parser>');

        $xml->addChild('nameserver', $this->nameserver);
        $xml->addChild('email', $this->email);
        $xml->addChild('serial', $this->serial);
        $xml->addChild('refresh', $this->refresh);
        $xml->addChild('retry', $this->retry);
        $xml->addChild('expiry', $this->expiry);
        $xml->addChild('minimum', $this->minimum);

        return $xml->asXML();
    }
}