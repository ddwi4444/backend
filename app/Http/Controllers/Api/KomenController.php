<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KomenModel;
use App\Models\KomikModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KomenController extends Controller
{
    // Membuat komen
    public function create(Request $request, $idKomik)
    {
        $data = KomikModel::where('id', $idKomik)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'isi' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = KomenModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'Comment'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'Comment'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $komen_by = auth()->user()->nama_persona;

        $dataKomen = collect($request)->only(KomenModel::filters())->all();
        $dataKomen['uuid'] = $uuid;
        $dataKomen['user_id'] = $user_id;
        $dataKomen['komen_by'] = $komen_by;
        $dataKomen['sub_komik_id'] = $idKomik;
        $forum = KomenModel::create($dataKomen);

        return response([
            'message' => 'Komen Successfully Added',
            'data' => $forum,
        ], 200);
    }

    // Membuat komen balasan
    public function createKomenBalasan(Request $request, $idKomen, $idKomik)
    {
        $dataKomen = KomenModel::where('id', $idKomen)->first();

        if(is_null($dataKomen)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $dataKomik = KomikModel::where('id', $idKomik)->first();

        if(is_null($dataKomik)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }
        
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'isi' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = KomenModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'Comment'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'Comment'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $komen_by = auth()->user()->nama_persona;

        $dataKomen = collect($request)->only(KomenModel::filters())->all();
        $dataKomen['uuid'] = $uuid;
        $dataKomen['user_id'] = $user_id;
        $dataKomen['komen_by'] = $komen_by;
        $dataKomen['sub_komik_id'] = $idKomik;
        $dataKomen['komen_parent_id'] = $idKomen;
        $forum = KomenModel::create($dataKomen);


        return response([
            'message' => 'Komen Successfully Added',
            'data' => $forum,
        ], 200);
    }

    // Untuk membaca komen
    public function read($uuid)
    {
        $data = KomenModel::where('uuid', $uuid)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Forum Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Forum Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    // Menghapus komen
    public function delete($uuid)
    {
        $data = KomenModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Deleted']);
    }
}
