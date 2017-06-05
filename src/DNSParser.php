<?php

namespace Whoisdoma\DNSParser;

use Whoisdoma\DNSParser\Helpers\Utils;
use Whoisdoma\DNSParser\Result\AuthNSRecord;
use Whoisdoma\DNSParser\Result\Result;
use Whoisdoma\DNSParser\Exception\AbstractException;
use Whoisdoma\DNSParser\Exception\NoQueryException;
use Whoisdoma\DNSParser\Result\SOARecord;

class DNSParser {


    /**
     * DNSParserResult object
     *
     * @var \Whoisdoma\DNSParser\Result\Result
     * @access protected
     */
    protected $Result;

    /**
     * Query string sent to the DNSParser
     *
     * @var object
     * @access protected
     */
    protected $Query;

    /**
     * Should the exceptions be thrown or caugth and trapped in the response?
     *
     * @var boolean
     * @access protected
     */
    protected $throwExceptions = false;

    /**
     * Output format 'object', 'array', 'json', 'serialize' or 'xml'
     *
     * @var string
     * @access protected
     */
    protected $format = 'object';
    /**
     * Output format for dates
     *
     * @var string
     * @access protected
     */
    protected $dateformat = '%Y-%m-%d %H:%M:%S';

    /**
     * Activate cache
     *
     * @var string Cache location
     * @access protected
     */
    protected $cachePath = null;

    /**
     * Creates a DNSParser object
     *
     * @param  string $format
     */
    public function __construct($format = 'object')
    {
        $this->Query = new \stdClass();
        $this->setFormat($format);
    }

    /**
     * Returns DNSParserResult instance
     *
     * @return object
     */
    public function getResult()
    {
        return $this->Result;
    }

    /**
     * Set output format
     *
     * You may choose between 'object', 'array', 'json', 'serialize' or 'xml' output format
     *
     * @param  string $format
     * @return void
     */
    public function setFormat($format = 'object')
    {
        $this->format = filter_var($format, FILTER_SANITIZE_STRING);
    }
    /**
     * Set date format
     *
     * You may choose your own date format. Please check http://php.net/strftime for further
     * details
     *
     * @param  string $dateformat
     * @return void
     */
    public function setDateFormat($dateformat = '%Y-%m-%d %H:%M:%S')
    {
        $this->dateformat = $dateformat;
    }

    /**
     * Set the throwExceptions flag
     *
     * Set whether exceptions encounted in the dispatch loop should be thrown
     * or caught and trapped in the response object.
     *
     * Default behaviour is to trap them in the response object; call this
     * method to have them thrown.
     *
     * @param  boolean $throwExceptions
     * @return void
     */
    public function throwExceptions($throwExceptions = false)
    {
        $this->throwExceptions = filter_var($throwExceptions, FILTER_VALIDATE_BOOLEAN);
    }
    /**
     * Set the path to use for on-disk cache. If NULL, cache is disabled.
     *
     * @param string|null $path Cache path
     */
    public function setCachePath($path)
    {
        $this->cachePath = $path;
    }

    public function lookup($query = '')
    {
        $this->Result = new Result();

        try {
            if ($query == '') {
                throw new NoQueryException('No lookup query given');
            }

            $this->Result->addItem('success', true);
            $this->Result->addItem('domain', $query);
            $this->Result->addItem('ip', Utils::getIP($query));
            $this->Result->addItem('authns', $this->Result->getAuthNS($query));
            $this->Result->addItem('soa', $this->Result->getSOARecord($query));
            $this->Result->addItem('records', Utils::getallRecords($query));

        } catch (AbstractException $e) {
            if ($this->throwExceptions) {
                throw $e;
            }

            $this->Result->addItem('success', false);
            $this->Result->addItem('exception', $e->getMessage());

        }


        // peparing output of Result by format
        switch ($this->format) {
            case 'json':
                return $this->Result->toJson();
                break;
            case 'serialize':
                return $this->Result->serialize();
                break;
            case 'array':
                return $this->Result->toArray();
                break;
            case 'xml':
                return $this->Result->toXml();
                break;
            default:
                return $this->Result;
        }
    }

    public function getSOA($query = '')
    {
        $this->Result = new SOARecord();

        try {
            if ($query == '') {
                throw new NoQueryException('No lookup query given');
            }

            //get the soa dns record
            $soaData = dns_get_record($query, DNS_SOA);

            foreach($soaData as $soaInfo) {

                $this->Result->addItem('nameserver', $soaInfo['mname']);
                $this->Result->addItem('email', $soaInfo['rname']);
                $this->Result->addItem('serial', $soaInfo['serial']);
                $this->Result->addItem('refresh', $soaInfo['refresh']);
                $this->Result->addItem('retry', $soaInfo['retry']);
                $this->Result->addItem('expiry', $soaInfo['expire']);
                $this->Result->addItem('minimum', $soaInfo['minimum-ttl']);

            }

        } catch (AbstractException $e) {
            if ($this->throwExceptions) {
                throw $e;
            }

            $this->Result->addItem('success', false);
            $this->Result->addItem('exception', $e->getMessage());

        }


        // peparing output of Result by format
        switch ($this->format) {
            case 'json':
                return $this->Result->toJson();
                break;
            case 'serialize':
                return $this->Result->serialize();
                break;
            case 'array':
                return $this->Result->toArray();
                break;
            case 'xml':
                return $this->Result->toXml();
                break;
            default:
                return $this->Result;
        }
    }


}