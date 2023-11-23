<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransaksiLayananModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;


class TransaksiLayananController extends Controller
{
    //Membuat Transaksi Layanan
    public function create(Request $request, $idServicer)
    {

        $dataServicer = User::where('id', $idServicer)->first();

        if (is_null($dataServicer)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'project_name' => 'required',
            'offering_cost' => 'required',
            'description' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = TransaksiLayananModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'SvcTrk'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'SvcTrk'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $customer_name = auth()->user()->nama_persona;

        $dataTransaksiLayanan = collect($request)->only(TransaksiLayananModel::filters())->all();

        if (isset($request->storyboard)) {
            $image_name = \Str::random(15);
            $file = $dataTransaksiLayanan['storyboard'];
            $extension = $file->getClientOriginalExtension();

            $uploadDoc = $request->storyboard->storeAs(
                'storyboard_transaksiLayanan',
                $image_name . '.' . $extension,
                ['disk' => 'public']
            );

            $dataTransaksiLayanan['storyboard'] = $uploadDoc;
        }

        $dataTransaksiLayanan['uuid'] = $uuid;
        $dataTransaksiLayanan['user_id_customer'] = $user_id;
        $dataTransaksiLayanan['user_id_servicer'] = $idServicer;
        $dataTransaksiLayanan['customer_name'] = $customer_name;

        $TransaksiLayanan = TransaksiLayananModel::create($dataTransaksiLayanan);

        return response([
            'message' => 'TransaksiLayanan Successfully Added',
            'data' => $TransaksiLayanan,
        ], 200);
    }

    // Melihat Transaksi Layanan
    public function read($uuid)
    {
        $data = TransaksiLayananModel::where('uuid', $uuid)->first();

        if(!is_null($data)){
            return response([
                'message' => 'Transaksi Layanan Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Transaksi Layanan Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    //Menghapus Transaksi Layanan
    public function delete($uuid)
    {
        $data = TransaksiLayananModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Transaksi Layanan Successfully Deleted']);
    }

    // Show all transaksi layanan
    public function getAll(){
        $data = TransaksiLayananModel::orderBy('created_at', 'desc')->get();

        return response([
            'message' => 'Services transaction is succesfully show',
            'data' => $data,
        ], 200);
    }
}
