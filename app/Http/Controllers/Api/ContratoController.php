<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//add
use App\Http\Requests\ClienteContratoDetalleContratoRequest;
use App\Models\Cliente;
use App\Models\ClienteHasContrato;
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
            //debemos verificar si la desarrolladora existe en la base de datos por seguridad y estabilidad del sistema
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

    public function store(ClienteContratoDetalleContratoRequest $request)
    {
        try {

            $desarrolladora = Desarrolladora::where('status', true)
                ->where('nombres', $request->input('desarrolladora'))
                ->first();
            //debemos verificar si la desarrolladora existe en la base de datos por seguridad y estabilidad del sistema
            if ($desarrolladora == null) {
                return response()->json([
                    'records' => null,
                    'status' => false,
                    'message' => "No se encontro la desarrolladora {$request->input('desarrolladora')}",
                ], 404);
            }


            $clients = $request->input('clients'); //es un array
            //para crear un nuevo cliente
            foreach ($clients as $client_data) {
                $cliente = new Cliente($client_data);
                $cliente->id_desarrolladora = $desarrolladora->id;
                $cliente->status = true;
                $cliente->save();
                $registered_clients[] = $cliente;
            }

            $contrato = new Contrato($request->input('contrato'));
            $contrato->n_contrato = $this->generateNumContrato();
            $contrato->status = true;
            $contrato->save();

            $detalle_contrato = new DetalleContrato($request->input('detalle_contrato'));
            $detalle_contrato->status = true;
            $detalle_contrato->id_contrato = $contrato->id;
            $detalle_contrato->save();

            foreach ($registered_clients  as $client) {
                $cliente_has_contrato = new ClienteHasContrato();
                $cliente_has_contrato->id_contrato = $contrato->id;
                $cliente_has_contrato->id_cliente = $client->id;
                $cliente_has_contrato->save();
            }

            //ahora creamos el archivo pdf
            $contrato->archivo_pdf = $this->generatePdfContrato(
                count($registered_clients),
                $request->input('detalle_contrato.add_info_terreno'),
                $contrato->id
            );
            $contrato->update();

            $id_contrato = $contrato->id;
            //no es necessatio verificar el 'status' de cad atabla ya que es creacion de un nuevo registro
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
                ->where('contratos.id', $id_contrato)
                ->first();

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

    public function generateNumContrato()
    {
        //obtenemos ultimo contrato registrato por ciudad
        //y no necesitamos verificar el status
        // ya que aunque el registro se haya eliminado igualmente contatos los registros de contratos para generar un n_contrato
        $ultimo_contrato_id = Contrato::join('detalle_contratos', 'detalle_contratos.id_contrato', '=', 'contratos.id')
            ->join('clientes_has_contratos', 'clientes_has_contratos.id_contrato', '=', 'contratos.id')
            ->join('clientes', 'clientes.id', '=', 'clientes_has_contratos.id_cliente')
            ->select(
                'contratos.*',
            )
            ->where('clientes.status', true)
            ->where('contratos.status', true)
            ->where('detalle_contratos.status', true)
            ->max('contratos.id');

        $ultimo_contrato = Contrato::find($ultimo_contrato_id);

        // verificamos si es el primer registro, entonces es nulo y asignamos el numero 0000/2023
        $old_numero_contrato = ($ultimo_contrato == null) ? '0000/2023' : $ultimo_contrato->n_contrato;

        $parts = explode('/', $old_numero_contrato); // Divide la cadena en partes usando "/"
        $num_result = $parts[0]; // Obtiene la primera subparte, que es '0000'

        $num_result = $num_result + 1;
        $num_result = str_pad($num_result, 4, '0', STR_PAD_LEFT); //agregar ceros al numero
        $year =  date('Y');;
        return  "{$num_result}/{$year}";
    }

    public function update(ClienteContratoDetalleContratoRequest $request,)
    {
        try {
            //el update no esta hecho nada

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
            // $contrato->archivo_pdf = $this->generatePdfContrato(,$contrato->id);
            $contrato->update();

            // si todo es ok entonces eliminados el archivo pdf antiguo
            $parse_path_pdf = str_replace('/storage', "", $antiguo_pdf_path);
            Storage::disk('public')->delete($parse_path_pdf);

            //agregamos para enviar datos al frontend
            $contrato['fecha_firma_contrato'] = $request->input('fecha_firma_contrato');

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

    public function showDetalleContrato(Request $request)
    {
        try {
            $detalle_contrato = DetalleContrato::where('status', true)
                ->where('id_contrato', $request->input('id_contrato'))
                ->first();

            return response()->json([
                'status' => true,
                'message' => 'OK',
                'record' => $detalle_contrato,
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => null,
            ], 500);
        }
    }

    public function generatePdfContrato(int $number_of_clients, string $add_info_terreno, int $id)
    {
        $contrato_cliente = Contrato::join('detalle_contratos', 'detalle_contratos.id_contrato', '=', 'contratos.id')
            ->join('clientes_has_contratos', 'clientes_has_contratos.id_contrato', '=', 'contratos.id')
            ->join('clientes', 'clientes.id', '=', 'clientes_has_contratos.id_cliente')
            ->select(
                'clientes.nombres',
                'clientes.apellido_paterno',
                'clientes.apellido_materno',
                'clientes.ci',
                'clientes.ci_expedido',
                'contratos.*',
                'detalle_contratos.*'
            )
            ->where('clientes.status', true)
            ->where('contratos.status', true)
            ->where('detalle_contratos.status', true)
            ->where('contratos.id', $id)
            ->first();

        $pdf = PDF::loadView('pdf/contrato/contrato-pdf', [
            'number_of_clients' => $number_of_clients,
            'add_info_terreno' => $add_info_terreno,
            'contrato_cliente' => $contrato_cliente
        ]);
        //$pdf->setPaper((array(0, 0, 612.00, 1008.00)),'landscape');//oficio horizontal
        $pdf->setPaper('legal', 'portrait'); //ocicio  vertical

        //debemos parsear el numero de contrato porque tiene el formato '2023/0001_LP' 
        // no se puede guardar archivos con ese tipo de nombre entonces  obtenemos el nombre de este formato '2023_0001_LP'
        $n_contrato_parse = str_replace('/', '_', $contrato_cliente->n_contrato);
        $pdf_path = "pdf/contrato/{$n_contrato_parse}-{$contrato_cliente->nombres}-{$contrato_cliente->apellido_paterno}-{$contrato_cliente->apellido_materno}-" . uniqid() . ".pdf";

        Storage::disk('public')->put($pdf_path,  $pdf->download()->getOriginalContent());
        return  "/storage/{$pdf_path}";
    }
}//class
