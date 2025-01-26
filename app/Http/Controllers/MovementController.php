<?php

namespace App\Http\Controllers;

use App\Services\StockItemService;
use App\Services\ContactService;
use App\Services\UnitService;
use App\Services\WarehouseService;
use App\Services\ShelfService;
use App\Services\MovementService;
use App\Services\SellService;
use App\Services\SettingsService;
use App\Services\HiddenService;
use Illuminate\Http\Request;
use DataTables;
use Milon\Barcode\DNS2D;
use Milon\Barcode\DNS1D;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Exports\SellExport;
use App\Exports\CheckinExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;


class MovementController extends Controller
{
    protected $stockitemService;
    protected $contactService;
    protected $unitService;
    protected $warehouseService;
    protected $shelfService;
    protected $movementService;
    protected $sellService;
    protected $settingsService;
    protected $hiddenService;


    /**
     * Constructor for the controller.
     *
     * @param  StockItemService  $stockitemService
     * @param  ContactService  $ContactService
     * @param  UnitService  $unitService
     * @param  WarehouseService  $warehouseService
     * @param  MovementService  $movementService
     * @param  SellService  $sellService
     * @param  ShelfService  $shelfService
     * @param  SettingsService  f$settingsService
     * @param  HiddenService  f$hiddenService
     * @return void
     */
    public function __construct(StockItemService $stockitemService, 
                                ContactService $contactService,
                                UnitService $unitService,
                                WarehouseService $warehouseService,
                                MovementService $movementService,
                                SellService $sellService,
                                ShelfService $shelfService,
                                SettingsService $settingsService,
                                HiddenService $hiddenService,
                                )
    {
        $this->middleware('checkLogin');
        $this->stockitemService     = $stockitemService;
        $this->contactService       = $contactService;
        $this->unitService          = $unitService;
        $this->warehouseService     = $warehouseService;
        $this->shelfService         = $shelfService;
        $this->movementService      = $movementService;
        $this->sellService          = $sellService;
        $this->settingsService      = $settingsService;
        $this->hiddenService        = $hiddenService;
    }


    /**
     * Display the index page for movement items.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request){
        $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;
        $warehouses = $this->warehouseService->getAll();
         return view('movement.list')->with([
             'hasSeeHiddenPermission' => $hasSeeHiddenPermission,
             'warehouses' => $warehouses,
         ]);
    }

    public function get(Request $request){
        
        // Retrieve permissions from the middleware
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;
        $hasViewPermission   = $request->hasViewPermission;
        $hasSeeHiddenPermission   = $request->hasSeeHiddenPermission;
        if ($request->ajax()) {
            $filter = [
                'keyword' => $request->get('keyword'),
                'startdate' => $request->get('startdate'),
                'enddate' => $request->get('enddate'),
                'source_warehouse' => $request->get('source_warehouse'),
                'target_warehouse' => $request->get('target_warehouse'),
            ];
            $data = $this->movementService->getList();
            return DataTables::of($data)
            ->addColumn('action', function ($data) use ($hasEditPermission, $hasDeletePermission, $hasViewPermission) {

                $actionHtml = '<div class="d-flex">';

                // Edit Button
                if ($hasEditPermission) {
                    $actionHtml .= '<a href="'.route('movement.edit', ['id' => $data->reference]).'" id="btnedit" class="btn btn-sm btn-success d-flex align-items-center">
                    <span class="material-symbols-rounded">edit</span> '.__('text.edit').'
                </a>&nbsp;';
                }

                // Delete Button
                if ($hasDeletePermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="' . $data->reference . '" class="btn btn-sm btn-danger d-flex align-items-center">
                        <span class="material-symbols-rounded">delete</span> '.__('text.delete').'
                    </a>&nbsp;';
                }

                // View/print Button
                if ($hasViewPermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#detailModel" id="btndetail" data-btndetail="' . $data->reference . '" class="btn btn-sm btn-warning d-flex align-items-center">
                    <span class="material-symbols-rounded">quick_reference</span> '.__('text.detail').'
                </a>';
                }
                $actionHtml .= '</div>';
                return $actionHtml;
                
            })
            ->addColumn('movement_date', function($data){
                    //get setting 
                    $setting = $this->settingsService->getdataById(1);
                    $dateformat = date($setting['datetime'], strtotime($data->movement_date));
                    return $dateformat;
            })
            ->addColumn('creator', function($data) {
                return Auth::user()->name;
            })
            ->addColumn('stockitems', function($data) use ($hasSeeHiddenPermission) {
                $stockitems = $this->movementService->getMovementByReference($data->reference);
                $retvalue = "";
                foreach($stockitems as $stockitem) {
                    if ($hasSeeHiddenPermission) {
                        if ($stockitem->hidden_amount > 0) {
                            $retvalue .= "<span style='font-weight: bold'>" . $stockitem->name . "</span>:" . $stockitem->quantity . $stockitem->unitname .  "(<span class='text-red'>" . $stockitem->hidden_amount . " hidden</span>) , <br/>";
                        } else {
                            $retvalue .= "<span style='font-weight: bold'>" . $stockitem->name . "</span>:" . $stockitem->quantity . $stockitem->unitname . ", <br/>";
                        }
                    } else {
                        if ($stockitem->quantity - $stockitem->hidden_amount > 0) {
                            $retvalue .= "<span style='font-weight: bold'>" . $stockitem->name . "</span>(" . $stockitem->quantity - $stockitem->hidden_amount . $stockitem->unitname . "), <br/>";
                        }
                    }
                }
                // echo $retvalue;
                return $retvalue;
            })
            ->rawColumns(['action', 'stockitems', 'hidden_items'])
            ->filter(function ($query) use ($request) {
                if (!empty($request->get('keyword'))) {
                    $query->where(function($q) use ($request) {
                        $q->where('stockitem.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('movement.reference', 'like', "%{$request->get('keyword')}%");
                    });
				}
                if (!empty($request->get('startdate')) && !empty($request->get('enddate'))) {
                    $setting = $this->settingsService->getdataById(1);

                    $dateObject = DateTime::createFromFormat($setting['datetime'], $request->get('startdate'));
                    $start_date = $dateObject->format('Y-m-d');

                    $dateObject = DateTime::createFromFormat($setting['datetime'], $request->get('enddate'));
                    $end_date = $dateObject->format('Y-m-d');
                    // $startDate = date('Y-m-d', strtotime($request->get('startdate')));
                    // $endDate = date('Y-m-d', strtotime($request->get('enddate')));
					$query->whereBetween('movement.movement_date', [$start_date, $end_date]);
				}
                if (!empty($request->get('source_warehouse'))) {
					$query->where('source_warehouse_id', '=', $request->get('source_warehouse'));
				}
                if (!empty($request->get('target_warehouse'))) {
					$query->where('target_warehouse_id', '=', $request->get('target_warehouse'));
				}
            })
            ->make(true)
            ->getData(true);

            // + ['total_quantity' => $sum_data[0]->total_quantity, 'total_price' => $sum_data[0]->total_price];
        }

        return abort(403, 'Unauthorized access.');
    }

    

    /**
     * Display the index page for movement items.
     *
     * @return \Illuminate\View\View
     */
    public function create(){
        // Get suppliers and all stock items
        $warehouses = $this->warehouseService->getAll();
        $stock      = $this->stockitemService->getAll();
        $units      = $this->unitService->getAll();
        // Generate a reference for the movement
        $ref        = $this->generatereference();
        // Pass data to the view
        return view('movement.create')->with([
            'stock' => $stock,
            'warehouses' => $warehouses,
            'units' => $units,
            'ref' => $ref
        ]);
    }

    /**
     * Display the index page for movement items.
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $request, $id){

        // Get suppliers, all stock items, and the specific check-in transaction
        $warehouses = $this->warehouseService->getAll();
        $contact     = $this->contactService->getsupplier();
        $stock       = $this->stockitemService->getAll();
        $transaction = $this->movementService->getMovementByReference($id);
        // var_dump(count($transaction));
        // die();
        $singletransaction =  $transaction[0];
        $units      = $this->unitService->getAll();
        // Generate a reference for the transaction

        // Pass data to the view
        return view('movement.edit')->with([
            'warehouses' => $warehouses,
            'stock' => $stock,
            'transaction'=>$transaction,
            'singletransaction'=>$singletransaction,
            'contacts' => $contact,
            'units' => $units,
            'hasSeeHiddenPermission' => $request->hasSeeHiddenPermission
        ]);
    }

    /**
     * Show details of a specific transaction.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $data = $this->movementService->getMovementByReference($id);
            if ($data) {
                $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$id, "QRCODE"]);
                $dataURL = "data:image/png;base64," . ($code);

                $setting         = $this->settingsService->getdataById(1);

                // Iterate over each item in the collection
                $movement_date = $data->map(function ($item) use ($setting) {
                    return date($setting['datetime'], strtotime($item->movement_date));
                });
                $created_at = $data->map(function ($items) use ($setting) {
                    $date = Carbon::parse($items->created_at);
                    return $date->format($setting['datetime']);
                });
                // $detail = [];
                // foreach($data as $item) {
                //     $item['converted_quantity'] = $this->settingsService->formatQuantity($item['converted_quantity']);
                //     $detail[] = $item;
                // }                
                return response()->json(['dataURL' => $dataURL, 'data' => $data, 'created_at' => $created_at,  'movement_date' => $movement_date]);
            }
        }else{
            return abort(403, 'Unauthorized access.');
        }
    }
    /**
     * Generate Reference
     *
     * @return object
     */

    public function generatereference() {
        $lastNumber = 0;
        $prefex = "MM";//PZ for checkin and WZ for checkout
        $lastNumber = $this->movementService->getLastRef();  
        $newNumber = $lastNumber + 1;

       
        $res =  $prefex.date('dmy').sprintf('%03d', $newNumber);;
       
        return $res;
    }
    /**
     * Store a new transaction based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {   
       
        $quantity   = $request->input('quantity');
        $unit   = $request->input('unit');
        $price   = $request->input('price');
        $itemid     = $request->input('stockitemid');
        $source_warehouse_id     = $request->input('source_warehouse_id');
        $target_warehouse_id     = $request->input('target_warehouse_id');

        $data = $request->only(['movement_date', 'source_warehouse_id', 'target_warehouse_id', 'reference', 'description']);
        
        DB::beginTransaction();
        try {
            $transaction = $this->movementService->createOrder($data, $itemid, $quantity, $unit, $price);
            if($transaction){
                $transaction = $this->movementService->getMovementByReference($data['reference']);
                $this->movementService->updatestock($transaction, 1);
                if (!$this->stockitemService->checkstock()) {
                    throw new \Exception('Process failed');
                }
                DB::commit();
                return redirect()->route('movement.index')->with('success', __('text.msg_movement_created'));
            }            
            
            throw new \Exception('Process failed'); 
        } catch (\Exception $e) {
            DB::rollBack(); 
            \Log::error($e->getMessage());
            return redirect()->back()->with(['error' => __('text.msg_error_occurred_while_processing')]);
        }
    }
    /**
     * Store a new transaction based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {   
       
        $quantity   = $request->input('quantity');
        $unit   = $request->input('unit');
        $price   = $request->input('price');
        $itemid     = $request->input('stockitemid');
        $reference     = $request->input('reference');
        $source_warehouse_id     = $request->input('source_warehouse_id');
        $target_warehouse_id     = $request->input('target_warehouse_id');

        $data = $request->only(['movement_date', 'source_warehouse_id', 'target_warehouse_id', 'reference', 'description']);
        
        DB::beginTransaction();
        try {
            $transaction = $this->movementService->getMovementByReference($reference);
            $order = $this->movementService->updateOrder($data, $itemid, $quantity, $unit, $reference);
            if($order){
                $this->movementService->updatestock($transaction, -1);
                $transaction = $this->movementService->getMovementByReference($reference);
                $this->movementService->updatestock($transaction, 1);
                if (!$this->stockitemService->checkstock()) {
                    throw new \Exception('Process failed');
                }
                DB::commit();
                return redirect()->route('movement.index')->with('success', __('text.msg_movement_updated'));
            }
            
            throw new \Exception('Process failed'); 
        } catch (\Exception $e) {
            DB::rollBack(); 
            \Log::error($e->getMessage());
            return redirect()->back()->with(['error' => __('text.msg_error_occurred_while_processing')]);
        }
    }


    /**
     * Delete a transaction based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id     = $request->only('deleteid');
        $status = $request->input('status');
        $transaction = $this->movementService->getMovementByReference($id);

        if($transaction){
            $this->movementService->delete($id);
            $this->movementService->updatestock($transaction, -1);
        }

        return redirect()->route('movement.index')->with('success', __('text.msg_movement_deleted'));
        
    }
    /**
     * Check if a transaction code already exists.
     *
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcode($code){
       
        $checkcode = $this->movementService->CheckCode($code);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }


    /**
     * Check if a transaction code already exists, excluding a specific ID.
     *
     * @param  string  $code
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcodeid($code, $id){
       
        $checkcode = $this->movementService->CheckCodeId($code, $id);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }



}
 