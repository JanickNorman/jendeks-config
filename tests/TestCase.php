<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function getCellValue(LaravelExcelWriter $excel, $cell) {
      return $excel->getActiveSheet()->getCell($cell)->getValue();
   }
}
