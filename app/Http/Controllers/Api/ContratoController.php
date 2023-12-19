<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DetalleCByContratoRequest;
use Illuminate\Http\Request;
//add
use App\Models\Desarrolladora;
use App\Models\Contrato;
use App\Models\DetalleContrato;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $desarrolladora = Desarrolladora::where('status', true)
                ->where('nombres', $request->input('desarrolladora'))
                ->first();
            //debemos verificar si la desarrolladora existe en la base de datos por seguridad
            if ($desarrolladora == null) {
                return response()->json([
                    'records' => null,
                    'status' => false,
                    'message' => "No se encontro la desarrolladora {$request->input('desarrolladora')}",
                ], 404);
            }
            $contrato = Contrato::join('detalle_contratos', 'detalle_contratos.id_contrato', '=', 'contratos.id')
                ->join('clientes_has_contratos', 'clientes_has_contratos.id_contrato', '=', 'contratos.id')
                ->join('clientes', 'clientes.id', '=', 'clientes_has_contratos.id_cliente')
                ->join('desarrolladoras', 'desarrolladoras.id', '=', 'clientes.id_desarrolladora')
                ->select(
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                    'contratos.*',
                    'detalle_contratos.fecha_firma_contrato'
                )
                ->where('clientes.status', true)
                ->where('contratos.status', true)
                ->where('detalle_contratos.status', true)
                ->where('desarrolladoras.id', $desarrolladora->id)
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'OK',
                'records' => $contrato,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'records' => [],
            ], 500);
        }
    }


    public function store(DetalleCByContratoRequest $request)
    {
        try {
            //el $fillable del modelo son todos los campos que se insertan en la base de datos
            //si el campo a insertar no esta listado en el array $fillable entonces
            //ese campo no se insertara en la base de datos, aunque en $request->all() este ese campo
            //ese campo sera omitido
            $contrato = new Contrato($request->all());
            $contrato->n_contrato = $this->generateNumContrato($request->input('ciudad'));
            $contrato->status = true;
            $contrato->save();

            $detalle_contrato = new DetalleContrato($request->except('ciudad'));
            $detalle_contrato->status = true;
            $detalle_contrato->id_contrato = $contrato->id;
            $detalle_contrato->save();

           

            $contrato->archivo_pdf = $this->generatePdfContrato($contrato->id);
            $contrato->save();

            return response()->json([
                'status' => true,
                'message' => "Contrato generado exitosamente!",
                'record' => $contrato,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => [],
            ], 500);
        }
    }

    public function generateNumContrato(string $ciudad)
    {
        $ciudades_key = [
            'Santa-Cruz' => 'SC',
            'Chuquisaca' => 'CH',
            'Cochabamba' => 'CB',
            'Potosi' => 'PT',
            'Beni' => 'BN',
            'La-Paz' => 'LP',
            'Pando'  => 'PA',
            'Tarija'  => 'TJ',
            'Oruro' => 'OR',
            'Otros' => 'OTS',
        ];

        //obtenemos ultimo contrato registrato por ciudad
        //y no necesitamos verificar el status
        // ya que aunque el registro se haya eliminado igualmente contatos los registros de contratos para generar un n_contrato
        $ultimo_contrato_id = Contrato::join('clientes', 'clientes.id', '=', 'contratos.id_cliente')
            ->join('grupos', 'grupos.id', '=', 'clientes.id_grupo')
            ->join('ciudades', 'ciudades.id', '=', 'grupos.id_ciudad')
            ->select('clientes.nombres', 'clientes.apellido_paterno', 'clientes.apellido_materno', 'contratos.*')
            ->where('ciudades.city_name',  $ciudad)
            ->max('contratos.id');
        $ultimo_contrato = Contrato::find($ultimo_contrato_id);


        // verificamos si es el primer registro, entonces es nulo y asignamos el numero cero
        $old_numero_contrato = ($ultimo_contrato == null) ? '0000/0000_AS' : $ultimo_contrato->n_contrato;

        $parts = explode('/', $old_numero_contrato); // Divide la cadena en partes usando "/"
        $middle_part = $parts[1]; // Obtiene la segunda parte, que es '009_LP'
        $sub_parts = explode('_', $middle_part); // Divide la parte intermedia usando "_"
        $num_result = $sub_parts[0]; // Obtiene la primera subparte, que es '009'

        $num_result = $num_result + 1;
        $num_result = str_pad($num_result, 4, '0', STR_PAD_LEFT); //agregar ceros al numero
        $year =  date('Y');;
        return  "{$year}/{$num_result}_{$ciudades_key[$ciudad]}";
    }


    public function update(DetalleCByContratoRequest $request,)
    {
        try {
            //el $fillable del modelo son todos los campos que se insertan en la base de datos
            //si el campo a insertar no esta listado en el array $fillable entonces
            //ese campo no se insertara en la base de datos, aunque en $request->all() este ese campo
            //ese campo sera omitido
            $contrato = Contrato::where('status', true)->where('id', $request->input('id'))->first();
            $contrato->update($request->all());

            $detalle_contrato = DetalleContrato::where('status', true)->where('id_contrato', $request->input('id'))->first();
            $detalle_contrato->update($request->all());

            //obtenemos el path del pdf antiguo
            $antiguo_pdf_path = $contrato->archivo_pdf;
            //agregamos el nuevo archivo
            $contrato->archivo_pdf = $this->generatePdfContrato($contrato->id);
            $contrato->update();

            // si todo es ok entonces eliminados el archivo pdf antiguo
            Storage::disk('public')->delete(str_replace("storage/", "", $antiguo_pdf_path));

            return response()->json([
                'status' => true,
                'message' => "Contrato modificado exitosamente!",
                'record' => $contrato,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => [],
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        try {
            $contrato = Contrato::find($request->input('id'));
            $contrato->status = false;
            $contrato->update();

            $detalle_contrato = DetalleContrato::where('id_contrato', $request->input('id'))->first();
            $detalle_contrato->status = false;
            $detalle_contrato->update();

            return response()->json([
                'status' => true,
                'message' => "Contrato eliminado exitosamente!",
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    public function rowTableroByIdDetalleContrato(Request $request)
    {
        try {
            $detalle_contrato = DetalleContrato::where('status', true)->where('id_contrato', $request->input('id_contrato'))->first();

            return response()->json([
                'status' => true,
                'message' => 'OK',
                'record' => $detalle_contrato,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => [],
            ], 500);
        }
    }

    public function generatePdfContrato(int $id)
    {
        $contrato = Contrato::join('clientes', 'clientes.id', '=', 'contratos.id_cliente')
            ->join('detalles_contratos', 'detalles_contratos.id_contrato', '=', 'contratos.id')
            ->select(
                'clientes.nombres',
                'clientes.apellido_paterno',
                'clientes.apellido_materno',
                'clientes.ci',
                'clientes.ci_expedido',
                'contratos.*',
                'detalles_contratos.*',
            )
            ->where('clientes.status', true)
            ->where('contratos.status', true)
            ->where('detalles_contratos.status', true)
            ->where('contratos.id', $id)
            ->first();

        $pdf = PDF::loadView('contrato/pdf-contrato-ciudad', ['contrato' => $contrato,]);
        //el tamaño del papel no esta definido

        //debemos parsear el numero de contrato porque tiene el formato '2023/0001_LP' 
        // no se puede guardar archivos con ese tipo de nombre entonces  obtenemos el nombre de este formato '2023_0001_LP'
        $n_contrato_parse = str_replace('/', '_', $contrato->n_contrato);
        $pdf_path = "pdfs-contratos/{$n_contrato_parse}_{$contrato->nombres}_{$contrato->apellido_paterno}_{$contrato->apellido_materno}_" . uniqid() . ".pdf";

        Storage::disk('public')->put($pdf_path,  $pdf->download()->getOriginalContent());
        return  "/storage/{$pdf_path}";
    }
}//class
