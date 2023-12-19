<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DesarrolladoraRequest;
//add
use App\Models\Desarrolladora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DesarrolladoraController extends Controller
{

    public function index()
    {
        try {
            $desarrolladora = Desarrolladora::where('status', true)->get();
            return response()->json([
                'records' => $desarrolladora,
                'status' => true,
                'message' => 'OK',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'records' => null,
                'status' => true,
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    public function store(DesarrolladoraRequest $request)
    {
        try {

            $desarrolladora = new Desarrolladora($request->except('logo'));
            $desarrolladora->status = true;
            $image_path = $request->file('logo')->store('img/desarrolladora', 'public');
            $desarrolladora->logo = "/storage/{$image_path}";
            $desarrolladora->save();

            return response()->json([
                'status' => true,
                'record' => $desarrolladora,
                'message' => 'Registro creado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => null,
            ], 500);
        }
    }

    public function update(DesarrolladoraRequest $request)
    {
        try {
            $desarrolladora = Desarrolladora::where('status', true)->where('id', $request->input('id'))->first();
            $desarrolladora->fill($request->except('logo'));
            //verificar si subio una nueva imagen
            if ($request->file('logo') != null) {
                $parse_path_image=str_replace("/storage", "", $desarrolladora->logo);
                Storage::disk('public')->delete($parse_path_image);
                $image_path = $request->file('logo')->store('img/desarrolladora', 'public');
                $desarrolladora->logo = "/storage/{$image_path}";
            }
            $desarrolladora->update();

            return response()->json([
                'status' => true,
                'record' => $desarrolladora,
                'message' => 'Registro modificado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => null,
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {

            $desarrolladora = Desarrolladora::where('status', true)->where('id', $request->input('id'))->first();
            $desarrolladora->status = false;
            $desarrolladora->update();

            return response()->json([
                'status' => true,
                'message' => 'Registro eliminado!',
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}//class
