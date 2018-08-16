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
    return redirect('excel/ticketfields');
});

Route::post('login', 'ZendeskLoginController@sourceLogin');
Route::prefix('excel')->group(function() {
   Route::get('ticketfields', "ExcelTicketFieldsController@home")->name('excelTicketFields');
   Route::post('ticketfields/download', "ExcelTicketFieldsController@download");
   Route::post('ticketfields/upload', "ExcelTicketFieldsController@upload");

   Route::get('ticketforms', "ExcelTicketFormsController@home")->name('excelTicketForms');
   Route::post('ticketforms/download', "ExcelTicketFormsController@download");
   Route::post('ticketforms/upload', "ExcelTicketFormsController@upload");

   Route::get('triggers', "ExcelTriggersController@home")->name('excelTriggers');
   Route::post('triggers/download', "ExcelTriggersController@download");
   Route::post('triggers/upload', "ExcelTriggersController@upload");

   Route::get('automations', "ExcelAutomationsController@home")->name('excelAutomations');
   Route::post('automations/download', "ExcelAutomationsController@download");
   Route::post('automations/upload', "ExcelAutomationsController@upload");

   Route::get('slas', "ExcelSLAsController@home")->name('excelSLAs');
   Route::post('slas/download', "ExcelSLAsController@download");
   Route::post('slas/upload', "ExcelSLAsController@upload");

   Route::get('macros/', "ExcelMacrosController@home")->name('excelMacros');
   Route::post('macros/download', "ExcelMacrosController@download");
   Route::post('macros/upload', "ExcelMacrosController@upload");

   Route::get('views/', "ExcelViewsController@home")->name('excelViews');
   Route::post('views/download', "ExcelViewsController@download");
   Route::post('views/upload', "ExcelViewsController@upload");

   Route::get('groups/', "ExcelGroupsController@home")->name('excelGroups');
   Route::post('groups/download', "ExcelGroupsController@download");
   Route::post('groups/upload', "ExcelGroupsController@upload");

   //Belakangan
   Route::get('brands/', "ExcelBrandsController@home")->name('excelBrands');

   Route::get('users/', "ExcelUsersController@home")->name('excelUsers');

   Route::get('organizations/', "ExcelOrganizationsController@home")->name('excelOrganizations');

   Route::get('sharingagreements/', "ExcelSharingAgreementsController@home")->name('excel');
});

Route::get('chats/oauth', 'ChatTokenController@home');
Route::post('chats/oauth', 'ChatTokenController@oauth');
Route::get('redirect', 'ChatTokenController@redirect');

