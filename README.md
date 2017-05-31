Whoisdoma DNS Parser
====================

Lookup dns records for domains

Copyright (c) 2016 XAOS Interactive (http://xaosia.com) | Whoisdoma (http://whoisdoma.com)

Licensed under the MIT License (the "License").

Installation
------------

Installing using composer: 
```
composer require whoisdoma/dnsparser
composer require whoisdoma/domainparser
```

Installing from source: `git clone git://github.com/Whoisdoma/DNSParser.git` or [download the latest release](https://github.com/Whoisdoma/WhoisParser/zipball/master)

See Whoisdoma Domain Parser (http://github.com/Whoisdoma/DomainParser) or [download the latest release](https://github.com/WhoisdomaDomainParser/zipball/master) and install it as well.

Move the source code to your preferred project folder.

Usage
-----

* Include Parser.php
```
require_once 'DomainParser/Parser.php';
require_once 'DNSParser/Parser.php';
```

* or if using composer:
```
use Whoisdoma\DomainParser\Parser as DomainParser;
use Whoisdoma\DNSParser\Parser as DNSParser;
```

* Create Parser() object
```
$Parser = new Whoisdoma\DNSParser\Parser();
```

* Call lookup() method
```
$result = $Parser->lookup($domain);
```

* Access DNS record, the object oriented way.
```
echo $result->created; // get create date of domain name
print_r($result->rawdata); // get raw output as array
```

* You may choose 5 different return types. the types are array, object, json, serialize and
xml. By default it is object. If you want to change that call the format method before calling
the parse method or provide to the constructer.
```
$Parser->setFormat('json');
$Parser = new Whoisdoma\DNSParser\Parser('json');
```

* You may set your own date format if you like. Please check http://php.net/strftime for further
details
```
$Parser->setDateFormat('%d.%m.%Y %H:%M:%S');
```

ToDos
-----
* Caching of data for better performance and to reduce requests
* Change HTTP Adapter to use GET/POST
* Change Socket Adapter to be able to use Socks to split requests.

Known bugs to be fixed in further versions
------------------------------------------


3rd Party Libraries
-------------------
We are using our own Domain Parser:
* Whoisdoma: http://github.com/Whoisdoma/DomainParser (Version 1.0.0 and above)

ChangeLog
---------
See ChangeLog at https://github.com/Whoisdoma/DNSParser/blob/master/CHANGELOG.md

Issues
------
Please report any issues via https://github.com/Whoisdoma/DNSParser/issues

LICENSE and COPYRIGHT
-----------------------
Copyright (c) 2016 XAOS Interactive (http://xaosia.com) | Whoisdoma (http://whoisdoma.com)

License: https://github.com/Whoisdoma/DNSParser/blob/master/LICENSE