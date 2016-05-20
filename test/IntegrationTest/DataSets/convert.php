<?php

$xml = new SimpleXMLElement(file_get_contents('default.dataset.xml'));
$converted = [];

foreach ($xml->database->table_data as $tableData) {
    $tableName = (string) $tableData['name'];
    $converted[$tableName] = [];
    /** @var SimpleXMLElement $rowData */
    foreach ($tableData->row as $rowData) {
        $row = [];
        foreach ($rowData->field as $fieldData) {
            $fieldName       = (string) $fieldData['name'];
            $fieldValue      = (string) $fieldData;

            $row[$fieldName] = $fieldValue === "" ? null : $fieldValue;
        }

        array_push($converted[$tableName], $row);
    }
}

$export = "<?php" . PHP_EOL . PHP_EOL;
$export .= 'return ' . var_export($converted, true) . ';' . PHP_EOL;

file_put_contents('default.dataset.php', $export);
echo 'done';