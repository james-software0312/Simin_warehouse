<?php

namespace App\Http\Controllers;

use App\Services\UnitService;
use Illuminate\Http\Request;
use DataTables;

class UnitController extends Controller
{
    protected $unitService;

    public function __construct(UnitService $unitService)
    {
        // Middleware to check login status
        $this->middleware('checkLogin');
        $this->unitService = $unitService;
    }

    /**
     * Display the index view for units.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalunits = $this->unitService->getAll();
        return view('unit.index')->with([
            'totalunits' => $totalunits,
        ]);
    }

    /**
     * Get unit data for DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        // Retrieve permissions from the middleware
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;

        if ($request->ajax()) {
            $data = $this->unitService->getAll();

            return DataTables::of($data)
                ->addColumn('action', function ($data) use ($hasEditPermission, $hasDeletePermission) {
                    if($data->name == 'karton' || $data->name == 'para') return '';
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

        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get all unit data for JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        $data = $this->unitService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Get unit data by ID for JSON response.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->unitService->getById($id);
        return response()->json($data);
    }

    /**
     * Store a new unit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->only(['code', 'name', 'description']);
        $group = $this->unitService->create($data);

        return redirect()->route('unit.index')->with('success', __('text.msg_unit_created'));
    }

    /**
     * Check if a unit code already exists.
     *
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcode($code)
    {
        $checkcode = $this->unitService->CheckCode($code);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }

    /**
     * Check if a unit code exists for a specific ID.
     *
     * @param  string  $code
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcodeid($code, $id)
    {
        $checkcode = $this->unitService->CheckCodeId($code, $id);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }

    /**
     * Update an existing unit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $id   = $request->only(['editid']);
        $data = $request->only(['code', 'name', 'description']);
        $group = $this->unitService->update($id, $data);

        return redirect()->route('unit.index')->with('success', __('text.msg_unit_updated'));
    }

    /**
     * Delete a unit.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->only('deleteid');
        $this->unitService->delete($id);

        return redirect()->route('unit.index')->with('success', __('text.msg_unit_deleted'));
    }

    public function edit(Request $request) {
        $id = $request->id;
        $unit = $this->unitService->getById($id);
        return view('unit.edit')->with([
            "data" => $unit
        ]);
     }
}
