<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storeproduct;
use App\Http\Requests\UpdateProduct;
use App\product;
use App\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = Section::all();
        $products = Product::all();
        return view('categories.products', compact('products', 'sections'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProduct $request)
    {
        // Retrieve the validated input data...
        $validated = $request->validated();

        // Create a new product
        Product::create([
            'product_name' => $request->product_name,
            'section_id' => $request->section_id,
            'description' => $request->description,
        ]);

        // Redirect to the product index page with success message
        session()->flash('add', 'تم اضافة القسم بنجاح');
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProduct $request)
    {
        // Retrieve the validated input data...
        $request->validated();

        // Find the product by id
        $product = Product::find($request->id);

        // Update the product
        $product->update([
            'product_name' => $request->product_name,
            'section_id' => $request->section_id,
            'description' => $request->description,
        ]);

        // Redirect to the product index page with success message
        session()->flash('edit', 'تم تعديل القسم بنجاح');
        return redirect('/products');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        Product::findOrFail($request->id)->delete();

        // Redirect to the product index page with success message
        session()->flash('delete', 'تم حذف القسم بنجاح');
        return redirect('/products');
    }
}
