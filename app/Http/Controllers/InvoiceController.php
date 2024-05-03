<?php

namespace App\Http\Controllers;

use App\AttachmentsInvoice;
use App\DetailsInvoice;
use App\Invoice;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = Invoice::all();
        return view('invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sections = Section::all();
        return view('invoices.add', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Invoice::create([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
        ]);

        $invoice_id = Invoice::latest()->first()->id;
        DetailsInvoice::create([
            'id_Invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->Section,
            'Status' => 'غير مدفوعة',
            'Value_Status' => 2,
            'note' => $request->note,
            'user' => (Auth::user()->name),
        ]);

        if ($request->hasFile('pic')) {

            $invoice_id = Invoice::latest()->first()->id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $attachments = new AttachmentsInvoice();
            $attachments->file_name = $file_name;
            $attachments->invoice_number = $invoice_number;
            $attachments->Created_by = Auth::user()->name;
            $attachments->invoice_id = $invoice_id;
            $attachments->save();

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('attachments/invoices/' . $invoice_number), $imageName);
        }

        session()->flash('add', 'تم اضافة الفاتورة بنجاح');
        return redirect('/invoices');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(string $id)
    {
        $invoices = Invoice::where('id', $id)->first();
        $details = DetailsInvoice::where('id_Invoice', $id)->get();
        $attachments = AttachmentsInvoice::where('invoice_id', $id)->get();
        if (!empty($invoices) && !empty($attachments) && !empty($details)) {
            return view('invoices.details', compact('invoices', 'details', 'attachments'));
        } else {
            session()->flash('not_found');
            return redirect('/invoices');
        }
    }

    /**
     * Display the status of invoice.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function status(string $id)
    {
        $invoices = Invoice::where('id', $id)->first();
        return view('invoices.status', compact('invoices'));
    }

    /**
     * Display the status of invoice.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function paid(Request $request)
    {
        $invoices = Invoice::where('Value_Status', 1)->get();
        return view('invoices.paid', compact('invoices'));
    }
    /**
     * Display the status of invoice.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function unpaid(Request $request)
    {
        $invoices = Invoice::where('Value_Status', 2)->get();
        return view('invoices.unpaid', compact('invoices'));
    }

    /**
     * Display the status of invoice.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function partial(Request $request)
    {
        $invoices = Invoice::where('Value_Status', 3)->get();
        return view('invoices.partial', compact('invoices'));
    }

    /**
     * Display the status of invoice.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function archives(Request $request)
    {
        $invoices = Invoice::onlyTrashed()->get();
        return view('invoices.archive', compact('invoices'));
    }

    /**
     * Display the status of invoice.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unarchive(Request $request)
    {
        $id = $request->invoice_id;
        Invoice::withTrashed()->where('id', $id)->restore();
        session()->flash('restore_invoice');
        return redirect('/invoices');
    }

    /**
     * Display the status of invoice.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function print(string $id)
    {
        $invoices = Invoice::findOrFail($id);
        return view('invoices.print', compact('invoices'));
    }

    /**
     * Display the status of invoice.
     *
     * @param  Request  $request
     * @param string $id
     * @return \Illuminate\Http\Response
     */
    public function status_update(string $id, Request $request)
    {
        $invoices = Invoice::findOrFail($id);

        if ($request->Status === 'مدفوعة') {

            $invoices->update([
                'Value_Status' => 1,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);

            DetailsInvoice::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 1,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        } else {
            $invoices->update([
                'Value_Status' => 3,
                'Status' => $request->Status,
                'Payment_Date' => $request->Payment_Date,
            ]);
            DetailsInvoice::create([
                'id_Invoice' => $request->invoice_id,
                'invoice_number' => $request->invoice_number,
                'product' => $request->product,
                'Section' => $request->Section,
                'Status' => $request->Status,
                'Value_Status' => 3,
                'note' => $request->note,
                'Payment_Date' => $request->Payment_Date,
                'user' => (Auth::user()->name),
            ]);
        }
        session()->flash('Status_Update');
        return redirect('/invoices');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(string $id)
    {
        $invoices = Invoice::where('id', $id)->first();
        $sections = Section::all();
        return view('invoices.edit', compact('sections', 'invoices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $invoices = Invoice::findOrFail($request->invoice_id);
        $invoices->update([
            'invoice_number' => $request->invoice_number,
            'invoice_Date' => $request->invoice_Date,
            'Due_date' => $request->Due_date,
            'product' => $request->product,
            'section_id' => $request->Section,
            'Amount_collection' => $request->Amount_collection,
            'Amount_Commission' => $request->Amount_Commission,
            'Discount' => $request->Discount,
            'Value_VAT' => $request->Value_VAT,
            'Rate_VAT' => $request->Rate_VAT,
            'Total' => $request->Total,
            'note' => $request->note,
        ]);

        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return redirect('invoices');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->invoice_id;
        $invoices = Invoice::where('id', $id)->first();
        $attachments = AttachmentsInvoice::where('invoice_id', $id)->first();
        $id_page = $request->id_page;

        if (!$id_page == 2) {
            //* If the invoice has any attachments.
            if (!empty($attachments->invoice_number)) {
                Storage::disk('public_uploads')->deleteDirectory($attachments->invoice_number);
            }
            $invoices->forceDelete();
            session()->flash('delete_invoice');
            return redirect('/invoices');
        } else {
            $invoices->delete();
            session()->flash('archive_invoice');
            return redirect('/invoices/archive');
        }
    }
}
