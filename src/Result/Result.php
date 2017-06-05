<?php

namespace Whoisdoma\DNSParser\Result;

use Whoisdoma\DNSParser\Exception\NoQueryException;
use Whoisdoma\DNSParser\Helpers\Utils;

class Result extends AbstractResult {

    /**
     * Indicates if the dns query was successful
     *
     * @var boolean
     */
    protected $success;

    /**
     * The domain to perform dns queries for
     *
     * @var string
     * @access protected
     */
    protected $domain;

    /**
     * The ip address of the domain
     *
     * @var string
     */
    protected $ip;

    /**
     * The authority nameserver for the domain
     *
     * @var string
     */
    protected $authns;

    /**
     * The soa record for the authns
     *
     * @var array
     */
    protected $soa;

    /**
     * All records for the requested query
     *
     * @var array
     */
    protected $records;


    /**
     * Exception
     *
     * @var string
     * @access protected
     */
    protected $exception;


    public function __construct()
    {

    }

    public function getAuthNS($domain) {

        //get auth ns data
        $authnsData = dns_get_record($domain, DNS_NS);
        $jsondata = json_encode($authnsData);
        $nsjson = json_decode($jsondata);
        $authns = array();

        //put the results into a nice array
        foreach($nsjson as $nsdata)
        {
            $authns[] = array(
                'nameserver' => $nsdata->target,
                'ip' => Utils::getIP($nsdata->target),
                'location' => Utils::getipLocation(Utils::getIP($nsdata->target))
            );

            //$this->authns->addItem("location", Utils::getipLocation(Utils::getipLocation($nsdata->target)));
        }

        return $authns;
    }

    public function getSOARecord($domain) {

        //get the soa dns record
        $soaData = dns_get_record($domain, DNS_SOA);

        foreach($soaData as $soaInfo) {

            $soaRecord = array(
                'nameserver' => $soaInfo['mname'],
                'email' => $soaInfo['rname'],
                'serial' => $soaInfo['serial'],
                'refresh' => $soaInfo['refresh'],
                'retry' => $soaInfo['retry'],
                'expiry' => $soaInfo['expire'],
                'minimum' => $soaInfo['minimum-ttl']
            );

            return $soaRecord;

        }
    }


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

        $xml->addChild('success', true);
        $xml->addChild('domain', $this->domain);



        return $xml->asXML();
    }


}