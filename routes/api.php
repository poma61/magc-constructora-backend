<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ContratoController;
use App\Http\Controllers\Api\DesarrolladoraController;
use App\Http\Controllers\Api\HistorialDePagoClienteController;
use App\Http\Controllers\Api\PersonalController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::prefix('/auth')->middleware(['jwt'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/me', [AuthController::class, 'me']);
});

Route::prefix('/desarrolladora')->middleware(['jwt'])->group(function () {
    Route::post('/all-data', [DesarrolladoraController::class, 'index']);
    Route::post('/new-data', [DesarrolladoraController::class, 'store']);
    Route::put('/edit-data', [DesarrolladoraController::class, 'update']);
    Route::post('/delete-data', [DesarrolladoraController::class, 'destroy']);
});


Route::prefix('/cliente')->middleware(['jwt'])->group(function () {
    Route::post('/all-data', [ClienteController::class, 'index']);
    Route::post('/new-data', [ClienteController::class, 'store']);
    Route::put('/edit-data', [ClienteController::class, 'update']);
    Route::post('/delete-data', [ClienteController::class, 'destroy']);
    Route::post('/search-cliente', [ClienteController::class, 'recordByCi']);
});

Route::prefix('/historial-de-pago-cliente')->middleware(['jwt'])->group(function () {
    Route::post('/all-data', [HistorialDePagoClienteController::class, 'index']);
});


Route::prefix('/contrato')->middleware(['jwt'])->group(function () {
    Route::post('/all-data', [ContratoController::class, 'index']);
    Route::post('/new-data', [ContratoController::class, 'store']);
    Route::put('/edit-data', [ContratoController::class, 'update']);
    Route::post('/delete-data', [ContratoController::class, 'destroy']);
    Route::post('/see-detalle-contrato', [ContratoController::class, 'showDetalleContrato']);
    Route::post('/actualizar-pdf', [ContratoController::class, 'updatePdfFile']);
});

Route::prefix('/personal')->middleware(['jwt'])->group(function () {
    Route::post('/all-data', [PersonalController::class, 'index']);
    Route::post('/new-data', [PersonalController::class, 'store']);
    Route::put('/edit-data', [PersonalController::class, 'update']);
    Route::post('/delete-data', [PersonalController::class, 'destroy']);
    Route::post('/by-ci-personal', [PersonalController::class, 'recordByCi']);

});
