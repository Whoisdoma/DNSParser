<?php

namespace Whoisdoma\DNSParser\Result;

class AuthNSRecord extends AbstractResult {

    /**
     * The name server of the AuthNS record
     *
     * @var $nameserver
     */
    protected $nameserver;

    /**
     * The ip of the AuthNS
     *
     * @var $ip
     */
    protected $ip;

    /**
     * The location of the AuthNS
     *
     * @var $location
     */
    protected $location;

}