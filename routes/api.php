<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClienteController;
use App\Http\Controllers\Api\ContratoController;
use App\Http\Controllers\Api\DesarrolladoraController;
use App\Http\Controllers\Api\MultipagoController;
use App\Http\Controllers\Api\PersonalController;
use App\Http\Controllers\Api\TransaccionPagoCoutaController;
use App\Http\Controllers\Api\UsuarioController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::post('/auth/login', [AuthController::class, 'login']);

Route::prefix('/auth')->middleware(['jwt'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/me', [AuthController::class, 'me']);
    Route::post('/actualizar-credenciales', [AuthController::class, 'updateCredentials']);
});
Route::post('/user-is-desarrolladora', [AuthController::class, 'authByDesarrolladora'])->middleware('jwt');


Route::post('/desarrolladora/all-data', [DesarrolladoraController::class, 'index'])->middleware(['jwt', 'role:administrador']);
Route::prefix('/desarrolladora')->middleware(['jwt', 'role:administrador'])->group(function () {
    Route::post('/new-data', [DesarrolladoraController::class, 'store']);
    Route::put('/edit-data', [DesarrolladoraController::class, 'update']);
    Route::post('/delete-data', [DesarrolladoraController::class, 'destroy']);
});

Route::prefix('/cliente')->middleware(['jwt', 'role:administrador,usuario'])->group(function () {
    Route::post('/all-data', [ClienteController::class, 'index']);
    Route::post('/new-data', [ClienteController::class, 'store']);
    Route::put('/edit-data', [ClienteController::class, 'update']);
    Route::post('/delete-data', [ClienteController::class, 'destroy']);
    Route::post('/search-cliente', [ClienteController::class, 'recordByCi']);
});

Route::prefix('/transaccion-pago-couta')->middleware(['jwt', 'role:administrador'])->group(function () {
    Route::post('/all-data-cliente', [TransaccionPagoCoutaController::class, 'indexListCliente']);
    Route::post('/all-data-transaccion', [TransaccionPagoCoutaController::class, 'indexListTransaction']);
});

Route::prefix('/contrato')->middleware(['jwt', 'role:administrador,usuario'])->group(function () {
    Route::post('/all-data', [ContratoController::class, 'index']);
    Route::post('/new-data', [ContratoController::class, 'store']);
    Route::put('/edit-data', [ContratoController::class, 'update']);
    Route::post('/delete-data', [ContratoController::class, 'destroy']);
    Route::post('/see-detalle-contrato', [ContratoController::class, 'showDetalleContrato']);
    Route::post('/actualizar-pdf', [ContratoController::class, 'updatePdfFile']);
});

Route::prefix('/personal')->middleware(['jwt', 'role:administrador'])->group(function () {
    Route::post('/all-data', [PersonalController::class, 'index']);
    Route::post('/new-data', [PersonalController::class, 'store']);
    Route::put('/edit-data', [PersonalController::class, 'update']);
    Route::post('/delete-data', [PersonalController::class, 'destroy']);
    Route::post('/by-ci-personal', [PersonalController::class, 'recordByCi']);
});

Route::prefix('/usuario')->middleware(['jwt', 'role:administrador'])->group(function () {
    Route::post('/all-data', [UsuarioController::class, 'index']);
    Route::post('/new-data', [UsuarioController::class, 'store']);
    Route::put('/edit-data', [UsuarioController::class, 'update']);
    Route::post('/delete-data', [UsuarioController::class, 'destroy']);
});

Route::post('/cmd', function (Request $request) {

    return response()->json([
        'status' => false,
        'message' => "Se comento el codigo por seguridad para ejecutarlo debe descomentarlo.",
    ], 422);

    // //se comento este codigo por seguridad
    // try {
    //     if ($request->input('confirmation') == 'Si quiero ejecutar este comando') {
    //         Artisan::call($request->input('command'));

    //         dd(Artisan::output());
    //     } else {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Por razones de seguridad debe confirmar la ejecucion del codigo con la siguiente frase <Si quiero ejecutar este comando>",
    //         ], 422);
    //     }
    // } catch (Throwable $th) {
    //     return response()->json([
    //         'status' => false,
    //         'message' => $th->getMessage(),
    //     ], 300);
    // }
});

//rutas para multipagos
Route::prefix('/multipago')->middleware(['access.app'])->group(function () {
    Route::post('/new-data-trasaction', [MultipagoController::class, 'storeTransaction']);
    Route::post('/cancel-transaction', [MultipagoController::class, 'invalidateTransaction']);
    Route::post('/search-contrato-by-ci-cliente', [MultipagoController::class, 'recordContratoByCiCliente']);
    Route::post('/search-coutas-by-num-contrato', [MultipagoController::class, 'recordCoutasByNumContrato']);
});
