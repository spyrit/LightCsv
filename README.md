Spyrit LightCSV
===============

[![Build Status](https://travis-ci.org/spyrit/LightCsv.png)](https://travis-ci.org/spyrit/LightCsv)

A light and simple  CSV Reader/Writer PHP 5.3 Library

Installation
------------

* get composer http://getcomposer.org/ and install dependencies

        curl -s https://getcomposer.org/installer | php

* add "spyrit/light-csv" package to your composer.json file require section

* install dependencies
    
        php composer.phar install

* include vendor/autoload.php

How To
------

###Read

Instanciate a new CSVReader with the following CSV parameters:

* field delimiter (default for Excel = ; )
* field enclosure character  (default for Excel = " ) 
* character encoding = (default for Excel = CP1252 )
* end of line character (default for Excel = "\r\n" )
* escape character (default for Excel = "\\" )
* UTF8 BOM (default false) force removing BOM
* transliteration (default for Excel = null ) available options : 'translit', 'ignore', null
* force encoding detection (default for Excel = false )
* skip empty lines (default for Excel = false ) lines which all values are empty

```php
use Spyrit\LightCsv\CsvReader;

// create the reader
$reader = new CsvReader(';', '"', 'CP1252', "\r\n", "\\");

//Open the csv file to read
$reader->open('test.csv');

//Read each row
foreach ($reader as $row) {
    // do what you want with the current row array : $row
}

//close the csv file
$reader->close();
```

###Write

Instanciate a new CSVWriter with the following CSV parameters:

* field delimiter (default for Excel = ; )
* field enclosure character  (default for Excel = " ) 
* character encoding = (default for Excel = CP1252 ) 
* end of line character (default for Excel = "\r\n" )
* escape character (default for Excel = "\\" )
* UTF8 BOM (default false) force writing BOM if encoding is UTF-8
* transliteration (default for Excel = null ) available options : 'translit', 'ignore', null

```php
use Spyrit\LightCsv\CsvWriter;

// create the writer
$writer = new CsvWriter(';', '"', 'CP1252', "\r\n", "\\", false);

//Open the csv file to write
$writer->open('test.csv');

//Write a row
$writer->writeRow(array('a', 'b', 'c'));

//Write multiple rows at the same time
$writer->writeRows(array(
    array('d', 'e', 'f'),
    array('g', 'h', 'i'),
    array('j', 'k', 'l'),
));

//close the csv file
$writer->close();
```

Requirements
------------

* PHP >= 5.3.3
* extension mbstring

Suggested :

* extension iconv

Licensing
---------

License LGPL 3

* Copyright (C) 2012 Spyrit Systeme

This file is part of LightCSV.

LightCSV is free software: you can redistribute it and/or modify
it under the terms of the GNU Lesser General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

LightCSV is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License
along with LightCSV.  If not, see <http://www.gnu.org/licenses/>.




