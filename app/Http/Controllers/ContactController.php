<?php

namespace App\Http\Controllers;

use App\Services\ContactService;
use Illuminate\Http\Request;
use DataTables;

class ContactController extends Controller
{
    protected $contactService;

    /**
     * Constructor for ContactController.
     *
     * @param \App\Services\ContactService $contactService The service for contact management.
     * @return void
     */

    public function __construct(ContactService $contactService)
    {
        // Middleware to check login status.
        $this->middleware('checkLogin');

        // Injecting the ContactService.
        $this->contactService = $contactService;
    }

   /**
     * Display the index view for customer contacts.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('contact.customer.index');
    }

    /**
     * Display the index view for supplier contacts.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function supplier()
    {
        return view('contact.supplier.index');
    }

    /**
     * Get customer contact data for DataTables.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        // Retrieve permissions from the middleware.
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;
        $hasSeeHiddenPermission = $request->hasSeeHiddenPermission;
        $status = $request->status;
        if ($request->ajax()) {
            // Get all customer contact data.
            if ($status == 1) {
                $data = $this->contactService->getCustomerData();
            } else {
                $data = $this->contactService->getsupplier();
            }
            // $data = $this->contactService->getAll(); 

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
                
                ->addColumn('address', function($data) use($hasSeeHiddenPermission) {
                    $ret = "";
                    $ret = $data->address;
                    if($data->postal_code) $ret = $ret . ', ' . $data->postal_code;
                    if($data->city) $ret = $ret . ', ' . $data->city;
                    if($data->country) $ret = $ret . ', ' . $data->country;
                    return $ret;
                })
                
                ->filter(function ($query) use ($request) {
                    // Apply filter only if the user doesn't have permission to see hidden amounts
                    if (!empty($request->get('name'))) {
                        $query->where(function($q) use ($request) {
                            $q->where('name', 'like', "%{$request->get('name')}%")
                            ->orWhere('surname', 'like', "%{$request->get('name')}%");
                        });
                    }
                    if (!empty($request->get('email'))) {
                        $query->where('email', 'like', "%{$request->get('email')}%");
                    }
                    if (!empty($request->get('whatsapp'))) {
                        $query->where('whatsapp', 'like', "%{$request->get('whatsapp')}%");
                    }
                    if (!empty($request->get('vat_number'))) {
                        $query->where('vat_number', 'like', "%{$request->get('vat_number')}%");
                    }
                })
                ->make(true)
                ->getData(true);
        }

        // Return 403 error for unauthorized access.
        return abort(403, 'Unauthorized access.');
    }

    /**
     * Get supplier contact data for DataTables.
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getsupplier(Request $request)
    {
        // Retrieve permissions from the middleware.
        $hasEditPermission   = $request->hasEditPermission;
        $hasDeletePermission = $request->hasDeletePermission;

        if ($request->ajax()) {
            // Get all supplier contact data.
            $data = $this->contactService->getsupplier(); 

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
     * Get all contact data as JSON.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function GetAllData()
    {
        // Get all contact data.
        $data = $this->contactService->getAll();
        return response()->json(['data' => $data]);
    }

    /**
     * Get contact details by ID.
     *
     * @param int $id The ID of the contact.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Get contact details by ID.
        $data = $this->contactService->getById($id);
        return response()->json($data);
    }


    /**
     * Store a new contact (customer or supplier).
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {   
        $data = $request->only(['name', 'surname', 'company', 'address', 'city', 'postal_code', 'country', 'email','phone', 'vat_number','whatsapp','status','description']);
        $group = $this->contactService->create($data);

        if($request->input('status')=='2'){
            return redirect()->route('supplier.index')->with('success', __('text.msg_contact_created'));
        }
        if($request->input('status')=='1'){
            return redirect()->route('customer.index')->with('success', __('text.msg_contact_created'));
        }
        
    }

   
     /**
     * updated contact (customer or supplier).
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {   
        $id   = $request->only(['editid']);
        $data = $request->only(['name', 'surname', 'company', 'address', 'city', 'postal_code', 'country', 'email','phone', 'vat_number','whatsapp','status','description']);
        $group = $this->contactService->update($id, $data);

        return redirect()->route('customer.index')->with('success', __('text.msg_contact_updated'));
    }


     /**
     * updated contact (supplier).
     *
     * @param \Illuminate\Http\Request $request The HTTP request.
     * @return \Illuminate\Http\RedirectResponse
     */

    public function updatesupplier(Request $request)
    {   
        $id   = $request->only(['editid']);
        $data = $request->only(['name','company','email','phone','address','note','status', 'description']);
        $group = $this->contactService->update($id, $data);

        return redirect()->route('supplier.index')->with('success', __('text.msg_contact_updated'));
    }

    /**
 * Destroy a customer contact.
 *
 * @param \Illuminate\Http\Request $request The HTTP request.
 * @return \Illuminate\Http\RedirectResponse
 */
public function destroy(Request $request)
{
    // Get the ID of the contact to be deleted.
    $id = $request->only('deleteid');
    
    // Delete the contact.
    $this->contactService->delete($id);

    // Redirect with success message.
    return redirect()->route('customer.index')->with('success', __('text.msg_contact_deleted'));
}

/**
 * Destroy a supplier contact.
 *
 * @param \Illuminate\Http\Request $request The HTTP request.
 * @return \Illuminate\Http\RedirectResponse
 */
public function destroysupplier(Request $request)
{
    // Get the ID of the contact to be deleted.
    $id = $request->only('deleteid');
    
    // Delete the contact.
    $this->contactService->delete($id);

    // Redirect with success message.
    return redirect()->route('supplier.index')->with('success', __('text.msg_contact_deleted'));
}

/**
 * Check if an email exists (for validation).
 *
 * @param string $email The email to check.
 * @return \Illuminate\Http\JsonResponse
 */
public function checkemail($email)
{
    // Check if the email exists.
    $checkemail = $this->contactService->CheckEmail($email);
    
    // Return JSON response.
    return response()->json([
        'exists' => !!$checkemail,
    ]);
}

/**
 * Check if an email exists, excluding a specific contact ID (for validation during update).
 *
 * @param string $email The email to check.
 * @param int $id The ID of the contact to exclude from the check.
 * @return \Illuminate\Http\JsonResponse
 */
public function checkemailid($email, $id)
{
    // Check if the email exists, excluding a specific contact ID.
    $checkemail = $this->contactService->CheckEmailId($email, $id);
    
    // Return JSON response.
    return response()->json([
        'exists' => !!$checkemail,
    ]);
}


}
 