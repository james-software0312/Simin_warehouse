<?php

namespace App\Http\Controllers;

use App\Models\SHProductInventoryModel;
use App\Services\StockItemService;
use App\Services\ContactService;
use App\Services\UnitService;
use App\Services\WarehouseService;
use App\Services\ShelfService;
use App\Services\TransactionService;
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
use App\Models\SHMediaUploadModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use DateTime;

class TransactionController extends Controller
{
    protected $stockitemService;
    protected $contactService;
    protected $unitService;
    protected $warehouseService;
    protected $shelfService;
    protected $transactionService;
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
     * @param  TransactionService  $transactionService
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
                                TransactionService $transactionService,
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
        $this->transactionService   = $transactionService;
        $this->sellService          = $sellService;
        $this->settingsService      = $settingsService;
        $this->hiddenService        = $hiddenService;
    }


    /**
     * Display the index page for transaction items.
     *
     * @return \Illuminate\View\View
     */
    public function index(){
        $unit       = $this->unitService->getAll();
        $warehouse  = $this->warehouseService->getAll();
        $shelf      = $this->shelfService->getAll();
        $generate   = $this->generateproductcode();
        return view('transaction.index')->with([
            'units' => $unit,
            'warehouses' => $warehouse,
            'shelfs' => $shelf,
            'generate' => $generate
        ]);
    }


     /**
     * Display the checkin list page for transaction items.
     *
     * @return \Illuminate\View\View
     */
    public function checkinlist(Request $request){
       $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;
       $suppliers = $this->contactService->getsupplier();
       $warehouses = $this->warehouseService->getAll();
        return view('transaction.checkinlist')->with([
            'hasSeeHiddenPermission' => $hasSeeHiddenPermission,
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
        ]);
    }

    /**
     * Display the checkout list page for transaction items.
     *
     * @return \Illuminate\View\View
     */
    public function checkoutlist(Request $request){
        $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;

        return view('transaction.checkoutlist')->with([
            'hasSeeHiddenPermission' => $hasSeeHiddenPermission,
        ]);
    }

    /**
     * Display the check-in form.
     *
     * @return \Illuminate\View\View
     */

    public function checkin(){
        // Get suppliers and all stock items
        $warehouses = $this->warehouseService->getAll();
        $contact    = $this->contactService->getsupplier();
        $stock      = $this->stockitemService->getAll();
        $units      = $this->unitService->getAll();
        // Generate a reference for the transaction
        $ref        = $this->generatereference(0);
        $show_ref        = $this->generatereference1(0);
        // Pass data to the view
        return view('transaction.checkin')->with([
            'stock' => $stock,
            'contacts' => $contact,
            'warehouses' => $warehouses,
            'units' => $units,
            'ref' => $ref,
            'show_ref' => $show_ref,
        ]);
    }


    /**
     * Display the check-out form.
     *
     * @return \Illuminate\View\View
     */
    public function checkout(){

        // Get customers and all stock items
        $warehouses = $this->warehouseService->getAll();
        $contact    = $this->contactService->getcustomer();
        $stock      = $this->stockitemService->getAll();
        $units      = $this->unitService->getAll();
        // Generate a reference for the transaction
        $ref        = $this->generatereference();
        $show_ref        = $this->sellService->getNewShowReference('bank_transfer', date('Y-m-d'));

        // Pass data to the view
        return view('transaction.checkout')->with([
            'stock' => $stock,
            'contacts' => $contact,
            'warehouses' => $warehouses,
            'units' => $units,
            'ref' => $ref,
            'show_ref' => $show_ref
        ]);
    }


    /**
     * Display the form for editing a check-in transaction.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function checkinedit(Request $request, $id){

        // Get suppliers, all stock items, and the specific check-in transaction
        $warehouses = $this->warehouseService->getAll();
        $contact     = $this->contactService->getsupplier();
        $stock       = $this->stockitemService->getAll();
        $transaction = $this->transactionService->getByRef($id,1);
        // var_dump(count($transaction));
        // die();
        $singletransaction =  $this->transactionService->getByRef($id,1)->first();
        $units      = $this->unitService->getAll();
        // Generate a reference for the transaction

        // Pass data to the view
        return view('transaction.checkinedit')->with([
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
     * Display the form for editing a check-out transaction.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function checkoutedit($id){

        // Get customers, all stock items, and the specific check-out transaction
        $warehouses = $this->warehouseService->getAll();
        $contact     = $this->contactService->getcustomer();
        $stock       = $this->stockitemService->getAll();
        $units       = $this->unitService->getAll();
        $transaction = $this->sellService->getStockItemsByReference($id);
        $singletransaction =  $this->sellService->getSellOrder($id);

        // Generate a reference for the transaction

        // Pass data to the view
        return view('transaction.checkoutedit')->with([
            'warehouses' => $warehouses,
            'stock' => $stock,
            'transaction'=>$transaction,
            'singletransaction'=>$singletransaction,
            'contacts' => $contact,
            'units' => $units
        ]);
    }

    /**
     * Generate Product Code
     *
     * @return object
     */

     public function generateproductcode() {
        $lastid = $this->stockitemService->getBarcodeId();
        if ( $lastid ) {
            $res =  'STK'.date('ymd').$lastid->id;
        } else{
            $res =  'STK'.date('ymd').'1';
        }
        return $res;
    }

    /**
     * get print label page
     * @return object
     */
    public function print($id){
        $data = $this->stockitemService->getById($id);
        if ($data === null) {
            return redirect()->route('stock.index');
        }else{
            $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$data->code, "QRCODE"]);
            $dataURL = "data:image/png;base64," . ($code);
            return view('stock.generate', ['data'=> $data, 'dataurl'=>$dataURL]);
        }
    }


     /**
     * Search data for based on the query
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){
        $term = $request->get('query');
        $warehouseid = $request->get('warehouseid');
        $data = $this->stockitemService->SearchItem($term, $warehouseid);
        return response()->json($data);
    }


    /**
     * Get data checkin for transaction, used in DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getcheckin(Request $request)
    {
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
                'supplier' => $request->get('supplier'),
                'warehouse' => $request->get('warehouse'),
            ];
            $data = $this->transactionService->getcheckin();
            $sum_data = $this->transactionService->getcheckinsum($filter);
            return DataTables::of($data)
            ->addColumn('action', function ($data) use ($hasEditPermission, $hasDeletePermission, $hasViewPermission) {

                $actionHtml = '<div class="d-flex">';

                // Edit Button
                if ($hasEditPermission && !$data->confirmed) {
                    $actionHtml .= '<a href="'.route('transaction.checkinedit', ['id' => $data->reference]).'" id="btnedit" class="btn btn-sm btn-success d-flex align-items-center">
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
                    // $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#detailModel" id="btndetail" data-btndetail="' . $data->reference . '" class="btn btn-sm btn-warning d-flex align-items-center">
                //     <span class="material-symbols-rounded">quick_reference</span> '.__('text.detail').'
                // </a>';
                    $actionHtml .= '<a href="'.route('transaction.checkinedit', ['id' => $data->reference]).'" id="btnedit" class="btn btn-sm btn-success d-flex align-items-center">
                        <span class="material-symbols-rounded">quick_reference</span> '.__('text.detail').'
                    </a>&nbsp;';
                }
                $actionHtml .= '</div>';
                return $actionHtml;

            })
            ->addColumn('transactiondate', function($data){
                // dd($data);
                    //get setting
                    $setting = $this->settingsService->getdataById(1);
                    $dateformat = date($setting['datetime'], strtotime($data->transactiondate));
                    return $dateformat;
            })
            ->addColumn('total_quantity', function ($data) {
                return $data->total_quantity . " " . $data->unit_name;
            })
            ->addColumn('total_price', function ($data) {
                return $data->total_price . __('text.PLN');
            })
            ->addColumn('creator', function($data) {
                $user = User::where('id', $data->creator)->first();
                return $user->name;
            })
            ->addColumn('stockitems', function($data) use ($hasSeeHiddenPermission) {
                $stockitems = $this->transactionService->getStockItemsByReference($data->reference);
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
            ->addColumn('hidden_items', function($data) use ($hasSeeHiddenPermission) {
                $stockitems = $this->transactionService->getStockItemsByReference($data->reference);
                $retvalue = "";
                foreach($stockitems as $stockitem) {
                    if ($hasSeeHiddenPermission) {
                        $retvalue .= "<span style='font-weight: bold'>" . $stockitem->name . "</span>(" . $stockitem->hidden_amount . $stockitem->unitname . "), <br/>";
                    }
                }
                // echo $retvalue;
                return $retvalue;
            })
            ->rawColumns(['action', 'stockitems', 'hidden_items'])
            ->filter(function ($query) use ($request) {
                if (!empty($request->get('keyword'))) {
                    $query->where(function($q) use ($request) {
                        $q->where('contact.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('transaction_order.show_reference', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('transaction.reference', 'like', "%{$request->get('keyword')}%");
                    });
				}
                if (!empty($request->get('startdate')) && !empty($request->get('enddate'))) {
                    $setting = $this->settingsService->getdataById(1);

                    $dateObject = DateTime::createFromFormat($setting['datetime'], $request->get('startdate'));
                    $start_date = $dateObject->format('Y-m-d');

                    $dateObject = DateTime::createFromFormat($setting['datetime'], $request->get('enddate'));
                    $end_date = $dateObject->format('Y-m-d');
					$query->whereBetween('transaction.transactiondate', [$start_date, $end_date]);
				}
                if (!empty($request->get('supplier'))) {
					$query->where('transaction.contactid', '=', $request->get('supplier'));
				}
                if (!empty($request->get('warehouse'))) {
					$query->where('stockitem.warehouseid', '=', $request->get('warehouse'));
				}
            })
            ->make(true)
            ->getData(true) // This will get the underlying data array
            + ['total_quantity' => $sum_data[0]->total_quantity, 'total_price' => $sum_data[0]->total_price];
        }

        return abort(403, 'Unauthorized access.');
    }

    public function getcheckinforhide(Request $request)
    {
        if ($request->ajax()) {
            // $data = $this->transactionService->getcheckin();
            $data = $this->transactionService->getcheckinItemsForHide($request->input('stockitemid'));
            // dd($data);

            return DataTables::of($data)
            ->addColumn('action', function($data) {
                $addHTML = '<button class="btn btn-sm btn-green d-flex align-items-center select-hidden" data-transactionid="' . $data->id . '" data-stockitemid="' . $data->stockitem_id . '">
                        <span class="material-symbols-rounded">check</span> ' . __('text.select') . '
                    </button>';
                return $addHTML;
            })
            ->addColumn('quantity', function($data) {
                return $data->quantity . " " . $data->transactionunitname;
            })
            ->addColumn('transactiondate', function($data) {
                    //get setting
                    $setting = $this->settingsService->getdataById(1);
                    $dateformat = date($setting['datetime'], strtotime($data->transactiondate));
                    return $dateformat;
            })
            ->rawColumns(['action'])
            ->toJson();
        }

        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get data checkout for transaction, used in DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getcheckout(Request $request)
    {
        // Retrieve permissions from the middleware
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;
        $hasViewPermission   = $request->hasViewPermission;
        $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;
        if ($request->ajax()) {
            if ($hasSeeHiddenPermission) {
                $data = $this->sellService->getcheckout();
            } else {
                $data = $this->hiddenService->getcheckout();
            }

            return DataTables::of($data)

            ->addColumn('action', function ($data) use ($hasEditPermission, $hasDeletePermission, $hasViewPermission, $hasSeeHiddenPermission) {
                // $data = $data->first();
                $actionHtml = '
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle text-right px-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%">
                            '.__('text.action').'
                        </button>
                        <div class="dropdown-menu" style="right: 0" aria-labelledby="dropdownMenuButton">';
                if ($hasEditPermission && !$data->confirmed && !$data->withinvoice && $data->reference) {
                    $actionHtml .= '<a href="' . route("transaction.checkoutedit", ['id' => $data->reference]) . '" class="dropdown-item d-flex align-items-center"><span class="material-symbols-rounded">edit</span> ' . __('text.edit') . '</a>';
                }

                if ($hasDeletePermission && !$data->withinvoice && !$data->confirmed) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="' . $data->reference . '" class="dropdown-item d-flex align-items-center" href="#"><span class="material-symbols-rounded">delete</span> ' . __('text.delete') . '</a>';
                }

                if ($hasViewPermission) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#detailModel" id="btndetail" data-btndetail="' . $data->reference . '" class="dropdown-item d-flex align-items-center" href="#"><span class="material-symbols-rounded">quick_reference</span> ' . __('text.detail') . '</a>';
                }

                if ($hasSeeHiddenPermission && !$data->withinvoice && $data->confirmed && !$data->hidden) {
                    $actionHtml .= '<a href="' . route("transaction.checkouthide", ['id' => $data->reference]) . '" class="dropdown-item d-flex align-items-center"><span class="material-symbols-rounded">visibility_off</span> ' . __('text.hide') . '</a>';
                }

                if ($data->hidden) {
                    $actionHtml .= '<a href="' . route("transaction.hiddehistory", ['id' => $data->reference]) . '" class="dropdown-item d-flex align-items-center"><span class="material-symbols-rounded">visibility</span> ' . __('text.hidden_history') . '</a>';
                }

                $actionHtml .= '</div></div>';
                return $actionHtml;

            })
            ->addColumn('transactiondate', function($data){
                //get setting
                $setting = $this->settingsService->getdataById(1);
                $dateformat = date($setting['datetime'], strtotime($data->transactiondate));
                return $dateformat;
            })
            ->addColumn('stockitems', function($data) {
                $stockitems = $this->sellService->getStockItemsByReference($data->reference);
                $retvalue = "";
                foreach($stockitems as $stockitem) {
                    $retvalue .= $stockitem->name . "(" . $stockitem->quantity . $stockitem->sellorderunitname . "),<br />";
                }
                // echo $retvalue;
                return $retvalue;
            })
            ->rawColumns(['action', 'stockitems'])
            ->filter(function ($query) use ($request) {
                if (!empty($request->get('keyword'))) {
                    $query->where(function($q) use ($request) {
                        $q->where('contact.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('sell_order.show_reference', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('sell_order_detail.reference', 'like', "%{$request->get('keyword')}%");
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
					$query->whereBetween('sell_order.selldate', [$start_date, $end_date]);
				}
            })
            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get all transaction data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData(){
        $data = $this->stockitemService->getAll();
        return response()->json(['data' => $data]);
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
            $data = $this->transactionService->getByRef($id, $status);
            if ($data) {
                $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$id, "QRCODE"]);
                $dataURL = "data:image/png;base64," . ($code);

                $setting         = $this->settingsService->getdataById(1);

                // Iterate over each item in the collection
                $transactiondate = $data->map(function ($item) use ($setting) {
                    return date($setting['datetime'], strtotime($item->transactiondate));
                });
                $created_at = $data->map(function ($items) use ($setting) {
                    $date = Carbon::parse($items->created_at);
                    return $date->format($setting['datetime']);
                });
                $detail = [];
                foreach($data as $item) {
                    $item['converted_quantity'] = $this->settingsService->formatQuantity($item['converted_quantity']);
                    $detail[] = $item;
                }
                return response()->json(['dataURL' => $dataURL, 'data' => $detail, 'created_at' => $created_at,  'transactiondate' => $transactiondate]);
            }
        }else{
            return abort(403, 'Unauthorized access.');
        }
    }

    public function sellshow(Request $request, $id)
    {
        if ($request->ajax()) {
            $status = $request->input('status');
            $sellOrder = $this->sellService->getOrderByRef($id);
            $data = $this->sellService->getByRef($id);
            if ($data) {
                $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$id, "QRCODE"]);
                $dataURL = "data:image/png;base64," . ($code);

                $setting         = $this->settingsService->getdataById(1);

                // Iterate over each item in the collection
                $transactiondate = $data->map(function ($item) use ($setting) {
                    return date($setting['datetime'], strtotime($item->transactiondate));
                });
                $created_at = $data->map(function ($items) use ($setting) {
                    $date = Carbon::parse($items->created_at);
                    return $date->format($setting['datetime']);
                });
                $detail = [];
                foreach($data as $item) {
                    $item['converted_quantity'] = $this->settingsService->formatQuantity($item['converted_quantity']);
                    $detail[] = $item;
                }
                return response()->json(['dataURL' => $dataURL, 'data' => $detail, 'created_at' => $created_at,  'transactiondate' => $transactiondate, 'sellOrder' => $sellOrder]);
            }
        }else{
            return abort(403, 'Unauthorized access.');
        }
    }


    /**
     * Store a new transaction based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storecheckin(Request $request)
    {
        $quantity   = $request->input('quantity');
        $unit   = $request->input('unit');
        // dd($unit);
        $price   = $request->input('price');
        $itemid     = $request->input('stockitemid');
        $warehouseid     = $request->input('warehouse');
        $data = $request->only(['transactiondate', 'contactid','reference', 'show_reference','description']);
        $confirmed = $request->boolean('confirmed');

        DB::beginTransaction();
        try {
            $group = $this->transactionService->createcheckin($data, $warehouseid, $quantity, $price, $itemid, $unit, $confirmed);
            if($group){
                if ($confirmed) {
                    $transaction = $this->transactionService->getByRef($data['reference'], 1);
                    $this->stockitemService->updatestock($transaction, 1, 1);
                    if (!$this->stockitemService->checkstock()) {
                        throw new \Exception('Process failed');
                    }
                }
                DB::commit();
                return redirect()->route('transaction.checkinlist')->with('success', __('text.msg_checkin_created'));
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
    public function storecheckout(Request $request)
    {
        // $count = count($request);
        $unit   = $request->input('unit');
        $price   = $request->input('price');
        $discount   = $request->input('discount');
        // dd($request->input('unitconverter'));
        $itemid     = $request->input('stockitemid');
        $warehouseid     = $request->input('warehouse');
        $quantity   = $request->input('quantity');
        $unitconverter = $request->input('unitconverter');

        // $id = StockItemModel::where('id', $request)
        // dd($quantity);
        $data = $request->only(['transactiondate', 'contactid','reference','description', 'discount_type', 'total_discount', 'payment_type', 'show_reference']);

        $withInvoice = $request->boolean('with_invoice');
        $confirmed = $request->boolean('confirmed');
        $pre_order = $request->boolean('pre_order');
        DB::beginTransaction();
        try {
            $transaction = $this->sellService->createcheckout($data, $warehouseid, $withInvoice, $confirmed, $quantity, $unit, $price, $discount, $itemid, $pre_order, $unitconverter);
            $transaction = $this->sellService->getStockItemsByReference($data['reference']);
            // $transaction['quantity'] /= $transaction['unitconverter'];
            // foreach($transaction as $tran){
            //     $tran['quantity'] /=$tran['unitconverter'];

            // }
            // dd($transaction);
            $this->stockitemService->updatestock($transaction, -1, 1);
            if (!$this->stockitemService->checkstock()) {
                throw new \Exception('Process failed');
            }
            DB::commit();
            return redirect()->route('transaction.checkoutlist')->with('success', __('text.msg_checkout_created'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return redirect()->back()->with(['error' => __('text.msg_error_occurred_while_processing')]);
        }
    }


     /**
     * Update an existing transaction based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatecheckin(Request $request)
    {

        $validatedData = $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $quantity   = $request->input('quantity');
        $price   = $request->input('price');
        $unit   = $request->input('unit');
        $warehouseid     = $request->input('warehouse');

        $itemid     = $request->input('stockitemid');
        $reference  = $request->input('reference');

        $transaction = $this->transactionService->getByRef($reference, 1);
        $transactionOrder = $this->transactionService->getTransactionOrder($reference);
        if ($transactionOrder->confirmed) {
            return redirect()->back()->with(['error' => __('text.purchase_already_confirmed')]);
        }

        $data = $request->only(['transactiondate', 'contactid','description', 'show_reference']);
        $confirmed = $request->boolean('confirmed');

        // get updated values of new transactions with old transaction.
        DB::beginTransaction();
        try {
            $group = $this->transactionService->updatecheckin($data, $warehouseid, $quantity, $price, $itemid, $reference, $unit, $confirmed);
            if($group){
                // $this->stockitemService->updatestock($transaction, 1, -1);
                if ($confirmed) {
                    $transaction = $this->transactionService->getByRef($reference, 1);
                    $this->stockitemService->updatestock($transaction, 1, 1);
                    if (!$this->stockitemService->checkstock()) {
                        throw new \Exception('Process failed');
                    }
                }
            }
            DB::commit();
            return redirect()->route('transaction.checkinlist')->with('success', __('text.msg_checkin_updated'));
            throw new \Exception('Process failed');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return redirect()->back()->with(['error' => __('text.msg_error_occurred_while_processing')]);
        }

    }


    /**
     * Update an existing transaction based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatecheckout(Request $request)
    {

        $validatedData = $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $quantity   = $request->input('quantity');
        $itemid     = $request->input('stockitemid');
        $unit   = $request->input('unit');
        $price   = $request->input('price');
        $discount   = $request->input('discount');
        $reference  = $request->input('reference');
        $warehouseid     = $request->input('warehouse');

        $data = $request->only(['transactiondate', 'contactid','description', 'discount_type', 'total_discount', 'payment_type', 'show_reference', 'id']);

        $withInvoice = $request->boolean('with_invoice');
        $confirmed = $request->boolean('confirmed');
        $pre_order = $request->boolean('pre_order');
        $sellOrder = $this->sellService->getOrderById($data['id']);

        if ($sellOrder->confirmed) {
            return redirect()->route('transaction.checkoutlist')->with('error', __('text.already_confirmed'));
        } else {

            DB::beginTransaction();
            try {
                $transaction = $this->sellService->getStockItemsByReference($reference);
                $group = $this->sellService->updatecheckout($data, $warehouseid, $withInvoice, $confirmed, $quantity, $unit, $price, $discount, $itemid, $reference, $pre_order);
                if($group) {
                    $this->stockitemService->updatestock($transaction, -1, -1);
                    $transaction = $this->sellService->getStockItemsByReference($reference);
                    $this->stockitemService->updatestock($transaction, -1, 1);
                    if (!$this->stockitemService->checkstock()) {
                        throw new \Exception('Process failed');
                    }
                    DB::commit();
                }
                return redirect()->route('transaction.checkoutlist')->with('success', __('text.update_checkout_success'));

                throw new \Exception('Process failed');
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error($e->getMessage());
                return redirect()->back()->with(['error' => __('text.msg_error_occurred_while_processing')]);
            }
        }

    }


    /**
     * Check if a transaction code already exists.
     *
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcode($code){

        $checkcode = $this->transactionService->CheckCode($code);
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

        $checkcode = $this->transactionService->CheckCodeId($code, $id);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }



    /**
     * Update an existing transaction based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        $id   = $request->only(['editid']);
        $image = $request->file('photo');
        $data = $request->only(['code', 'categoryid', 'unitid','warehouseid','shelfid','name','quantity','description']);
        $group = $this->stockitemService->update($id, $data, $image);

        return redirect()->route('stock.index')->with('success', __('text.msg_stock_updated'));
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
        // dd($id);
        $status = $request->input('status');
        if ($status == 1) {
            $transaction = $this->transactionService->getByRef($id, $status);
        } else if($status == 2) {
            $transaction = $this->sellService->getStockItemsByReference($id);
        }

        if ($status == 1) {
            $delete = $this->transactionService->delete($id);
        } else if ($status == 2) {
            $delete = $this->sellService->delete($id);
        }
        if($status==1){
            $stockupdate = $this->stockitemService->updatestock($transaction, 1, -1);
                return redirect()->route('transaction.checkinlist')->with('success', __('text.msg_checkin_deleted'));
        }
        if($status==2){
            $stockupdate = $this->stockitemService->updatestock($transaction, -1, -1);
                return redirect()->route('transaction.checkoutlist')->with('success', __('text.msg_checkout_deleted'));
        }
    }

    /**
     * Generate Reference
     *
     * @return object
     */

     public function generatereference1($type) {
        $lastNumber = 0;
        $prefex = "";//PZ for checkin and WZ for checkout
        if($type == 0){
            $lastNumber = $this->transactionService->getLastRef();
            $prefex = "PZ";
        }else{
            $lastNumber = $this->sellService->getLastRef();
            $prefex = "WZ";
        }
        $newNumber = $lastNumber + 1;

        $res =  $prefex.'/'.date('d/m/y').'/'.sprintf('%04d', $newNumber);

        return $res;
    }

    public function generatereference() {
        $randomString = Str::random(3);

        $res =  'REF'.date('ymd').$randomString;

        return $res;
    }

    public function changesellstatus(Request $request)
    {
        $reference = $request->input('reference');
        $value = $request->input('value');
        $type = $request->input('type');
        $transaction = $this->sellService->getSellOrder($reference);
        if ($type == "withinvoice") {
            $transaction->withinvoice = $value == 'true' ? 1 : 0;
            $transaction->save();
        }

        if ($type == "confirmed") {
            $transaction->confirmed = $value == 'true' ? 1 : 0;
            $transaction->save();
        }
        if ($type == "pre_order") {
            $transaction->pre_order = $value == 'true' ? 1 : 0;
            $transaction->save();
            $this->sellService->resetShowReferenceNum($transaction->payment_type, $transaction->selldate);
            $this->sellService->resetShowReferenceNum('pre_order', $transaction->selldate);
        }
        return response()->json(["success" => true]);
    }

    public function changepurchasestatus(Request $request)
    {
        $reference = $request->input('reference');
        $value = $request->input('value');
        $type = $request->input('type');
        $transaction = $this->transactionService->getTransactionOrder($reference);

        if ($type == "confirmed") {
            $transactionitems = $this->transactionService->getByRef($reference, 1);
            $this->stockitemService->updatestock($transactionitems, 1, 1);
            $transaction->confirmed = $value == 'true' ? 1 : 0;
            $transaction->save();
            if (!$this->stockitemService->checkstock()) {
                throw new \Exception('Process failed');
            }

        }
        return response()->json(["success" => true]);
    }

    public function checkouthide($id)
    {
        // check if it is possible for hide.
        $sellitems = $this->sellService->getStockItemsByReferenceForHide($id);
        $is_available = true;
        for($i = 0; $i < count($sellitems); $i++) {
            if ($sellitems[$i]->sellunitname == 'karton') {
                continue;
            }
            $convertedqty = $sellitems[$i]->stockunit == $sellitems[$i]->unitid ? $sellitems[$i]->quantity * ($sellitems[$i]->unitconverter1 / $sellitems[$i]->unitconverter) : $sellitems[$i]->quantity * ($sellitems[$i]->unitconverter / $sellitems[$i]->unitconverter1);
            if (fmod($convertedqty, 1) != 0) {
                // It has a decimal part
                $is_available = false;
            }
            $sellitems[$i]->quantity = $convertedqty;
            $sellitems[$i]->sellunitname = 'karton';
        }
        if (!$is_available) {
            return redirect()->route('transaction.checkoutlist')->with('error', __('text.not_available_hide'));
        }
        $sellOrder = $this->sellService->getOrderByRef($id);

        return view('transaction.checkouthide')->with([
            "sellitems" => $sellitems,
            "sellReference" => $id,
            "sellOrder" => $sellOrder
        ]);
    }

    public function savecheckouthidden(Request $request)
    {
        $select_purchases = $request->input('selectedPurchases');
        $sell_reference = $request->input('sellReference');

        DB::beginTransaction();
        try {

            foreach($select_purchases as $data) {
                $this->transactionService->hidetransaction($data);
            }
            $this->hiddenService->hideSell($sell_reference, $select_purchases);
            if (!$this->stockitemService->checkstock()) {
                throw new \Exception('Process failed');
            }
            DB::commit();
            return response()->json(["success" => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return response()->json(["success" => false]);
        }


    }

    public function checksellquantity(Request $request)
    {
        $stockitemid = $request->input('stockitemid');
        $quantity = $request->input('quantity');
        $unit = $request->input('unit');
        // get current qty
        $currentQty = $this->stockitemService->getitemcurrentQty($stockitemid);
        // $quantity = $unit == $currentQty['unitid'] ? $quantity : $quantity * $currentQty['unitconverter'];
        if ($quantity > $currentQty['quantity']*$currentQty['unitconverter']) {
            return response()->json(["avaiable" => false]);
        } else {
            return response()->json(["avaiable" => true]);
        }
    }

    public function hiddehistory($id) {
        $history = $this->hiddenService->getHiddenHistory($id);
        $sell_detail = $this->sellService->getStockItemsByReferenceForHide($id);
        $sell =  $this->sellService->getSellOrder($id);
        return view('transaction.checkouthiddenhistory')->with([
            'history' => $history,
            'sell_detail' => $sell_detail,
            'sell' => $sell,
        ]);
    }

    public function sellexport(Request $request)
    {
        $keyword = $request->input('keyword');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $filter = [
            'keyword' => $keyword,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        $data = $this->sellService->getSellDetail($filter);

        $excel_data = [];
        $reference = "";
        $setting = $this->settingsService->getdataById(1);
        foreach($data as $index => $item) {
            if (!$item->stockitemid) {
                continue;
            }
            if (!array_key_exists($item->reference, $excel_data)) {

                $excel_data[$item->reference] = [
                    "contact_name" => $item->contact_name,
                    "contact_email" => $item->contact_email,
                    "reference" => $item->reference,
                    "order_date" => date($setting['datetime'], strtotime($item->selldate)),
                    "total_discount" => $item->total_discount,
                    "discount_type" => $item->discount_type,
                    "stock_items" => []
                ];
            }
            $stock_item_data = [
                "item_name" => $item->name,
                "item_code" => $item->code,
                "item_size" => $item->size,
                "item_category" => $item->category_name,
                "item_subtype" => $item->itemsubtype,
                "item_vat" => $item->vat,
                "item_unitconverter" => $item->unitconverter,
                "base_unit_name" => $item->unitid == $item->stock_unitid ? $item->base_unit_name : $item->converted_unit_name,
                "converted_unit_name" => $item->unitid != $item->stock_unitid ? $item->base_unit_name : $item->converted_unit_name,
                "item_base_quantity" => $item->quantity,
                "item_converted_quantity" => $item->unitid == $item->stock_unitid
                                ?
                                $this->settingsService->formatQuantity($item->quantity * $item->unitconverter1 / $item->unitconverter)
                                :
                                $this->settingsService->formatQuantity($item->quantity * $item->unitconverter / $item->unitconverter1),
                "sale_price" => $item->price + $item->discount,
                "discount" => $item->discount
            ];
            $excel_data[$item->reference]["stock_items"][] = $stock_item_data;
        }
        // var_dump($excel_data);
        // die();
        $file_name = 'sale_' . time() . '.xlsx';
        return Excel::download(new SellExport($excel_data), $file_name);
    }

    public function checkinexport(Request $request)
    {
        $keyword = $request->input('keyword');
        $startdate = $request->input('startdate');
        $enddate = $request->input('enddate');
        $filter = [
            'keyword' => $keyword,
            'startdate' => $startdate,
            'enddate' => $enddate,
        ];
        $data = $this->transactionService->getCheckinDetail($filter);

        $excel_data = [];
        $reference = "";
        $setting = $this->settingsService->getdataById(1);

        foreach($data as $index => $item) {
            if (!$item->stockitemid) {
                continue;
            }
            if (!array_key_exists($item->reference, $excel_data)) {

                $excel_data[$item->reference] = [
                    "contact_name" => $item->contact_name,
                    "contact_email" => $item->contact_email,
                    "reference" => $item->reference,
                    "checkin_date" => date($setting['datetime'], strtotime($item->transactiondate)),
                    "stock_items" => []
                ];
            }
            $stock_item_data = [
                "item_name" => $item->name,
                "item_code" => $item->code,
                "item_size" => $item->size,
                "item_category" => $item->category_name,
                "item_subtype" => $item->itemsubtype,
                "item_unitconverter" => $item->unitconverter,
                "base_unit_name" => $item->unitid == $item->stock_unitid ? $item->base_unit_name : $item->converted_unit_name,
                "converted_unit_name" => $item->unitid != $item->stock_unitid ? $item->base_unit_name : $item->converted_unit_name,
                "item_base_quantity" => $item->quantity,
                "item_converted_quantity" => $item->unitid == $item->stock_unitid
                                ?
                                $this->settingsService->formatQuantity($item->quantity * $item->unitconverter1 / $item->unitconverter)
                                :
                                $this->settingsService->formatQuantity($item->quantity * $item->unitconverter / $item->unitconverter1),
                "price" => $item->price,
            ];
            $excel_data[$item->reference]["stock_items"][] = $stock_item_data;
        }
        // var_dump($excel_data);
        // die();
        $file_name = 'purchase_' . time() . '.xlsx';
        return Excel::download(new CheckinExport($excel_data), $file_name);
    }

    public function printSellOrders(Request $request)
    {
        $references = $request->input('selected_orders');
        $filter = [
            'references' => $references
        ];
        $data = $this->sellService->getSellDetail($filter);
        // dd($data);
        $excel_data = [];
        $reference = "";
        $setting = $this->settingsService->getdataById(1);
        foreach($data as $index => $item) {
            if (!array_key_exists($item->reference, $excel_data)) {

                $excel_data[$item->reference] = [
                    "contact_name" => $item->contact_name,
                    "contact_email" => $item->contact_email,
                    "contact_phone" => $item->contact_phone,
                    "contact_address" => $item->contact_address,
                    "reference" => $item->reference,
                    "show_reference" => $item->show_reference,
                    "order_date" => date($setting['datetime'], strtotime($item->selldate)),
                    "created_at" => $item->order_created_at,
                    "total_discount" => $item->total_discount,
                    "discount_type" => $item->discount_type,
                    "creator" => $item->creator,
                    "creator_email" => $item->creator_email,
                    "description" => $item->description,
                    "stock_items" => []
                ];
            }
            $stock_item_data = [
                "item_name" => $item->name,
                "item_code" => $item->code,
                "item_size" => $item->size,
                "item_category" => $item->category_name,
                "item_subtype" => $item->itemsubtype,
                "item_vat" => $item->vat,
                "item_unitconverter" => $item->unitconverter,
                "base_unit_name" => $item->unitid == $item->stock_unitid ? $item->base_unit_name : $item->converted_unit_name,
                "converted_unit_name" => $item->unitid != $item->stock_unitid ? $item->base_unit_name : $item->converted_unit_name,
                "item_base_quantity" => $item->quantity,
                "item_converted_quantity" => $item->unitid == $item->stock_unitid
                                ?
                                $this->settingsService->formatQuantity($item->quantity * $item->unitconverter1 / $item->unitconverter)
                                :
                                $this->settingsService->formatQuantity($item->quantity * $item->unitconverter / $item->unitconverter1),
                "sale_price" => $item->price,
                "discount" => $item->discount,
                "stock_qty" => $item->stock_qty
            ];
            $excel_data[$item->reference]["stock_items"][] = $stock_item_data;
        }
        // dd($excel_data);
        return view('transaction.checkoutprint')->with([
            'data' => $excel_data,
            'setting' => $setting
        ]);
    }


    public function deletehiddenOrders(Request $request)
    {
        $references = $request->input('selected_orders');
        DB::beginTransaction();
        try {
            foreach($references as $index => $reference) {
                $hiddenData = $this->hiddenService->getHiddenHistory($reference);
                if(!$hiddenData || count($hiddenData) == 0){
                    DB::commit();
                    return response()->json(['success'=>false, 'message' => __('text.msg_hidden_checkout_not_exist')]);
                }

                $this->sellService->delete($reference);
                $this->hiddenService->deletehiddenHistory($reference);
            }
            DB::commit();
            return response()->json(['success'=>true, 'message' => __('text.msg_hidden_checkout_deleted')]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error($e->getMessage());
            return response()->json(['success'=>false, 'message' => __('text.msg_error_occurred_while_processing')]);
        }

    }
    public function getsellpricehistory(Request $request)
    {
        $stockitemid = $request->get('stockitemid');
        $data = $this->sellService->getPriceHistory($stockitemid);
        return DataTables::of($data)
            ->addColumn('updated_at', function($data) {
                // $date = date('Y-m-d H:i:s', strtotime($data->updated_at));
                // $setting = $this->settingsService->getdataById(1);
                // $dateformat1 = date('H:i:s', strtotime($date));
                // dd($data->updated_at);
                // $dateformat = date($setting['datetime'] . ' H:i:s', $data->updated_at);
                return  $data->updated_at;
            })
            ->addColumn('reference', function($data) {
                return  $data->reference;
            })
            ->addColumn('price', function($data) {
                // $price = $data->price*$data->unitconverter*$data->quantity;
                $price = $data->price;
                // if ($data->sell_unit_name != 'karton') {
                //     $price = $data->price;
                // } else {
                //     if ($data->unitid == $data->stockunitid) {
                //         $price = $data->price * ($data->unitconverter / $data->unitconverter1);
                //     } else {
                //         $price = $data->price * ($data->unitconverter1 / $data->unitconverter);
                //     }
                // }
                return number_format($data->price, 2)  . __('text.PLN');
            })
            ->toJson();
    }

    public function getpurchasepricehistory(Request $request)
    {
        $stockitemid = $request->get('stockitemid');
        $data = $this->transactionService->getPriceHistory($stockitemid);
        return DataTables::of($data)
            ->addColumn('updated_at', function($data) {
                return  $data->updated_at;
            })
            ->addColumn('reference', function($data) {
                return  $data->reference;
            })
            ->addColumn('price', function($data) {
                $price = 0;
                $price = $data->price;
                // $price = $data->price*$data->unitconverter*$data->quantity;
                // if ($data->sell_unit_name != 'karton') {
                //     $price = $data->price;
                // } else {
                //     if ($data->unitid == $data->stockunitid) {
                //         $price = $data->price * ($data->unitconverter / $data->unitconverter1);
                //     } else {
                //         $price = $data->price * ($data->unitconverter1 / $data->unitconverter);
                //     }
                // }
                return number_format($price, 2)  . __('text.PLN');
            })
            ->toJson();
    }
    /**
    * Search data for based on the query
    *
    * @param  int  $id
    * @return \Illuminate\Http\JsonResponse
    */
    public function scan(Request $request){
       $code = $request->get('code');
       $warehouseid = $request->get('warehouseid');
       $data = $this->stockitemService->getItem($code, $warehouseid);
       $data_code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$code, "QRCODE"]);
       $img_path = SHMediaUploadModel::where('id',$data->photo)->first();
       $dataURL = "data:image/png;base64," . ($data_code);
        if(!$data){
            return response()->json(['success' => false,  'dataURL' => $dataURL, ]);
        }
       if($data->photo ==''){
           $photo =  asset('public/storage/items/item-placeholder.png');
       }else{
        //    $photo =  asset('public/storage/items/'.$data->photo);
           $photo =  env('MEDIA_UPLOADER_URL').$img_path->path;
       }

        return response()->json(['success' => true, 'dataURL' => $dataURL, 'data' => $data, 'photo' => $photo]);
    }


    public function getNewShowRef(Request $request) {
        $date = $request->input('date');
        $payment_type = $request->input('payment_type');
        $new_ref_num = $this->sellService->getNewShowReference($payment_type, $date);
        return response()->json(['new_ref_num' => $new_ref_num]);
    }

    public function getUpdatedShowRef(Request $request) {
        $date = $request->input('date');
        $payment_type = $request->input('payment_type');
        $id = $request->input('id');
        $updated_ref_num = $this->sellService->getUpdatedShowRef($payment_type, $date, $id);
        return response()->json(['updated_ref_num' => $updated_ref_num]);
    }

    public function getNewPurchaseShowRef(Request $request) {
        $date = $request->input('date');
        $new_ref_num = $this->transactionService->getNewShowReference($date);
        return response()->json(['new_ref_num' => $new_ref_num]);
    }

    public function getUpdatedPurchaseShowRef(Request $request) {
        $date = $request->input('date');
        $id = $request->input('id');
        $updated_ref_num = $this->transactionService->getUpdatedShowRef($date, $id);
        return response()->json(['updated_ref_num' => $updated_ref_num]);
    }
}
