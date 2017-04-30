<?php

Route::group(['namespace' => 'Api', 'middleware' => ['api']], function () {
    Route::get('/events', ['uses' => 'EventsController@index']);
    Route::get('/events/{id}', ['uses' => 'EventsController@show']);
    Route::post('/events', ['uses' => 'EventsController@store']);
    Route::put('/events/{id}', ['uses' => 'EventsController@update']);
    Route::delete('/events/{id}', ['uses' => 'EventsController@delete']);
});
