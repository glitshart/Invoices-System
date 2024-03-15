<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSection;
use App\Http\Requests\UpdateSection;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = Section::all();
        return view('categories.sections', compact('sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSection $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        // Create a new section
        Section::create([
            'section_name' => $request->section_name,
            'description' => $request->description,
            'created_by' => (Auth::user()->name),
        ]);

        // Redirect to the section index page with success message
        session()->flash('add', 'تم اضافة القسم بنجاح');
        return redirect('/sections');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function show(Section $section)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function edit(Section $section)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSection $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        // Find the section by id
        $section = Section::find($request->id);

        // Update the section
        $section->update([
            'section_name' => $request->section_name,
            'description' => $request->description,
        ]);

        // Redirect to the section index page with success message
        session()->flash('edit', 'تم تعديل القسم بنجاح');
        return redirect('/sections');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Section  $section
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Section::findOrFail($request->id)->delete();

        // Redirect to the section index page with success message
        session()->flash('delete', 'تم حذف القسم بنجاح');
        return redirect('/sections');
    }
}
