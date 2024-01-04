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
                    //No se muestra todos los datos del cliente en el tablero, pero se envia todos los datos del cliente con el fin de mostrar todos
                    // los datos del cliente al momento de editar el registro
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                    'clientes.ci',
                    'clientes.ci_expedido',
                    'clientes.n_de_contacto',
                    'clientes.direccion',
                    'clientes.descripcion as descripcion_cliente',
                    'contratos.*',
                    'detalle_contratos.fecha_firma_contrato',
                    //debemos enviar un campo unico para mostrar en el v-data-table  el campo contrato.id no cuenta ya que mas
                    //de un registro puede tener el mismo contrato.id porque clientes y contratos tiene una relacion de muchos a muchos
                    'clientes_has_contratos.id as id_clientes_has_contratos',
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

            switch ($request->input('type_of_register_client')) {
                case 'cliente-nuevo':
                    //para crear un nuevo cliente
                    foreach ($clients as $client_data) {
                        $cliente = new Cliente($client_data);
                        $cliente->id_desarrolladora = $desarrolladora->id;
                        $cliente->status = true;
                        $cliente->save();
                        $registered_clients[] = $cliente;
                    }
                    break;
                case 'cliente-registrado':
                    //almacenar en un array el el cliente registrado
                    foreach ($clients as $client_data) {
                        $registered_clients[] = $client_data;
                    }
                    break;
                default:
                    return response()->json([
                        'status' => false,
                        'message' => "No se pudo encontrar ningun tipo de registro para el cliente!",
                        'record' => null,
                    ], 422);
                    break;
            } //switch

            $request_contrato = $request->input('contrato');
            $request_detalle_contrato = $request->input('detalle_contrato');

            $contrato = new Contrato($request_contrato);
            $contrato->n_contrato = $this->generateNumContrato();
            $contrato->status = true;
            $contrato->save();

            if ($request_detalle_contrato['add_info_terreno']) {
                $detalle_contrato = new DetalleContrato($request_detalle_contrato);
                $detalle_contrato->status = true;
                $detalle_contrato->id_contrato = $contrato->id;
                $detalle_contrato->save();
            } else {
                // Lista de campos a ignorar
                $ignored_fields = ['terreno_valor_total_numeral', 'terreno_valor_total_literal', 'terreno_val_couta_inicial_numeral', 'terreno_val_couta_mensual_numeral'];
                // Ignorar campos especificados
                $request_detalle_contrato = collect($request_detalle_contrato)->except($ignored_fields)->toArray();
                $detalle_contrato = new DetalleContrato($request_detalle_contrato);
                $detalle_contrato->status = true;
                $detalle_contrato->id_contrato = $contrato->id;
                $detalle_contrato->save();
            }


            foreach ($registered_clients  as $client) {
                $cliente_has_contrato = new ClienteHasContrato();
                $cliente_has_contrato->id_contrato = $contrato->id;
                $cliente_has_contrato->id_cliente = $client['id']; //accedemos asi porque es un array
                $cliente_has_contrato->save();
            }

            //ahora creamos el archivo pdf
            $contrato->archivo_pdf = $this->generatePdfContrato(
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
                    //No se muestra todos los datos del cliente en el tablero, pero se envia todos los datos del cliente con el fin de mostrar todos
                    // los datos del cliente al momento de editar el registro
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                    'clientes.ci',
                    'clientes.ci_expedido',
                    'clientes.n_de_contacto',
                    'clientes.direccion',
                    'clientes.descripcion as descripcion_cliente',
                    'contratos.*',
                    'detalle_contratos.fecha_firma_contrato',
                    //debemos enviar un campo unico para mostrar en el v-data-table  el campo contrato.id no cuenta ya que mas
                    //de un registro puede tener el mismo contrato.id porque clientes y contratos tiene una relacion de muchos a muchos
                    'clientes_has_contratos.id as id_clientes_has_contratos',
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
                'record' => null,
            ], 500);
        }
    }

    public function update(ClienteContratoDetalleContratoRequest $request,)
    {
        try {
            $request_contrato = $request->input('contrato');
            $request_detalle_contrato = $request->input('detalle_contrato');

            $contrato = Contrato::where('id', $request_contrato['id']) //accedemos asi porque es un array
                ->where('status', true)
                ->first();

            $detalle_contrato = DetalleContrato::where('id_contrato', $request_contrato['id'])
                ->where('status', true)
                ->first();

            //verificamos si el registro existe por establidad del sistema
            if ($contrato == null || $detalle_contrato == null) {
                return response()->json([
                    'status' => false,
                    'message' => "Este registro no se encuentra en el sistema!",
                ], 404);
            }

            $contrato->update($request_contrato);

            if ($request_detalle_contrato['add_info_terreno']) {
                $detalle_contrato->update($request_detalle_contrato);
            } else {
                // en caso de que estos campos se hayan llenado anteriormente
                //y se requiera quitar la informacion adicional del terreno entonces asignamos nulo
                $detalle_contrato->fill($request_detalle_contrato);
                $detalle_contrato->terreno_valor_total_numeral = null;
                $detalle_contrato->terreno_valor_total_literal = null;
                $detalle_contrato->terreno_val_couta_inicial_numeral = null;
                $detalle_contrato->terreno_val_couta_mensual_numeral = null;
                $detalle_contrato->update();
            }

            //ahora modificamos el archivo pdf y eliminamos el antiguo pdf
            $parse_path_archivo_pdf = str_replace("/storage", "", $contrato->archivo_pdf);
            Storage::disk('public')->delete($parse_path_archivo_pdf);
            $contrato->archivo_pdf = $this->generatePdfContrato($contrato->id);
            $contrato->update();

            $id_contrato = $contrato->id;
            //no es necessatio verificar el 'status' de cad atabla ya que es creacion de un nuevo registro
            $contrato = Contrato::join('detalle_contratos', 'detalle_contratos.id_contrato', '=', 'contratos.id')
                ->join('clientes_has_contratos', 'clientes_has_contratos.id_contrato', '=', 'contratos.id')
                ->join('clientes', 'clientes.id', '=', 'clientes_has_contratos.id_cliente')
                ->join('desarrolladoras', 'desarrolladoras.id', '=', 'clientes.id_desarrolladora')
                //No se muestra todos los datos del cliente en el tablero, pero se envia todos los datos del cliente con el fin de mostrar todos
                // los datos del cliente al momento de editar el registro
                ->select(
                    'clientes.nombres',
                    'clientes.apellido_paterno',
                    'clientes.apellido_materno',
                    'clientes.ci',
                    'clientes.ci_expedido',
                    'clientes.n_de_contacto',
                    'clientes.direccion',
                    'clientes.descripcion as descripcion_cliente',
                    'contratos.*',
                    'detalle_contratos.fecha_firma_contrato',
                    //debemos enviar un campo unico para mostrar en el v-data-table  el campo contrato.id no cuenta ya que mas
                    //de un registro puede tener el mismo contrato.id porque clientes y contratos tiene una relacion de muchos a muchos
                    'clientes_has_contratos.id as id_clientes_has_contratos',
                )
                ->where('contratos.id', $id_contrato)
                ->first();

            return response()->json([
                'status' => true,
                'message' => "Contrato actualizado exitosamente!",
                'record' => $contrato,
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
            $contrato = Contrato::find($request->input('id'));
            $detalle_contrato = DetalleContrato::where('id_contrato', $request->input('id'))->first();

            //verificamos si el registro existe por establidad del sistema
            if ($contrato == null || $detalle_contrato == null) {
                return response()->json([
                    'status' => false,
                    'message' => "Este registro no se encuentra en el sistema!",
                ], 404);
            }

            $contrato->status = false;
            $contrato->update();
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
            //se agrega el add_info_terreno=> para que en el frontend se muestre habilitado el checkbox
            //add_info_terreno se muestren los datos

            //verificamos si el registro existe por establidad del sistema
            if ($detalle_contrato == null) {
                return response()->json([
                    'status' => false,
                    'message' => "Este registro no se encuentra en el sistema!",
                ], 404);
            }

            if ($detalle_contrato->terreno_valor_total_numeral == null || $detalle_contrato->terreno_val_couta_inicial_numeral == null || $detalle_contrato->terreno_val_couta_mensual_numeral == null) {
                $detalle_contrato['add_info_terreno'] = false;
            } else {
                $detalle_contrato['add_info_terreno'] = true;
            }

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

    public function generatePdfContrato(int $id)
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
                'clientes.direccion',
                'contratos.*',
                'detalle_contratos.*'
            )
            ->where('clientes.status', true)
            ->where('contratos.status', true)
            ->where('detalle_contratos.status', true)
            ->where('contratos.id', $id)
            //se ordena de manera ascendente porque al crear un registro en clientes_has_contratos
            //se crea segun el orden del cliente en el metodo 'store'
            //entonces se debe ordenar el cliente  1 y cliente 2
            //si ordenamos por cliente.id en algunos casos no funcionara al crear un contrato, entonces
            //con 'cliente registrado',  ahi el usuario decide quien es cliente 1 y cliente 2,por lo tanto para este modo lo funcionar con clientes_has_contratos, pero
            //si ordenamos cliente.id ahi si  solo funcionara para 'cliente nuevo' al crear el cotrato
            //pero con clientes_has_contratos funciona para ambos casos 'cliente registrado' 'cliente nuevo'
            ->orderBy('clientes_has_contratos.id', 'ASC')
            ->take(2)
            ->get();

        $number_of_clients = $contrato_cliente->count();
        if ($contrato_cliente[0]->terreno_valor_total_numeral == null || $contrato_cliente[0]->terreno_val_couta_inicial_numeral == null || $contrato_cliente[0]->terreno_val_couta_mensual_numeral == null) {
            $add_info_terreno = false;
        } else {
            $add_info_terreno = true;
        }

        $pdf = PDF::loadView('pdf/contrato/contrato-pdf', [
            'number_of_clients' => $number_of_clients,
            'add_info_terreno' => $add_info_terreno,
            'contrato_cliente' => $contrato_cliente
        ]);
        //$pdf->setPaper((array(0, 0, 612.00, 1008.00)),'landscape');//oficio horizontal
        $pdf->setPaper('legal', 'portrait'); //oficio  vertical

        //debemos parsear el numero de contrato porque tiene el formato '2023/0001_LP' 
        // no se puede guardar archivos con ese tipo de nombre entonces  obtenemos el nombre de este formato '2023_0001_LP'
        $n_contrato_parse = str_replace('/', '_', $contrato_cliente[0]->n_contrato);
        $pdf_path = "pdf/contrato/{$n_contrato_parse}_{$contrato_cliente[0]->nombres}_{$contrato_cliente[0]->apellido_paterno}_{$contrato_cliente[0]->apellido_materno}_" . uniqid() . ".pdf";

        Storage::disk('public')->put($pdf_path,  $pdf->download()->getOriginalContent());
        return  "/storage/{$pdf_path}";
    }

    public function updatePdfFile(Request $request)
    {
        //creamos esta funcion para actualizar el pdf
        //en caso de que se actualize los datos del cliente en el menu clientes entonces se debe volver a
        //generar el archivo pdf
        try {
            $contrato = Contrato::where('id', $request->input('id_contrato')) //accedemos asi porque es un array
                ->select()
                ->where('status', true)
                ->first();
            //verificamos si el registro existe por establidad del sistema
            if ($contrato == null) {
                return response()->json([
                    'status' => false,
                    'message' => "Este registro no se encuentra en el sistema!",
                ], 404);
            }

            //ahora actualizamos el archivo pdf y eliminamos el archivo pdf anterior
            $parse_path_archivo_pdf = str_replace("/storage", "", $contrato->archivo_pdf);
            Storage::disk('public')->delete($parse_path_archivo_pdf);
            $contrato->archivo_pdf = $this->generatePdfContrato($contrato->id);
            $contrato->update();
            return response()->json([
                'status' => true,
                'message' => "Archivo pdf actualizado!",
                'record' => ['archivo_pdf' => $contrato->archivo_pdf],
            ], 200);
        } catch (Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
                'record' => null,
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
}//class