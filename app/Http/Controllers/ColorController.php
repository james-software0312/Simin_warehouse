<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ColorService;
use DataTables;


class ColorController extends Controller
{
    protected $colorService;

    public function __construct(ColorService $colorService)
    {
        // Middleware to check login status
        $this->middleware('checkLogin');
        $this->colorService = $colorService;
    }

    /**
     * Display the index view for colors.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalcolors = $this->colorService->getAll();
        return view('color.index')->with([
            'totalcolors' => $totalcolors,
        ]);
    }

    public function get(Request $request)
    {
        // Retrieve permissions from the middleware
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;
        if ($request->ajax()) {
            $data = $this->colorService->getAll();

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

    public function store(Request $request)
    {
        $data = $request->only(['name', 'description']);
        $group = $this->colorService->create($data);

        return redirect()->route('color.index')->with('success', __('text.msg_color_created'));
    }
}
