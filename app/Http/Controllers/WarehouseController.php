<?php

namespace App\Http\Controllers;

use App\Services\WarehouseService;
use Illuminate\Http\Request;
use DataTables;

class WarehouseController extends Controller
{
    protected $warehouseService;

    public function __construct(WarehouseService $warehouseService)
    {
        //$this->middleware('checkLogin');
        $this->warehouseService = $warehouseService;
    }

    /**
     * Display the index view for warehouses.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('warehouse.index');
    }

    /**
     * Get warehouse data for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->warehouseService->getAll(); 

            return DataTables::of($data)
            ->addColumn('action', function ($data) {
                return '<div class="d-flex"><a data-bs-toggle="modal" data-bs-target="#EditModal" id="btnedit" data-editid="' . $data->id . '" class="btn btn-sm btn-success d-flex align-items-center" data-toggle="modal" data-target="#edit">
                    <span class="material-symbols-rounded">edit</span> '.__('text.edit').'
                </a>&nbsp;
                <a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="' . $data->id . '" class="btn btn-sm btn-danger d-flex align-items-center">
                    <span class="material-symbols-rounded">delete</span> '.__('text.delete').'
                </a></div>';
            })
            ->toJson();
        }

        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get all warehouse data for JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        $data = $this->warehouseService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Get warehouse data by ID for JSON response.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->warehouseService->getById($id);
        return response()->json($data);
    }

    /**
     * Store a new warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {   
        // $request->validate([
        //     'code' => 'unique:category,code',
        //     // Add more validation rules as needed
        // ]);

        // $code   = $request->only(['code']);
        
        $data = $request->only(['name', 'description']);
        $group = $this->warehouseService->create($data);

        return redirect()->route('warehouse.index')->with('success', __('text.msg_warehouse_created'));
    }

    /**
     * Update an existing warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {   
        $primary = $this->warehouseService->getPrimary();
        if($primary && $request->input('is_primary')){            
            return redirect()->route('warehouse.index')->with('error', __('text.msg_primary_warehouse_already_exist'));
        }
        $id   = $request->only(['editid']);
        $data = $request->only(['name', 'description', 'is_primary']);
        $group = $this->warehouseService->update($id, $data);

        return redirect()->route('warehouse.index')->with('success', __('text.msg_warehouse_updated'));
    }

    /**
     * Delete a warehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->only('deleteid');
        $this->warehouseService->delete($id);

        return redirect()->route('warehouse.index')->with('success', __('text.msg_warehouse_deleted'));;
    }
}
