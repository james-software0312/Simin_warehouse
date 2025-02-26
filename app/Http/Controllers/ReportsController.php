<?php

namespace App\Http\Controllers;

use App\Services\StockItemService;
use App\Services\CategoryService;
use App\Services\UnitService;
use App\Services\WarehouseService;
use App\Services\ShelfService;
use App\Services\TransactionService;
use App\Services\UserService;
use App\Services\SettingsService;
use App\Services\ContactService;
use App\Services\SellService;
use App\Services\HiddenService;
use App\Models\SHProductCategoryModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DataTables;
use Milon\Barcode\DNS2D;
use Milon\Barcode\DNS1D;
use DateTime;


class ReportsController extends Controller
{
    protected $stockitemService;
    protected $categoryService;
    protected $unitService;
    protected $warehouseService;
    protected $shelfService;
    protected $transactionService;
    protected $userService;
    protected $settingsService;
    protected $contactService;
    protected $sellService;
    protected $hiddenService;


    // Constructor to inject services and apply middleware
    public function __construct(StockItemService $stockitemService,
                                CategoryService $categoryService,
                                UnitService $unitService,
                                WarehouseService $warehouseService,
                                TransactionService $transactionService,
                                UserService $userService,
                                ContactService $contactService,
                                SettingsService $settingsService,
                                SellService $sellService,
                                HiddenService $hiddenService,
                                ShelfService $shelfService)
    {
        $this->middleware('checkLogin');
        $this->stockitemService     = $stockitemService;
        $this->categoryService      = $categoryService;
        $this->unitService          = $unitService;
        $this->warehouseService     = $warehouseService;
        $this->shelfService         = $shelfService;
        $this->transactionService   = $transactionService;
        $this->userService          = $userService;
        $this->contactService       = $contactService;
        $this->settingsService      = $settingsService;
        $this->sellService          = $sellService;
        $this->hiddenService          = $hiddenService;
    }

    // Render the index view for reports
    public function index(){
        return view('reports.index');
    }


    // Render the home view for overall statistics
    public function home(){

        // getting current year, month, date informations.
        // current date data
        $current_date_data = $this->sellService->getSumPrice(date('d/m/Y'), date('d/m/Y'));
        $current_date = [
            'price' => 0,
            'carton_qty' => 0,
            'pair_qty' => 0,
        ];
        foreach($current_date_data as $data) {
            $current_date['price'] += $data->total_amount + $data->discount;
            $current_date['carton_qty'] += $data->carton_qty;
            $current_date['pair_qty'] += $data->pair_qty;
        }

        $startOfMonth = (new DateTime('first day of this month'))->format('d/m/Y');
        $endOfMonth = (new DateTime('last day of this month'))->format('d/m/Y');
        $current_month_data = $this->sellService->getSumPrice($startOfMonth, $endOfMonth);
        $current_month = [
            'price' => 0,
            'carton_qty' => 0,
            'pair_qty' => 0,
        ];
        foreach($current_month_data as $data) {
            $current_month['price'] += $data->total_amount + $data->discount;
            $current_month['carton_qty'] += $data->carton_qty;
            $current_month['pair_qty'] += $data->pair_qty;
        }

        $startOfYear = (new DateTime('first day of January'))->format('d/m/Y');
        $endOfYear = (new DateTime('last day of December'))->format('d/m/Y');
        $current_year_data = $this->sellService->getSumPrice($startOfYear, $endOfYear);
        $current_year = [
            'price' => 0,
            'carton_qty' => 0,
            'pair_qty' => 0,
        ];
        foreach($current_year_data as $data) {
            $current_year['price'] += $data->total_amount + $data->discount;
            $current_year['carton_qty'] += $data->carton_qty;
            $current_year['pair_qty'] += $data->pair_qty;
        }

        // $totalallitem           = $this->stockitemService->totalallitem();
        $totalcheckins          = $this->transactionService->totalallitem(1);
        // $totalcheckouts         = $this->sellService->totalallitem();
        // $totalusers             = $this->userService->totalitem();
        // $totalcontacts          = $this->contactService->totalitem();
        $totalstockwarehouse    = $this->stockitemService->getstockbywarehouse();
        $top10productquantity   = $this->stockitemService->gettopstockbyquantity();
        $top10productbysale     = $this->sellService->gettopstockbysale();
        $monthlydataCheckin     = $this->transactionService->monthlydata(1);
        $monthlydataCheckout    = $this->sellService->monthlydata();

        // Get all months from January to December
        $allMonths = collect([
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
        ]);

        // Populate the counts for all months, setting count to 0 for months with no data
        $dataTotalCheckin = $allMonths->map(function($month) use ($monthlydataCheckin) {
            return $monthlydataCheckin->has($month) ? $monthlydataCheckin[$month] : 0;
        });

        // Populate the counts for all months, setting count to 0 for months with no data
        $dataTotalCheckout = $allMonths->map(function($month) use ($monthlydataCheckout) {
            return $monthlydataCheckout->has($month) ? $monthlydataCheckout[$month] : 0;
        });


        return view('home.index')->with([
            // 'totalitem' => $totalallitem,
            'totalcheckins' => $totalcheckins,
            // 'totalcheckouts' => $totalcheckouts,
            // 'totalusers' => $totalusers,
            // 'totalcontacts' => $totalcontacts,
            'totalstockwarehouse' => $totalstockwarehouse,
            'dataTotalCheckin' => $dataTotalCheckin->values(),
            'dataTotalCheckout' => $dataTotalCheckout->values(),
            'top10productquantity' => $top10productquantity,
            'top10productbysale' => $top10productbysale,
            'current_date_sum' => $current_date,
            'current_month_sum' => $current_month,
            'current_year_sum' => $current_year,
        ]);

        // return view('home.index');
    }

    // Render the overall view for overall statistics
    public function overall(){
        $totalitem          = $this->stockitemService->totalitem();
        $totalcategories    = $this->categoryService->totalitem();
        $totalwarehouses    = $this->warehouseService->totalitem();
        $totalunits         = $this->unitService->totalitem();
        $totalshelf         = $this->shelfService->totalitem();
        $totalcheckins      = $this->transactionService->totalitemcheckin();
        $totalcheckouts     = $this->transactionService->totalitemcheckout();
        $totalusers         = $this->userService->totalitem();
        $totalcontacts      = $this->contactService->totalitem();

        return view('reports.overall')->with([
            'totalitem' => $totalitem,
            'totalcategories' => $totalcategories,
            'totalwarehouses' => $totalwarehouses,
            'totalshelf' => $totalshelf,
            'totalunits' => $totalunits,
            'totalcheckins' => $totalcheckins,
            'totalcheckouts' => $totalcheckouts,
            'totalusers' => $totalusers,
            'totalcontacts' => $totalcontacts

        ]);
    }

     // Render the stock view for stock-related statistics
    public function stock(){
        $totalitem          = $this->stockitemService->totalitem();
        $totalcategories    = $this->categoryService->getAll();
        $totalwarehouses    = $this->warehouseService->getAll();
        $totalunits         = $this->unitService->getAll();
        $totalshelf         = $this->shelfService->getAll();
        $totalcheckins      = $this->transactionService->totalitemcheckin();
        $totalcheckouts     = $this->transactionService->totalitemcheckout();
        $totalusers         = $this->userService->totalitem();
        $totalcontacts      = $this->contactService->totalitem();
        $totalallitem       = $this->stockitemService->totalallitem();
        $stockmonthlydata   = $this->stockitemService->monthlydata();


        // Get all months from January to December
        $allMonths = collect([
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
        ]);

        // Populate the counts for all months, setting count to 0 for months with no data
        $dataTotal = $allMonths->map(function($month) use ($stockmonthlydata) {
            return $stockmonthlydata->has($month) ? $stockmonthlydata[$month] : 0;
        });


        return view('reports.stock')->with([
            'totalitem' => $totalitem,
            'dataTotal' => $dataTotal->values(),
            'totalcategories' => $totalcategories,
            'totalwarehouses' => $totalwarehouses,
            'totalunits' => $totalunits,
            'totalshelf' => $totalshelf,
            'totalallitem' => $totalallitem
        ]);
    }

     // Render the warehouse view for warehouse statistics
    public function warehouse(){

        $warehouses             = $this->warehouseService->getAll();
        $totalwarehouses        = $this->warehouseService->totalitem();
        $getstockbywarehouse    = $this->stockitemService->getstockbywarehouse();

        return view('reports.warehouse')->with([
            'totalwarehouses' => $totalwarehouses,
            'warehouses' => $warehouses,
            'warehousechart' => $getstockbywarehouse,
        ]);
    }

     // Render the category view for category statistics
    public function category(){

        $categories             = $this->categoryService->getAll();
        $totalcategories        = $this->categoryService->totalitem();
        $getstockbycategory     = $this->stockitemService->getstockbycategory();

        return view('reports.category')->with([
            'totalcategories' => $totalcategories,
            'categories' => $categories,
            'categorychart' => $getstockbycategory,
        ]);
    }

    public function checkin()
    {
        $suppliers = $this->contactService->getsupplier();
        $warehouses = $this->warehouseService->getAll();
        $categories = $this->categoryService->getAll();
        return view('reports.checkin')->with([
            'suppliers' => $suppliers,
            'warehouses' => $warehouses,
            'categories' => $categories,
        ]);
    }


     // Render the checkin view for checkin statistics
    public function checkin1(){
        $totalcheckins      = $this->transactionService->totalitemcheckin();
        $monthlydata        = $this->transactionService->monthlydata(1);
        $totalcategories    = $this->categoryService->getAll();
        $totalwarehouses    = $this->warehouseService->getAll();
        $totalunits         = $this->unitService->getAll();
        $totalsupplier      = $this->contactService->getsupplier();
        $totalshelf         = $this->shelfService->getAll();
        $totalcontacts      = $this->contactService->totalitem();
        $totalallitem       = $this->transactionService->totalallitem(1);
        // Get all months from January to December
        $allMonths = collect([
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
        ]);

        // Populate the counts for all months, setting count to 0 for months with no data
        $dataTotal = $allMonths->map(function($month) use ($monthlydata) {
            return $monthlydata->has($month) ? $monthlydata[$month] : 0;
        });

        return view('reports.checkin')->with([
            'totalallitem' => $totalallitem,
            'dataTotal' => $dataTotal->values(),
            'totalcategories' => $totalcategories,
            'totalsupplier' => $totalsupplier,
            'totalwarehouses' => $totalwarehouses,
            'totalunits' => $totalunits,
            'totalshelf' => $totalshelf,
        ]);
    }

    public function checkout()
    {
        $customers    = $this->contactService->getcustomer();
        $warehouses = $this->warehouseService->getAll();
        $categories = $this->categoryService->getAll();
        $creators = $this->userService->getAllUser();
        return view('reports.checkout')->with([
            'customers' => $customers,
            'warehouses' => $warehouses,
            'categories' => $categories,
            'creators' =>$creators
        ]);
    }

     // Render the checkout view for checkout statistics
    public function checkout1(){
        $totalcheckout      = $this->transactionService->totalitemcheckout();
        $monthlydata        = $this->transactionService->monthlydata(2);
        $totalcategories    = $this->categoryService->getAll();
        $totalwarehouses    = $this->warehouseService->getAll();
        $totalunits         = $this->unitService->getAll();
        $totalcustomer     = $this->contactService->getcustomer();
        $totalshelf         = $this->shelfService->getAll();
        $totalcontacts      = $this->contactService->totalitem();
        $totalallitem       = $this->transactionService->totalallitem(2);

        // Get all months from January to December
        $allMonths = collect([
            '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'
        ]);

        // Populate the counts for all months, setting count to 0 for months with no data
        $dataTotal = $allMonths->map(function($month) use ($monthlydata) {
            return $monthlydata->has($month) ? $monthlydata[$month] : 0;
        });


        return view('reports.checkout')->with([
            'totalallitem' => $totalallitem,
            'dataTotal' => $dataTotal->values(),
            'totalcategories' => $totalcategories,
            'totalcustomer' => $totalcustomer,
            'totalwarehouses' => $totalwarehouses,
            'totalunits' => $totalunits,
            'totalshelf' => $totalshelf,
        ]);
    }


    // Render the report view for report statistics
    public function getstockreport(Request $request)
    {
        if ($request->ajax()) {


            $data = $this->stockitemService->getstockreport();

            return DataTables::of($data)
            ->addColumn('code', function($data) {
                return '<div>

                <p class="mb-0">'.$data->code.'</p>
                </div>';
            })->filter(function ($query) use ($request) {
				if (!empty($request->get('warehouse')) ) {
					$query->where('stockitem.warehouseid', 'like', "%{$request->get('warehouse')}%");
				}
				if (!empty($request->get('category'))) {
					$query->where('stockitem.categoryid', 'like', "%{$request->get('category')}%");
				}
				if (!empty($request->get('unit'))) {
					$query->where('stockitem.unitid', 'like', "%{$request->get('unit')}%");
				}
                if (!empty($request->get('shelf'))) {
					$query->where('stockitem.shelfid', 'like', "%{$request->get('shelf')}%");
				}
				if (!empty($request->get('startdate')) && !empty($request->get('enddate'))) {
                    $startDate = date('Y-m-d', strtotime($request->get('startdate')));
                    $endDate = date('Y-m-d', strtotime($request->get('enddate')));
					$query->whereBetween('stockitem.created_at', [$startDate, $endDate]);
				}

			})

            ->rawColumns(['code'])
            ->addColumn('transactiondate', function($data){
                //get setting
                $setting = $this->settingsService->getdataById(1);
                $dateformat = date($setting['datetime'], strtotime($data->transactiondate));
                return $dateformat;
        })

            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }

    // Render the report view for report statistics
    public function getcheckinreport(Request $request)
    {
        if ($request->ajax()) {
            $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;
            if($hasSeeHiddenPermission){
                $data = $this->transactionService->getcheckinreport();
            } else {
                $data = $this->hiddenService->getcheckinhiddenreport();
            }
            // dd($data);

            return DataTables::of($data)
            ->addColumn('code', function($data) {
                return '<div>

                <p class="mb-0">'.$data->code.'</p>
                </div>';
            })->filter(function ($query) use ($request) {

				if (!empty($request->get('category'))) {
					$query->where('stockitem.categoryid', '=', $request->get('category'));
				}
				if (!empty($request->get('keyword'))) {
                    $query->where(function($q) use ($request) {
                        $q->where('contact.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('stockitem.code', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('stockitem.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('transaction.reference', 'like', "%{$request->get('keyword')}%");
                    });
				}

                if (!empty($request->get('supplier'))) {
					$query->where('transaction.contactid', '=', $request->get('supplier'));
				}

                if (!empty($request->get('warehouse'))) {
					$query->where('transaction.warehouseid', '=', $request->get('warehouse'));
				}

				if (!empty($request->get('startdate')) && !empty($request->get('enddate'))) {
                    $startDate = date('Y-m-d', strtotime($request->get('startdate')));
                    $endDate = date('Y-m-d', strtotime($request->get('enddate')));
					$query->whereBetween('transaction.transactiondate', [$startDate, $endDate]);
				}

			})
            ->addColumn('carton_quantity', function ($data)  {
                $ret = "";
                $qty = 0;
                $carton_unitid = $data->stock_base_unit_name == 'karton' ? $data->stock_unitid : $data->unitconverterto;
                if ($data->unitid == $carton_unitid) {
                    $qty = $data->available_quantity;
                } else {
                    if ($data->stock_base_unit_name == 'karton' && $data->unitconverter1 != 0) {
                        $qty = $data->available_quantity * ($data->unitconverter / $data->unitconverter1);
                    } else if($data->unitconverter != 0){
                        $qty = $data->available_quantity * ($data->unitconverter1 / $data->unitconverter);
                    }
                }
                return $qty;
            })
            ->addColumn('pair_quantity', function ($data)  {
                $ret = "";
                $qty = 0;
                $carton_unitid = $data->stock_base_unit_name == 'para' ? $data->stock_unitid : $data->unitconverterto;
                if ($data->unitid == $carton_unitid) {
                    $qty = $data->available_quantity;
                } else {
                    if ($data->stock_base_unit_name == 'para' && $data->unitconverter1 != 0) {
                        $qty = $data->available_quantity * ($data->unitconverter / $data->unitconverter1);
                    } else if($data->unitconverter != 0){
                        $qty = $data->available_quantity * ($data->unitconverter1 / $data->unitconverter);
                    }
                }
                return $qty;
            })
            ->addColumn('unitconverter', function ($data)  {
                $qty = 0;
                if ($data->stock_base_unit_name == 'karton' && $data->unitconverter != 0) {
                    $qty = $data->unitconverter1 / $data->unitconverter;
                } else if($data->unitconverter1 != 0){
                    $qty = $data->unitconverter / $data->unitconverter1;
                }
                return $qty;
            })
            ->addColumn('category', function ($data) {
                // dd($data->categoryid);
                $category = SHProductCategoryModel::where('id', $data->categoryid)->first();
                return $category->title;
                // return $data->category;
            })
            ->addColumn('unit', function ($data)  {
                $ret = "";
                if ($data->unitid == $data->stock_unitid) {
                    $ret = $data->stock_base_unit_name;
                } else {
                    $ret = $data->stock_converted_unit_name;
                }
                return $ret;
            })
            ->addColumn('price', function ($data) {
                return $data->price . __('text.PLN');
            })
            ->addColumn('total_price', function ($data) {
                return $data->price * $data->quantity*$data->unitconverter . __('text.PLN');
            })
            ->rawColumns(['code', 'carton_qunatity', 'pair_quantity'])
            ->addColumn('transactiondate', function($data){
                //get setting
                $setting = $this->settingsService->getdataById(1);
                $dateformat = date($setting['datetime'], strtotime($data->transactiondate));
                return $dateformat;
            })
            ->addIndexColumn()
            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }
    // Render the report view for report statistics
    public function getcheckoutreport(Request $request)
    {
        if ($request->ajax()) {
            $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;
            // dd($hasSeeHiddenPermission);
            if($hasSeeHiddenPermission){
                $data = $this->sellService->getcheckoutreport();
            } else {
                $data = $this->hiddenService->getcheckouthiddenreport();
            }
            // dd($data);
            // dd($data->get());
            return DataTables::of($data)
            ->addColumn('code', function($data) {
                return '<div>

                <p class="mb-0">'.$data->code.'</p>
                </div>';
            })->filter(function ($query) use ($request) {
                // dd($query);

				if (!empty($request->get('category'))) {
					$query->where('stockitem.categoryid', '=', $request->get('category'));
				}
				if (!empty($request->get('keyword'))) {
                    $query->where(function($q) use ($request) {
                        $q->where('contact.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('stockitem.code', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('stockitem.name', 'like', "%{$request->get('keyword')}%")
                          ->orWhere('transaction.reference', 'like', "%{$request->get('keyword')}%");
                    });
				}

                if (!empty($request->get('customer'))) {
					$query->where('sell_order_detail.contactid', '=', $request->get('customer'));
				}

                if (!empty($request->get('warehouse'))) {
					$query->where('transaction.warehouseid', '=', $request->get('warehouse'));
				}

                if (!empty($request->get('creator'))) {
					$query->where('users.id', '=', $request->get('creator'));
				}

				if (!empty($request->get('startdate')) && !empty($request->get('enddate'))) {
                    $startDate = date('Y-m-d', strtotime($request->get('startdate')));
                    $endDate = date('Y-m-d', strtotime($request->get('enddate')));
					$query->whereBetween('sell_order_detail.selldate', [$startDate, $endDate]);
				}


			})
            ->addColumn('carton_quantity', function ($data)  {
                // if($data->reference != 'REF250226GzY' ) dd($data);
                $ret = "";
                $qty = 0;
                $qty = $data->quantity;
                // $carton_unitid = $data->stock_base_unit_name == 'karton' ? $data->stock_unitid : $data->unitconverterto;
                // if ($data->unitid == $carton_unitid) {
                //     $qty = $data->quantity;
                // } else {
                //     if ($data->stock_base_unit_name == 'karton') {
                //         $qty = $data->quantity * ($data->unitconverter / $data->unitconverter1);
                //     } else {
                //         $qty = $data->quantity * ($data->unitconverter1 / $data->unitconverter);
                //     }
                // }
                return round($qty, 2);
            })
            ->addColumn('creator', function($data) {
                return $data->user;
            })
            ->addColumn('pair_quantity', function ($data)  {
                $ret = "";
                $qty = 0;
                $carton_unitid = $data->stock_base_unit_name == 'para' ? $data->stock_unitid : $data->unitconverterto;
                $qty = $data->quantity*$data->unitconverter;
                // if ($data->unitid == $carton_unitid) {
                //     $qty = $data->quantity;
                // } else {
                //     if ($data->stock_base_unit_name == 'para') {
                //         $qty = $data->quantity * ($data->unitconverter / $data->unitconverter1);
                //     } else {
                //         $qty = $data->quantity * ($data->unitconverter1 / $data->unitconverter);
                //     }
                // }
                return round($qty, 2);
            })
            ->addColumn('unitconverter', function ($data)  {
                $qty = 0;
                if ($data->stock_base_unit_name == 'karton') {
                    $qty = $data->unitconverter1 / $data->unitconverter;
                } else {
                    $qty = $data->unitconverter / $data->unitconverter1;
                }
                return round($qty, 2);
            })
            ->addColumn('unit', function ($data)  {
                $ret = "";
                if ($data->unitid == $data->stock_unitid) {
                    $ret = $data->stock_base_unit_name;
                } else {
                    $ret = $data->stock_converted_unit_name;
                }
                return $ret;
            })
            ->addColumn('price', function ($data) {
                return $data->price . __('text.PLN');
            })
            ->addColumn('category', function ($data) {
                $category = SHProductCategoryModel::where('id', $data->categoryid)->first();
                return $category->title;
                // return $data->category;
            })
            ->addColumn('total_price', function ($data) {
                return $data->price * $data->quantity*$data->unitconverter . __('text.PLN');
            })
            ->rawColumns(['code', 'carton_qunatity', 'pair_quantity'])
            ->addColumn('selldate', function($data){
                //get setting
                $setting = $this->settingsService->getdataById(1);
                $dateformat = date($setting['datetime'], strtotime($data->selldate));
                return $dateformat;
            })
            ->addIndexColumn()
            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }


    // Render the warehouse view for warehouse statistics
    public function getwarehousereport(Request $request)
    {
        if ($request->ajax()) {


            $data = $this->stockitemService->getstockbywarehouse();

            return DataTables::of($data)
            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }

    // Render the category view for category statistics
    public function getcategoryreport(Request $request)
    {
        if ($request->ajax()) {


            $data = $this->stockitemService->getstockbycategory();

            return DataTables::of($data)

            ->make(true);
        }

        return abort(403, 'Unauthorized access.');
    }

    public function getSumData(Request $request)
    {
        $start = $request->input('date_range')[0];
        $end = $request->input('date_range')[1];
        // getting the sum of checkout sales.
        $checkout_price_data = $this->sellService->getSumPrice($start, $end);
        $sum_price = 0;
        $sum_carton_qty = 0;
        $sum_pair_qty = 0;

        foreach($checkout_price_data as $data) {
            $sum_price += $data->total_amount + $data->discount;
            $sum_carton_qty += $data->carton_qty;
            $sum_pair_qty += $data->pair_qty;
        }
        return response()->json(["sum_price" => $sum_price, 'sum_carton_qty' => $sum_carton_qty, 'sum_pair_qty' => $sum_pair_qty]);
    }
}
