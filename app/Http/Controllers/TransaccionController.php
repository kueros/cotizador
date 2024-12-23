<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use App\Models\AlertaDetalle;
use App\Models\TipoTransaccion;
use App\Models\TipoTransaccionCampoAdicional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;


class TransaccionController extends Controller
{
	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function index($id, Request $request, MyController $myController): View
	{
		#dd($id);
/* 		$permiso_listar_funciones = $myController->tiene_permiso('list_funciones');
		if (!$permiso_listar_funciones) {
			abort(403, '.');
			return false;
		}
*/		
		// Obtener los detalles de las transacciones
		$transacciones = Transaccion::where('tipo_transaccion_id', 'id')->paginate();
		#dd($transacciones);
        return view('transacciones.index', compact('transacciones', 'id'))
			->with('i', ($request->input('page', 1) - 1) * $transacciones->perPage());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function listado($id, Request $request)
	{
		try {
			// Obtener las columnas base de la tabla "transacciones"
			$columnsBase = [
				['nombre_campo' => 'nombre', 'nombre_mostrar' => 'Nombre', 'valores' => null, 'tipo' => 1],
				['nombre_campo' => 'descripcion', 'nombre_mostrar' => 'Descripción', 'valores' => null, 'tipo' => 1],
				['nombre_campo' => 'tipo_transaccion_id', 'nombre_mostrar' => 'Tipo Transacción', 'valores' => null, 'tipo' => 1, 'hidden' => true, 'default_value' => $id],
			];  
	
			// Validar si el tipo de transacción existe
			$tipoTransaccion = TipoTransaccion::find($id);
			if (!$tipoTransaccion) {
				return response()->json(['error' => 'Tipo de transacción no encontrado'], 400);
			}
	
			// Obtener los campos adicionales para el tipo de transacción
			$camposAdicionales = DB::table('tipos_transacciones_campos_adicionales')
			->where('tipo_transaccion_id', $id)
			->where('visible', 1)
			->orderBy('orden_listado', 'asc')
			->get(['nombre_campo', 'nombre_mostrar', 'valores', 'tipo'])
			->map(function ($item) { 
				$item = (array) $item; // Convertir stdClass a array
		
				// Manejar 'valores' según su tipo
				if (is_string($item['valores'])) {
					// Si 'valores' es un string, decodificar JSON
					$item['valores'] = json_decode($item['valores'], true);
				} elseif (!is_array($item['valores'])) {
					// Si no es un array ni un string JSON, establecer como null
					$item['valores'] = null;
				}
				return $item;
			})
			->toArray();
			// Unir las columnas base con los campos adicionales
			$columns = array_merge($columnsBase, $camposAdicionales);
	
			// Excluir columnas ocultas
			$columnsVisible = array_filter($columns, function ($column) {
				return empty($column['hidden']); // Solo incluir columnas que no estén ocultas
			});
	
			// Obtener los datos filtrados por tipo de transacción
			$transacciones = Transaccion::where('tipo_transaccion_id', $id)->get();
			// Preparar los datos para DataTables
			$data = $transacciones->map(function ($transaccion) use ($columns, $id) {
				$row = [];
			
				foreach ($columns as $column) {
					$nombreCampo = $column['nombre_campo'];
			
					if (isset($transaccion->$nombreCampo)) {
						$valorCampo = $transaccion->$nombreCampo;
						if ($column['tipo'] == 4 && is_array($column['valores'])) {
							$valores = $column['valores'];
							$row[$nombreCampo] = $valores[$valorCampo];
						} else {
							$row[$nombreCampo] = $valorCampo;
						}
					} else {
						$row[$nombreCampo] = '';
					}
				}
			
				// Agregar tipo_transaccion_id como dato oculto
				$row['tipo_transaccion_id'] = $id;
			
				// Agregar el ID como un campo con nombre
				$row['id'] = $transaccion->id;
			
				return $row;
			});
			#dd($data);
			// Preparar la respuesta JSON
			return response()->json([
				"columns" => array_values($columnsVisible), // Solo columnas visibles
				"data" => $data,
				"recordsTotal" => $transacciones->count(),
				"recordsFiltered" => $transacciones->count(),
			]);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}


	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function edit($id)
	{
		try {
			// Obtener la transacción específica
			$transaccion = Transaccion::findOrFail($id);
	
			// Obtener las columnas base
			$columnsBase = [
				['nombre_campo' => 'id', 'nombre_mostrar' => 'ID', 'valores' => null, 'tipo' => 1, 'hidden' => true, 'default_value' => $transaccion->id],
				['nombre_campo' => 'nombre', 'nombre_mostrar' => 'Nombre', 'valores' => null, 'tipo' => 1, 'default_value' => $transaccion->nombre],
				['nombre_campo' => 'descripcion', 'nombre_mostrar' => 'Descripción', 'valores' => null, 'tipo' => 1, 'default_value' => $transaccion->descripcion],
				['nombre_campo' => 'tipo_transaccion_id', 'nombre_mostrar' => 'Tipo Transacción', 'valores' => null, 'tipo' => 1, 'hidden' => true, 'default_value' => $transaccion->tipo_transaccion_id],
			];

			// Obtener los campos adicionales asociados al tipo de transacción
			$tipoTransaccionId = $transaccion->tipo_transaccion_id;
			$camposAdicionales = TipoTransaccionCampoAdicional::where('tipo_transaccion_id', $tipoTransaccionId)
				->get(['nombre_campo', 'nombre_mostrar', 'tipo', 'valores'])
				->map(function ($campo) use ($transaccion) {
					return [
						'nombre_campo' => $campo->nombre_campo,
						'nombre_mostrar' => $campo->nombre_mostrar,
						'tipo' => $campo->tipo, // 1=Texto, 2=Número, 3=Fecha, 4=Select
						'default_value' => $transaccion->{$campo->nombre_campo} ?? '', // Valor actual en la transacción
						'valores' => $campo->valores ? json_decode($campo->valores, true) : null, // Parsear valores si existen
						'hidden' => false, // Modificar si necesitas campos ocultos
					];
				})
				->toArray();
	
			// Combinar las columnas base con los campos adicionales
			$columns = array_merge($columnsBase, $camposAdicionales);
	
			return response()->json([
				'columns' => $columns, // Estructura similar a listado
			]);
		} catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], 500);
		}
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function store(Request $request, MyController $myController)
	{
		#dd($request->all());
		// Limpia el campo nombre
		$request->merge([
			'nombre' => preg_replace('/\s+/', ' ', trim($request->input('nombre')))
		]);
	
		// Obtener las columnas de la tabla transacciones
		$transaccionesColumns = Schema::getColumnListing('transacciones');
	
		// Reglas de validación
		$validatedData = Validator::make($request->all(), [
			'nombre' => [
				'required',
				'string',
				'max:100',
				'min:3',
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/', // Solo letras sin acentos y espacios
				Rule::unique('transacciones', 'nombre') // Verifica la unicidad en la tabla
			],
			'descripcion' => [
				'string',
				'max:255',
				'min:3'
			],
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
			'nombre.unique' => 'Este nombre ya está en uso.',
		]);
	
		// Si la validación falla
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => $validatedData->errors()
			]);
		}
	
		$validated = $validatedData->validated(); // Datos validados
	
		// Filtrar los datos del request para incluir solo las columnas válidas
		$filteredData = array_filter($request->all(), function ($key) use ($transaccionesColumns) {
			return in_array($key, $transaccionesColumns);
		}, ARRAY_FILTER_USE_KEY);
		// Crear el registro utilizando fill() y save()
		$transaccion = new Transaccion();
		$transaccion->fill($validated); // Solo los campos definidos en fillable
		foreach ($filteredData as $key => $value) {
			$transaccion->{$key} = $value; // Asignar dinámicamente los campos adicionales
		}
		#dd($transaccion);
		$transaccion->save();
	
		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Creó la transacción \"$transaccion->nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Transacción creada correctamente.'
		]);
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function update(Request $request, Transaccion $transacciones, MyController $myController)
	{
		#dd($request->all());
		// Obtener el modelo de la transacción
		$transacciones = Transaccion::findOrFail($request->id);

		$data = $request->except(['_token', '_method', 'accion']);
		#dd($data);
		$typeValidationRules = [
			'int' => 'nullable|integer',
			'string' => 'nullable|string|max:255',
			'text' => 'nullable|string|max:65535',
			'date' => 'nullable|date|date_format:Y-m-d',
		];
	
		$rules = [
			'nombre' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/',
			],
		];
	
		$dynamicFields = DB::table('tipos_campos')
			->where('id', $request->id)
			->pluck('tipo', 'nombre')
			->toArray();
	
		foreach ($dynamicFields as $fieldName => $fieldType) {
			if (isset($typeValidationRules[$fieldType])) {
				$rules[$fieldName] = $typeValidationRules[$fieldType];
			}
		}
		// Validar todos los datos
		$validatedData = Validator::make($request->all(), $rules, [
			'nombre.regex' => 'El nombre solo puede contener letras, números, espacios y ciertos caracteres especiales (, .).'
		]);

		// Si la validación falla
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => 'Errores de validación',
				'errors' => $validatedData->errors()
			]);
		}
		$validatedData = Validator::make($request->all(), $rules)->validated();

		// Obtener el registro existente (usando findOrFail para garantizar un modelo único)
		$transaccionExistente = Transaccion::where('id', $request->id)->first();
		if (!$transaccionExistente) {
			return response()->json([
				'status' => 0,
				'message' => 'Transacción no encontrada.'
			]);
		}
		
		// Construir el mensaje de cambios
		$cambios = [];
		foreach (['nombre'] as $campo) {
			if ($transaccionExistente->$campo != $validatedData[$campo]) {
				$cambios[] = "cambiando $campo de \"{$transaccionExistente->$campo}\" a \"{$validatedData[$campo]}\"";
			}
		}
	
		$mensajeCambios = implode(', ', $cambios);
		$username = Auth::user()->username;
		$message = "Actualizó la transacción \"{$transaccionExistente->nombre}\" $mensajeCambios.";

		$transacciones->update($data);		
		$transacciones->save();

		// Registrar en el log
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Transacción actualizada correctamente.'
		]);
}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function delete($id, MyController $myController) {
		// Validar si existe el tipo de alerta
		$transaccion = Transaccion::find($id);
		if (!$transaccion) {
			return response()->json(["error" => "El tipo de alerta no existe."], 404);
		}
	
		// Datos para el log
		$nombre = $transaccion->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Eliminó la transacción \"$nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Eliminar el tipo de alerta
		$transaccion->delete();
	
		return response()->json(["status" => true]);
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function importar(Request $request)
	{
		#dd($request->all());
		try {
			$datos = json_decode($request->input('datos'), true);
			$errores = [];
			$importados = 0;
			foreach ($datos as $index => $fila) {
				// Normalizar los datos
				$fila['nombre'] = trim($fila['nombre'] ?? '');
				$fila['descripcion'] = trim($fila['descripcion'] ?? '');
				#$fila['tipo_transaccion_id'] = intval($fila['tipo_transaccion_id'] ?? 0);
				// Validar que todos los campos necesarios estén presentes
				if (empty($fila['nombre']) || empty($fila['descripcion'])) {// || empty($fila['tipo_transaccion_id'])) {
					$errores[] = "Faltan datos en la fila " . ($index + 1);
					continue; // Saltar esta fila
				}
	
				// Procesar y guardar cada fila válida
				Transaccion::create([
					'nombre' => $fila['nombre'],
					'descripcion' => $fila['descripcion'],
					#'tipo_transaccion_id' => $fila['tipo_transaccion_id'],
				]);
				$importados++;
			}
	
			return response()->json([
				'success' => true,
				'message' => "$importados filas importadas correctamente.",
				'errores' => $errores
			]);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function generar_bandeja_dependencias(){
        $this->has_permission(225);

        $niveles_modelo = $this->generic->get_from_table("modelo_niveles",array("disabled" => 0),"posicion asc");

        $niveles_all = array();
        foreach($niveles_modelo as $nivel){
            $niveles_all[$nivel->id] = $nivel;
        }

        $niveles_ignorar = array();
        foreach($niveles_modelo as $nivel){
            if($nivel->dependencia_id){
                if($nivel->posicion < $niveles_all[$nivel->id]->posicion){
                    $this->session->set_flashdata('error_message', "El nivel ".$nivel->nombre." no puede posicionarse antes que su dependencia ".$niveles_all[$nivel->id]->nombre);
                    redirect(base_url('modelo_negocio'));
                }

                if(isset($niveles_ignorar[$nivel->dependencia_id])){
                    unset($niveles_ignorar[$nivel->dependencia_id]);
                }
            }else{
                $niveles_ignorar[$nivel->id] = true;
            }
        }

        //Genero el excel de importacion ignorando los niveles no dependientes
        

        $file_name = 'Bandeja entrada registros y dependencias modelo de negocio '.'_'.time().'.xlsx';
        $archivo = 'uploads/documentos/'.$file_name;

        //Inicializo el archivo
        $objeto_php_excel = $this->instanciar_objeto_php_excel();
        $this->excel_set_hoja_activa($objeto_php_excel,0);
        $this->excel_set_nombre_hoja($objeto_php_excel,"Registros y dependencias");

        $letra = "A";
        $letra_final = "A";
        $campos_ignorar = array("id","id_usuario","id_dependencia");
        foreach($niveles_modelo as $nivel){
            if(!isset($niveles_ignorar[$nivel->id])){
                //$campos_all = $this->generic->get_table_fields($nivel->tabla);
                $campos_all = $this->generic->get_from_table("registros_oyp_posicion",array('nivel_id' => $nivel->id),"posicion asc");

                foreach($campos_all as $campo){
                    if(!in_array($campo->campo,$campos_ignorar)){
                        $nombre_campo = ucfirst(str_replace('_',' ',$campo->campo));
                        $this->excel_escribir_celda($objeto_php_excel,$letra."1", $nivel->nombre." - ".$nombre_campo);
                        $letra_final = $letra;
                        $letra++;
                    }
                }
            }
        }
        $this->excel_pintar_celda_negrita($objeto_php_excel,"A1:".$letra_final."1");

        $this->generar_excel($objeto_php_excel,$archivo,true);
        exit;
    }

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function exportStructure(Request $request)
	{
		try {
			// Analiza la estructura de la tabla desde los resultados
			$tableColumns = $request->input('results')[0] ?? null;
	
			if (!$tableColumns) {
				return response()->json(['success' => false, 'message' => 'No hay datos disponibles para generar la estructura.']);
			}
	
			// Extrae los nombres de las columnas
			$columns = array_keys($tableColumns);
	
			// Define las columnas a excluir
			$excludedColumns = ['id', 'tipo_transaccion_id', 'created_at', 'updated_at'];
	
			// Filtra las columnas excluidas
			$filteredColumns = array_filter($columns, function ($column) use ($excludedColumns) {
				return !in_array($column, $excludedColumns);
			});
	
			// Define el orden deseado
			$customOrder = ['nombre', 'descripcion'];
	
			// Mueve las columnas deseadas al inicio en el orden especificado
			usort($filteredColumns, function ($a, $b) use ($customOrder) {
				$aPos = array_search($a, $customOrder);
				$bPos = array_search($b, $customOrder);
	
				if ($aPos === false && $bPos === false) {
					return 0; // Mantener el orden relativo de columnas no especificadas
				} elseif ($aPos === false) {
					return 1; // Mover $b antes de $a
				} elseif ($bPos === false) {
					return -1; // Mover $a antes de $b
				}
	
				return $aPos - $bPos;
			});
	
			// Crear una hoja de cálculo
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
	
			// Agregar nombres de columnas como encabezados
			foreach (array_values($filteredColumns) as $index => $column) {
				$columnLetter = Coordinate::stringFromColumnIndex($index + 1);
				$sheet->setCellValue($columnLetter . '1', $column); // Fila 1 para encabezados
			}
	
			// Configurar nombre del archivo
			$fileName = 'estructura_transacciones.xlsx';
	
			// Crear el archivo Excel
			$writer = new Xlsx($spreadsheet);
			$tempFile = tempnam(sys_get_temp_dir(), $fileName);
			$writer->save($tempFile);
	
			// Descargar el archivo Excel
			return Response::download($tempFile, $fileName)->deleteFileAfterSend(true);
		} catch (\Exception $e) {
			return response()->json(['success' => false, 'message' => 'Error al generar el archivo: ' . $e->getMessage()]);
		}
	}


	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function consultarRegistros(Request $request)
	{
		try {
			// Obtén los parámetros enviados desde JavaScript
			$table = $request->input('table');
			$where = $request->input('where', []);
			$order = $request->input('order', []);
			$limit = $request->input('limit', null);
			$select = $request->input('select', ['*']);

			// Llama a la función getFromTable
			$results = $this->getFromTable($table, $where, $order, $limit, $select);

			// Devuelve la respuesta como JSON
			return response()->json([
				'success' => true,
				'results' => $results
			]);
		} catch (\Exception $e) {
			return response()->json([
				'success' => false,
				'message' => 'Error al realizar la consulta: ' . $e->getMessage()
			], 500);
		}
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	private function getFromTable($table, $where = null, $order = null, $limit = null, $select = ['*'])
	{
		try {
			$query = \DB::table($table);

			if ($select) {
				$query->select($select);
			}

			if ($where) {
				$query->where($where);
			}

			if ($order) {
				foreach ($order as $column => $direction) {
					$query->orderBy($column, $direction);
				}
			}

			if ($limit) {
				$query->limit($limit);
			}

			return $query->get();
		} catch (\Exception $e) {
			throw new \Exception('Error en la consulta: ' . $e->getMessage());
		}
	}




}

