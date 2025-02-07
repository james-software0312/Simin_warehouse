<?php

namespace App\Http\Controllers;

use App\Services\SizeService;
use Illuminate\Http\Request;
use DataTables;

class SizeController extends Controller
{
    protected $sizeService;

    public function __construct(SizeService $sizeService)
    {
        // Middleware to check login status
        $this->middleware('checkLogin');
        $this->sizeService = $sizeService;
    }

    /**
     * Display the index view for sizes.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalsizes = $this->sizeService->getAll();
        return view('size.index')->with([
            'totalsizes' => $totalsizes,
        ]);
    }

    /**
     * Get size data for DataTables.
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
            $data = $this->sizeService->getAll();

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
     * Get all size data for JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        $data = $this->sizeService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Get size data by ID for JSON response.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->sizeService->getById($id);
        return response()->json($data);
    }

    /**
     * Store a new size.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->only(['name', 'description']);
        $group = $this->sizeService->create($data);

        return redirect()->route('size.index')->with('success', __('text.msg_size_created'));
    }

    public function ajaxstore(Request $request)
    {
        $data = $request->only(['name', 'description']);
        $group = $this->sizeService->create($data);

        return response()->json(['id' => $group->id]);
    }


    /**
     * Update an existing size.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $id   = $request->only(['editid']);
        $data = $request->only(['name', 'description']);
        $group = $this->sizeService->update($id, $data);

        return redirect()->route('size.index')->with('success', __('text.msg_size_updated'));
    }

    /**
     * Delete a size.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->only('deleteid');
        $this->sizeService->delete($id);

        return redirect()->route('size.index')->with('success', __('text.msg_size_deleted'));
    }

    public function edit(Request $request) {
        $id = $request->id;
        $size = $this->sizeService->getById($id);
        return view('size.edit')->with([
            "data" => $size
        ]);
     }
}
