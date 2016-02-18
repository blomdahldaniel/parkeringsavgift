<?php

Route::get('/', 'ParkeringsregelController@index');
Route::get('/kvitto/{exempelNr}', 'ParkeringsregelController@exempel');

