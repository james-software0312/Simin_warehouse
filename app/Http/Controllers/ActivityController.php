<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use DataTables;

class ActivityController extends Controller
{
    protected $categoryService;
    protected $settingsService;

    /**
     * Constructor for ActivityController.
     *
     * @param \App\Services\ActivityLogService $activitylogService The service for activity logs.
     * @return void
     */
    public function __construct(ActivityLogService $activitylogService, SettingsService $settingsService)
    {
        // Middleware to check login status.
        $this->middleware('checkLogin');

        // Injecting the ActivityLogService and SettingsService
        $this->activitylogService = $activitylogService;
        $this->settingsService    = $settingsService;
    }

    /**
     * Display the index view for activity logs.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('activity.index');
    }

    /**
     * Get activity log data for DataTables.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        if ($request->ajax()) {
            // Get all activity log data.
            $data = $this->activitylogService->getAll();

            // Prepare DataTables response.
            return DataTables::of($data)
                ->addColumn('properties', function ($data) {
                    // Format 'properties' column as code.
                    return '<div><code>'.$data->properties.'</code></div>';
                })
                ->addColumn('action', function ($data) {
                    // Add 'Detail' button with edit modal trigger.
                    return '<div class="d-flex"><a data-bs-toggle="modal" data-bs-target="#detailModel" id="btnedit" data-editid="' . $data->id . '" class="btn btn-sm btn-success d-flex align-items-center" data-toggle="modal" data-target="#edit">
                        <span class="material-symbols-rounded">edit</span> '.__('text.detail').'
                    </a></div>';
                })
                ->addColumn('created_at', function($data){
                    //get setting 
                    $setting = $this->settingsService->getdataById(1);
                    $dateformat = date($setting['datetime'], strtotime($data->created_at));
                    return $dateformat;
                })
                ->rawColumns(['properties','action', 'created_at'])
                ->toJson();
        }

        // Return 403 error for unauthorized access.
        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get all activity log data as JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        // Get all activity log data.
        $data = $this->activitylogService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Get activity log details by ID.
     *
     * @param int $id The ID of the activity log.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Get activity log details by ID.
        $data = $this->activitylogService->getById($id);

        // Return JSON response.
        return response()->json($data);
    }
}
