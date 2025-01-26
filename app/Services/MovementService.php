<?php
// app/Services/TransactionService.php

namespace App\Services;

use App\Models\StockItemModel;
use App\Models\MovementModel;
use App\Models\MovementOrderModel;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MovementService
{   

    /**
     * Get all transactions.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getAll()
    {
        return MovementModel::all();
    }

    /**
     * Get check-in transactions with additional details.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getList()
    {
        return MovementModel::leftJoin('stockitem', 'stockitem.id', '=', 'movement.stockitemid')
            ->leftJoin('warehouse as source_warehouse', 'source_warehouse.id', '=', 'movement.source_warehouse_id')
            ->leftJoin('warehouse as target_warehouse', 'target_warehouse.id', '=', 'movement.target_warehouse_id')
            ->leftJoin('unit', 'unit.id', '=', 'movement.unitid')
            ->select(
                'stockitem.name',
                'movement.*',
                'unit.name as unit_name',
                DB::raw('SUM(wh_movement.quantity) as total_quantity'),
                DB::raw('SUM(wh_movement.quantity * wh_movement.price) as total_price'),
                'source_warehouse.name as source_warehouse_name',
                'target_warehouse.name as target_warehouse_name'
            )
            ->where('creator', Auth::user()->id)
            ->groupBy('movement.reference')
            ->orderBy('movement.movement_date', 'DESC')
            ->orderBy('movement.created_at', 'DESC');
    }

    public function createOrder($data, $itemids, $quantity, $unit, $price)
    {
        $count = count($itemids);
        for ($i = 0; $i < $count; $i++) {
            $itemid = $itemids[$i];
            $stockItem = StockItemModel::where('id', $itemid)->first();
            if(!$stockItem) continue;
            // Rest of your code
            $data1 = MovementModel::create([
                'stockitemid' => $itemid,
                'code' => $stockItem->code,
                'source_warehouse_id' => $data['source_warehouse_id'],
                'target_warehouse_id' => $data['target_warehouse_id'],
                'reference' => $data['reference'],
                'movement_date' => $data['movement_date'],
                'price' => $stockItem->price,
                'quantity' => $quantity[$i],
                'unitid' => $unit[$i],
                'description' => $data['description'],
                'creator' => Auth::user()->id
            ]);
        }
        MovementOrderModel::create([
            'source_warehouse_id' => $data['source_warehouse_id'],
            'target_warehouse_id' => $data['target_warehouse_id'],
            'reference' => $data['reference'],
            'movement_date' => $data['movement_date'],
            'description' => $data['description'],
            'creator' => Auth::user()->id
        ]);

        return $data1;
    }

    public function updateOrder($data, $itemids, $quantity, $unit, $reference) 
    {
        // Use the reference to get the existing records
        $existingRecords = MovementModel::where('reference', $reference)->get();
        $existingOrderRecord = MovementOrderModel::where('reference', $reference)->first();
        // Create an associative array to map existing records by stockitemid
        $existingRecordsMap = $existingRecords->keyBy('stockitemid');

        // Identify items to delete
        $itemsToDelete = $existingRecords->pluck('stockitemid')->diff($itemids);

        // Delete records for items that are no longer present
        MovementModel::where('reference', $reference)->whereIn('stockitemid', $itemsToDelete)->delete();
        // Loop through each item and update or add as needed
        foreach ($itemids as $i => $itemid) {
            $singleQuantity = $quantity[$i];
            $stockItem = StockItemModel::where('id', $itemid)->first();
            if(!$stockItem) continue;
            // Check if the item exists in the existing records
            if ($existingRecord = $existingRecordsMap->get($itemid)) {
                // If it exists, update the existing record
                $existingRecord->update([
                    'stockitemid' => $itemid,
                    'code' => $stockItem->code,
                    'source_warehouse_id' => $data['source_warehouse_id'],
                    'target_warehouse_id' => $data['target_warehouse_id'],
                    'reference' => $data['reference'],
                    'movement_date' => $data['movement_date'],
                    'price' => $stockItem->price,
                    'quantity' => $quantity[$i],
                    'unitid' => $unit[$i],
                    'description' => $data['description'],
                    'creator' => Auth::user()->id
                ]);
            } else {
                // If it doesn't exist, add a new record
                $data1 = MovementModel::create([
                    'stockitemid' => $itemid,
                    'code' => $stockItem->code,
                    'source_warehouse_id' => $data['source_warehouse_id'],
                    'target_warehouse_id' => $data['target_warehouse_id'],
                    'reference' => $data['reference'],
                    'movement_date' => $data['movement_date'],
                    'price' => $stockItem->price,
                    'quantity' => $quantity[$i],
                    'unitid' => $unit[$i],
                    'description' => $data['description'],
                    'creator' => Auth::user()->id
                ]);
            }
        }
        $existingOrderRecord->update([
            'source_warehouse_id' => $data['source_warehouse_id'],
            'target_warehouse_id' => $data['target_warehouse_id'],
            'reference' => $data['reference'],
            'movement_date' => $data['movement_date'],
            'description' => $data['description'],
            'creator' => Auth::user()->id
        ]);
        return true;
    }

    public function getMovementByReference($reference)
    {
        return MovementModel::leftJoin('stockitem', 'stockitem.id' ,'=', 'movement.stockitemid')
                ->leftJoin('unit', 'unit.id', '=', 'movement.unitid')
                ->leftJoin('warehouse as source_warehouse', 'source_warehouse.id', '=', 'movement.source_warehouse_id')
                ->leftJoin('warehouse as target_warehouse', 'target_warehouse.id', '=', 'movement.target_warehouse_id')
                ->select(
                    'stockitem.id as stockitemid', 
                    'stockitem.name', 
                    'stockitem.code', 
                    'stockitem.price as stock_price', 
                    'stockitem.unitconverter', 
                    'stockitem.unitconverter1', 
                    'stockitem.unitconverterto', 
                    'stockitem.unitid as stockunit', 
                    'movement.*', 
                    'unit.name as movement_unitname',
                    'source_warehouse.name as source_warehouse_name',
                    'target_warehouse.name as target_warehouse_name'
                )
                ->where('movement.reference', $reference)
                ->get();
    }
    
    /**
     * Update a stock item.
     *
     * @param int $id The ID of the stock item to update.
     * @param array $data The updated data for the stock item.
     * @param mixed $image The updated image for the stock item.
     * @return \App\Models\StockItemModel
     */
    public function updatestock($itemData, $status){
        for ($i = 0; $i < count($itemData); $i++) {
            $item = $itemData[$i];
            $code = $item->code;
            $stockitemid = $item->stockitemid;
            $newQuantity = $item->quantity;
            $newUnitid = $item->unitid;

            $newSignleQuantity = $item->quantity;

            // caculate the real quantity for product unit regarding to the transaction unit
            $sourceStockItem = StockItemModel::where('warehouseid', $item->source_warehouse_id)->where('code', $code)->first();
            $targetStockItem = StockItemModel::where('warehouseid', $item->target_warehouse_id)->where('code', $code)->first();

            if (empty($targetStockItem) && !empty($sourceStockItem)) {
                // Create a new StockItem instance
                $targetStockItem = new StockItemModel();
                $data = $sourceStockItem->toArray();
                $data['warehouseid'] = $item->target_warehouse_id;
                $data['quantity'] = 0;
                $data['single_quantity'] = 0;
                $targetStockItem->fill($data);
                $targetStockItem->save();
                $targetStockItem = StockItemModel::where('warehouseid', $item->target_warehouse_id)->where('code', $code)->first();
            }
            
            if ($sourceStockItem->unitid != $newUnitid) {
                $newQuantity = $newQuantity * $sourceStockItem->unitconverter / $sourceStockItem->unitconverter1;
            }
            
            if($sourceStockItem->unitconverter > $sourceStockItem->unitconverter1 && $targetStockItem->unitid != $newUnitid){
                $newSignleQuantity = $newSignleQuantity * $sourceStockItem->unitconverter / $sourceStockItem->unitconverter1;
            }else if($sourceStockItem->unitconverter < $sourceStockItem->unitconverter1 && $targetStockItem->unitid == $newUnitid){
                $newSignleQuantity = $newSignleQuantity * $sourceStockItem->unitconverter1 / $sourceStockItem->unitconverter;
            }
            $updatedQuantity = $targetStockItem->quantity + $newQuantity * $status;
            $updatedSQuantity = $targetStockItem->single_quantity + $newSignleQuantity * $status;

            if($status == 1)                
                StockItemModel::where('code', $code)->where('warehouseid', $item->target_warehouse_id)->update([
                    'quantity' => $targetStockItem->quantity + $newQuantity * $status, 
                    'single_quantity' => $targetStockItem->single_quantity + $newSignleQuantity * $status, 
                    'purchase_price'=>$sourceStockItem->purchase_price, 
                    'contactid'=>$sourceStockItem->contactid
                ]);
            else
            StockItemModel::where('code', $code)->where('warehouseid', $item->target_warehouse_id)->update([
                'quantity' => $targetStockItem->quantity + $newQuantity * $status, 
                'single_quantity' => $targetStockItem->single_quantity + $newSignleQuantity * $status
            ]);
            StockItemModel::where('code', $code)->where('warehouseid', $item->source_warehouse_id)->update([
                'quantity' => $sourceStockItem->quantity - $newQuantity * $status, 
                'single_quantity' => $sourceStockItem->single_quantity - $newSignleQuantity * $status
            ]);
        }
    }

    /**
     * Check if a transaction with the given code and ID exists.
     *
     * @param string $code The code to check.
     * @param int $Id The ID to exclude from the check.
     * @return bool
     */
    public function CheckCodeId($code, $Id){
        return  MovementModel::where('reference', $code)->where('id', '!=', $Id)->exists();
    }

    /**
     * Check if a transaction with the given code and ID exists.
     *
     * @param string $code The code to check.
     * @return bool
     */
    public function CheckCode($code){
       
        return MovementModel::where('reference', $code)->exists();
       
    }


    /**
     * Delete transactions by reference.
     *
     * @param string $id The reference of the transactions to delete.
     * @return void
     */
    public function delete($id)
    {
        $record =  MovementModel::where('reference', $id)->get();
        if ($record) {
            $record->each->delete();
        }
        MovementOrderModel::where('reference', $id)->delete();
    }
    /**
     * Check if a transaction with the given warehouse ID exists.
     *
     * @param int $id The warehouse ID.
     * @return bool
     */
    public function getLastRef()
    {
        $lastReference = MovementOrderModel::whereDate('created_at', date('Y-m-d'))
                ->orderBy('created_at', 'desc')
                ->value('reference');
        $lastNumber = 0;
        if ($lastReference) {
            $lastNumber = (int) substr($lastReference, -3);
        }       
            
        return $lastNumber;
    
    }
}