<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Models\CategoryModel;
use App\Models\SHProductCategoryModel;
use App\Models\SHMediaUploadModel;
use Illuminate\Http\Request;
use DataTables;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use DB;

class CategoryController extends Controller
{
    protected $categoryService;

    /**
     * Constructor for CategoryController.
     *
     * @param \App\Services\CategoryService $categoryService The service for category management.
     * @return void
     */
    public function __construct(CategoryService $categoryService)
    {
        // Middleware to check login status.
        $this->middleware('checkLogin');

        // Injecting the CategoryService.
        $this->categoryService = $categoryService;
    }

    /**
     * Display the index view for categories.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('category.index');
    }

    /**
     * Get category data for DataTables.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        // Retrieve permissions from the middleware.
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;

        if ($request->ajax()) {
            // Get all category data.
            $data = $this->categoryService->getAll();

            // Prepare DataTables response.
            return DataTables::of($data)
                ->addColumn('action', function ($data) use ($hasEditPermission, $hasDeletePermission) {
                    $actionHtml = '<div class="d-flex">';

                    // Edit Button
                    if ($hasEditPermission) {
                        $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#EditModal" id="btnedit" data-editid="' . $data->id . '" class="btn btn-sm btn-success d-flex align-items-center" data-toggle="modal" data-target="#edit">
                            <span class="material-symbols-rounded">edit</span> '.__('text.edit').'
                        </a>&nbsp;';
                    }

                    // Delete Button
                    if ($hasDeletePermission) {
                        $actionHtml .= '<a data-bs-toggle="modal" data-bs-target="#DeleteModal" id="btndelete" data-deleteid="' . $data->id . '" class="btn btn-sm btn-danger d-flex align-items-center">
                            <span class="material-symbols-rounded">delete</span> '.__('text.delete').'
                        </a>&nbsp;';
                    }

                    $actionHtml .= '</div>';
                    return $actionHtml;
                })
                ->toJson();
        }

        // Return 403 error for unauthorized access.
        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get all category data as JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        // Get all category data.
        $data = $this->serviceService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Get category details by ID.
     *
     * @param int $id The ID of the category.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Get category details by ID.
        $data = $this->categoryService->getById($id);
        return response()->json($data);
    }

    /**
     * Check if a category code already exists.
     *
     * @param string $code The category code.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcode($code)
    {
        // Check if the category code exists.
        $checkcode = $this->categoryService->CheckCode($code);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }

    /**
     * Check if a category code exists, excluding a specified ID.
     *
     * @param string $code The category code.
     * @param int $id The ID to exclude.
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkcodeid($code, $id)
    {
        // Check if the category code exists, excluding the specified ID.
        $checkcode = $this->categoryService->CheckCodeId($code, $id);
        return response()->json([
            'exists' => !!$checkcode,
        ]);
    }

    /**
     * Delete a category.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        // Extract ID from the request.
        $id = $request->only('deleteid');

        // Delete the category.
        $this->categoryService->delete($id);

        // Redirect with success message.
        return redirect()->route('category.index')->with('success', __('text.msg_category_deleted'));
    }

    // Fetch categories for jsTree
    public function getCategories()
    {
        $categories = SHProductCategoryModel::leftJoin(DB::raw('sh_media_uploads'), DB::raw('sh_media_uploads.id'), '=', DB::raw('sh_product_categories.image'))
            ->select(DB::raw('sh_product_categories.*'), DB::raw('sh_media_uploads.path'))
            ->where('status', 'publish')
            ->get()
            ->toArray();

        return response()->json($categories);
    }

    // Create a new category
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        // echo "ddd";die();
        $insert_sh_category_data = [
            'title' => $request->name,
            'parent_id' => $request->parent_id,
            'status' => 'publish'
        ];
        $image = $request->file('photo');

        if ($image) {
            $image_dimension = getimagesize($image);
            $image_width = $image_dimension[0];
            $image_height = $image_dimension[1];
            $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
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
            $imageInst = Image::read($image);
            $resize_large_image = $imageInst->resize(width: 740);
            $resize_grid_image = $imageInst->resize(width: 350);
            $resize_p_grid_image = $imageInst->resize(width: 230);
            $resize_thumb_image = $imageInst->resize(width: 150, height: 150);
            $image->move($folder_path, $image_db);
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
            $insert_sh_category_data['image'] = $newMediaUpload->id;
        }
        $category = SHProductCategoryModel::create($insert_sh_category_data);

        return redirect()->route('category.index')->with('success', __('text.msg_category_created'));
    }

    // Update an existing category
    public function update(Request $request)
    {
        $id   = $request->only(['editid']);
        $validatedData = $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);
        // echo "ddd";die();
        $insert_sh_category_data = [
            'title' => $request->name,
        ];
        $image = $request->file('photo');

        if ($image) {
            $image_dimension = getimagesize($image);
            $image_width = $image_dimension[0];
            $image_height = $image_dimension[1];
            $image_dimension_for_db = $image_width . ' x ' . $image_height . ' pixels';
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
            $imageInst = Image::read($image);
            $resize_large_image = $imageInst->resize(width: 740);
            $resize_grid_image = $imageInst->resize(width: 350);
            $resize_p_grid_image = $imageInst->resize(width: 230);
            $resize_thumb_image = $imageInst->resize(width: 150, height: 150);
            $image->move($folder_path, $image_db);
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
            $insert_sh_category_data['image'] = $newMediaUpload->id;
        }
        $SetData = SHProductCategoryModel::findOrFail($id);
        $SetData->each->update($insert_sh_category_data);

        return redirect()->route('category.index')->with('success', __('text.msg_category_updated'));
    }

    public function deleteImage(Request $request)
    {
        // Extract ID from the request.
        $id = $request->input('deleteimage_id'); // Use input() to get a single value (not an array).

        // Find the model by ID.
        $SetData = SHProductCategoryModel::findOrFail($id); // Find the model by its ID.

        // Set the image field to null.
        $SetData->image = null;

        // Save the changes to the model.
        $SetData->save(); // This will persist the changes to the database.

        // Redirect with success message.
        return redirect()->route('category.index')->with('success', __('text.msg_img_category_deleted'));
    }

}
