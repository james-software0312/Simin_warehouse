<?php
// app/Services/HiddenService.php

namespace App\Services;

use App\Models\SellOrderModel;
use App\Models\SellOrderDetailModel;
use App\Models\SellHideHistoryModel;
use App\Models\StockItemModel;
use App\Models\TransactionOrderModel;
use App\Models\TransactionModel;
use App\Models\MovementModel;
use App\Models\ActivityLogModel;
use App\Services\SellService;
use DB;
use Auth;
use Carbon\Carbon;
use DateTime;

class HiddenService
{
    protected $sellService;
    public function __construct(SellService $sellService)
    {
        $this->sellService          = $sellService;
    }
    public function getcheckout()
    {
        return SellOrderModel::leftJoin('contact', 'contact.id', '=', 'sell_order.contactid')
        ->select('contact.name as supplier', 'sell_order.*')
        ->where('hidden', false)
        ->where(function($query) {
            $query->whereDate('selldate', Carbon::today())
                  ->orWhere(function($query) {
                      $query->whereDate('selldate', '<', Carbon::today())
                            ->where('withinvoice', true);
                  });
        })
        ->orderBy('sell_order.selldate','DESC');
    }

    public function getcheckinOrders()
    {
        return TransactionModel::leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->select('contact.name as supplier', 'transaction.*')
        ->where('transaction.status', 1)
        ->whereRaw('transaction.quantity - transaction.hidden_amount != 0')
        ->groupBy('transaction.reference')
        ->orderBy('transaction.transactiondate','DESC')
        ->get();
    }

    public function getStockItemHistory($id)
    {
        $stockItem = StockItemModel::where('id', $id)->first();
        $purchases = TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
            ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
            ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
            ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
            ->select(
                'transaction.id',
                'transaction.reference',
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
            // ->where('transaction.stockitemid', $id);
            ->where('stockitem.code', $stockItem->code)
            ->where('transaction.warehouseid', $stockItem->warehouseid);
            // dd($purchases);

        $sales = SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
            ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
            ->leftJoin('contact', 'contact.id', '=', 'sell_order_detail.contactid')
            ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
            ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
            ->select('sell_order_detail.id',
                'sell_order_detail.reference',
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
            // ->where('sell_order_detail.stockitemid', $id)
            ->where('stockitem.code', $stockItem->code)
            ->where('sell_order_detail.warehouseid', $stockItem->warehouseid)
            ->where('sell_order.hidden', false)
            ->where('sell_order.confirmed', true);

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
                DB::raw("'movement' as type"),
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
        $history = $purchases->union($sales)->union($movements)
            ->orderBy('date', 'DESC')
            ->orderBy('created_at', 'DESC');
        return $history;
    }

    public function hideSell($reference, $select_purchases)
    {
        $sell = SellOrderModel::where("reference", $reference)->first();
        $sell->hidden = true;
        $sell->confirmed = true;
        $sell->show_reference = "";
        $sell->save();
        // remove related acitivity logs.
        ActivityLogModel::where('properties', 'like', '%' . $reference . '%')->where('log_name', 'sell_order')->delete();
        if ($sell->pre_order) {
            $this->sellService->resetShowReferenceNum('pre_order', $sell->selldate);
        } else {
            $this->sellService->resetShowReferenceNum($sell->payment_type, $sell->selldate);
        }
        foreach($select_purchases as $purchase) {
            SellHideHistoryModel::create([
                'sell_reference' => $reference,
                'purchase_transaction_id' => $purchase['transactionid'],
                'hidden_amount' => $purchase['new_hidden']
            ]);
        }
    }

    public function getHiddenHistory($reference)
    {
        return SellHideHistoryModel::leftJoin("transaction", 'transaction.id' , '=', 'sell_hide_history.purchase_transaction_id')
                ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
                ->leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
                ->leftJoin('unit', 'unit.id', '=', 'transaction.unitid')
                ->select('transaction.reference', 'transaction.price', 'sell_hide_history.*', 'contact.name as supplier', 'unit.name as transactionunitname', 'stockitem.name as itemname', 'stockitem.code')
                ->where('sell_hide_history.sell_reference', $reference)
                ->get();
    }

    public function checkAvailableHide($reference)
    {
        $sale_order_items = SellOrderDetailModel::leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
                ->select(
                    'stockitem.unitid as stock_unitid',
                    'stockitem.unitconverter',
                    'stockitem.unitconverter1',
                    'stockitem.unitconverterto',
                    'sell_order_detail.unitid as order_unitid',
                    'sell_order_detail.quantity',
                )
                ->where('reference', $reference)->get();

    }

        /**
     * Delete transactions by reference.
     *
     * @param string $id The reference of the transactions to delete.
     * @return void
     */
    public function deletehiddenHistory($id)
    {
        $hiddenItems =  SellHideHistoryModel::where('sell_reference', $id)->get();
        foreach($hiddenItems as $item){
            TransactionModel::where('id', $item->purchase_transaction_id)->update([
                'hidden_amount' => \DB::raw('hidden_amount - ' . $item->hidden_amount),
                'quantity' => \DB::raw('quantity - ' . $item->hidden_amount),
            ]);
            $item->delete();
        }
    }


    public function getcheckouthiddenreport() {
        return SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
        ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'sell_order_detail.contactid')
        ->leftJoin('category', 'stockitem.categoryid', '=', 'category.id')
        ->leftJoin('unit', 'unit.id', '=', 'sell_order_detail.unitid')
        ->leftJoin('transaction', 'transaction.stockitemid', '=','sell_order_detail.stockitemid')
        ->leftJoin('users', 'sell_order.creator', '=', 'users.id')
        ->select('stockitem.name','contact.name as customer',
            'stockitem.categoryid',
            'category.name as category',
            'users.name as user',
            'sell_order_detail.*',
            // DB::raw('SUM(wh_sell_order_detail.quantity) as totalquantity'),
            'stockitem.unitconverter1','stockitem.unitconverter','stockitem.size' ,'stockitem.code','unit.name as unit_name')
        ->where('sell_order.confirmed', true)
        ->where('sell_order.hidden', false)
        // ->groupBy('sell_order_detail.reference')
        ->orderBy('sell_order_detail.selldate','DESC');
    }

    public function getcheckinhiddenreport() {
        return TransactionModel::leftJoin('stockitem', 'stockitem.id', '=', 'transaction.stockitemid')
        ->leftJoin('contact', 'contact.id', '=', 'transaction.contactid')
        ->leftJoin('unit as stock_base_unit', 'stock_base_unit.id', '=', 'stockitem.unitid')
        ->leftJoin('unit as stock_converted_unit', 'stock_converted_unit.id', '=', 'stockitem.unitconverterto')
        ->leftJoin('category', 'stockitem.categoryid', '=', 'category.id')
        ->leftJoin('users', 'users.id', '=', 'transaction.creator')
        ->select(
            'transaction.*',
            DB::raw('(wh_transaction.quantity - wh_transaction.hidden_amount) as available_quantity'),
            'stockitem.name',
            'stockitem.code',
            'stockitem.categoryid',
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
        ->whereRaw('wh_transaction.quantity - wh_transaction.hidden_amount != 0')
        ->orderBy('transaction.transactiondate','DESC')
        ->orderBy('transaction.created_at','DESC');
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
                'stockitem.quantity',
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
            ->where('sell_order.hidden', '=', 0)
            // ->where('sell_order.confirmed', true)
            ->orderBy('sell_order_detail.updated_at', 'desc')
            ->get();

    }

    public function getSumPrice($startTime, $endTime)
    {
        $dateObject = DateTime::createFromFormat('d/m/Y', $startTime);
        $start_date = $dateObject->format('Y-m-d');

        $dateObject = DateTime::createFromFormat('d/m/Y', $endTime);
        $end_date = $dateObject->format('Y-m-d');


        return SellOrderDetailModel::leftJoin('sell_order', 'sell_order.reference', '=', 'sell_order_detail.reference')
        ->leftJoin('stockitem', 'stockitem.id', '=', 'sell_order_detail.stockitemid')
        ->leftJoin('unit', 'unit.id', '=', 'sell_order_detail.unitid')
        // ->groupBy('sell_order.reference')
        ->select(
            'sell_order.reference',
            'sell_order.discount',
            'sell_order_detail.price',
            'sell_order_detail.quantity',
            'stockitem.unitconverter',
        )
        ->where('sell_order.hidden', false)
        ->whereBetween('sell_order.selldate', [$start_date, $end_date])
        ->get();
    }
}
