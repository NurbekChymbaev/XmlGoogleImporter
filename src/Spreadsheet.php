<?php

class Spreadsheet
{
    protected $client_credentials;

    public function __construct($client_credentials)
    {
        $this->client_credentials = $client_credentials;
    }

    public function create($title)
    {
        $sheets_service = new Google_Service_Sheets($this->getClient());
        $requestBody = new Google_Service_Sheets_Spreadsheet(['properties' => ['title' => $title]]);
        $response = $sheets_service->spreadsheets->create($requestBody);
        return $response->spreadsheetId;
    }

    public function push($spreadsheetId, $data)
    {
        $values = [];
        foreach ($data as $child) {
            $cellData = new Google_Service_Sheets_CellData();
            $value = new Google_Service_Sheets_ExtendedValue();
            $value->setStringValue((string)$child);
            $cellData->setUserEnteredValue($value);
            $values[] = $cellData;
        }

        $rowData = new Google_Service_Sheets_RowData();
        $rowData->setValues($values);

        $appendRequest = new Google_Service_Sheets_AppendCellsRequest();
        $appendRequest->setSheetId(0);
        $appendRequest->setRows($rowData);
        $appendRequest->setFields('userEnteredValue');

        $request = new Google_Service_Sheets_Request();
        $request->setAppendCells($appendRequest);

        $requests = [];
        $requests[] = $request;

        $batchUpdateRequest = new Google_Service_Sheets_BatchUpdateSpreadsheetRequest(['requests' => $requests]);

        try {
            $sheets_service = new Google_Service_Sheets($this->getClient());
            $sheets_service->spreadsheets->batchUpdate($spreadsheetId, $batchUpdateRequest);
        } catch (Exception $e) {
            error_log($e->getMessage());
        }

        return $requests;
    }

    public function getClient()
    {
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $this->client_credentials);
        $client = new Google_Client();
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS);
        $client->useApplicationDefaultCredentials();
        return $client;
    }
}