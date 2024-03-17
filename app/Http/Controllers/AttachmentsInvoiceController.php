<?php

namespace App\Http\Controllers;

use App\AttachmentsInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AttachmentsInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'file_name' => 'mimes:pdf,jpeg,png,jpg',
        ], [
            'file_name.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg',
        ]);

        $image = $request->file('file_name');
        $file_name = $image->getClientOriginalName();

        $attachments =  new AttachmentsInvoice();
        $attachments->file_name = $file_name;
        $attachments->invoice_number = $request->invoice_number;
        $attachments->invoice_id = $request->invoice_id;
        $attachments->Created_by = Auth::user()->name;
        $attachments->save();

        // move pic
        $imageName = $request->file_name->getClientOriginalName();
        $request->file_name->move(public_path('Attachments/' . $request->invoice_number), $imageName);

        session()->flash('Add', 'تم اضافة المرفق بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\AttachmentsInvoice  $attachmentsInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(string $invoice_number, string $file_name)
    {
        // View the file
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->file($files);
    }

    /**
     * Download the specified resource.
     *
     * @param  string  $invoice_number
     * @param  string  $file_name
     * @return \Illuminate\Http\Response
     */
    public function download(string $invoice_number, string $file_name)
    {
        // View the file
        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number . '/' . $file_name);
        return response()->download($files);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\AttachmentsInvoice  $attachmentsInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\AttachmentsInvoice  $attachmentsInvoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttachmentsInvoice $attachmentsInvoice)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\AttachmentsInvoice  $attachmentsInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        // Delete the specified resource from database.
        AttachmentsInvoice::findOrFail($request->id_file)->delete();

        // Delete the specified resource from storage.
        Storage::disk('public_uploads')->delete($request->invoice_number . '/' . $request->file_name);

        // Success Message
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }
}
