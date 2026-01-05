<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AutentifikacijaController;
use App\Http\Controllers\AutoRenginiaiController;
use App\Http\Controllers\RenginiuRegistracijosController;

Route::get('/testas', function () {
    return response()->json(['ok' => true]);
});

Route::get('/testas-swagger', fn () => response()->json(['ok' => true]));

// Auth
Route::post('/registruotis', [AutentifikacijaController::class, 'registruotis']);
Route::post('/prisijungti', [AutentifikacijaController::class, 'prisijungti']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/atsijungti', [AutentifikacijaController::class, 'atsijungti']);
    Route::get('/as', [AutentifikacijaController::class, 'as']);
});

// Auto renginiai (SVARBU: export.xml turi būti prieš {autoRenginys})
Route::get('/auto-renginiai/export.xml', [AutoRenginiaiController::class, 'exportXml']);

Route::get('/auto-renginiai', [AutoRenginiaiController::class, 'index']);
Route::get('/auto-renginiai/{autoRenginys}', [AutoRenginiaiController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auto-renginiai', [AutoRenginiaiController::class, 'store']);
    Route::put('/auto-renginiai/{autoRenginys}', [AutoRenginiaiController::class, 'update']);
    Route::delete('/auto-renginiai/{autoRenginys}', [AutoRenginiaiController::class, 'destroy']);

    // Registracijos į renginius
    Route::post('/auto-renginiai/{autoRenginys}/registracija', [RenginiuRegistracijosController::class, 'registruotis']);
    Route::delete('/auto-renginiai/{autoRenginys}/registracija', [RenginiuRegistracijosController::class, 'atsisakyti']);
});
