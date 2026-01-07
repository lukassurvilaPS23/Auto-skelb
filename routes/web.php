<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'puslapiai.pagrindinis')->name('pagrindinis');
Route::view('/renginiai', 'puslapiai.renginiai')->name('renginiai');

Route::get('/renginiai/{id}', function ($id) {
    return view('puslapiai.renginys', ['id' => (int)$id]);
})->whereNumber('id')->name('renginys');

Route::view('/prisijungti', 'puslapiai.prisijungti')->name('prisijungti');
Route::view('/registruotis', 'puslapiai.registruotis')->name('registruotis');
Route::view('/profilis', 'puslapiai.profilis')->name('profilis');
Route::view('/mano-renginiai', 'puslapiai.mano_renginiai')->name('mano_renginiai');

Route::redirect('/xml', '/api/auto-renginiai/export.xml')->name('xml');