<?php
// app/Services/TransactionService.php

namespace App\Services;

use App\Models\TransactionModel;
use App\Models\StockItemModel;
use App\Models\TransactionOrderModel;
use App\Models\SellOrderDetailModel;
use App\Models\MovementModel;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class TransactionService
{

    /**
     * Get all transactions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getAll()
    {
        return TransactionModel::all();
    }

    /**
     * Get the total count of checked-in items.
     *
     * @return int
     */

    public function totalitemcheckin(){
        return TransactionModel::where('status', '=', '1')->count();
    }

    /**
     * Get the total count of checked-out items.
     *
     * @return int
     */
    public function totalitemcheckout(){
        return TransactionModel::where('status', '=', '2')->count();
    }

    /**
     * Get the total count of all items.
     *
     * @return int
     */
    public function totalitem(){
        return TransactionModel::count();
    }


    public function getcheckbystockitem($stockItemId)
    {
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->select('stockitem.name','contact.name as supplier', 'transaction.*',DB::raw('SUM(wh_transaction.quantity) as quantity'),)
        ->where('transaction.stockitemid', $stockItemId)
        ->groupBy('transaction.reference')
        ->orderBy('transaction.transactiondate','DESC')
        ->get();
    }

    /**
     * Get check-in transactions with additional details.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getcheckin()
    {
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->leftJoin('unit', 'unit.id', '=', 'transaction.unitid')
        ->leftJoin('transaction_order', 'transaction_order.reference', '=', 'transaction.reference')
        ->select(
            'stockitem.name',
            'contact.name as supplier',
            'transaction.*',
            'unit.name as unit_name',
            'transaction_order.confirmed',
            'transaction_order.show_reference',
            DB::raw('SUM(wh_transaction.quantity) as total_quantity'),
            DB::raw('SUM(wh_transaction.quantity * wh_transaction.price) as total_price'),
        )
        ->where('transaction.status', 1)
        ->where('transaction_order.creator', Auth::user()->id)
        ->groupBy('transaction.reference')
        ->orderBy('transaction.transactiondate','DESC')
        ->orderBy('transaction.created_at','DESC');
    }

    public function getcheckinsum($filter)
    {
        $query = TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->leftJoin('unit', 'unit.id', '=', 'transaction.unitid')
        ->select(
            DB::raw('SUM(wh_transaction.quantity) as total_quantity'),
            DB::raw('SUM(wh_transaction.quantity * wh_transaction.price) as total_price'),
        )
        ->where('transaction.status', 1)
        ->where('creator', Auth::user()->id);

        if (!empty($filter['keyword'])) {
            $query->where('contact.name', 'like', "%{$filter['keyword']}%");
            $query->orWhere('transaction.reference', 'like', "%{$filter['keyword']}%");
        }
        if (!empty($filter['startdate']) && !empty($filter['enddate'])) {
            $startDate = date('Y-m-d', strtotime($filter['startdate']));
            $endDate = date('Y-m-d', strtotime($filter['enddate']));
            $query->whereBetween('transaction.transactiondate', [$startDate, $endDate]);
        }
        if (!empty($filter['supplier'])) {
            $query->where('transaction.contactid', '=', $filter['supplier']);
        }
        if (!empty($filter['warehouse'])) {
            $query->where('stockitem.warehouseid', '=', $filter['warehouse']);
        }
        return $query->get();
    }

    public function getcheckinItemsForHide($stockitemid)
    {
        $stockItem = StockItemModel::where('id', $stockitemid)->first();
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
                ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
                ->leftJoin('unit as transaction_unit', 'transaction_unit.id', '=', 'transaction.unitid')
                ->select(
                    DB::raw("'".$stockitemid."' as stockitem_id"),
                    'stockitem.name',
                    'stockitem.unitid as stockunitid',
                    'stockitem.unitconverter','contact.name as supplier',
                    'transaction.*',
                    'transaction_unit.name as transactionunitname'
                )
                // ->where('transaction.stockitemid', $stockitemid)
                ->where('stockitem.code', $stockItem->code)
                ->where('transaction.warehouseid', $stockItem->warehouseid)
                ->where('transaction.creator', Auth::user()->id)
                ->orderBy('transaction.transactiondate', 'DESC')
                ->orderBy('transaction.created_at', 'DESC')
                ->get();
    }

    public function getcheckinOrders()
    {
        return TransactionOrderModel::leftJoin('contact', 'contact.id', '=', 'transaction_order.contactid')
        ->select('contact.name as supplier', 'transaction_order.*')
        ->where('transaction_order.status', 1)
        ->groupBy('transaction_order.reference')
        ->orderBy('transaction_order.transactiondate','DESC')
        ->orderBy('transaction_order.created_at','DESC');
    }

    public function getStockItemsByReference($reference)
    {
        return TransactionModel::leftJoin('stockitem', 'stockitem.id' ,'=', 'transaction.stockitemid')
                ->leftJoin('unit', 'unit.id', '=', 'transaction.unitid')
                ->select('stockitem.name', 'transaction.quantity', 'transaction.unitid', 'unit.name as unitname', 'transaction.hidden_amount')
                ->where('transaction.reference', $reference)
                ->get();
    }


     /**
     * Get check-in transactions for reporting purposes.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getcheckinreport()
    {
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
        ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
        ->leftJoin('category', 'stockitem.categoryid', '=', 'category.id')
        ->leftJoin('users', 'users.id', '=', 'transaction.creator')
        ->select(
            'transaction.*',
            'stockitem.name',
            'stockitem.code',
            'stockitem.size',
            'stockitem.unitid as stock_unitid',
            'stockitem.unitconverter',
            'stockitem.unitconverter1',
            'stockitem.unitconverterto',
            'stock_base_unit.name as stock_base_unit_name',
            'stock_converted_unit.name as stock_converted_unit_name',
            'contact.name as supplier',
            'category.name as category',
            'users.name as user_name'
        )
        ->where('transaction.status', 1)
        ->orderBy('transaction.transactiondate','DESC')
        ->orderBy('transaction.created_at','DESC');
    }


    /**
     * Get check-out transactions with additional details.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getcheckout()
    {
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->select('stockitem.name','contact.name as supplier', 'transaction.*',DB::raw('SUM(wh_transaction.quantity) as quantity'),)
        ->where('transaction.status', 2)
        ->groupBy('transaction.reference')
        ->orderBy('transaction.transactiondate','DESC')
        ->get();
    }

    /**
     * Get check-out transactions for reporting purposes.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getcheckoutreport()
    {
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->leftJoin('category', 'stockitem.categoryid', '=', 'category.code')
        ->select('stockitem.name','contact.name as supplier','category.name as category', 'transaction.*',DB::raw('SUM(wh_transaction.quantity) as totalquantity'),)
        ->where('transaction.status', 2)
        ->groupBy('transaction.reference')
        ->orderBy('transaction.transactiondate','DESC');
    }

    /**
     * Get transactions by reference and status.
     *
     * @param string $ref The reference to search for.
     * @param int $status The status of the transactions.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByRef($ref, $status){
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->leftJoin('transaction_order', 'transaction_order.reference', '=', 'transaction.reference')
        ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
        ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
        ->select(
            'stockitem.name',
            'stockitem.code',
            'stockitem.unitid as stockitem_unitid',
            'stockitem.unitconverter',
            'stockitem.unitconverter1',
            'contact.name as supplier',
            'contact.email as supplieremail',
            'contact.company as suppliercompany',
            'transaction.*',
            'transaction_order.show_reference',
            'transaction_order.id as order_id',
            'stock_base_unit.name as stock_base_unit_name',
            'stock_converted_unit.name as stock_converted_unit_name',
            // Conditional logic for converted_quantity
            DB::raw('CASE
                WHEN wh_transaction.unitid = wh_stockitem.unitid THEN
                    (wh_transaction.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter)
                ELSE
                    (wh_transaction.quantity * wh_stockitem.unitconverter / wh_stockitem.unitconverter1)
            END as converted_quantity')
        )
        ->where('transaction.status', $status)
        ->where('transaction.reference', $ref)
        ->groupBy('stockitem.code')
        ->get();
    }

    /**
     * Get total count of items for different time periods and status.
     *
     * @param int $status The status of the transactions.
     * @return array
     */
    public function totalallitem($status){
        $overallCount = TransactionModel::select(DB::raw('count(*) as total'))
        ->where('status', $status)
        ->first();

        $yearCount = TransactionModel::select(DB::raw('count(*) as total'))
            ->whereYear('transactiondate',date('Y'))
            ->where('status', $status)
            ->first();

        $monthCount = TransactionModel::select(DB::raw('count(*) as total'))
            ->whereMonth('transactiondate',date('m'))
            ->whereYear('transactiondate',date('Y'))
            ->where('status', $status)
            ->first();

        $res['Overall'] = $overallCount->total;
        $res['Year']    = $yearCount->total;
        $res['Month']   = $monthCount->total;
        return $res;
    }

    /**
     * Get monthly data for transactions.
     *
     * @param int $status The status of the transactions.
     * @return \Illuminate\Support\Collection
     */
    public function monthlydata($status){
        // Group by month and get the sum of records for each month
        return TransactionModel::whereYear('transactiondate', date('Y'))
        ->get()
        ->where('status', $status)
        ->groupBy(function($date) {
            return Carbon::parse($date->transactiondate)->format('m');
        })
        ->map(function($group) {
            return $group->count();
        });
    }



    /**
     * Create check-in transactions.
     *
     * @param array $data The transaction data.
     * @param array $quantity The quantity array.
     * @param array $itemids The item IDs array.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createcheckin($data, $warehouseid, $quantity, $price, $itemids, $unit, $confirmed)
    {
        // TransactionModel::create($data);

        $count = count($itemids);
        for ($i = 0; $i < $count; $i++) {
            $itemid = $itemids[$i];
            $singleQuantity = $quantity[$i];
            // Rest of your code

           $data1 = TransactionModel::create([
                'stockitemid' => $itemid,
                'warehouseid' => $warehouseid,
                'quantity' => $singleQuantity,
                'price' => $price[$i],
                'status' => 1,
                'transactiondate' => $data['transactiondate'],
                'contactid' => $data['contactid'],
                'reference' => $data['reference'],
                'description' => $data['description'],
                'unitid' => $unit[$i],
                'creator' => Auth::id()
            ]);

        }
        TransactionOrderModel::create([
            'status' => 1,
            'transactiondate' => $data['transactiondate'],
            'warehouseid' => $warehouseid,
            'contactid' => $data['contactid'],
            'reference' => $data['reference'],
            'show_reference' => $data['show_reference'],
            'description' => $data['description'],
            'confirmed' => $confirmed,
            'creator' => Auth::id()
        ]);
         return $data1;
    }


    /**
     * Create check-out transactions.
     *
     * @param array $data The transaction data.
     * @param array $quantity The quantity array.
     * @param array $itemids The item IDs array.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createcheckout($data, $quantity, $itemids)
    {


        $count = count($itemids);
        for ($i = 0; $i < $count; $i++) {
            $itemid = $itemids[$i];
            $singleQuantity = $quantity[$i];
            // Rest of your code

           $data = TransactionModel::create([
                'stockitemid' => $itemid,
                'quantity' => $singleQuantity,
                'status' => 2,
                'transactiondate' => $data['transactiondate'],
                'contactid' => $data['contactid'],
                'reference' => $data['reference'],
                'description' => $data['description']
            ]);

        }

         return $data;
    }


    /**
     * Update check-in transactions.
     *
     * @param array $data The transaction data.
     * @param array $quantity The quantity array.
     * @param array $itemids The item IDs array.
     * @param string $reference The reference for updating.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updatecheckin($data, $warehouseid, $quantity, $price, $itemids, $reference, $unit, $confirmed)
    {
        // Use the reference to get the existing records
        $existingRecords = TransactionModel::where('reference', $reference)->get();
        $existingOrderRecord = TransactionOrderModel::where('reference', $reference)->first();

        // Create an associative array to map existing records by stockitemid
        $existingRecordsMap = $existingRecords->keyBy('stockitemid');

        // Identify items to delete
        $itemsToDelete = $existingRecords->pluck('stockitemid')->diff($itemids);

        // Delete records for items that are no longer present
        TransactionModel::where('reference', $reference)->whereIn('stockitemid', $itemsToDelete)->delete();

        // Loop through each item and update or add as needed
        foreach ($itemids as $index => $itemid) {
            $singleQuantity = $quantity[$index];
            // Check if the item exists in the existing records
            if ($existingRecord = $existingRecordsMap->get($itemid)) {
                // If it exists, update the existing record
                $existingRecord->update([
                    'quantity' => $singleQuantity,
                    'status' => 1,
                    'price' => $price[$index],
                    'transactiondate' => $data['transactiondate'],
                    'contactid' => $data['contactid'],
                    'description' => $data['description'],
                    'unitid' => $unit[$index]
                ]);
            } else {
                // If it doesn't exist, add a new record
                TransactionModel::create([
                    'stockitemid' => $itemid,
                    'quantity' => $singleQuantity,
                    'price' => $price[$index],
                    'status' => 1,
                    'transactiondate' => $data['transactiondate'],
                    'contactid' => $data['contactid'],
                    'description' => $data['description'],
                    'reference' => $reference,
                    'unitid' => $unit[$index]
                ]);
            }
        }

        $existingOrderRecord->update([
            'status' => 1,
            'transactiondate' => $data['transactiondate'],
            'contactid' => $data['contactid'],
            'description' => $data['description'],
            'show_reference' => $data['show_reference'],
            'confirmed' => $confirmed,
        ]);

        return $data;
    }

    public function checkupdatecheckinquantity($data, $quantity, $price, $itemids, $reference, $unit) {
        // Use the reference to get the existing records
        $existingRecords = TransactionModel::where('reference', $reference)->get();
        // Create an associative array to map existing records by stockitemid
        $existingRecordsMap = $existingRecords->keyBy('stockitemid');
        // Identify items to delete
        $itemsToDelete = $existingRecords->pluck('stockitemid')->diff($itemids);
        $changed_group = [
            "itemid" => [],
            "quantity" => [],
            "unit" => []
        ];
        foreach ($itemsToDelete as $index => $itemid) {
            $delete_transaction = TransactionModel::where('reference', $reference)->where('stockitemid', $itemid)->first();
            $changed_group["itemid"][] = $itemid;
            $changed_group["quantity"][] = -1 * $delete_transaction->quantity;
            $changed_group["unit"][] = $delete_transaction->unitid;
        }
        foreach ($itemids as $index => $itemid) {
            $singleQuantity = $quantity[$index];
            // Check if the item exists in the existing records
            if ($existingRecord = $existingRecordsMap->get($itemid)) {
                if ($existingRecord->quantity != $singleQuantity || $existingRecord->unitid != $unit[$index]) {
                    $old_quantity = $existingRecord->quantity;
                    $new_quantity = $singleQuantity;
                    $stockItem = StockItemModel::where('id', $itemid)->first();
                    $stockitemunitid = $stockItem->unitid;
                    if ($stockItem->unitid != $unit[$index]) {
                        $old_quantity = $stockItem->unitconverter * $old_quantity;
                        $new_quantity = $stockItem->unitconverter * $new_quantity;
                    }
                    $changed_quantity = $new_quantity - $old_quantity;
                    $changed_group["itemid"][] = $itemid;
                    $changed_group["quantity"][] = $changed_quantity;
                    $changed_group["unit"][] = $stockItem->unitid;
                }
            } else {
                $changed_group["itemid"][] = $itemid;
                $changed_group["quantity"][] = $singleQuantity;
                $changed_group["unit"][] = $unit[$index];
            }
        }
        return $changed_group;
    }

    /**
     * Update check-out transactions.
     *
     * @param array $data The transaction data.
     * @param array $quantity The quantity array.
     * @param array $itemids The item IDs array.
     * @param string $reference The reference for updating.
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updatecheckout($data, $quantity, $itemids, $reference)
    {
    // Use the reference to get the existing records
    $existingRecords = TransactionModel::where('reference', $reference)->get();

    // Create an associative array to map existing records by stockitemid
    $existingRecordsMap = $existingRecords->keyBy('stockitemid');

    // Identify items to delete
    $itemsToDelete = $existingRecords->pluck('stockitemid')->diff($itemids);

    // Delete records for items that are no longer present
    TransactionModel::where('reference', $reference)->whereIn('stockitemid', $itemsToDelete)->delete();

    // Loop through each item and update or add as needed
    foreach ($itemids as $index => $itemid) {
        $singleQuantity = $quantity[$index];

        // Check if the item exists in the existing records
        if ($existingRecord = $existingRecordsMap->get($itemid)) {
            // If it exists, update the existing record
            $existingRecord->update([
                'quantity' => $singleQuantity,
                'status' => 2,
                'transactiondate' => $data['transactiondate'],
                'contactid' => $data['contactid'],
                'description' => $data['description']
            ]);
        } else {
            // If it doesn't exist, add a new record
            TransactionModel::create([
                'stockitemid' => $itemid,
                'quantity' => $singleQuantity,
                'status' => 2,
                'transactiondate' => $data['transactiondate'],
                'contactid' => $data['contactid'],
                'description' => $data['description'],
                'reference' => $reference
            ]);
        }
    }

    return $data;
    }



    /**
     * Update a transaction by ID.
     *
     * @param int $id The ID of the transaction to update.
     * @param array $data The transaction data to update.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function update($id, $data)
    {
        $SetData = TransactionModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete transactions by reference.
     *
     * @param string $id The reference of the transactions to delete.
     * @return void
     */
    public function delete($id)
    {
        $record =  TransactionModel::where('reference', $id)->get();
        if ($record) {
            $record->each->delete();
        }
        $order = TransactionOrderModel::where('reference', $id)->first();
        $date = $order->transactiondate;
        TransactionOrderModel::where('reference', $id)->delete();
        $this->resetShowReferenceNum($date);
    }

    /**
     * Get a transaction by ID.
     *
     * @param int $id The ID of the transaction to retrieve.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getById($id)
    {
        return TransactionModel::findOrFail($id);
    }

    /**
     * Check if a transaction with the given contact ID exists.
     *
     * @param int $id The contact ID.
     * @return bool
     */
    public function getByContact($id)
    {
        return TransactionModel::where('contactid', $id)->exists();
    }

    /**
     * Check if a transaction with the given warehouse ID exists.
     *
     * @param int $id The warehouse ID.
     * @return bool
     */
    public function getByWarehouse($id)
    {
        return TransactionModel::where('warehouseid', $id)->exists();
    }

    /**
     * Check if a transaction with the given code and ID exists.
     *
     * @param string $code The code to check.
     * @param int $Id The ID to exclude from the check.
     * @return bool
     */
    public function CheckCodeId($code, $Id){
        return  TransactionModel::where('reference', $code)->where('id', '!=', $Id)->exists();
    }

    /**
     * Check if a transaction with the given code and ID exists.
     *
     * @param string $code The code to check.
     * @return bool
     */
    public function CheckCode($code){

        return TransactionModel::where('reference', $code)->exists();

    }

    public function hidetransaction($data) {
        $transaction = TransactionModel::find($data['transactionid']);
        $stockitem = StockItemModel::find($transaction->stockitemid);
        $transaction->hidden_amount += $data['new_hidden'];
        if ($transaction->unitid != $stockitem->unitid) {
            // $transaction->quantity = $transaction->quantity * $stockitem->unitconverter;
        }
        // $transaction->unitid = $stockitem->unitid;
        $transaction->save();

        // $stockitem ->hidden_amount += $data['new_hidden'];
        // $stockitem->save();
    }

    public function getStockItemHistory($id, $keyword = null)
    {
        $stockItem = StockItemModel::where('id', $id)->first();
        $purchases = TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
            ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
            ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
            ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
            ->leftJoin('transaction_order', 'transaction_order.reference', '=', 'transaction.reference')
            ->select(
                'transaction.id',
                'transaction_order.show_reference as reference',
                'transaction.transactiondate as date',
                'transaction.quantity',
                'transaction.price',
                DB::raw("'purchase' as type"),
                'transaction.description',
                'transaction.hidden_amount',
                'transaction.unitid',
                'stockitem.unitid as stockitemunitid',
                'contact.name as contactname',
                'stock_base_unit.name as base_unit_name',
                'stock_converted_unit.name as converted_unit_name',
                'transaction.created_at',
                DB::raw('CASE
                WHEN wh_transaction.unitid = wh_stockitem.unitid THEN
                        (wh_transaction.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter)
                    ELSE
                        (wh_transaction.quantity * wh_stockitem.unitconverter / wh_stockitem.unitconverter1)
                END as converted_quantity'),
                DB::raw('CASE
                WHEN wh_transaction.unitid = wh_stockitem.unitid THEN
                        (wh_transaction.hidden_amount * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter)
                    ELSE
                        (wh_transaction.hidden_amount * wh_stockitem.unitconverter / wh_stockitem.unitconverter1)
                END as converted_hidden_amount')
            )
            // ->where('transaction.stockitemid', $id)
            ->where('stockitem.code', $stockItem->code)
            ->where('transaction.warehouseid', $stockItem->warehouseid)
            ->where('transaction_order.confirmed', true);
        if ($keyword) {
            $purchases->where(function ($query) use ($keyword) {
                $query->where('contact.name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('transaction_order.show_reference', 'LIKE', '%' . $keyword . '%');
            });
        }


        // return $purchases->get();

        $sales = SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
            ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
            ->leftJoin('contact', 'contact.id', '=', 'sell_order_detail.contactid')
            ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
            ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
            ->select('sell_order_detail.id',
                'sell_order.show_reference as reference',
                'sell_order_detail.selldate as date',
                'sell_order_detail.quantity',
                'sell_order_detail.price',
                DB::raw("'sell' as type"),
                'sell_order_detail.description',
                DB::raw('0 as hidden_amount'),
                'sell_order_detail.unitid',
                'stockitem.unitid as stockitemunitid',
                'contact.name as contactname',
                'stock_base_unit.name as base_unit_name',
                'stock_converted_unit.name as converted_unit_name',
                'sell_order_detail.created_at',
                DB::raw('CASE
                WHEN wh_sell_order_detail.unitid = wh_stockitem.unitid THEN
                        (wh_sell_order_detail.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter)
                    ELSE
                        (wh_sell_order_detail.quantity * wh_stockitem.unitconverter / wh_stockitem.unitconverter1)
                END as converted_quantity'),
                DB::raw('0 as converted_hidden_amount'),
            )
            ->where('stockitem.code', $stockItem->code)
            // ->where('sell_order_detail.stockitemid', $id)
            // ->where('sell_order.confirmed', true)
            ->where('sell_order_detail.warehouseid', $stockItem->warehouseid);
        if ($keyword) {
            $sales->where(function ($query) use ($keyword) {
                $query->where('contact.name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('sell_order.show_reference', 'LIKE', '%' . $keyword . '%');
            });
        }

        $movements = MovementModel::leftJoin('stockitem', 'stockitem.id', '=', 'movement.stockitemid')
            ->leftJoin('warehouse as source_warehouse', 'source_warehouse.id', '=', 'movement.source_warehouse_id')
            ->leftJoin('warehouse as target_warehouse', 'target_warehouse.id', '=', 'movement.target_warehouse_id')
            ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
            ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
            ->select(
                'movement.id',
                'movement.reference',
                'movement.movement_date as date',
                'movement.quantity',
                'movement.price',
                // DB::raw("'movement' as type"),
                DB::raw("IF(wh_movement.source_warehouse_id != '".$stockItem->warehouseid."' , 'movement_in', 'movement_out') as type"),
                'movement.description',
                DB::raw('0 as hidden_amount'),
                'movement.unitid',
                'stockitem.unitid as stockitemunitid',
                DB::raw("IF(wh_movement.source_warehouse_id != '".$stockItem->warehouseid."' , wh_source_warehouse.name, wh_target_warehouse.name) as contactname"),
                'stock_base_unit.name as base_unit_name',
                'stock_converted_unit.name as converted_unit_name',
                'movement.created_at',
                DB::raw('CASE
                WHEN wh_movement.unitid = wh_stockitem.unitid THEN
                        (wh_movement.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter)
                    ELSE
                        (wh_movement.quantity * wh_stockitem.unitconverter / wh_stockitem.unitconverter1)
                END as converted_quantity'),
                DB::raw('0 as converted_hidden_amount'),
            )
            ->where(function($query) use ($stockItem) {
                $query->Where('movement.source_warehouse_id', $stockItem->warehouseid)
                        ->orWhere('movement.target_warehouse_id', $stockItem->warehouseid);
            })
            ->where('movement.code', $stockItem->code);
        if ($keyword) {
            $movements->where(function ($query) use ($keyword, $stockItem) {
                $query->where(DB::raw("IF(wh_movement.source_warehouse_id != '".$stockItem->warehouseid."', wh_source_warehouse.name, wh_target_warehouse.name)"), 'LIKE', '%' . $keyword . '%')
                        ->orWhere('movement.reference', 'LIKE', '%' . $keyword . '%');
            });
        }
        $history = $purchases->union($sales)->union($movements)
            ->orderBy('date', 'DESC')
            ->orderBy('created_at', 'DESC');
        return $history;
    }

    public function getCheckinDetail($filter)
    {
        $query = TransactionModel::leftJoin('stockitem', 'stockitem.id' ,'=', 'transaction.stockitemid')
                ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
                ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
                ->leftJoin('category', 'category.id', '=', 'stockitem.categoryid')
                ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
                ->select(
                    'transaction.*',
                    'stockitem.id as stockitemid',
                    'stockitem.name',
                    'stockitem.code',
                    'stockitem.size',
                    'stockitem.itemsubtype',
                    'stockitem.vat',
                    'stockitem.unitconverter',
                    'stockitem.unitconverter1',
                    'stockitem.unitid as stock_unitid',
                    'stock_base_unit.name as base_unit_name',
                    'stock_converted_unit.name as converted_unit_name',
                    'category.name as category_name',
                    'contact.name as contact_name',
                    'contact.email as contact_email',
                )
                ->orderBy('transaction.transactiondate','DESC')
                ->orderBy('transaction.created_at','DESC');
        // Check if filter has startdate and enddate
        if (!empty($filter['startdate']) && !empty($filter['enddate'])) {
            $startDate = $filter['startdate'];
            $endDate = $filter['enddate'];

            $query->whereBetween('transaction.transactiondate', [$startDate, $endDate]);
        }

        return $query->get();
    }

    public function getPriceHistory($id)
    {
        return TransactionModel::
            leftJoin('users', 'users.id', '=', 'transaction.creator')
            ->leftJoin('unit', 'unit.id', '=', 'transaction.unitid')
            ->leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
            ->leftJoin('transaction_order', 'transaction_order.reference', '=', 'transaction.reference')
            ->select(
                'transaction.reference',
                'stockitem.unitconverter',
                'stockitem.unitconverter1',
                'stockitem.unitconverterto',
                'stockitem.unitid as stockunitid',
                'users.name as creator',
                'transaction.price',
                'transaction.updated_at',
                'transaction.unitid',
                'unit.name as sell_unit_name',
                'transaction_order.show_reference'
            )
            ->where('transaction.stockitemid', '=', $id)
            ->orderBy('transaction.updated_at', 'desc')
            ->where('transaction_order.confirmed', true)
            ->get();

    }

    /**
     * Check if a transaction with the given warehouse ID exists.
     *
     * @param int $id The warehouse ID.
     * @return bool
     */
    public function getLastRef()
    {
        $lastReference = TransactionOrderModel::whereDate('transactiondate', date('Y-m-d'))
                ->orderBy('created_at', 'desc')
                ->value('show_reference');
        $lastNumber = 0;
        if ($lastReference) {
            $lastNumber = (int) substr($lastReference, -3);
        }

        return $lastNumber;
    }

    public function getTransactionOrder($reference)
    {
        return TransactionOrderModel::where('reference','=',$reference)->first();
    }

    public function getNewShowReference($date)
    {
        $transactions = TransactionOrderModel::where('transactiondate', '=', $date)->get();

        $prefix = 'PZ';
        $new_show_ref =  $prefix . "/" . date('d/m/Y', strtotime($date)). "/" . sprintf('%04d', count($transactions) + 1);
        return $new_show_ref;
    }

    public function getUpdatedShowRef($date, $id)
    {
        $transactions = TransactionOrderModel::where('transactiondate', '=', $date)->where("id", "<", $id)->get();
        $prefix = 'PZ';
        $new_show_ref =  $prefix . "/" . date('d/m/Y', strtotime($date)). "/" . sprintf('%04d', count($transactions) + 1);
        return $new_show_ref;
    }

    public function resetShowReferenceNum($date)
    {
        $transactions = TransactionOrderModel::where('transactiondate', '=', $date)->orderBy('id')->get();
        foreach($transactions as $index => $transaction) {
            if ($transaction->show_reference != "") {
                $array = explode("/", $transaction->show_reference);
                $sequence = $array[count($array) - 1];
                $transaction->show_reference = str_replace($sequence, sprintf('%04d', $index + 1), $transaction->show_reference);
                $transaction->save();
            }
        }
    }
}

