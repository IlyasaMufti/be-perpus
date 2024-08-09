<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Book;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:api','isOwner'])->except('index','show','dashboard');
    }

    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
         $book = Book::take(3)->get();

         return response()->json([
             'message' => 'Tampil data buku berhasil',
             'data'    => $book  
         ], 200);
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         $book = Book::with('category','listBorrow')->latest()->get();

         return response()->json([
             'message' => 'Tampil data buku berhasil',
             'data'    => $book  
         ], 200);
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'stok' => 'required',
            'category_id' => 'required | exists:categories,id',
            'image' => 'required | mimes:jpg,png,jpeg',
        ]);
 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $validator->validated();

        // membuat unique name pada gambar yang di input

        $imageName = time().'.'.$request->image->extension();

        // simpan gambar pada file storage

        $request->image->storeAs('public/images', $imageName);

        // menganti request nilai request image menjadi $imageName yang baru bukan berdasarkan request
        $path = env('APP_URL').'/storage/images/';
        $data['image'] = $path.$imageName;

        $book = Book::create($data);

        return response()->json(['Tambah data buku berhasil'], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $book = Book::with('category','listBorrow')->find($id);
        
        if(!$book){
            return response()->json([
                'message' => "id buku tidak ditemukan"
            ], 404);
        }

        return response()->json([
            'message' => 'Tampilkan detail data buku berhasil',
            'data'    => $book 
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'summary' => 'required',
            'stok' => 'required',
            'category_id' => 'required | exists:categories,id',
            'image' => 'required | mimes:jpg,png,jpeg',
        ]);
 
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        } 

        $data = $validator->validated();
        $book = Book::find($id);

        if(!$book){
            return response()->json([
                'message' => "id buku tidak ditemukan"
            ], 404);
        }
           
        $image = basename($book->image);
        Storage::delete('public/images/' . $image);
    

        $imageName = time().'.'.$request->image->extension();

        $request->image->storeAs('public/images', $imageName);

        $path = env('APP_URL').'/storage/images/';
        $data['image'] = $path.$imageName;
        

        $book->update($data);

        return response()->json([
            'message' => 'buku sudah berhasil diupdate',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $book = Book::find($id);
        
        if(!$book){
            return response()->json([
                'message' => "id buku tidak ditemukan"
            ], 404);
        }

        if($book) {
            if ($book->poster) {
                $poster = basename($book->poster);
                Storage::delete('public/images/' . $poster);
            }
            $book->delete();

            return response()->json([
                'message' => 'buku berhasil dihapus',
            ], 200);

        }

    }
}
