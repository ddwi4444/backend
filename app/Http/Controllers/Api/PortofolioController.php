<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PortofolioModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\Validator;

class PortofolioController extends Controller
{
    public function create(Request $request)
    {
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'thumbnail' => 'required|mimes:jpg,bmp,png',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = PortofolioModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'Portfolio'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'Portfolio'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        } 

        $user_id = auth()->user()->id;

        $dataPortofolio = collect($request)->only(PortofolioModel::filters())->all();

        $image_name = \Str::random(15);
        $file = $dataPortofolio['thumbnail'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->thumbnail->storeAs(
            'portofolio_thumbnail',
            $image_name.'.'.$extension,
            ['disk' => 'public']
        );

        $dataPortofolio['uuid'] = $uuid;
        $dataPortofolio['thumbnail'] = $uploadDoc;
        $dataPortofolio['user_id'] = $user_id;

        $portofolio = PortofolioModel::create($dataPortofolio);

        return response([
            'message' => 'Portofolio Successfully Added',
            'data' => $portofolio,
        ], 200);
    }

    public function read($uuid)
    {
        $data = PortofolioModel::where('uuid', $uuid)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Portofolio Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Portofolio Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    public function update(Request $request, $uuid){
        $data = PortofolioModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'thumbnail' => 'required|mimes:jpg,bmp,png',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataPortofolio = collect($request)->only(PortofolioModel::filters())->all();

        if(isset($request->thumbnail)){
            if(!empty($data->thumbnail)){
                Storage::delete("public/".$data->thumbnail);
            }
            $image_name = \Str::random(15);
            $file = $dataPortofolio['thumbnail'];
            $extension = $file->getClientOriginalExtension();
    
            $uploadDoc = $request->thumbnail->storeAs(
                'portofolio_thumbnail',
                $image_name.'.'.$extension,
                ['disk' => 'public']
            );
    
            $dataPortofolio['thumbnail'] = $uploadDoc;
        }

        $data->update($dataPortofolio);

        return response()->json(['Success'=> true, 'message'=> 'Portofolio Successfully Changed']);
    }

    public function delete($uuid)
    {
        $data = PortofolioModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Portofolio Successfully Deleted']);
    }

    // Show all NPC for Admin
    public function getAll(){
        $data = PortofolioModel::orderBy('created_at', 'desc')->get();

        return response([
            'message' => 'Portfolio is succesfully show',
            'data' => $data,
        ], 200);
    }
}
