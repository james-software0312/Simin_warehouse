<?php
// app/Services/StockItemService.php

namespace App\Services;

use App\Models\SHProductInventoryModel;
use App\Models\StockItemModel;
use App\Models\StockItemPriceHistoryModel;
use App\Models\WarehouseModel;
use App\Models\CategoryModel;
use App\Models\SHMediaUploadModel;
use App\Models\SHProductModel;
use App\Models\TransactionModel;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;

class StockItemService
{
    /**
     * Get all stock item data.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        // Query to get all stock item data with joins
        return StockItemModel::leftJoin(DB::raw('sh_product_categories'), 'stockitem.categoryid', '=', DB::raw('sh_product_categories.id'))
        ->leftJoin(DB::raw('sh_media_uploads'), DB::raw('sh_media_uploads.id'), '=', 'stockitem.photo')
        ->leftJoin('contact', 'contact.id', '=', 'stockitem.contactid')
        ->leftJoin('unit as base_unit', 'base_unit.id', '=', 'stockitem.unitid')
        ->leftJoin('unit as converted_unit', 'converted_unit.id', '=', 'stockitem.unitconverterto')
        ->leftJoin('warehouse', 'stockitem.warehouseid', '=', 'warehouse.id')
        ->select(
            'stockitem.*',
            'contact.name as suppiler',
            'base_unit.name as base_unit_name',
            'converted_unit.name as converted_unit_name',
            'warehouse.name as warehouse',
            DB::raw('sh_product_categories.title as category'),
            DB::raw('(wh_stockitem.quantity * wh_stockitem.unitconverter1 / wh_stockitem.unitconverter) as converted_quantity'),
            DB::raw('sh_media_uploads.path as image_path')
        );
    }

    /**
     * Get the total count of stock items.
     *
     * @return int
     */
    public function totalitem()
    {
        return StockItemModel::where('is_delete', false)->count();
    }

    /**
     * Get total count of stock items categorized by Overall, Year, and Month.
     *
     * @return array
     */
    public function totalallitem()
    {
        // Count overall, yearly, and monthly stock items
        $overallCount = StockItemModel::where('is_delete', false)->count();
        $yearCount = StockItemModel::select(DB::raw('count(*) as total'))
            ->whereYear('created_at', date('Y'))
            ->where('is_delete', false)
            ->first();
        $monthCount = StockItemModel::select(DB::raw('count(*) as total'))
            ->whereMonth('created_at', date('m'))
            ->whereYear('created_at', date('Y'))
            ->where('is_delete', false)
            ->first();

        $res['Overall'] = $overallCount;
        $res['Year'] = $yearCount->total;
        $res['Month'] = $monthCount->total;

        return $res;
    }


    /**
     * Get monthly data of stock items.
     *
     * @return \Illuminate\Support\Collection
     */
    public function monthlydata()
    {
        // Group by month and get the sum of records for each month
        return StockItemModel::whereYear('created_at', date('Y'))
            ->where('is_delete', false)
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            })
            ->map(function ($group) {
                return $group->count();
            });
    }
    /**
     * Create a new stock item.
     *
     * @param array $data The data for creating a new stock item.
     * @param mixed $image The image for the stock item.
     * @return \App\Models\StockItemModel
     */
    public function create($data, $image)
    {
        // dd($data['color']);
        // Create a new stock item with data and image
        $unique_name = StockItemModel::where('name', $data['name'])->first();
        if($unique_name) {
            return [
                'success' => false,
                'message' => 'Product name must unique',
            ];
        };
        $post = new StockItemModel();
        $post->fill($data);
        // save on website product table
        $insert_sh_product_data = [
            'title' => $data['name'],
            'description' => $data['description'],
            'category_id' => $data['categoryid'],
            'price' => $data['price'],
            'sale_price' => $data['price'],
            'slug' => Str::slug($data['name']),
            'color' => $data['color'],
            'size' => $data['size'],
            'status' => ($data['is_visible'] == 1) ? 'publish' : 'draft'
        ];

        if ($image) {
            $image_dimension = getimagesize($image);
            $image_width = $image_dimension[0];
            $image_height = $image_dimension[1];
            // $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
            $image_dimension_for_db = 1200 . ' x ' . '1600 pixels';
            $image_size_for_db = $image->getSize();
            $image_extenstion = $image->getClientOriginalExtension();
            $image_name_with_ext = $image->getClientOriginalName();

            $image_name = pathinfo($image_name_with_ext, PATHINFO_FILENAME);
            $image_name = strtolower(Str::slug($image_name));

            $image_db = $image_name . time() . '.' . $image_extenstion;
            $image_grid = 'grid-' . $image_db;
            $image_large = 'large-' . $image_db;
            $image_thumb = 'thumb-' . $image_db;
            $image_p_grid = 'product-' . $image_db;

            $folder_path = base_path(env('MEDIA_UPLOADER_PATH'));
            // dd($image_name_with_ext);
            $imageInst = Image::read($image);
            $resize_large_image = $imageInst->resize(width: 150, height: 150 );
            $resize_grid_image = $imageInst->resize(width: 350, height: 466.67);
            $resize_p_grid_image = $imageInst->resize(width: 230, height: 306.67);
            $resize_thumb_image = $imageInst->resize(width: 740, height: 740);
            $image->move($folder_path, $image_db);
            // $imageInst =$imageInst->resize(width: 262.50, height:300);
            // $imageInst->save($folder_path. $image_db);
            $newMediaUpload = SHMediaUploadModel::create([
                'title' => $image_name_with_ext,
                'size' => formatBytes($image_size_for_db),
                'path' => $image_db,
                'dimensions' => $image_dimension_for_db,
                'user_id' => Auth::user()->id
            ]);

            if ($image_width > 150) {
                $resize_large_image->save($folder_path . $image_large);
                $resize_thumb_image->save($folder_path . $image_thumb);
                $resize_grid_image->save($folder_path . $image_grid);
                $resize_p_grid_image->save($folder_path . $image_p_grid);
            }
            $post->photo = $newMediaUpload->id;
            $insert_sh_product_data['image'] = $newMediaUpload->id;
        }

        // if ($image) {
        //     $image_dimension = getimagesize($image);
        //     $image_width = $image_dimension[0];
        //     $image_height = $image_dimension[1];
        //     $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
        //     $image_size_for_db = $image->getSize();

        //     $image_extenstion = $image->getClientOriginalExtension();
        //     $image_name_with_ext = $image->getClientOriginalName();

        //     $image_name = pathinfo($image_name_with_ext, PATHINFO_FILENAME);
        //     $image_name = strtolower(Str::slug($image_name));

        //     $image_db = $image_name . time() . '.' . $image_extenstion;
        //     $image_grid = 'grid-' . $image_db;
        //     $image_large = 'large-' . $image_db;
        //     $image_thumb = 'thumb-' . $image_db;
        //     $image_p_grid = 'product-' . $image_db;

        //     $folder_path = base_path(env('MEDIA_UPLOADER_PATH'));
        //     $imageInst = Image::read($image);
        //     $resize_large_image = $imageInst->resize(width: 740);
        //     $resize_grid_image = $imageInst->resize(width: 350);
        //     $resize_p_grid_image = $imageInst->resize(width: 230);
        //     $resize_thumb_image = $imageInst->resize(width: 150, height: 150);
        //     $image->move($folder_path, $image_db);
        //     $newMediaUpload = SHMediaUploadModel::create([
        //         'title' => $image_name_with_ext,
        //         'size' => formatBytes($image_size_for_db),
        //         'path' => $image_db,
        //         'dimensions' => $image_dimension_for_db,
        //         'user_id' => Auth::user()->id
        //     ]);

        //     if ($image_width > 150) {
        //         $resize_thumb_image->save($folder_path . $image_thumb);
        //         $resize_grid_image->save($folder_path . $image_grid);
        //         $resize_large_image->save($folder_path . $image_large);
        //         $resize_p_grid_image->save($folder_path . $image_p_grid);
        //     }
        //     $post->photo = $newMediaUpload->id;
        //     $insert_sh_product_data['image'] = $newMediaUpload->id;
        // }
        $sh_product = SHProductModel::create($insert_sh_product_data);
        $post->product_id = $sh_product->id;
        $post->save();


        return [
            'success' => true,
            'message' => 'Product created successfully',
            'post' => $post,
        ];
    }


    /**
     * Update a stock item.
     *
     * @param int $id The ID of the stock item to update.
     * @param array $data The updated data for the stock item.
     * @param mixed $image The updated image for the stock item.
     * @return \App\Models\StockItemModel
     */
    public function update($id, $data, $image)
    {
        if ($image) {
            $image_dimension = getimagesize($image);
            $image_width = $image_dimension[0];
            $image_height = $image_dimension[1];
            // $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
            $image_dimension_for_db = 1200 . ' x ' . '1600 pixels';
            $image_size_for_db = $image->getSize();
            $image_extenstion = $image->getClientOriginalExtension();
            $image_name_with_ext = $image->getClientOriginalName();

            $image_name = pathinfo($image_name_with_ext, PATHINFO_FILENAME);
            $image_name = strtolower(Str::slug($image_name));

            $image_db = $image_name . time() . '.' . $image_extenstion;
            $image_grid = 'grid-' . $image_db;
            $image_large = 'large-' . $image_db;
            $image_thumb = 'thumb-' . $image_db;
            $image_p_grid = 'product-' . $image_db;

            $folder_path = base_path(env('MEDIA_UPLOADER_PATH'));
            // dd($image_name_with_ext);
            $imageInst = Image::read($image);
            $resize_large_image = $imageInst->resize(width: 740, height: 986.67 );
            $resize_grid_image = $imageInst->resize(width: 350, height: 466.67);
            $resize_p_grid_image = $imageInst->resize(width: 230, height: 306.67);
            $resize_thumb_image = $imageInst->resize(width: 150 , height: 150);
            $image->move($folder_path, $image_db);
            // $imageInst =$imageInst->resize(width: 262.50, height:300);
            // $imageInst->save($folder_path. $image_db);
            $newMediaUpload = SHMediaUploadModel::create([
                'title' => $image_name_with_ext,
                'size' => formatBytes($image_size_for_db),
                'path' => $image_db,
                'dimensions' => $image_dimension_for_db,
                'user_id' => Auth::user()->id
            ]);

            if ($image_width > 150) {
                $resize_thumb_image->save($folder_path . $image_thumb);
                $resize_grid_image->save($folder_path . $image_grid);
                $resize_large_image->save($folder_path . $image_large);
                $resize_p_grid_image->save($folder_path . $image_p_grid);
            }
        }

        // if ($image) {
        //     $image_dimension = getimagesize($image);
        //     $image_width = $image_dimension[0];
        //     $image_height = $image_dimension[1];
        //     $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
        //     $image_size_for_db = $image->getSize();

        //     $image_extenstion = $image->getClientOriginalExtension();
        //     $image_name_with_ext = $image->getClientOriginalName();

        //     $image_name = pathinfo($image_name_with_ext, PATHINFO_FILENAME);
        //     $image_name = strtolower(Str::slug($image_name));

        //     $image_db = $image_name . time() . '.' . $image_extenstion;
        //     $image_grid = 'grid-' . $image_db;
        //     $image_large = 'large-' . $image_db;
        //     $image_thumb = 'thumb-' . $image_db;
        //     $image_p_grid = 'product-' . $image_db;

        //     $folder_path = base_path(env('MEDIA_UPLOADER_PATH'));
        //     $imageInst = Image::read($image);
        //     $resize_large_image = $imageInst->resize(width: 740);
        //     $resize_grid_image = $imageInst->resize(width: 350);
        //     $resize_p_grid_image = $imageInst->resize(width: 230);
        //     $resize_thumb_image = $imageInst->resize(width: 150, height: 150);
        //     $image->move($folder_path, $image_db);
        //     $newMediaUpload = SHMediaUploadModel::create([
        //         'title' => $image_name_with_ext,
        //         'size' => formatBytes($image_size_for_db),
        //         'path' => $image_db,
        //         'dimensions' => $image_dimension_for_db,
        //         'user_id' => Auth::user()->id
        //     ]);

        //     if ($image_width > 150) {
        //         $resize_thumb_image->save($folder_path . $image_thumb);
        //         $resize_grid_image->save($folder_path . $image_grid);
        //         $resize_large_image->save($folder_path . $image_large);
        //         $resize_p_grid_image->save($folder_path . $image_p_grid);
        //     }
        // }
        // Find the stock item by ID
        $SetData = StockItemModel::findOrFail($id);
        $sh_product = SHProductModel::where('id', $SetData[0]->product_id)->firstOrFail();
        $sh_product->fill($data);
        $sh_product->save();
        if ($data['price'] != $SetData[0]->price) { // update stockitem history
            StockItemPriceHistoryModel::create([
                'stockitem_id' => $id['editid'],
                'creator_id' => Auth::user()->id,
                'price' => $data['price']
            ]);
        }
        $dataUpdate = $SetData->each->update($data);

        // Update the image if provided
        if ($image) {
            $sh_product['image'] = $newMediaUpload->id;
            $sh_product->save();
            // $photoName = time() . '.' . $image->getClientOriginalName();
            // $photoPath = $image->storeAs('public/items', $photoName);
            $dataUpdate->each->update(['photo' => $newMediaUpload->id]);
        }

        return $dataUpdate;
    }

    public function checkstock(){
        return $stockItem = StockItemModel::where('single_quantity', '<', '0')->count() == 0;
    }
    /**
     * Update a stock item.
     *
     * @param int $id The ID of the stock item to update.
     * @param array $data The updated data for the stock item.
     * @param mixed $image The updated image for the stock item.
     * @return \App\Models\StockItemModel
     */
    public function updatestock($itemData, $type, $status){
        for ($i = 0; $i < count($itemData); $i++) {
            $item = $itemData[$i];
            // $inventory = StockItemModel::where('id', $item->stockitemid)->first();

            // SHProductInventoryModel::insert([
            //     'sku' => $inventory->code,
            //     'product_id' => $inventory->product_id,
            //     'stock_count' => $item->quantity*$inventory->unitconverter,
            //     'sold_count' => 0,
            //     'created_at' => now(),
            //     'updated_at' => now()
            // ]);
            $code = $item->code;
            $stockitemid = $item->stockitemid;
            $warehouseid = $item->warehouseid;
            $newQuantity = $item->quantity;
            $newUnitid = $item->unitid;

            $newSignleQuantity = $item->quantity;

            // caculate the real quantity for product unit regarding to the transaction unit
            $stockItem = StockItemModel::where('warehouseid', $warehouseid)->where('code', $code)->first();
            $otherStockItem = StockItemModel::where('code', $item->code)->first();

            if (empty($stockItem) && empty($otherStockItem)) continue;
            if (empty($stockItem) && !empty($otherStockItem)) {
                // Create a new StockItem instance
                $stockItem = new StockItemModel();
                $data = $otherStockItem->toArray();
                $data['warehouseid'] = $warehouseid;
                $data['quantity'] = 0;
                $data['single_quantity'] = 0;
                $stockItem->fill($data);
                $stockItem->save();
                $stockItem = StockItemModel::where('warehouseid', $warehouseid)->where('code', $code)->first();
                TransactionModel::where('warehouseid', $warehouseid)->where('stockitemid', $stockitemid)->update(['stockitemid'=>$stockItem->id]);
            }
            $stockitemunitid = $stockItem->unitid;
            if ($stockItem->unitid != $newUnitid) {
                $newQuantity = $newQuantity ;
                //newQuantity == 100;
            }

            if($stockItem->unitconverter > $stockItem->unitconverter1 && $stockItem->unitid != $newUnitid){
                $newSignleQuantity = $newSignleQuantity * $stockItem->unitconverter / $stockItem->unitconverter1;
                //newSignleQuantity = 100;
            }else if($stockItem->unitconverter < $stockItem->unitconverter1 && $stockItem->unitid == $newUnitid){
                $newSignleQuantity = $newSignleQuantity * $stockItem->unitconverter1 / $stockItem->unitconverter;
            }
            $updatedQuantity = $stockItem->quantity + $newQuantity * $type * $status;

            $updatedSQuantity = $stockItem->single_quantity + $newSignleQuantity * $type * $status;
            if($type == 1 && $status == 1){
                $purchase_price = 0;
                $contactid = 0;
                if(isset($item->price)) $purchase_price = $item->price;
                if(isset($item->contactid)) $contactid = $item->contactid;

                StockItemModel::where('code', $code)->where('warehouseid', $warehouseid)->update([ 'quantity' => $updatedQuantity, 'single_quantity' => $updatedSQuantity, 'purchase_price'=>$purchase_price, 'contactid'=>$contactid ]);
                $stockitem =  StockItemModel::where('code', $code)->where('warehouseid', $warehouseid)->first();
                SHProductModel::where('id', $stockitem->product_id)->update([
                    'contact_id' =>$contactid,
                    // 'sale_price' => $stockitem->purchase_price,
                ]);
            }else{
                StockItemModel::where('code', $code)->where('warehouseid', $warehouseid)->update(['quantity' => $updatedQuantity, 'single_quantity' => $updatedSQuantity]);
            }
        }
    }



    /**
     * Delete a stock item and its associated records.
     *
     * @param int $id The ID of the stock item to delete.
     */
    public function delete($id)
    {
        $SetData = StockItemModel::where("id", $id)->first();
        $SetData->is_delete = true;
        $SetData->save();
    }

    /**
     * Get details of a stock item by its ID with additional information.
     *
     * @param int $id The ID of the stock item.
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getById($id)
    {
        return StockItemModel::leftJoin(DB::raw('sh_product_categories'), 'stockitem.categoryid', '=', DB::raw('sh_product_categories.id'))
            ->leftJoin(DB::raw('sh_media_uploads'), DB::raw('sh_media_uploads.id'), '=', 'stockitem.photo')
            ->leftJoin('unit', 'stockitem.unitid', '=', 'unit.id')
            ->leftJoin('warehouse', 'stockitem.warehouseid', '=', 'warehouse.id')
            ->select('stockitem.*', 'unit.name as unit',
                    'warehouse.name as warehouse', DB::raw('sh_product_categories.title as category'),
                    'warehouse.id as warehouseid', DB::raw('sh_media_uploads.path as image_path'))
            ->where('stockitem.id', '=', $id)
            ->first();
    }


    /**
     * Get a query builder for stock item reports with additional information.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function getstockreport()
    {

        return StockItemModel::leftJoin('category', 'stockitem.categoryid', '=', 'category.id')
            ->leftJoin('unit', 'stockitem.unitid', '=', 'unit.code')
            ->leftJoin('warehouse', 'stockitem.warehouseid', '=', 'warehouse.id')
            ->where('stockitem.is_delete', false)
            ->select('stockitem.*', 'unit.name as unit',
                    'warehouse.name as warehouse', 'category.name as category',
                    'unit.code as codeunit',
                    'warehouse.id as warehouseid', 'category.code as codecategory');
    }


    /**
     * Get total stock items count for each warehouse with additional details.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getstockbywarehouse()
    {
        // Get all warehouse IDs
        $warehouse = WarehouseModel::select('id', 'name')->get();
        $warehouseIds = $warehouse->pluck('id');

        // Get the total count of items for each warehouse
        $data = \DB::table('stockitem')
            ->select(
                'warehouseid',
                \DB::raw('COUNT(id) as total')
            )
            ->where('is_delete', false)
            ->groupBy('warehouseid')
            ->get();

        // Use map to ensure all warehouse IDs are represented
        $result = $warehouseIds->map(function ($warehouseId) use ($data, $warehouse) {
            $item = $data->where('warehouseid', $warehouseId)->first();
            return [
                'warehouseid' => $warehouseId,
                'warehousename' => $warehouse->where('id', $warehouseId)->first()->name,
                'total' => $item ? $item->total : 0,
            ];
        });

        // $result now contains an array with warehouse IDs, names, and total counts

        return $result;
    }


    /**
     * Get top stock items by quantity.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function gettopstockbyquantity()
    {
        // Get the total count of items for each warehouse
        $data = StockItemModel::where('is_delete', false)
            ->orderByDesc('quantity')
            ->take(10)
            ->get(['id', 'name', 'quantity']);

        return $data;
    }


    /**
     * Get total stock items count for each category with additional details.
     *
     * @return \Illuminate\Support\Collection
     */

    public function getstockbycategory(){

        // Get all category IDs
        $category = CategoryModel::select('code', 'name')->get();
        $categoryIds = $category->pluck('code');

        // Get the total count of items for each category
        $data = \DB::table('stockitem')
            ->where('is_delete', false)
            ->select(
                'categoryid',
                \DB::raw('COUNT(id) as total')
            )
            ->groupBy('categoryid')
            ->get();

        // Use map to ensure all category IDs are represented
        $result = $categoryIds->map(function ($categoryId) use ($data, $category) {
            $item = $data->where('categoryid', $categoryId)->first();
            return [
                'categoryid' => $categoryId,
                'categoryname' => $category->where('code', $categoryId)->first()->name,
                'total' => $item ? $item->total : 0,
            ];
        });

        // $result now contains an array with warehouse IDs, names, and total counts

        return $result;
        }



    /**
     * Get the latest barcode ID from the stock items.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    public function getBarcodeId(){
        return StockItemModel::where('is_delete', false)->orderBy('id', 'desc')->first();
    }

    /**
     * Check if a stock item with the given category ID exists.
     *
     * @param int $id The category ID.
     * @return bool
     */
    public function getByCategory($id)
    {
        return StockItemModel::where('categoryid', $id)->where('is_delete', false)->exists();
    }

    /**
     * Check if a stock item with the given unit ID exists.
     *
     * @param int $id The unit ID.
     * @return bool
     */
    public function getByUnit($id)
    {
        return StockItemModel::where('unitid', $id)->where('is_delete', false)->exists();
    }

    /**
     * Check if a stock item with the given warehouse ID exists.
     *
     * @param int $id The warehouse ID.
     * @return bool
     */
    public function getByWarehouse($id)
    {
        return StockItemModel::where('warehouseid', $id)->where('is_delete', false)->exists();
    }


    /**
     * Check if a stock item with the given shelf ID exists.
     *
     * @param int $id The shelf ID.
     * @return bool
     */
    public function getByShelf($id)
    {
        return StockItemModel::where('shelfid', $id)->where('is_delete', false)->exists();
    }

    /**
     * Check if a stock item with the given code and ID exists.
     *
     * @param string $code The code to check.
     * @param int $Id The ID to exclude from the check.
     * @return bool
     */
    public function CheckCodeId($code, $Id){
        return  StockItemModel::where('code', $code)->where('id', '!=', $Id)->exists();
    }


    /**
     * Check if a stock item with the given code exists.
     *
     * @param string $code The code to check.
     * @return bool
     */
    public function CheckCode($code){

        return StockItemModel::where('code', $code)->exists();

    }

    /**
     * Get stock items by reference.
     *
     * @param string $reference The reference to search for.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function GetByReference($reference){
        return StockItemModel::where('reference', $reference)->where('is_delete', false)->get();
    }


    /**
     * Search for stock items based on the provided query.
     *
     * @param string $query The search query.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function SearchItem($query, $warehouseid){
        $q = StockItemModel::leftJoin('unit', 'unit.id', '=', 'stockitem.unitid')
            ->select('stockitem.*', 'unit.name as unitname')
            ->where('stockitem.is_delete', false)
            ->where(function ($queryBuilder) use ($query, $warehouseid) {
            $queryBuilder->where('stockitem.code', 'LIKE', '%' . $query . '%')
                         ->orWhere('stockitem.name', 'LIKE', '%' . $query . '%');
            });

        if(!empty($warehouseid)) $q->where('stockitem.warehouseid', $warehouseid);
        return $q->get();
    }

    public function getitemcurrentQty($id) {
        $item = StockItemModel::find($id);
        return ['quantity' => $item->quantity, 'unitid' => $item->unitid, 'unitconverter' => $item->unitconverter];
    }

    /**
     * Search for stock items based on the provided query.
     *
     * @param string $query The search query.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getItem($code, $warehouseid){
        // dd(StockItemModel::leftJoin('category', 'stockitem.categoryid', '=', 'category.id')
        // ->leftJoin('unit', 'stockitem.unitid', '=', 'unit.code')
        // ->leftJoin('warehouse', 'stockitem.warehouseid', '=', 'warehouse.id')
        // ->select('stockitem.*', 'unit.name as unit',
        //         'warehouse.name as warehouse', 'category.name as category',
        //         'unit.code as codeunit',
        //         'warehouse.id as warehouseid', 'category.code as codecategory')
        // ->where('stockitem.code', $code )
        // ->where('stockitem.is_delete', false)
        // ->where('stockitem.quantity', '>', 0)
        // ->where('stockitem.warehouseid', $warehouseid )
        // ->first());
        return StockItemModel::leftJoin('category', 'stockitem.categoryid', '=', 'category.id')
            ->leftJoin('unit', 'stockitem.unitid', '=', 'unit.code')
            ->leftJoin('warehouse', 'stockitem.warehouseid', '=', 'warehouse.id')
            ->select('stockitem.*', 'unit.name as unit',
                    'warehouse.name as warehouse', 'category.name as category',
                    'unit.code as codeunit',
                    'warehouse.id as warehouseid', )
            ->where('stockitem.code', $code )
            ->where('stockitem.is_delete', false)
            ->where('stockitem.quantity', '>', 0)
            ->where('stockitem.warehouseid', $warehouseid )
            ->first();

        // return StockItemModel::leftJoin('unit', 'unit.id', '=', 'stockitem.unitid')
        //     ->leftJoin('category', 'category.id', '=', 'stockitem.categoryid')
        //     ->select('stockitem.*', 'unit.name as unitname', 'category.name as categoryname')
        //     ->where('stockitem.code', $code )
        //     ->where('stockitem.warehouseid', $warehouseid )
        //     ->first();
    }

    public function getpricehistory($id) {
        return StockItemPriceHistoryModel::leftJoin('users', 'stockitem_price_history.creator_id', '=', 'users.id')
            ->where('stockitem_price_history.stockitem_id', '=', $id)
            ->select('stockitem_price_history.*', 'users.name as creator')
            ->get();
    }
}
