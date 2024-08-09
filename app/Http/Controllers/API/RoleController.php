<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class RoleController extends Controller
{

   public function index()
   {
        $role = Role::latest()->get();

        return response()->json([
            'message' => 'Tampil data Role berhasil',
            'data'    => $role  
        ], 200);
       
   }

   
   public function store(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'name' => 'required',
       ]);

       if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
       }

       $role = Role::create($request->all());

       return response()->json([
           'message' => 'Role berhasil ditambahkan',
       ], 201);

   }


   public function show(string $id)
   {
       $role = Role::find($id);
       
       if(!$role){
           return response()->json([
               'message' => "id Role tidak ditemukan"
           ], 404);
       }

       return response()->json([
           'message' => 'Tampilkan detail data Role berhasil',
           'data'    => $role 
       ], 200);
   }

   
   public function update(Request $request, string $id)
   {
       $validator = Validator::make($request->all(), [
           'name' => 'required',
       ]);

       if ($validator->fails()) {
           return response()->json($validator->errors(), 422);
       }
       
       $role = Role::find($id);
       
       if(!$role){
           return response()->json([
               'message' => "id Role tidak ditemukan"
           ], 404);
       }

       if($role) {
           $role->update([
               'name'     => $request->name,
           ]);

           return response()->json([
               'message' => 'Role sudah berhasil diupdate',
           ], 200);
       };
   }

   
   public function destroy(string $id)
   {
       $role = Role::find($id);
       
       if(!$role){
           return response()->json([
               'message' => "id Role tidak ditemukan"
           ], 404);
       }

       if($role) {

           $role->delete();

           return response()->json([
               'message' => 'Role berhasil dihapus',
           ], 200);

       }

   }
}
