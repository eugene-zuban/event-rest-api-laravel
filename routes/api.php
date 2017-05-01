<?php

Route::group(['namespace' => 'Api', 'middleware' => ['api']], function () {
    Route::get('/events', ['uses' => 'EventsController@index']);

    Route::get('/events/{id}', ['uses' => 'EventsController@show'])
        ->where('id', '[0-9]+');

    Route::post('/events', ['uses' => 'EventsController@store']);

    Route::put('/events/{id}', ['uses' => 'EventsController@update']);

    Route::delete('/events/{id}', ['uses' => 'EventsController@destroy'])
        ->where('id', '[0-9]+');
});
