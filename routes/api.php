<?php

use App\Models\Company;
use App\Http\Controllers\CompanyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::resource('companies', CompanyController::class);

Route::post('/companies/insert',[CompanyController::class,'insert']);
Route::post('/companies/search/{ids}',[CompanyController::class,'getByIds']);
Route::post('/companies/search',[CompanyController::class,'getByIdsV2']);

Route::post('/companies/list-company-activity',[CompanyController::class,'listCompanyActivity']);

Route::post('/companies/list-found-date',[CompanyController::class,'listFoundDate']);

Route::put('/companies/update/{id}', [CompanyController::class, 'update']);
Route::put('/companies/update', [CompanyController::class, 'updateV2']);

Route::post('/upload-content',[CompanyController::class,'uploadContent'])->name('import.content');
Route::delete('/reset',[CompanyController::class,'reset']);