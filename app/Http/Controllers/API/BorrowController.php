<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Borrow;

class BorrowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $borrow = Borrow::with('user','book')->latest()->get();

        return response()->json([
            'message' => 'Tampil data peminjaman berhasil',
            'data'    => $borrow  
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'load_date' => 'required|date',
            'barrow_date' => 'nullable|date',
            'book_id' => 'required|exists:books,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();
        $borrow = Borrow::updateOrCreate(
            [   'user_id' => $user->id,
                'book_id' => $request->book_id
            ],
            [   
                'load_date' => $request->load_date,
                'barrow_date' => $request->barrow_date,
            ]
        );

        return response()->json([
            'message' => 'Peminjaman berhasil dibuat/diubah',
            'data' => $borrow,
        ]);
    }
}
