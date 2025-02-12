<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\VatController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\StockItemController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\MovementController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('setup')->group(function(){
    Route::get('start', [InstallController::class, 'index'])->name('setup.index');
    Route::get('requirements', [InstallController::class, 'requirements'])->name('setup.requirements');

    Route::get('license', [InstallController::class, 'license'])->name('setup.license');
    Route::group(['middleware' => 'check.pantet'], function () {
        Route::get('database', [InstallController::class, 'database'])->name('setup.database');
    });
    Route::get('account', [InstallController::class, 'account'])->name('setup.account');
    Route::post('database-submit', [InstallController::class, 'databaseSubmit'])->name('setup.database.submit');
    Route::post('license-submit', [InstallController::class, 'licenseSubmit'])->name('setup.license.submit');
    Route::post('account-submit', [InstallController::class, 'accountSubmit'])->name('setup.account.submit');
    Route::get('configuration', [InstallController::class, 'configuration'])->name('setup.configuration');
    Route::post('configuration-submit', [InstallController::class, 'configurationSubmit'])->name('setup.configuration.submit');
    Route::get('complete', [InstallController::class, 'setupComplete'])->name('setup.complete');
});

//storage link
Route::get('/createstoragelink', function () {
    Artisan::call('storage:link');
    return 'Success: Created the storage link. You can <a href="login">login</a> now';

});

Route::prefix('category')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/get', [CategoryController::class, 'getCategories'])->name('category.get');
    Route::get('/GetAllData', [CategoryController::class, 'GetAllData'])->name('category.GetAllData');
    Route::get('/{id}', [CategoryController::class, 'show'])->name('category.show');
    Route::get('/checkcode/{code}', [CategoryController::class, 'checkcode'])->name('category.checkcode');
    Route::get('/checkcode/{code}/{id}', [CategoryController::class, 'checkcodeid'])->name('category.checkcodeid');
    Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
    Route::post('/update', [CategoryController::class, 'update'])->name('category.update');
    Route::post('/destroy', [CategoryController::class, 'destroy'])->name('category.destroy');
    Route::post('/deleteImage', [CategoryController::class, 'deleteImage'])->name('category.deleteImage');
});

Route::prefix('shelf')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [ShelfController::class, 'index'])->name('shelf.index');
    Route::get('/get', [ShelfController::class, 'get'])->name('shelf.get');
    Route::get('/GetAllData', [ShelfController::class, 'GetAllData'])->name('shelf.GetAllData');
    Route::get('/{id}', [ShelfController::class, 'show'])->name('shelf.show');
    Route::get('/checkcode/{code}', [ShelfController::class, 'checkcode'])->name('shelf.checkcode');
    Route::get('/checkcode/{code}/{id}', [ShelfController::class, 'checkcodeid'])->name('shelf.checkcodeid');
    Route::post('/store', [ShelfController::class, 'store'])->name('shelf.store');
    Route::post('/update', [ShelfController::class, 'update'])->name('shelf.update');
    Route::post('/destroy', [ShelfController::class, 'destroy'])->name('shelf.destroy');
    Route::get('/GetByWarehouse/{warehouse}', [ShelfController::class, 'GetByWarehouse'])->name('shelf.GetByWarehouse');

});

Route::prefix('warehouse')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/get', [WarehouseController::class, 'get'])->name('warehouse.get');
    Route::get('/GetAllData', [WarehouseController::class, 'GetAllData'])->name('warehouse.GetAllData');
    Route::get('/{id}', [WarehouseController::class, 'show'])->name('warehouse.show');
    Route::post('/store', [WarehouseController::class, 'store'])->name('warehouse.store');
    Route::post('/update', [WarehouseController::class, 'update'])->name('warehouse.update');
    Route::post('/destroy', [WarehouseController::class, 'destroy'])->name('warehouse.destroy');

    Route::middleware(['check.view.access'])->get('/movement/index', [MovementController::class, 'index'])->name('movement.index');
    Route::get('/movement/create', [MovementController::class, 'create'])->name('movement.create');
    Route::get('/movement/edit/{id}', [MovementController::class, 'edit'])->name('movement.edit');
    Route::get('/movement/get', [MovementController::class, 'get'])->name('movement.get');
    Route::get('/movement/export', [MovementController::class, 'export'])->name('movement.export');
    Route::post('/movement/store', [MovementController::class, 'store'])->name('movement.store');
    Route::post('/movement/update', [MovementController::class, 'update'])->name('movement.update');
    Route::post('/movement/destroy', [MovementController::class, 'destroy'])->name('movement.destroy');
    Route::get('/movement/create/checkcode/{code}', [MovementController::class, 'checkcode'])->name('movement.checkcode');
    Route::get('/movement/checkcode/{code}/{id}', [MovementController::class, 'checkcodeid'])->name('movement.checkcodeid');

    Route::get('/movement/detail/{id}', [MovementController::class, 'show'])->name('movement.show');
});

Route::prefix('unit')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [UnitController::class, 'index'])->name('unit.index');
    Route::get('/get', [UnitController::class, 'get'])->name('unit.get');
    Route::get('/GetAllData', [UnitController::class, 'GetAllData'])->name('unit.GetAllData');
    Route::get('/{id}', [UnitController::class, 'show'])->name('unit.show');
    Route::get('/checkcode/{code}', [UnitController::class, 'checkcode'])->name('unit.checkcode');
    Route::get('/checkcode/{code}/{id}', [UnitController::class, 'checkcodeid'])->name('unit.checkcodeid');
    Route::post('/store', [UnitController::class, 'store'])->name('unit.store');
    Route::post('/update', [UnitController::class, 'update'])->name('unit.update');
    Route::post('/destroy', [UnitController::class, 'destroy'])->name('unit.destroy');
    Route::get('/edit/{id}', [UnitController::class, 'edit'])->name('unit.edit');
});

Route::prefix('size')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [SizeController::class, 'index'])->name('size.index');
    Route::get('/get', [SizeController::class, 'get'])->name('size.get');
    Route::get('/GetAllData', [SizeController::class, 'GetAllData'])->name('size.GetAllData');
    Route::get('/{id}', [SizeController::class, 'show'])->name('size.show');
    Route::post('/store', [SizeController::class, 'store'])->name('size.store');
    Route::post('/ajaxstore', [SizeController::class, 'ajaxstore'])->name('size.ajaxstore');
    Route::post('/update', [SizeController::class, 'update'])->name('size.update');
    Route::post('/destroy', [SizeController::class, 'destroy'])->name('size.destroy');
    Route::get('/edit/{id}', [SizeController::class, 'edit'])->name('size.edit');
});

Route::prefix('color')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [ColorController::class, 'index'])->name('color.index');
    Route::get('/get', [ColorController::class, 'get'])->name('color.get');
    Route::get('/{id}', [ColorController::class, 'insert'])->name('color.insert');
    Route::post('/store', [ColorController::class, 'store'])->name('color.store');
    Route::get('/edit/{id}', [ColorController::class, 'edit'])->name('color.edit');
    Route::post('/destroy', [ColorController::class, 'destroy'])->name('color.destroy');
    Route::post('/update', [ColorController::class, 'update'])->name('color.update');
});

Route::prefix('vat')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [VatController::class, 'index'])->name('vat.index');
    Route::get('/get', [VatController::class, 'get'])->name('vat.get');
    Route::get('/GetAllData', [VatController::class, 'GetAllData'])->name('vat.GetAllData');
    Route::get('/{id}', [VatController::class, 'show'])->name('vat.show');
    Route::post('/store', [VatController::class, 'store'])->name('vat.store');
    Route::post('/update', [VatController::class, 'update'])->name('vat.update');
    Route::get('/edit/{id}', [VatController::class, 'edit'])->name('vat.edit');
    Route::post('/destroy', [VatController::class, 'destroy'])->name('vat.destroy');
});

Route::prefix('activity')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [ActivityController::class, 'index'])->name('activity.index');
    Route::get('/get', [ActivityController::class, 'get'])->name('activity.get');
    Route::get('/GetAllData', [ActivityController::class, 'GetAllData'])->name('activity.GetAllData');
    Route::get('/{id}', [ActivityController::class, 'show'])->name('activity.show');
});


Route::prefix('customer')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [ContactController::class, 'index'])->name('customer.index');
    Route::get('/get', [ContactController::class, 'get'])->name('customer.get');
    Route::get('/GetAllData', [ContactController::class, 'GetAllData'])->name('customer.GetAllData');
    Route::get('/{id}', [ContactController::class, 'show'])->name('customer.show');
    Route::post('/store', [ContactController::class, 'store'])->name('customer.store');
    Route::post('/update', [ContactController::class, 'update'])->name('customer.update');
    Route::post('/destroy', [ContactController::class, 'destroy'])->name('customer.destroy');
    Route::get('/checkemail/{email}', [ContactController::class, 'checkemail'])->name('customer.checkemail');
    Route::get('/checkemail/{email}/{id}', [ContactController::class, 'checkemailid'])->name('customer.checkemailid');
});

Route::prefix('supplier')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [ContactController::class, 'supplier'])->name('supplier.index');
    Route::get('/get', [ContactController::class, 'getsupplier'])->name('supplier.get');
    Route::get('/GetAllData', [ContactController::class, 'GetAllData'])->name('supplier.GetAllData');
    Route::get('/{id}', [ContactController::class, 'show'])->name('supplier.show');
    Route::post('/store', [ContactController::class, 'store'])->name('supplier.store');
    Route::post('/update', [ContactController::class, 'updatesupplier'])->name('supplier.update');
    Route::post('/destroy', [ContactController::class, 'destroysupplier'])->name('supplier.destroy');
    Route::get('/checkemail/{email}', [ContactController::class, 'checkemail'])->name('supplier.checkemail');
    Route::get('/checkemail/{email}/{id}', [ContactController::class, 'checkemailid'])->name('supplier.checkemailid');
});


Route::prefix('stock')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [StockItemController::class, 'index'])->name('stock.index');
    Route::get('/getHistory', [StockItemController::class, 'getHistory'])->name('stock.getHistory');
    Route::get('/get', [StockItemController::class, 'get'])->name('stock.get');
    Route::get('/add', [StockItemController::class, 'add'])->name('stock.add');
    Route::get('/edit/{id}', [StockItemController::class, 'edit'])->name('stock.edit');
    Route::get('/history/{id}', [StockItemController::class, 'history'])->name('stock.history');
    Route::get('/sellpricehistory/{id}', [StockItemController::class, 'sellpricehistory'])->name('stock.sellpricehistory');
    Route::get('/pricehistory/{id}', [StockItemController::class, 'pricehistory'])->name('stock.pricehistory');
    Route::get('/getpricehistory', [StockItemController::class, 'getpricehistory'])->name('stock.getpricehistory');
    Route::get('/purchasepricehistory/{id}', [StockItemController::class, 'purchasepricehistory'])->name('stock.purchasepricehistory');
    Route::get('/GetAllData', [StockItemController::class, 'GetAllData'])->name('stock.GetAllData');
    Route::get('print/{id}', [StockItemController::class, 'print'])->name('stock.print');
    Route::get('print_multi', [StockItemController::class, 'print_multi'])->name('stock.print_multi');
    Route::get('/{id}', [StockItemController::class, 'show'])->name('stock.show');
    Route::post('/store', [StockItemController::class, 'store'])->name('stock.store');
    Route::post('/update', [StockItemController::class, 'update'])->name('stock.update');
    Route::post('/destroy', [StockItemController::class, 'destroy'])->name('stock.destroy');
    Route::post('/multidelete', [StockItemController::class, 'multidelete'])->name('stock.multidelete');
    Route::get('/add/checkcode/{code}', [StockItemController::class, 'checkcode'])->name('stock.checkcode');
    Route::get('/edit/{id}/checkcode/{code}', [StockItemController::class, 'checkcodeid'])->name('stock.checkcodeid');
});


Route::prefix('transaction')->group(function () {
    // Route::middleware(['check.view.access'])->get('/checkinlist', [TransactionController::class, 'checkinlist'])->name('transaction.checkinlist');
    Route::middleware(['check.view.access'])->get('/checkoutlist', [TransactionController::class, 'checkoutlist'])->name('transaction.checkoutlist');
    Route::get('/getHistory', [StockItemController::class, 'getHistory'])->name('transaction.getHistory');
    // Route::get('/getcheckin', [TransactionController::class, 'getcheckin'])->name('transaction.getcheckin');
    Route::get('/getcheckinforhide', [TransactionController::class, 'getcheckinforhide'])->name('transaction.getcheckinforhide');
    Route::get('/getcheckout', [TransactionController::class, 'getcheckout'])->name('transaction.getcheckout');
    // Route::get('/checkin', [TransactionController::class, 'checkin'])->name('transaction.checkin');
    Route::get('/checkout', [TransactionController::class, 'checkout'])->name('transaction.checkout');
    Route::get('/checkout/hide/{id}', [TransactionController::class, 'checkouthide'])->name('transaction.checkouthide');
    Route::get('/checkout/hiddehistory/{id}', [TransactionController::class, 'hiddehistory'])->name('transaction.hiddehistory');
    Route::get('/checkout/savehidden', [TransactionController::class, 'savecheckouthidden'])->name('transaction.savecheckouthidden');
    Route::get('/checkout/checksellquantity', [TransactionController::class, 'checksellquantity'])->name('transaction.checksellquantity');
    Route::get('/changesellstatus', [TransactionController::class, 'changesellstatus'])->name('transaction.changesellstatus');
    Route::get('/changepurchasestatus', [TransactionController::class, 'changepurchasestatus'])->name('transaction.changepurchasestatus');
    // Route::get('/checkinedit/{id}', [TransactionController::class, 'checkinedit'])->name('transaction.checkinedit');
    Route::get('/checkoutedit/{id}', [TransactionController::class, 'checkoutedit'])->name('transaction.checkoutedit');
    // Route::get('/detail/{id}', [TransactionController::class, 'show'])->name('transaction.show');
    Route::get('/selldetail/{id}', [TransactionController::class, 'sellshow'])->name('transaction.sellshow');
    Route::get('/GetAllData', [TransactionController::class, 'GetAllData'])->name('transaction.GetAllData');
    Route::get('print/{id}', [TransactionController::class, 'print'])->name('transaction.print');
    Route::get('/printSellOrders', [TransactionController::class, 'printSellOrders'])->name('transaction.printSellOrders');
    Route::get('/deletehiddenOrders', [TransactionController::class, 'deletehiddenOrders'])->name('transaction.deletehiddenOrders');
    // Route::post('/storecheckin', [TransactionController::class, 'storecheckin'])->name('transaction.storecheckin');
    // Route::post('/updatecheckin', [TransactionController::class, 'updatecheckin'])->name('transaction.updatecheckin');
    Route::post('/storecheckout', [TransactionController::class, 'storecheckout'])->name('transaction.storecheckout');
    Route::post('/updatecheckout', [TransactionController::class, 'updatecheckout'])->name('transaction.updatecheckout');
    Route::post('/update', [TransactionController::class, 'update'])->name('transaction.update');
    Route::post('/destroy', [TransactionController::class, 'destroy'])->name('transaction.destroy');
    Route::get('/search', [TransactionController::class, 'search'])->name('transaction.searchitem');
    // Route::get('/checkin/checkcode/{code}', [TransactionController::class, 'checkcode'])->name('transaction.checkin.checkcode');
    // Route::get('/checkin/checkcode/{code}/{id}', [TransactionController::class, 'checkcodeid'])->name('transaction.checkin.checkcodeid');
    Route::get('/checkout/checkcode/{code}', [TransactionController::class, 'checkcode'])->name('transaction.checkout.checkcode');
    Route::get('/checkout/checkcode/{code}/{id}', [TransactionController::class, 'checkcodeid'])->name('transaction.checkout.checkcodeid');
    Route::get('/checkout/sellexport', [TransactionController::class, 'sellexport'])->name('transaction.sellexport');
    // Route::get('/checkout/checkinexport', [TransactionController::class, 'checkinexport'])->name('transaction.checkinexport');
    Route::get('/getsellpricehistory', [TransactionController::class, 'getsellpricehistory'])->name('transaction.getsellpricehistory');
    // Route::get('/getpurchasepricehistory', [TransactionController::class, 'getpurchasepricehistory'])->name('transaction.getpurchasepricehistory');
    Route::get('/scan', [TransactionController::class, 'scan'])->name('transaction.scan');
    Route::get('/getNewShowRef', [TransactionController::class, 'getNewShowRef'])->name('transaction.getNewShowRef');
    Route::get('/getUpdatedShowRef', [TransactionController::class, 'getUpdatedShowRef'])->name('transaction.getUpdatedShowRef');
    Route::get('/getNewPurchaseShowRef', [TransactionController::class, 'getNewPurchaseShowRef'])->name('transaction.getNewPurchaseShowRef');
    Route::get('/getUpdatedPurchaseShowRef', [TransactionController::class, 'getUpdatedPurchaseShowRef'])->name('transaction.getUpdatedPurchaseShowRef');
});

Route::prefix('purchase')->group(function () {
    Route::middleware(['check.view.access'])->get('/checkinlist', [TransactionController::class, 'checkinlist'])->name('transaction.checkinlist');
    Route::get('/getcheckin', [TransactionController::class, 'getcheckin'])->name('transaction.getcheckin');
    Route::get('/checkin', [TransactionController::class, 'checkin'])->name('transaction.checkin');
    Route::get('/checkinedit/{id}', [TransactionController::class, 'checkinedit'])->name('transaction.checkinedit');
    Route::get('/detail/{id}', [TransactionController::class, 'show'])->name('transaction.show');
    Route::get('/GetAllData', [TransactionController::class, 'GetAllData'])->name('transaction.GetAllData'); // ?
    Route::get('print/{id}', [TransactionController::class, 'print'])->name('transaction.print'); // ?
    Route::post('/storecheckin', [TransactionController::class, 'storecheckin'])->name('transaction.storecheckin');
    Route::post('/updatecheckin', [TransactionController::class, 'updatecheckin'])->name('transaction.updatecheckin');
    Route::post('/update', [TransactionController::class, 'update'])->name('transaction.update'); // ?
    Route::post('/destroy', [TransactionController::class, 'destroy'])->name('transaction.destroy'); // ?
    // Route::get('/search', [TransactionController::class, 'search'])->name('transaction.searchitem'); // ?
    Route::get('/checkin/checkcode/{code}', [TransactionController::class, 'checkcode'])->name('transaction.checkin.checkcode');
    Route::get('/checkin/checkcode/{code}/{id}', [TransactionController::class, 'checkcodeid'])->name('transaction.checkin.checkcodeid');
    Route::get('/checkout/checkinexport', [TransactionController::class, 'checkinexport'])->name('transaction.checkinexport');
    Route::get('/getpurchasepricehistory', [TransactionController::class, 'getpurchasepricehistory'])->name('transaction.getpurchasepricehistory');
});

Route::prefix('setting')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [SettingsController::class, 'index'])->name('setting.index');
    Route::get('/get', [SettingsController::class, 'get'])->name('setting.get');
    Route::post('/store', [SettingsController::class, 'store'])->name('setting.store');
    Route::post('/update', [SettingsController::class, 'update'])->name('setting.update');
    Route::post('/updatewithimage', [SettingsController::class, 'updatewithimage'])->name('setting.updatewithimage');
    Route::post('/destroy', [SettingsController::class, 'destroy'])->name('setting.destroy');
});

Route::middleware(['web', 'auth', 'check.permissions'])->group(function () {

    Route::prefix('user')->group(function () {
        Route::middleware(['check.view.access'])->get('/', [UserController::class, 'index'])->name('user.index');
        Route::get('/get', [UserController::class, 'get'])->name('user.get');
        Route::get('/{id}', [UserController::class, 'show'])->name('user.show');
        Route::post('/store', [UserController::class, 'store'])->name('user.store');
        Route::post('/update', [UserController::class, 'update'])->name('user.update');
        Route::post('/destroy', [UserController::class, 'destroy'])->name('user.destroy');
        Route::post('/role', [UserController::class, 'role'])->name('user.role');
        Route::get('/getrolebyid/{id}', [UserController::class, 'getrolebyid'])->name('user.getrolebyid');
    });
});

Route::post('/forgot-password', [UserController::class, 'sendResetLinkEmail'])->name('forgot-password');
Route::get('/reset-password/{token}', [UserController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [UserController::class, 'reset'])->name('user.reset');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/profile', [UserController::class, 'profile'])->name('profile');
Route::middleware(['check.view.access'])->get('/report', [ReportController::class, 'index'])->name('index');


Route::prefix('reports')->group(function () {
    Route::middleware(['check.view.access'])->get('/', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/overall', [ReportsController::class, 'overall'])->name('reports.overall');
    Route::get('/stock', [ReportsController::class, 'stock'])->name('reports.stock');
    Route::get('/checkin', [ReportsController::class, 'checkin'])->name('reports.checkin');
    Route::get('/checkout', [ReportsController::class, 'checkout'])->name('reports.checkout');
    Route::get('/warehouse', [ReportsController::class, 'warehouse'])->name('reports.warehouse');
    Route::get('/category', [ReportsController::class, 'category'])->name('reports.category');
    Route::get('/getcheckinreport', [ReportsController::class, 'getcheckinreport'])->name('reports.getcheckinreport');
    Route::get('/getcheckoutreport', [ReportsController::class, 'getcheckoutreport'])->name('reports.getcheckoutreport');
    Route::get('/getstockreport', [ReportsController::class, 'getstockreport'])->name('reports.getstockreport');
    Route::get('/getwarehousereport', [ReportsController::class, 'getwarehousereport'])->name('reports.getwarehousereport');
    Route::get('/getcategoryreport', [ReportsController::class, 'getcategoryreport'])->name('reports.getcategoryreport');
    Route::get('/GetAllData', [ReportsController::class, 'GetAllData'])->name('reports.GetAllData');
    Route::get('/getSumData', [ReportsController::class, 'getSumData'])->name('reports.getSumData');
    // Route::get('/{id}', [ReportsController::class, 'show'])->name('reports.show');
});


Route::prefix('home')->group(function () {
    Route::get('/', [ReportsController::class, 'home'])->name('home.index');
});

Route::prefix('/')->group(function () {
    Route::get('/', [ReportsController::class, 'home'])->name('home.main.index');
});

Route::prefix('login')->group(function () {
    Route::get('/', [UserController::class, 'login'])->name('user.login');
    Route::post('/dologin', [UserController::class, 'dologin'])->name('user.dologin');

});
