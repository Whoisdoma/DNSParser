<?php


namespace Whoisdoma\DNSParser\Helpers;

class Utils {

    public static function getIP($query){
        //get the nameserver ip
        $ns_ip = gethostbyname($query);

        return $ns_ip;
    }

    public static function getipLocation($ip) {
        $details = json_decode(file_get_contents("https://ipinfo.io/{$ip}"));;
        return $details->city;
    }

    public static function getallRecords($query) {

        //assign a variable for the dns lookup
        $dnsData = dns_get_record($query, DNS_ANY);

        //return the dns results
        return $dnsData;

    }

}
