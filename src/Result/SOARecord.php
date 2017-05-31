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
}