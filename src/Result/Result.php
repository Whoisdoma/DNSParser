<?php

namespace Whoisdoma\DNSParser\Result;

class Result extends AbstractResult {

    /**
     * The domain to perform dns queries for
     *
     * @var string
     * @access protected
     */
    protected $domain;

    /**
     * Indicates if the dns query was successful
     *
     * @var boolean
     */
    protected $success;

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
     * Array of Nameservers
     *
     * @var array
     * @access protected
     */
    protected $nameserver;

    /**
     * Array of Nameservers IPs
     *
     * @var array
     * @access protected
     */
    protected $ips;

    /**
     * Raw response from dns query
     *
     * @var array
     * @access protected
     */
    public $rawdata = array();

    /**
     * Exception
     *
     * @var string
     * @access protected
     */
    protected $exception;

    public function __construct()
    {
        $this->authns = new \stdClass();
        $this->soa = new \stdClass();
    }

    /**
     * @param  string $target
     * @param  mixed $value
     * @param bool $append Append values rather than overwriting? (Ignored for registrars and contacts)
     * @return void
     */
    public function addItem($target, $value, $append = false)
    {
        if (is_array($value) && sizeof($value) === 1) {
            $value = $value[0];
        }
        // Don't overwrite existing values with empty values, unless we explicitly pass through NULL
        if (is_array($value) && (sizeof($value) === 0)) {
            return;
        }
        if (is_string($value) && (strlen($value) < 1) && ($value !== NULL)) {
            return;
        }

        if ($target == 'rawdata') {
            $this->{$target}[] = $value;
            return;
        }



        if ($append && isset($this->{$target})) {
            if (!is_array($this->{$target})) {
                $this->{$target} = array($this->{$target});
            }
            $this->{$target}[] = $value;
        } else {
            $this->{$target} = $value;
        }
    }

    /**
     * Resets the result properties to empty
     *
     * @return void
     */
    public function reset()
    {
        foreach ($this as $key => $value) {
            $this->$key = null;
        }

        // need to set contacts to stdClass otherwise it will not working to
        // add items again
        $this->authns = new \stdClass();
        $this->soa = new \stdClass();
    }

    /**
     * Convert properties to array
     *
     * @return array
     */
    public function toArray()
    {
        $output = get_object_vars($this);
        $network = array();

        if (!empty($this->network)) {
            // lookup network for all properties
            foreach ($this->network as $type => $value) {
                // if there is an object we need to convert it to array
                if (is_object($value)) {
                    $value = (array) $value;
                    // if converted array is empty there is no need to add it
                    if (! empty($value)) {
                        $network[$type] = $value;
                    }
                } else {
                    $network[$type] = $value;
                }
            }
            $output['network'] = $network;
        }

        return $output;
    }

    /**
     * Convert properties to xml by using SimpleXMLElement
     *
     * @return string
     */
    public function toXml()
    {
        $xml = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><whois></whois>');

        $output = get_object_vars($this);

        // lookup all object variables
        foreach ($output as $name => $var) {
            // if variable is an array add it to xml
            if (is_array($var)) {
                $child = $xml->addChild($name);

                foreach ($var as $firstKey => $firstValue) {
                    $child->addChild('item', trim(htmlspecialchars($firstValue)));
                }
            } elseif (is_object($var)) {
                // if variable is an object we need to convert it to array
                $child = $xml->addChild($name);

                // if it is not a stdClass object we have the toArray() method
                if (! $var instanceof \stdClass) {
                    $firstArray = $var->toArray();

                    foreach ($firstArray as $firstKey => $firstValue) {
                        if (! is_array($firstValue)) {
                            $child->addChild($firstKey, trim(htmlspecialchars($firstValue)));
                        } else {
                            $secondChild = $child->addChild($firstKey);

                            foreach ($firstValue as $secondKey => $secondString) {
                                $secondChild->addChild('item', trim(htmlspecialchars($secondString)));
                            }
                        }
                    }
                } else {
                    // if it is an stdClass object we need to convert it
                    // manually

                    // lookup all properties of stdClass and convert it
                    foreach ($var as $firstKey => $firstValue) {
                        if (! $firstValue instanceof \stdClass && ! is_array($firstValue) &&
                            ! is_string($firstValue)) {
                            $secondChild = $child->addChild($firstKey);

                            $firstArray = $firstValue->toArray();

                            foreach ($firstArray as $secondKey => $secondValue) {
                                $secondChild->addChild($secondKey, trim(htmlspecialchars($secondValue)));
                            }
                        } elseif (is_array($firstValue)) {
                            $secondChild = $child->addChild($firstKey);

                            foreach ($firstValue as $secondKey => $secondValue) {
                                $secondArray = $secondValue->toArray();
                                $thirdChild = $secondChild->addChild('item');

                                foreach ($secondArray as $thirdKey => $thirdValue) {
                                    if (! is_array($thirdValue)) {
                                        $thirdChild->addChild($thirdKey, trim(htmlspecialchars($thirdValue)));
                                    } else {
                                        $fourthChild = $thirdChild->addChild($thirdKey);

                                        foreach ($thirdValue as $fourthKey => $fourthValue) {
                                            $fourthChild->addChild('item', trim(htmlspecialchars($fourthValue)));
                                        }
                                    }
                                }
                            }
                        } elseif (is_string($firstValue)) {
                            $secondChild = $child->addChild($firstKey, $firstValue);
                        }
                    }
                }
            } else {
                $xml->addChild($name, trim($var));
            }
        }

        return $xml->asXML();
    }
    /**
     * cleanUp method will be called before output
     *
     * @return void
     */
    public function cleanUp($config, $dateformat)
    {
        // add WHOIS server to output

        // remove helper vars from result
        if (isset($this->lastId)) {
            unset($this->lastId);
        }

        if (isset($this->lastHandle)) {
            unset($this->lastHandle);
        }

        // format dates

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
     * Format given dates by date format
     *
     * @param  string $dateformat
     * @param  string $date
     * @return string
     */
    private function formatDate($dateformat, $date)
    {
        if (!is_string($date)) {
            return null;
        }
        $timestamp = strtotime(str_replace('/', '-', $date));
        if ($timestamp == '') {
            $timestamp = strtotime(str_replace('/', '.', $date));
        }
        return (strlen($timestamp) ? strftime($dateformat, $timestamp) : $date);
    }
    /**
     * Merge another result with this one, taking the other results values as preferred.
     *
     * @param Result $result
     */
    public function mergeFrom(Result $result)
    {
        $properties = array_keys(get_object_vars($result));
        foreach ($properties as $prop) {
            // Foreign value not set
            if ($result->$prop === null) {
                continue;
            }
            // Foreign value is an empty array
            if (is_array($result->$prop) && (count($result->$prop) < 1)) {
                continue;
            }
            // Foreign value is an empty string
            if (is_string($result->$prop) && (strlen($result->$prop) < 1)) {
                continue;
            }
            $this->$prop = $result->$prop;
        }
    }

}