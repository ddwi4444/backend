<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MerchandiseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MerchandiseController extends Controller
{
    public function create(Request $request)
    {
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'nama' => 'required',
            'deskripsi' => 'required',
            'thumbnail' => 'required|mimes:jpg,bmp,png',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = MerchandiseModell::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'Merchandise'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'Merchandise'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;

        $dataMerchandise = collect($request)->only(MerchandiseModel::filters())->all();

        $image_name = \Str::random(5).str_replace(' ', '', $dataMerchandise['nama']).\Str::random(5);
        $file = $dataMerchandise['thumbnail'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->thumbnail->storeAs(
            'merchandise_thumbnail',
            $image_name.'.'.$extension,
            ['disk' => 'public']
        );

        $dataMerchandise['uuid'] = $uuid;
        $dataMerchandise['thumbnail'] = $uploadDoc;
        $dataMerchandise['user_id'] = $user_id;

        $merchandise = MerchandiseModel::create($dataMerchandise);

        return response([
            'message' => 'Merchandise Successfully Added',
            'data' => $merchandise,
        ], 200);
    }

    public function read($uuid)
    {
        $data = MerchandiseModel::where('uuid', $uuid)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Merchandise Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Merchandise Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    public function update(Request $request, $uuid){
        $data = MerchandiseModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'nama' => 'required',
            'deskripsi' => 'required',
            'thumbnail' => 'required|mimes:jpg,bmp,png',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataMerchandise = collect($request)->only(MerchandiseModel::filters())->all();

        if(isset($request->thumbnail)){
            if(!empty($data->thumbnail)){
                Storage::delete("public/".$data->thumbnail);
            }
            $image_name = \Str::random(5).$request->jenis_kendaraan_id.str_replace(' ', '', $dataMerchandise['nama']).\Str::random(5);
            $file = $dataMerchandise['thumbnail'];
            $extension = $file->getClientOriginalExtension();
    
            $uploadDoc = $request->thumbnail->storeAs(
                'merchandise_thumbnail',
                $image_name.'.'.$extension,
                ['disk' => 'public']
            );
    
            $dataMerchandise['thumbnail'] = $uploadDoc;
        }

        $data->update($dataMerchandise);

        return response()->json(['Success'=> true, 'message'=> 'Merchandise Successfully Changed']);
    }

    public function delete($uuid)
    {
        $data = MerchandiseModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Merchandise Successfully Deleted']);
    }

    
}
