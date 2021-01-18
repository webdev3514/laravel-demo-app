<?php

namespace App\Http\Controllers;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class ProductController extends Controller
{
    public function index()
    {
        try
        {
            return response()->json(Product::all(),200);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    
    public function store(Request $request)
    {
        try {

                $validator = Validator::make($request->all(), [
                        'name' => 'required',
                        'description' => 'required',
                        'units' => 'required|numeric',
                        'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
                        'image' => 'required'
                    ]);

                    if ($validator->fails()) {
                        return redirect()->back()->withInput($request->input())->withErrors($validator->errors());
                    }

                $product = Product::create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'units' => $request->units,
                    'price' => $request->price,
                    'image' => $request->image
                ]);
                
                return response()->json([
                    'status' => (bool) $product,
                    'data'   => $product,
                    'message' => $product ? 'Product Created!' : 'Error Creating Product'
                ]);
         } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    
    public function show(Product $product)
    {
        try{
            return response()->json($product,200); 
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }    
    }

    public function uploadFile(Request $request)
    {
        try
        {
            if($request->hasFile('image')){
                $name = time()."_".$request->file('image')->getClientOriginalName();
                $request->file('image')->move(public_path('images'), $name);
            }
            return response()->json(asset("images/$name"),201);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function update(Request $request, Product $product)
    {
         try {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'units' => 'required|numeric',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/',
            'image' => 'required'
        ]);

                    if ($validator->fails()) {
                        return redirect()->back()->withInput($request->input())->withErrors($validator->errors());
                    }
                $status = $product->update(
                    $request->only(['name', 'description', 'units', 'price', 'image'])
                );
                
                return response()->json([
                    'status' => $status,
                    'message' => $status ? 'Product Updated!' : 'Error Updating Product'
                ]);
         } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
    
    public function updateUnits(Request $request, Product $product)
    {
         try {
            $product->units = $product->units + $request->get('units');
            $status = $product->save();
            
            return response()->json([
                'status' => $status,
                'message' => $status ? 'Units Added!' : 'Error Adding Product Units'
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        try
        {
            $status = $product->delete();
            
            return response()->json([
                'status' => $status,
                'message' => $status ? 'Product Deleted!' : 'Error Deleting Product'
            ]);
        } catch (Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }
}
