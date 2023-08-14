<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KomikModel;
use Validator;

class KomikController extends Controller
{
    // Untuk membuat komik
    public function create(Request $request)
    {
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'judul' => 'required',
            'genre' => 'required',
            'thumbnail' => 'required',
            'instagram_author' => 'required',
            'content' => 'required',
            'nama_author' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $nama_author = auth()->user()->nama_persona;
        $user_id = auth()->user()->id;

        $dataKomik = collect($request)->only(KomikModel::filters())->all();
        $dataKomik['post_by'] = $nama_author;
        $dataKomik['user_id'] = $user_id;
        $komik = KomikModel::create($dataKomik);

        return response([
            'message' => 'Komik Successfully Added',
            'data' => $komik,
        ], 200);
    }

    // Menampilkan komik pada single page
    public function read($id)
    {
        $data = KomikModel::where('id', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Komik Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Komik Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    // Untuk mengupdate komik
    public function update(Request $request, $id)
    {
        $data = KomikModel::find($id);

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'judul' => 'required',
            'genre' => 'required',
            'thumbnail' => 'required',
            'instagram_author' => 'required',
            'content' => 'required',
            'nama_author' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataKomik = collect($request)->only(KomikModel::filters())->all();
        $data->update($dataKomik);

        return response()->json(['Success'=> true, 'message'=> 'Komik Successfully Changed']);
    }

    // Menghapus Komik
    public function delete($id)
    {
        $data = KomikModel::where('id', $id)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Komik Successfully Deleted']);
    }

}
