<?php

// app/Services/ContactService.php

namespace App\Services;

use App\Models\ContactModel;

class ContactService
{
    /**
     * Get all contacts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return ContactModel::all();
    }

    /**
     * Get all suppliers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getsupplier(){
        return ContactModel::where('status', '2')->get();
    }

    /**
     * Get all customers.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getcustomer(){
        return ContactModel::where('status', '1')->get();
    }

    public function getCustomerData(){
        return ContactModel::where('status', '1');
    }

    /**
     * Get the total number of items in the contact.
     *
     * @return int
     */
    public function totalitem(){
        return ContactModel::count();
    }

    /**
     * Create a new contact.
     *
     * @param array $data The data for creating a new contact.
     * @return \App\Models\ContactModel
     */
    public function create($data)
    {
        return ContactModel::create($data);
    }

    /**
     * Update an existing contact.
     *
     * @param int $id The ID of the contact to update.
     * @param array $data The data for updating the contact.
     * @return \App\Models\ContactModel
     */
    public function update($id, $data)
    {
        $SetData = ContactModel::findOrFail($id);
        $SetData->each->update($data);
        return $SetData;
    }

    /**
     * Delete an existing contact.
     *
     * @param int $id The ID of the contact to delete.
     * @return void
     */
    public function delete($id)
    {
        $SetData = ContactModel::findOrFail($id);
        $SetData->each->delete();
    }

    /**
     * Get a contact by its ID.
     *
     * @param int $id The ID of the contact to retrieve.
     * @return \App\Models\ContactModel
     */
    public function getById($id)
    {
        return ContactModel::findOrFail($id);
    }

    /**
     * Check if a contact with a given status exists.
     *
     * @param int $status The status to check.
     * @return bool
     */
    public function getByStatus($status)
    {
        return ContactModel::where('status', $status)->exists();
    }

    /**
     * Check if a contact with a given email and ID exists (excluding a specific ID).
     *
     * @param string $email The email to check.
     * @param int $Id The ID to exclude from the check.
     * @return bool
     */
    public function CheckEmailId($email, $Id){
        return  ContactModel::where('email', $email)->where('id', '!=', $Id)->exists();
    }

    /**
     * Check if a contact with a given email exists.
     *
     * @param string $email The email to check.
     * @return bool
     */
    public function CheckEmail($email){
        return ContactModel::where('email', $email)->exists();
    }
}
