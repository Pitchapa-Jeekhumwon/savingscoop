<?php
/**
 * Created by PhpStorm.
 * User: macmini2
 * Date: 2019-08-02
 * Time: 09:58
 */


$temp = array(
    "id" => '010195',
    "name" => 'KANLAYA KAMONWATIN',
    "date" => '2019-03-26',
    "amount" => 20000,
    "interest" => 114,
    "remark" => ""
);

$sheet_number = 0;
$excel = new PHPExcel();
$excel->createSheet($sheet_number);
$excel->setActiveSheetIndex($sheet_number);

