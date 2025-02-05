<?php

namespace App\Http\Controllers;

use App\Services\StockItemService;
use App\Services\CategoryService;
use App\Services\UnitService;
use App\Services\SizeService;
use App\Services\ColorService;
use App\Services\VatService;
use App\Services\WarehouseService;
use App\Services\TransactionService;
use App\Services\HiddenService;
use App\Services\SettingsService;
use DataTables;
use Milon\Barcode\DNS2D;
use Milon\Barcode\DNS1D;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class StockItemController extends Controller
{
    protected $stockitemService;
    protected $categoryService;
    protected $unitService;
    protected $sizeService;
    protected $colorService;
    protected $vatService;
    protected $warehouseService;
    protected $transactionService;
    protected $hiddenService;
    protected $settingsService;

    /**
     * Constructor for the controller.
     *
     * @param  StockItemService  $stockitemService
     * @param  CategoryService  $categoryService
     * @param  UnitService  $unitService
     * @param  WarehouseService  $warehouseService
     * @param  TransactionService  $transactionService
     * @param  SettingsService  f$settingsService
     * @return void
     */

    public function __construct(
        StockItemService $stockitemService,
        CategoryService $categoryService,
        UnitService $unitService,
        SizeService $sizeService,
        ColorService $colorService,
        VatService $vatService,
        WarehouseService $warehouseService,
        TransactionService $transactionService,
        SettingsService $settingsService,
        HiddenService $hiddenService
    ) {
        // Apply middleware to check login status
        $this->middleware('checkLogin');

        // Inject services into the controller
        $this->stockitemService = $stockitemService;
        $this->categoryService = $categoryService;
        $this->unitService = $unitService;
        $this->sizeService = $sizeService;
        $this->colorService = $colorService;
        $this->vatService = $vatService;
        $this->warehouseService = $warehouseService;
        $this->transactionService   = $transactionService;
        $this->settingsService      = $settingsService;
        $this->hiddenService      = $hiddenService;
    }

    /**
     * Display the index page for stock items.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get all categories, units, warehouses, and shelves
        $category   = $this->categoryService->getAll();
        $unit       = $this->unitService->getAll();
        $size       = $this->sizeService->getAll();
        $color       = $this->colorService->getAll();
        $vat       = $this->vatService->getAll();
        $warehouse  = $this->warehouseService->getAll();

        // Generate a product code
        $generate   = $this->generateproductcode();

        // Pass data to the view
        return view('stock.index')->with([
            'categories' => $category,
            'units' => $unit,
            'size' => $size,
            'color' => $color,
            'vat' => $vat,
            'warehouses' => $warehouse,
            'generate' => $generate
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
            $res =  'STK'.date('ymd').$lastid->id+1;
        } else{
            $res =  'STK'.date('ymd').'1';
        }
        return $res;
    }

   /**
     * Display the print label page for a specific stock item.
     *
     * @param  int  $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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

    public function print_multi(Request $request) {
        $ids = explode(",", $request->input('ids'));
        $ret_data = [];
        foreach($ids as $index => $id) {
            $data = $this->stockitemService->getById($id);
            $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$data->code, "QRCODE"]);
            $dataURL = "data:image/png;base64," . ($code);
            $ret_data[$index]['data'] = $data;
            $ret_data[$index]['dataurl'] = $dataURL;
        }
        // var_dump($ret_data[0]);
        // die();
        return view('stock.generatemulti', ['ret_data' => $ret_data]);
        // if ($data === null) {
        //     return redirect()->route('stock.index');
        // }else{
        //     $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$data->code, "QRCODE"]);
        //     $dataURL = "data:image/png;base64," . ($code);
        //     return view('stock.generate', ['data'=> $data, 'dataurl'=>$dataURL]);
        // }
    }

    /**
     * Get data for stock, used in DataTables.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function get(Request $request)
    {
        // Retrieve permissions from the middleware
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;
        $hasViewPermission   = $request->hasViewPermission;

        if ($request->ajax()) {
            $data = $this->stockitemService->getAll();
            return DataTables::of($data)

            ->addColumn('item_photo', function($data) {
                if($data->photo ==''){
                    $photo =  asset('public/storage/items/item-placeholder.png');
                }else{
                    $photo = env('MEDIA_UPLOADER_URL') . $data->image_path;
                }
                return '<div>
                <div class="barcodecode text-center">
                <img src="' . $photo . '"alt="barcode" width="50"  />
                </div>';
            })
            ->addColumn('code', function($data) {
                return '<div>
                <div class="barcodecode text-center">
                <img src="data:image/png;base64,' . call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$data->code, "QRCODE"]). '"alt="barcode" width="70"  />
                <p class="text-center mb-0">'.$data->code.'</p>
                </div>';
            })
            ->addColumn('action', function ($data) use ($hasEditPermission, $hasDeletePermission, $hasViewPermission) {
                $actionHtml = '
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle text-right px-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 100%">
                            '.__('text.action').'
                        </button>
                        <div class="dropdown-menu" style="right: 0" aria-labelledby="dropdownMenuButton">';

                if ($hasEditPermission) {
                    $actionHtml .= '<a href="' . route("stock.edit", ['id' => $data->id]) . '" class="dropdown-item d-flex align-items-center"><span class="material-symbols-rounded">edit</span> ' . __('text.edit') . '</a>';
                }

                if ($hasDeletePermission && $data->single_quantity == 0) {
                    $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="' . $data->id . '" class="dropdown-item d-flex align-items-center" href="#"><span class="material-symbols-rounded">delete</span> ' . __('text.delete') . '</a>';
                }
                if ($hasEditPermission) {
                    $actionHtml .= '<a href="' . route("stock.history", ['id' => $data->id]) . '" class="dropdown-item d-flex align-items-center"><span class="material-symbols-rounded">history</span> ' . __('text.history') . '</a>';
                }

                if ($hasViewPermission) {
                        $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#detailModel" id="btndetail" data-btndetail="' . $data->id . '" class="dropdown-item d-flex align-items-center" href="#">
                            <span class="material-symbols-rounded">quick_reference</span> ' . __('text.detail') . '
                        </a><a href="stock/print/'.$data->id.'" class="dropdown-item d-flex align-items-center">
                            <span class="material-symbols-rounded">print</span> '.__('text.print').'
                        </a>';
                }

                $actionHtml .= '</div></div>';
                return $actionHtml;

            })
            ->addColumn('quantity', function($data) {
                // $quantity = $data->single_quantity;
                // if($data->unitconverter < $data->unitconverter1 ) $quantity = round($data->single_quantity * $data->unitconverter / $data->unitconverter1, 2);
                // $mainQty = '<span class="text-red">' . $this->settingsService->formatQuantity($quantity) . " " . $data->base_unit_name . '</span>';
                $mainQty = '<span class="text-red">' . $this->settingsService->formatQuantity($data->single_quantity) . ' para</span>';
                return $mainQty;
            })
            ->addColumn('convertedQty', function($data) {
                $quantity = $data->single_quantity;
                if($data->unitconverter > $data->unitconverter1 ) {
                    $quantity = round($data->single_quantity * $data->unitconverter1 / $data->unitconverter, 2);
                } else {
                    $quantity = round($data->single_quantity * $data->unitconverter / $data->unitconverter1, 2);
                }
                return '<span class="text-primary">' . $this->settingsService->formatQuantity($quantity) . " " . ' karton</span>';
            })
            ->rawColumns(['item_photo', 'code','action', 'quantity', 'convertedQty'])
            ->filter(function ($query) use ($request) {
                $query->Where('stockitem.is_delete', false);
                if (!empty($request->get('keyword'))) {
                    $query->where(function($q) use ($request) {
                        $q->where('stockitem.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('stockitem.code', 'like', "%{$request->get('keyword')}%");
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
					$query->whereBetween('stockitem.created_at', [$start_date, $end_date]);
				}
                if (!empty($request->get('supplier'))) {
					$query->where('warehouseid', $request->get('supplier'));
                }
                if (!empty($request->get('filterby'))) {
                    if($request->get('filterby') == 'order')
                        $query->orderBy('id');
                    elseif($request->get('filterby') == 'website')
                        $query->orderBy('id');
                    elseif($request->get('filterby') == 'supplier')
                        $query->orderBy('warehouse.name');
                    else
                        $query->orderBy('id');
                }
                if (!empty($request->get('isVisible')) && $request->get('isVisible') == 1) {
                    $query->where('stockitem.quantity', '>', '0');
                }
                if (!empty($request->get('isWithoutPhoto')) && $request->get('isWithoutPhoto') == 1) {
                    $query->whereNull('stockitem.photo');
                }
                if (!empty($request->get('subtype'))) {
                    $query->Where('stockitem.itemsubtype', 'like', "%{$request->get('subtype')}%");
				}

            })
            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }


    /**
     * Get all stock data.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData(){
        $data = $this->stockitemService->getAll();
        return response()->json(['data' => $data]);
    }


    /**
     * Show details of a specific stock.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->stockitemService->getById($id);

        if ($data) {
            $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$data->code, "QRCODE"]);
            $dataURL = "data:image/png;base64," . ($code);
            if($data->photo ==''){
                $photo =  asset('public/storage/items/item-placeholder.png');
            }else{
                $photo =  asset('public/storage/items/'.$data->photo);
            }

            return response()->json(['dataURL' => $dataURL, 'data' => $data, 'photo' => $photo]);
        }
    }


    /**
     * Store a new stock based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        $image = $request->file('photo');

        $data = $request->only(['code', 'categoryid', 'unitid','warehouseid','name','quantity','description', 'size', 'color', 'price', 'vat', 'itemsubtype', 'unitconverter', 'unitconverterto', 'unitconverter1', 'is_visible']);
        $group = $this->stockitemService->create($data, $image);
        if($group['success'] == false) {
            
            return response()->json([
                'success' => false,
                'message' => $group['message'],
            ]); 
        } else return response() -> json([
            'success' => true,
        ]);

            // return redirect()->route('stock.index')->with('success', __('text.msg_stock_created'));
    }


    /**
     * Check if a stock code already exists.
     *
     * @param  string  $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcode($code){

        $checkcode = $this->stockitemService->CheckCode($code);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }

    /**
     * Check if a stock code already exists, excluding a specific ID.
     *
     * @param  string  $code
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function checkcodeid($code, $id){

        $checkcode = $this->stockitemService->CheckCodeId($code, $id);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }


    /**
     * Update an existing stock based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        $id   = $request->only(['editid']);
        $image = $request->file('photo');
        $data = $request->only(['code', 'product_id', 'categoryid', 'unitid','warehouseid','name','quantity','description', 'itemsubtype', 'price', 'vat','size', 'unitconverter', 'unitconverterto', 'unitconverter1', 'is_visible', 'color']);
        $group = $this->stockitemService->update($id, $data, $image);
        return redirect()->route('stock.index');
        // return redirect()->route('stock.edit', ['id' => $id["editid"]])->with('success', __('text.msg_stock_updated'));
    }


     /**
     * Delete a stock based on the provided request data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->only('deleteid');
        $this->stockitemService->delete($id);

        return redirect()->route('stock.index')->with('success', __('text.msg_stock_deleted'));
    }
    public function multidelete(Request $request)
    {

        $selected_ids = $request->input('selected_ids');
        $ids = explode(',', $selected_ids);
        foreach($ids as $id) {
            $this->stockitemService->delete($id);
        }
        return redirect()->route('stock.index')->with('success', __('text.msg_stock_deleted'));
    }

    public function history(Request $request)
    {

        $data = $this->stockitemService->getById($request->id);

        if ($data) {
            if($data->photo ==''){
                $photo =  asset('public/storage/items/item-placeholder.png');
            }else{
                $photo =  asset('public/storage/items/'.$data->photo);
            }
        }
        return view('stock.history')->with(['stockitemid' => $request->id, 'data' => $data, 'photo' => $photo]);
    }

    public function getHistory(Request $request)
    {
        // Retrieve permissions from the middleware
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;
        $hasViewPermission   = $request->hasViewPermission;
        $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;

        if ($request->ajax()) {
            if ($hasSeeHiddenPermission) {
                $data = $this->transactionService->getStockItemHistory($request->stockitemid, $request->input('search')['value']);
            } else {
                $data = $this->hiddenService->getStockItemHistory($request->stockitemid);
            }

            return DataTables::of($data)
            ->addColumn('price', function($data) use($hasSeeHiddenPermission) {
                $ret = "";
                if ($data->type != "sell") {
                    if ($hasSeeHiddenPermission) {
                        $ret = $data->price * $data->quantity . __('text.PLN');
                    } else {
                        $ret = "--";
                    }
                } else {
                    $ret = ($data->price - $data->discount) * $data->quantity . __('text.PLN');
                }
                return $ret;
            })
            ->addColumn('quantity', function($data) use ($hasSeeHiddenPermission) {
                $ret = "";
                $base_unit = $data->unitid == $data->stockitemunitid ? $data->base_unit_name : $data->converted_unit_name;
                if ($base_unit == 'para') {
                    if ($hasSeeHiddenPermission) {
                        $ret = $data->quantity . " " . $base_unit;
                        if ($data->hidden_amount > 0) {
                            $ret .= "<span class='text-red'><br />(hidden: " . $data->hidden_amount . ")</span>";
                        }
                    } else {
                        $ret = $data->quantity - $data->hidden_amount . " " . $base_unit;
                    }
                    return $ret;
                } else {
                    $ret = "";
                    $converted_unit = $data->unitid != $data->stockitemunitid ? $data->base_unit_name : $data->converted_unit_name;
                    $converted_quantity = $this->settingsService->formatQuantity($data->converted_quantity);
                    $converted_hidden_amount = $this->settingsService->formatQuantity($data->converted_hidden_amount);
                    if ($hasSeeHiddenPermission) {
                        $ret = $converted_quantity . " " . $converted_unit;
                        if ($converted_hidden_amount > 0) {
                            $ret .= "<span class='text-red'><br />(hidden: " . $converted_hidden_amount . ")</span>";
                        }
                    } else {
                        $ret = $converted_quantity - $converted_hidden_amount . " " . $converted_unit;
                    }
                    return $ret;
                }
            })
            ->addColumn('converted_quantity', function($data) use ($hasSeeHiddenPermission) {
                $ret = "";
                $base_unit = $data->unitid == $data->stockitemunitid ? $data->base_unit_name : $data->converted_unit_name;
                if ($base_unit != 'para') {
                    if ($hasSeeHiddenPermission) {
                        $ret = $data->quantity . " " . $base_unit;
                        if ($data->hidden_amount > 0) {
                            $ret .= "<span class='text-red'><br />(hidden: " . $data->hidden_amount . ")</span>";
                        }
                    } else {
                        $ret = $data->quantity - $data->hidden_amount . " " . $base_unit;
                    }
                    return $ret;
                } else {
                    $ret = "";
                    $converted_unit = $data->unitid != $data->stockitemunitid ? $data->base_unit_name : $data->converted_unit_name;
                    $converted_quantity = $this->settingsService->formatQuantity($data->converted_quantity);
                    $converted_hidden_amount = $this->settingsService->formatQuantity($data->converted_hidden_amount);
                    if ($hasSeeHiddenPermission) {
                        $ret = $converted_quantity . " " . $converted_unit;
                        if ($converted_hidden_amount > 0) {
                            $ret .= "<span class='text-red'><br />(hidden: " . $converted_hidden_amount . ")</span>";
                        }
                    } else {
                        $ret = $converted_quantity - $converted_hidden_amount . " " . $converted_unit;
                    }
                    return $ret;
                }
            })
            ->filter(function ($query) use ($hasSeeHiddenPermission, $request) {
                // Apply filter only if the user doesn't have permission to see hidden amounts
                if (!$hasSeeHiddenPermission) {
                    $query->whereRaw('wh_transaction.quantity - wh_transaction.hidden_amount > 0');
                }
                // if (!empty($request->get('search'))) {
                //     $query->where(function($q) use ($request) {
                //         $q->where('contactname', 'like', "%{$request->get('search')['value']}%")
                //           ->orWhere('reference', 'like', "%{$request->get('search')['value']}%");
                //     });
				// }
            })
            ->rawColumns(['quantity', 'converted_quantity'])
            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }

    public function add(Request $request)
    {
        $generate   = $this->generateproductcode();
        $category   = $this->categoryService->getAll();
        $unit       = $this->unitService->getAll();
        $size       = $this->sizeService->getAll();
        $vat        = $this->vatService->getAll();
        $warehouse  = $this->warehouseService->getAll();
        $color      = $this->colorService->getAll();
        return view('stock.add')->with([
            "units" => $unit,
            "size" => $size,
            "vat" => $vat,
            "categories" => $category,
            "warehouses" => $warehouse,
            "generate" => $generate,
            "color" => $color,
        ]);
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        $category   = $this->categoryService->getAll();
        $unit       = $this->unitService->getAll();
        $size       = $this->sizeService->getAll();
        $color       = $this->colorService->getAll();
        $vat       = $this->vatService->getAll();
        $warehouse  = $this->warehouseService->getAll();
        $data = $this->stockitemService->getById($id);
        $hasEditQtyPermission = $request->hasEditQtyPermission;

        if ($data) {
            $code = call_user_func_array([new DNS2D(), 'getBarcodePNG'], [$data->code, "QRCODE"]);
            $dataURL = "data:image/png;base64," . ($code);
            if($data->photo ==''){
                $photo =  asset('public/storage/items/item-placeholder.png');
            }else{
                $photo =  env('MEDIA_UPLOADER_URL') . $data->image_path;
            }
        }
        // dd($data->size);

        return view('stock.edit')->with([
            "units" => $unit,
            "size" => $size,
            "vat" => $vat,
            "categories" => $category,
            "warehouses" => $warehouse,
            "data" => $data,
            "dataURL" => $dataURL,
            "photo" => $photo,
            'color' =>$color,
            'hasEditQtyPermission' => $hasEditQtyPermission
        ]);
    }

    public function sellpricehistory($id)
    {
        $data = $this->stockitemService->getById($id);

        if ($data) {
            if($data->photo ==''){
                $photo =  asset('public/storage/items/item-placeholder.png');
            }else{
                $photo =  asset('public/storage/items/'.$data->photo);
            }
        }
        return view('stock.sellpricehistory')->with(['stockitemid' => $id, 'data' => $data, 'photo' => $photo]);
    }

    public function purchasepricehistory($id)
    {
        $data = $this->stockitemService->getById($id);

        if ($data) {
            if($data->photo ==''){
                $photo =  asset('public/storage/items/item-placeholder.png');
            }else{
                $photo =  asset('public/storage/items/'.$data->photo);
            }
        }
        return view('stock.purchasepricehistory')->with(['stockitemid' => $id, 'data' => $data, 'photo' => $photo]);

    }

    public function pricehistory($id)
    {
        $data = $this->stockitemService->getById($id);

        if ($data) {
            if($data->photo ==''){
                $photo =  asset('public/storage/items/item-placeholder.png');
            }else{
                $photo =  asset('public/storage/items/'.$data->photo);
            }
        }
        return view('stock.pricehistory')->with(['stockitemid' => $id, 'data' => $data, 'photo' => $photo]);
    }

    public function getpricehistory(Request $request)
    {
        $stockitemid = $request->input('stockitemid');
        if ($request->ajax()) {
            $data = $this->stockitemService->getpricehistory($stockitemid);
            return DataTables::of($data)
                    ->addColumn('updated_at', function($data) {
                        // $date = date('Y-m-d H:i:s', strtotime($data->updated_at));
                        // $setting = $this->settingsService->getdataById(1);
                        // $dateformat1 = date('H:i:s', strtotime($date));
                        // dd($data->updated_at);
                        // $dateformat = date($setting['datetime'] . ' H:i:s', $data->updated_at);
                        return  $data->updated_at;
                    })
                    ->toJson();
        }
    }
}
