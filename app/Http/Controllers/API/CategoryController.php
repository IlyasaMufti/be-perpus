<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api','isOwner'])->except('index','show');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $category = Category::latest()->get();

         return response()->json([
             'message' => 'Tampil data Category berhasil',
             'data'    => $category  
         ], 200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category = Category::create($request->all());

        return response()->json([
            'message' => 'Category berhasil ditambahkan',
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::with('listBook')->find($id);
        
        if(!$category){
            return response()->json([
                'message' => "id Category tidak ditemukan"
            ], 404);
        }

        return response()->json([
            'message' => 'Tampilkan detail data Category berhasil',
            'data'    => $category 
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $category = Category::find($id);
        
        if(!$category){
            return response()->json([
                'message' => "id Category tidak ditemukan"
            ], 404);
        }

        if($category) {
            $category->update([
                'name'     => $request->name,
            ]);

            return response()->json([
                'message' => 'Category sudah berhasil diupdate', 
            ], 200);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        
        if(!$category){
            return response()->json([
                'message' => "id Category tidak ditemukan"
            ], 404);
        }

        if($category) {

            $category->delete();

            return response()->json([
                'message' => 'Category berhasil dihapus',
            ], 200);

        }

    }
}
