<?php
// app/Services/SellService.php

namespace App\Services;

use App\Models\SellOrderModel;
use App\Models\SellOrderDetailModel;
use App\Models\SellHideHistoryModel;
use App\Models\StockItemModel;
use App\Models\TransactionModel;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DateTime;

class SellService
{
    public function getAll()
    {
        return SellOrderModel::all();
    }

    public function createcheckout($data, $warehouseid, $withInvoice, $confirmed, $quantity, $unit, $price, $discount, $itemids, $pre_order)
    {
        $count = count($itemids);
        try {
            for ($i = 0; $i < $count; $i++) {
                $itemid = $itemids[$i];
                $singleQuantity = $quantity[$i];
                // Rest of your code
               $data1 = SellOrderDetailModel::create([
                    'stockitemid' => $itemid,
                    'warehouseid' => $warehouseid,
                    'contactid' => $data['contactid'],
                    'reference' => $data['reference'],
                    'selldate' => $data['transactiondate'],
                    'price' => $price[$i],
                    'discount' => $discount[$i],
                    'quantity' => $singleQuantity,
                    'unitid' => $unit[$i],
                    'description' => $data['description'],
                ]);
            }
            SellOrderModel::create([
                'warehouseid' => $warehouseid,
                'contactid' => $data['contactid'],
                'reference' => $data['reference'],
                'selldate' => $data['transactiondate'],
                'discount' => $data['total_discount'],
                'discount_type' => $data['discount_type'],
                'confirmed' => $confirmed,
                'pre_order' => $pre_order,
                'withinvoice' => $withInvoice,
                'payment_type' => $data['payment_type'],
                'description' => $data['description'],
                'show_reference' => $data['show_reference'],
                'creator' => Auth::user()->id
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        return $data1;
    }

    public function updatecheckout($data,  $warehouseid, $withInvoice, $confirmed, $quantity, $unit, $price, $discount, $itemids, $reference, $pre_order)
    {
        // Use the reference to get the existing records
        $existingOrderRecord = SellOrderModel::where('reference', $reference)->first();
        $existingRecords = SellOrderDetailModel::where('reference', $reference)->get();
        // Create an associative array to map existing records by stockitemid
        $existingRecordsMap = $existingRecords->keyBy('stockitemid');

        // Identify items to delete
        $itemsToDelete = $existingRecords->pluck('stockitemid')->diff($itemids);

        // Delete records for items that are no longer present
        SellOrderDetailModel::where('reference', $reference)->whereIn('stockitemid', $itemsToDelete)->delete();
        // Loop through each item and update or add as needed
        foreach ($itemids as $index => $itemid) {
            $singleQuantity = $quantity[$index];
            // Check if the item exists in the existing records
            if ($existingRecord = $existingRecordsMap->get($itemid)) {
                // If it exists, update the existing record
                $existingRecord->update([
                    'stockitemid' => $itemid,
                    'contactid' => $data['contactid'],
                    'selldate' => $data['transactiondate'],
                    'price' => $price[$index],
                    'discount' => $discount[$index],
                    'quantity' => $singleQuantity,
                    'unitid' => $unit[$index],
                    'reference' => $reference,
                    'description' => $data['description']
                ]);
            } else {
                // If it doesn't exist, add a new record
                $data1 = SellOrderDetailModel::create([
                    'stockitemid' => $itemid,
                    'contactid' => $data['contactid'],
                    'reference' => $reference,
                    'selldate' => $data['transactiondate'],
                    'price' => $price[$index],
                    'discount' => $discount[$index],
                    'quantity' => $singleQuantity,
                    'unitid' => $unit[$index],
                    'description' => $data['description']
                ]);
            }
        }
        $original_payment_type = $existingOrderRecord->payment_type;

        $existingOrderRecord->update([
            'contactid' => $data['contactid'],
            'selldate' => $data['transactiondate'],
            'discount' => $data['total_discount'],
            'discount_type' => $data['discount_type'],
            'confirmed' => $confirmed,
            'withinvoice' => $withInvoice,
            'payment_type' => $data['payment_type'],
            'show_reference' => $data['show_reference'],
            'pre_order' => $pre_order,
            'description' => $data['description']
        ]);

        if ($original_payment_type != $data['payment_type']) {
            $this->resetShowReferenceNum($original_payment_type, $data['transactiondate']);
            $this->resetShowReferenceNum($data['payment_type'], $data['transactiondate']);
        }
        return true;
    }

    public function getcheckout()
    {
        return SellOrderModel::leftJoin('contact', 'contact.id', '=', 'sell_order.contactid')
        ->select('contact.name as supplier', 'sell_order.*')
        ->orderBy('sell_order.selldate','DESC')
        ->orderBy('sell_order.created_at','DESC');
    }

    public function getStockItemsByReference($reference)
    {
        return SellOrderDetailModel::leftJoin('stockitem', 'stockitem.id' ,'=', 'sell_order_detail.stockitemid')
                ->leftJoin('unit', 'unit.id', '=', 'stockitem.unitid')
                ->leftJoin('unit as sell_unit', 'sell_unit.id', '=', 'sell_order_detail.unitid')
                ->select(
                    'stockitem.id as stockitemid',
                    'stockitem.name',
                    'stockitem.code',
                    'stockitem.price as stock_price',
                    'stockitem.unitconverter',
                    'stockitem.unitconverter1',
                    'stockitem.unitconverterto',
                    'stockitem.unitid as stockunit',
                    'sell_order_detail.*',
                    'unit.name as stockunitname',
                    'sell_unit.name as sellorderunitname'
                )
                ->where('sell_order_detail.reference', $reference)
                ->get();
    }

    public function getStockItemsByReferenceForHide($reference)
    {
        return SellOrderDetailModel::leftJoin('stockitem', 'stockitem.id' ,'=', 'sell_order_detail.stockitemid')
            ->leftJoin('unit as sell_unit', 'sell_unit.id', '=', 'sell_order_detail.unitid')
            ->select(
                'sell_order_detail.*',
                'stockitem.id as stockitemid',
                'stockitem.name',
                'stockitem.code',
                'stockitem.unitconverter',
                'stockitem.unitconverter1',
                'stockitem.unitconverterto',
                'stockitem.unitid as stockunit',
                'sell_unit.name as sellunitname'
            )
            ->where('sell_order_detail.reference', $reference)
            ->get();
    }

    public function getSellOrder($id)
    {
        return SellOrderModel::leftJoin('contact', 'contact.id', '=', 'sell_order.contactid')
                ->select('sell_order.*', 'contact.name as customername')
                ->where('reference', $id)->first();
    }

    public function checkupdatecheckoutquantity($data, $quantity, $price, $itemids, $reference, $unit) {
        // Use the reference to get the existing records
        $existingRecords = SellOrderDetailModel::where('reference', $reference)->get();
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
            $delete_transaction = SellOrderDetailModel::where('reference', $reference)->where('stockitemid', $itemid)->first();
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
     * Delete transactions by reference.
     *
     * @param string $id The reference of the transactions to delete.
     * @return void
     */
    public function delete($id)
    {
        $record =  SellOrderDetailModel::where('reference', $id)->get();
        if ($record) {
            $record->each->delete();
        }
        $order = SellOrderModel::where('reference', $id)->first();
        $payment_type = $order->payment_type;
        $pre_order = $order->pre_order;
        $date = $order->selldate;
        SellOrderModel::where('reference', $id)->delete();
        if ($pre_order) {
            $this->resetShowReferenceNum("pre_order", $date);
        } else {
            $this->resetShowReferenceNum($payment_type, $date);
        }
    }

    public function getByRef($ref){
        return SellOrderDetailModel::leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'sell_order_detail.contactid')
        ->leftJoin('unit', 'unit.id', '=', 'sell_order_detail.unitid')
        ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
        ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
        ->select(
            'sell_order_detail.*',
            'stockitem.name',
            'stockitem.unitid as stockitem_unitid',
            'stockitem.code',
            'contact.name as supplier',
            'contact.email as supplieremail',
            'contact.company as suppliercompany',
            'stock_base_unit.name as base_unit_name',
            'stock_converted_unit.name as converted_unit_name',
            DB::raw('CASE
                WHEN wh_sell_order_detail.unitid = wh_stockitem.unitid THEN
                        (wh_sell_order_detail.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter)
                    ELSE
                        (wh_sell_order_detail.quantity * wh_stockitem.unitconverter / wh_stockitem.unitconverter1)
                END as converted_quantity')
        )
        ->where('sell_order_detail.reference', $ref)
        ->groupBy('stockitem.code')
        ->get();
    }

    public function getOrderByRef($ref) {
        return SellOrderModel::where('reference', $ref)->first();
    }

    public function getOrderById($id) {
        return SellOrderModel::find($id);
    }

    public function getSellDetail($filter)
    {
        $query = SellOrderDetailModel::leftJoin('stockitem', 'stockitem.id' ,'=', 'sell_order_detail.stockitemid')
                ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
                ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
                ->leftJoin('category', 'category.id', '=', 'stockitem.categoryid')
                ->leftJoin('contact', 'contact.id', '=', 'sell_order_detail.contactid')
                ->leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
                ->leftJoin('users', 'users.id', '=', 'sell_order.creator')
                ->select(
                    'sell_order.discount as total_discount',
                    'sell_order.show_reference as show_reference',
                    'sell_order.discount_type',
                    'sell_order.created_at as order_created_at',
                    'sell_order_detail.*',
                    'stockitem.id as stockitemid',
                    'stockitem.name',
                    'stockitem.code',
                    'stockitem.size',
                    'stockitem.itemsubtype',
                    'stockitem.vat',
                    'stockitem.unitconverter',
                    'stockitem.unitconverter1',
                    'stockitem.quantity as stock_qty',
                    'stockitem.unitid as stock_unitid',
                    'stock_base_unit.name as base_unit_name',
                    'stock_converted_unit.name as converted_unit_name',
                    'category.name as category_name',
                    'contact.name as contact_name',
                    'contact.email as contact_email',
                    'contact.phone as contact_phone',
                    'contact.address as contact_address',
                    'users.name as creator',
                    'users.email as creator_email',
                )
                ->orderBy('sell_order.selldate','DESC')
                ->orderBy('sell_order.created_at','DESC');
        // Check if filter has startdate and enddate
        if (!empty($filter['startdate']) && !empty($filter['enddate'])) {
            $startDate = $filter['startdate'];
            $endDate = $filter['enddate'];

            $query->whereBetween('sell_order.selldate', [$startDate, $endDate]);
        }
        if (!empty($filter['references'])) {
            $query->whereIn('sell_order.reference', $filter['references']);
        }
        return $query->get();
    }

    public function getPriceHistory($id)
    {
        return SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
            ->leftJoin('users', 'users.id', '=', 'sell_order.creator')
            ->leftJoin('unit', 'unit.id', '=', 'sell_order_detail.unitid')
            ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
            ->select(
                'sell_order.reference',
                'sell_order.show_reference',
                'stockitem.unitconverter',
                'stockitem.unitconverter1',
                'stockitem.unitconverterto',
                'stockitem.unitid as stockunitid',
                'users.name as creator',
                'sell_order_detail.price',
                'sell_order_detail.updated_at',
                'sell_order_detail.unitid',
                'unit.name as sell_unit_name',
            )
            ->where('sell_order_detail.stockitemid', '=', $id)
            // ->where('sell_order.confirmed', true)
            ->orderBy('sell_order_detail.updated_at', 'desc')
            ->get();

    }


    /**
     * Get total count of items for different time periods and status.
     *
     * @param int $status The status of the transactions.
     * @return array
     */
    public function totalallitem(){
        $overallCount = SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
        ->select(DB::raw('count(wh_sell_order_detail.id) as total'))
        ->where('sell_order.confirmed', 1)
        ->first();

        $yearCount = SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
            ->select(DB::raw('count(wh_sell_order_detail.id) as total'))
            ->whereYear('sell_order.selldate',date('Y'))
            ->where('sell_order.confirmed', 1)
            ->first();

        $monthCount = SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
            ->select(DB::raw('count(wh_sell_order_detail.id) as total'))
            ->whereMonth('sell_order.selldate',date('m'))
            ->whereYear('sell_order.selldate',date('Y'))
            ->where('sell_order.confirmed', 1)
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
    public function monthlydata(){
        // Group by month and get the sum of records for each month
        return SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
        ->whereYear('sell_order_detail.selldate',date('Y'))
        ->where('sell_order.confirmed', 1)
        ->get("sell_order_detail.*")
        ->groupBy(function($date) {
            return Carbon::parse($date->selldate)->format('m');
        })
        ->map(function($group) {
            return $group->count();
        });
    }

    public function gettopstockbysale()
    {
        // Get the total count of items for each warehouse
        $data = SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
            ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
            ->select(
                'stockitem.id',
                'stockitem.name',
                'stockitem.unitconverterto',
                'stockitem.unitid as stockunitid',
                DB::raw('SUM(wh_sell_order_detail.quantity) as quantity')
            )
            ->where('sell_order.confirmed', true)
            ->groupBy('stockitem.id')
            ->orderByDesc('quantity')
            ->take(10)
            ->get(['id', 'name', 'quantity']);

        return $data;
    }

    public function getLastRef()
    {
        $lastReference = SellOrderModel::whereDate('selldate', date('Y-m-d'))
                ->orderBy('created_at', 'desc')
                ->value('show_reference');
        $lastNumber = 0;
        if ($lastReference) {
            $lastNumber = (int) substr($lastReference, -3);
        }

        return $lastNumber;

    }

    /**
     * Get check-out transactions for reporting purposes.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getcheckoutreport()
    {
        return SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
        ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'sell_order_detail.contactid')
        ->leftJoin('category', 'stockitem.categoryid', '=', 'category.code')
        ->leftJoin('unit', 'unit.id', '=', 'sell_order_detail.unitid')
        ->select('stockitem.name','contact.name as customer','category.name as category', 'sell_order_detail.*',DB::raw('SUM(wh_sell_order_detail.quantity) as totalquantity'),'stockitem.unitconverter1','stockitem.unitconverter','stockitem.size' ,'stockitem.code','unit.name as unit_name')
        ->where('sell_order.confirmed', true)
        ->groupBy('sell_order_detail.reference')
        ->orderBy('sell_order_detail.selldate','DESC');
    }

    public function getNewShowReference($payment_type, $date)
    {
        if ($payment_type != 'pre_order')
            $sell_orders = SellOrderModel::where('selldate', '=', $date)->where('payment_type', '=', $payment_type)->where('pre_order', '=', false)->get();
        else
            $sell_orders = SellOrderModel::where('selldate', '=', $date)->where('pre_order', '=', true)->get();
        $prefix = $this->setShowRefPrefix($payment_type);
        $new_show_ref =  $prefix . "/" . date('d/m/Y', strtotime($date)). "/" . sprintf('%04d', count($sell_orders) + 1);
        return $new_show_ref;
    }

    public function getUpdatedShowRef($payment_type, $date, $id)
    {
        $order = SellOrderModel::find($id);
        if ($payment_type != 'pre_order')
            $sell_orders = SellOrderModel::where('selldate', '=', $date)->where('payment_type', '=', $payment_type)->where('pre_order', '=', false)->where("id", "<", $id)->get();
        else
            $sell_orders = SellOrderModel::where('selldate', '=', $date)->where('pre_order', '=', true)->where("id", "<", $id)->get();
        $prefix = $this->setShowRefPrefix($payment_type);
        $updated_show_ref =  $prefix . "/" . date('d/m/Y', strtotime($order->selldate)). "/" . sprintf('%04d', count($sell_orders) + 1);
        return $updated_show_ref;
    }

    public function resetShowReferenceNum($payment_type, $date)
    {
        if ($payment_type != 'pre_order')
            $sell_orders = SellOrderModel::where('selldate', '=', $date)->where('payment_type', '=', $payment_type)->where('pre_order', '=', false)->where('hidden', false)->orderBy('id')->get();
        else
            $sell_orders = SellOrderModel::where('selldate', '=', $date)->where('pre_order', '=', true)->where('hidden', false)->orderBy('id')->get();
        foreach($sell_orders as $index => $sell_order) {
            if ($sell_order->show_reference != "") {
                $array = explode("/", $sell_order->show_reference);
                $sequence = $array[count($array) - 1];
                $prefix = $array[0];
                $new_prefix = $this->setShowRefPrefix($payment_type);
                $sell_order->show_reference = str_replace($sequence, sprintf('%04d', $index + 1), $sell_order->show_reference);
                if ($payment_type != 'pre_order') {
                    $sell_order->show_reference = str_replace($prefix, $new_prefix, $sell_order->show_reference);
                }
                $sell_order->save();
            }
        }
    }

    private function setShowRefPrefix($payment_type)
    {
        $prefix = "";
        if ($payment_type == 'bank_transfer') {
            $prefix = "WM";
        } else if ($payment_type == 'cash') {
            $prefix = "WZ";
        } else if ($payment_type == 'pre_order') {
            $prefix = "WP";
        } else if ($payment_type == 'cash_on_delivery') {
            $prefix = "WD";
        }

        return $prefix;
    }

    public function getSumPrice($startTime, $endTime)
    {
        // $timestamp = strtotime(preg_replace('/\\s\\(.*\\)$/', '', $startTime));
        // $start_date = date('Y-m-d H:i:s', $timestamp);

        // $timestamp = strtotime(preg_replace('/\\s\\(.*\\)$/', '', $endTime));
        // $end_date = date('Y-m-d H:i:s', $timestamp);
        $dateObject = DateTime::createFromFormat('d/m/Y', $startTime);
        $start_date = $dateObject->format('Y-m-d');

        $dateObject = DateTime::createFromFormat('d/m/Y', $endTime);
        $end_date = $dateObject->format('Y-m-d');


        return SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
        ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
        ->leftJoin('unit', 'unit.id', '=', 'sell_order_detail.unitid')
        ->groupBy('sell_order.reference')
        ->select(
            'sell_order.discount',
            DB::raw('SUM(wh_sell_order_detail.price * wh_sell_order_detail.quantity) as total_amount'),
            DB::raw('SUM(wh_sell_order_detail.quantity) as total_qty'),
            DB::raw('SUM(
                    CASE
                        WHEN wh_sell_order_detail.unitid = wh_stockitem.unitid and wh_sell_order_detail.unitid=1 THEN
                            wh_sell_order_detail.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter
                                    WHEN wh_sell_order_detail.unitid != wh_stockitem.unitid and wh_sell_order_detail.unitid=1 THEN
                            wh_sell_order_detail.quantity * wh_stockitem.unitconverter / wh_stockitem.unitconverter1
                        ELSE
                            wh_sell_order_detail.quantity
                    END
                ) AS carton_qty'),
            DB::raw('SUM(
                CASE
                    WHEN wh_sell_order_detail.unitid = wh_stockitem.unitid and wh_sell_order_detail.unitid=2 THEN
                        wh_sell_order_detail.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter
                                WHEN wh_sell_order_detail.unitid != wh_stockitem.unitid and wh_sell_order_detail.unitid=2 THEN
                        wh_sell_order_detail.quantity * wh_stockitem.unitconverter / wh_stockitem.unitconverter1
                    ELSE
                        wh_sell_order_detail.quantity
                END
            ) AS pair_qty'),

        )
        ->whereBetween('sell_order.selldate', [$start_date, $end_date])
        ->get();
    }
}

