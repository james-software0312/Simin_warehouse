<?php

namespace App\Http\Controllers;

use App\Services\ShelfService;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use DataTables;

class ShelfController extends Controller
{
    protected $shelfService;
    protected $warehouseService;

    public function __construct(ShelfService $shelfService, WarehouseService $warehouseService,)
    {
        // Inject ShelfService into the controller
        $this->middleware('checkLogin');
        $this->shelfService         = $shelfService;
        $this->warehouseService     = $warehouseService;
    }

    /**
     * Display the index page for shelves.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {   
        $totalstockwarehouses        = $this->warehouseService->getAll();
        // Render the shelf index view
        return view('shelf.index')->with([
            'totalwarehouses' => $totalstockwarehouses,
        ]);
    }

    /**
     * Get data for shelves, used in DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {   
        // Retrieve permissions from the middleware
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;

        // Check if the request is AJAX
        if ($request->ajax()) {
            // Get all shelf data
            $data = $this->shelfService->getAll(); 

            // Prepare data for DataTables, including action buttons
            return DataTables::of($data)
            ->addColumn('action', function ($data) use($hasEditPermission, $hasDeletePermission){
                $actionHtml = '<div class="d-flex">';
                // Edit Button
                if ($hasEditPermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#EditModal" id="btnedit" data-editid="' . $data->id . '" class="btn btn-sm btn-success d-flex align-items-center" data-toggle="modal" data-target="#edit">
                        <span class="material-symbols-rounded">edit</span> '.__('text.edit').'
                    </a>&nbsp;';
                }

                // Delete Button
                if ($hasDeletePermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="' . $data->id . '" class="btn btn-sm btn-danger d-flex align-items-center">
                        <span class="material-symbols-rounded">delete</span> '.__('text.delete').'
                    </a>&nbsp;';
                }
                $actionHtml .= '</div>';
                return $actionHtml;
            })
            ->toJson();
        }

        // If not AJAX, return unauthorized access
        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get all shelf data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        // Get all shelf data and return as JSON
        $data = $this->shelfService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Show details of a specific shelf.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Get shelf data by ID and return as JSON
        $data = $this->shelfService->getById($id);
        return response()->json($data);
    }

    /**
     * Store a new shelf based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Extract relevant data from the request
        $data = $request->only(['code', 'name','warehouseid', 'description']);
        
        // Create a new shelf using the extracted data
        $group = $this->shelfService->create($data);

        // Redirect to the shelf index page with a success message
        return redirect()->route('shelf.index')->with('success', __('text.msg_shelf_created'));
    }

    /**
     * Update an existing shelf based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {   
        // Extract relevant data from the request
        $id   = $request->only(['editid']);
        $data = $request->only(['code','name', 'warehouseid', 'description']);
        
        // Update the shelf with the extracted data
        $group = $this->shelfService->update($id, $data);

        // Redirect to the shelf index page with a success message
        return redirect()->route('shelf.index')->with('success', __('text.msg_shelf_updated'));
    }

    /**
     * Delete a shelf based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Extract the ID of the shelf to be deleted
        $id = $request->only('deleteid');
        
        // Delete the shelf with the specified ID
        $this->shelfService->delete($id);

        // Redirect to the shelf index page with a success message
        return redirect()->route('shelf.index')->with('success', __('text.msg_shelf_deleted'));;
    }

    /**
     * Check if a shelf code already exists.
     *
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcode($code)
    {
        // Check if the provided shelf code already exists
        $checkcode = $this->shelfService->CheckCode($code);
        
        // Return the result as JSON
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }

    /**
     * Check if a shelf code already exists, excluding a specific ID.
     *
     * @param  string  $code
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcodeid($code, $id)
    {
        // Check if the provided shelf code already exists, excluding a specific ID
        $checkcode = $this->shelfService->CheckCodeId($code, $id);
        
        // Return the result as JSON
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }

    /**
     * Get all shelf data by warehouse.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetByWarehouse($warehouse)
    {
        // Get all shelf data and return as JSON
        $data = $this->shelfService->GetByWarehouse($warehouse);
        return response()->json($data);
    }

    
}
