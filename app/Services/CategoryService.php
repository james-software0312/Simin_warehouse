<?php
// app/Services/CategoryService.php

namespace App\Services;

use App\Models\CategoryModel;
use App\Models\SHProductCategoryModel;

class CategoryService
{
    /**
 * Get all categories.
 *
 * @return \Illuminate\Database\Eloquent\Collection
 */
public function getAll()
{
    return SHProductCategoryModel::all();
}

/**
 * Get the total number of items in the category.
 *
 * @return int
 */
public function totalitem(){
    return SHProductCategoryModel::count();
}

/**
 * Create a new category.
 *
 * @param array $data The data for creating a new category.
 * @return \App\Models\CategoryModel
 */
public function create($data)
{
    return SHProductCategoryModel::create($data);
}

/**
 * Update an existing category.
 *
 * @param int $id The ID of the category to update.
 * @param array $data The data for updating the category.
 * @return \App\Models\CategoryModel
 */
public function update($id, $data)
{
    $SetData = SHProductCategoryModel::findOrFail($id);
    $SetData->each->update($data);
    return $SetData;
}

/**
 * Delete an existing category.
 *
 * @param int $id The ID of the category to delete.
 * @return void
 */
public function delete($id)
{
    $SetData = SHProductCategoryModel::findOrFail($id);
    $SetData->each->delete();
}

/**
 * Get a category by its ID.
 *
 * @param int $id The ID of the category to retrieve.
 * @return \App\Models\CategoryModel
 */
public function getById($id)
{
    return SHProductCategoryModel::findOrFail($id);
}

/**
 * Check if a category with a given code and ID exists (excluding a specific ID).
 *
 * @param string $code The code to check.
 * @param int $Id The ID to exclude from the check.
 * @return bool
 */
public function CheckCodeId($code, $Id){
    return SHProductCategoryModel::where('code', $code)->where('id', '!=', $Id)->exists();
}

/**
 * Check if a category with a given code exists.
 *
 * @param string $code The code to check.
 * @return bool
 */
public function CheckCode($code){
    return SHProductCategoryModel::where('code', $code)->exists();
}


   
}