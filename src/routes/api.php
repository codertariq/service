<?php
Route::group(['prefix' => 'api'], function () {
    Route::get('/install/pre-requisite', 'TRT\Service\Controllers\InstallController@preRequisite');
    Route::post('/install/validate/{option}', 'TRT\Service\Controllers\InstallController@store');
    Route::post('/install', 'TRT\Service\Controllers\InstallController@store');
    Route::post('/license', 'TRT\Service\Controllers\LicenseController@verify');

    Route::get('/about', 'TRT\Service\Controllers\HomeController@about');
    Route::get('/support', 'TRT\Service\Controllers\SupportController@index');
    Route::post('/support', 'TRT\Service\Controllers\SupportController@submit');
    Route::get('/update', 'TRT\Service\Controllers\UpdateController@index');
    Route::post('/download', 'TRT\Service\Controllers\UpdateController@download');
    Route::post('/update', 'TRT\Service\Controllers\UpdateController@update');
    Route::post('/help/content', 'TRT\Service\Controllers\HomeController@helpDoc');
});
