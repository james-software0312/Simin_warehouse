<?php

namespace App\Http\Controllers;

use App\Services\VatService;
use Illuminate\Http\Request;
use DataTables;

class VatController extends Controller
{
    protected $vatService;

    public function __construct(VatService $vatService)
    {
        // Middleware to check login status
        $this->middleware('checkLogin');
        $this->vatService = $vatService;
    }

    /**
     * Display the index view for vats.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalvats = $this->vatService->getAll();
        return view('vat.index')->with([
            'totalvats' => $totalvats,
        ]);
    }

    /**
     * Get vat data for DataTables.
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
            $data = $this->vatService->getAll();

            return DataTables::of($data)
                ->addColumn('action', function ($data) use ($hasEditPermission, $hasDeletePermission) {
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
     * Get all vat data for JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        $data = $this->vatService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Get vat data by ID for JSON response.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->vatService->getById($id);
        return response()->json($data);
    }

    /**
     * Store a new vat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->only(['name', 'description']);
        $group = $this->vatService->create($data);

        return redirect()->route('vat.index')->with('success', __('text.msg_vat_created'));
    }


    /**
     * Update an existing vat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $id   = $request->only(['editid']);
        $data = $request->only(['name', 'description']);
        $group = $this->vatService->update($id, $data);

        return redirect()->route('vat.index')->with('success', __('text.msg_vat_updated'));
    }

    /**
     * Delete a vat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->only('deleteid');
        $this->vatService->delete($id);

        return redirect()->route('vat.index')->with('success', __('text.msg_vat_deleted'));
    }
}
