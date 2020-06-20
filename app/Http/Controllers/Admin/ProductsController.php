<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::join('categories', 'categories.id', '=', 'products.category_id')
            ->select([
                'products.*',
                'categories.name as category_name'
            ])
            ->paginate();

        return View::make('admin.products.index', [
            'products' => $products,
        ]); // view()
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return View::make('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'category_id' => 'required|int|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'image',
        ]);

        $image_path = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $image_path = $image->store('products', 'public');
        }
        
        /*$request->merge([
            'image' => $image_path,
        ]);*/

        $data = $request->all();
        $data['image'] = $image_path;
        $product = Product::create($data);

        // redirect()
        return Redirect::route('admin.products.index')
            ->with('alert.success', "Product ({$product->name}) created!");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return View::make('admin.products.show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return View::make('admin.products.edit', [
            'product' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255|min:3',
            'category_id' => 'required|int|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'image',
        ]);

        $product->update($request->all());

        return Redirect::route('admin.products.index')
            ->with('alert.success', "Product ({$product->name}) updated!");
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return Redirect::route('admin.products.index')
            ->with('alert.success', "Product ({$product->name}) deleted!");
    }
}
