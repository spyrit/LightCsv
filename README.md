Spyrit LightCSV
===============

[![Latest Stable Version](https://poser.pugx.org/spyrit/light-csv/v/stable.png)](https://packagist.org/packages/spyrit/light-csv)
[![Latest Unstable Version](https://poser.pugx.org/spyrit/light-csv/v/unstable.png)](https://packagist.org/packages/spyrit/light-csv)
[![Build Status](https://travis-ci.org/spyrit/LightCsv.png)](https://travis-ci.org/spyrit/LightCsv)

A light and simple CSV Reader/Writer PHP 5.3 Library.
Fully Tested, very memory efficient and able to parse/write CSV files that weigh 100 Mb.

Installation
------------

* get composer http://getcomposer.org/ and install dependencies

```bash
curl -s https://getcomposer.org/installer | php
```

* add "[https://packagist.org/packages/spyrit/light-csv](spyrit/light-csv)" package to your composer.json file require section

```bash
php composer.phar require spyrit/light-csv:1.0.*
```

* install dependencies

```bash
php composer.phar install
```
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
* trim (default = false for Excel) trim all values

```php
use Spyrit\LightCsv\CsvReader;

// create the reader
$reader = new CsvReader(array(
    'delimiter' => ';', 
    'enclosure' => '"', 
    'encoding' => 'CP1252', 
    'eol' => "\r\n", 
    'escape' => "\\", 
    'bom' => false, 
    'translit' => 'translit',
    'force_encoding_detect' => false,
    'skip_empty' => false,
    'trim' => false,
));

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
* enclosing_mode (default = 'minimal'), possible values :
  * all : always enclose string
  * minimal : enclose string only if the delimiter, enclosure or line ending character is present
  * nonumeric : enclose string only if the value is non numeric (other character than digits and dot)
* escape_double (default = true) if true double the enclosure to escape it, else escape it with escape character
* UTF8 BOM (default false) force writing BOM if encoding is UTF-8
* transliteration (default for Excel = null ) available options : 'translit', 'ignore', null
* trim (default = false for Excel) trim all values

```php
use Spyrit\LightCsv\CsvWriter;

// create the writer
$writer = new CsvWriter(array(
    'delimiter' => ';', 
    'enclosure' => '"', 
    'encoding' => 'CP1252', 
    'enclosing_mod' => 'minimal',
    'escape_double' => true,
    'eol' => "\r\n", 
    'escape' => "\\", 
    'bom' => false, 
    'translit' => 'translit',
    'trim' => false,
));

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

### Configuration : Dialect class

Instead of giving directly an array to the CsvReader or CsvWriter constructor, you can create a Dialect object, use setter methods to change parameters and pass it to the CsvReader (or CsvWriter) :

```php
use Spyrit\LightCsv\Dialect;
use Spyrit\LightCsv\CsvReader;
use Spyrit\LightCsv\CsvWriter;

// create a dialect with some CSV parameters
$dialect = new Dialect(array(
    'delimiter' => ';', 
    'enclosure' => '"', 
    'enclosing_mode' => 'minimal',
    'encoding' => 'CP1252', 
    'eol' => "\r\n", 
    'escape' => "\\", 
    'escape_double' => true,
    'bom' => false, 
    'translit' => 'translit',
    'force_encoding_detect' => false,
    'skip_empty' => false,
    'trim' => false,
);

// change a parameter
$dialect->setLineEndings("\n");

// create the reader
$reader = new CsvReader($dialect);

//or a writer
$writer = new CsvWriter($dialect);

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
