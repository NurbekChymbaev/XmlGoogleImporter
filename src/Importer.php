<?php

class Importer
{
    const XML_ITEM_NAME = 'item';

    protected $reader;

    protected $spreadsheet;

    protected $source_file;

    public function __construct($source_file)
    {
        $this->reader = new XMLReader();
        $this->spreadsheet = new Spreadsheet('../sample/credentials.json');
        $this->source_file = $source_file;
    }

    public function doImport()
    {
        $this->reader->open($this->source_file);

        $spreadsheetId = $this->spreadsheet->create(basename($this->source_file));

        while ($this->reader->read()) {

            if ($this->reader->nodeType == XMLReader::ELEMENT && $this->reader->name == self::XML_ITEM_NAME) {

                $property = new SimpleXMLElement($this->reader->readOuterXML());

                $this->spreadsheet->push($spreadsheetId, $property->children());
            }
        }
    }
}