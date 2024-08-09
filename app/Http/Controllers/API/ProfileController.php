<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Profile;


class ProfileController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'age' => 'required',
            'bio' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = auth()->user();
        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            [   
                'age' => $request->age,
                'bio' => $request->bio,
            ]
        );

        return response()->json([
            'message' => 'profile berhasil dibuat/diubah',
            'profile' => $profile,
        ]);
    }
}
