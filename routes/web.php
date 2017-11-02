<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('excel')->group(function() {
   Route::get('ticketfields', "ExcelTicketFieldsController@home")->name('excelTicketFields');
   Route::post('ticketfields/download', "ExcelTicketFieldsController@download");
   Route::post('ticketfields/upload', "ExcelTicketFieldsController@upload");

   Route::get('ticketforms', "ExcelTicketFormsController@home")->name('excelTicketForms');
   Route::get('ticketforms/{sheet}', "ExcelTicketFormsController@read");
   Route::post('ticketforms/download', "ExcelTicketFormsController@download");
   Route::post('ticketforms/upload', "ExcelTicketFormsController@upload");

   Route::get('triggers', "ExcelTriggersController@home")->name('excelTriggers');
   Route::get('triggers/{sheet}', "ExcelTriggersController@read");
   Route::post('triggers/download', "ExcelTriggersController@download");
   Route::post('triggers/upload', "ExcelTriggersController@upload");

   Route::get('automations', "ExcelAutomationsController@home")->name('excelAutomations');
   Route::get('automations/{sheet}', "ExcelAutomationsController@read");
   Route::post('automations/download', "ExcelAutomationsController@download");
   Route::post('automations/upload', "ExcelAutomationsController@upload");

   Route::get('slas', "ExcelSLAsController@home")->name('excelSLAs');
   Route::get('slas/{sheet}', "ExcelSLAsController@read");
   Route::post('slas/download', "ExcelSLAsController@download");
   Route::post('slas/upload', "ExcelSLAsController@upload");

   Route::get('macros/', "ExcelMacrosController@home")->name('excelMacros');
   Route::get('macros/{sheet}', "ExcelMacrosController@read");
   Route::post('macros/download', "ExcelMacrosController@download");
   Route::post('macros/upload', "ExcelMacrosController@upload");

   Route::get('views/', "ExcelViewsController@home")->name('excelViews');
   Route::get('views/{sheet}', "ExcelViewsController@read");
   Route::post('views/download', "ExcelViewsController@download");
   Route::post('views/upload', "ExcelViewsController@upload");

   Route::get('groups/', "ExcelGroupsController@home")->name('excelGroups');
   Route::get('groups/{sheet}', "ExcelGroupsController@read");
   Route::post('groups/download', "ExcelGroupsController@download");
   Route::post('groups/upload', "ExcelGroupsController@upload");

   //Belakangan
   Route::get('brands/', "ExcelBrandsController@home")->name('excelBrands');

   Route::get('users/', "ExcelUsersController@home")->name('excelUsers');

   Route::get('organizations/', "ExcelOrganizationsController@home")->name('excelOrganizations');

   Route::get('sharingagreements/', "ExcelSharingAgreementsController@home")->name('excel');
});
