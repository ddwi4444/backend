<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReviewLayananModel;
use App\Models\TransaksiLayananModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewLayananController extends Controller
{
    public function create(Request $request, $idTransaksiLayanan)
    {

        $dataTransaksiLayanan = TransaksiLayananModel::where('id', $idTransaksiLayanan)->first();

        if(is_null($dataTransaksiLayanan)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }
        
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'isi' => 'required',
            'rating' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user_id = auth()->user()->id;
        $post_by = auth()->user()->nama_persona;
        $idServicer = $dataTransaksiLayanan->user_id_servicer;


        $dataReviewLayanan = collect($request)->only(ReviewLayananModel::filters())->all();

        $dataReviewLayanan['user_id_customer'] = $user_id;
        $dataReviewLayanan['user_id_servicer'] = $idServicer;
        $dataReviewLayanan['transaksi_layanan_id'] = $idTransaksiLayanan;
        $dataReviewLayanan['post_by'] = $post_by;

        $ReviewLayanan = ReviewLayananModel::create($dataReviewLayanan);

        return response([
            'message' => 'ReviewLayanan Successfully Added',
            'data' => $ReviewLayanan,
        ], 200);
    }

    public function read($id)
    {
        $data = ReviewLayananModel::where('id', $id)->first();

        if(!is_null($data)){
            return response([
                'message' => 'ReviewLayanan Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'ReviewLayanan Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    public function update(Request $request, $id){
        $data = ReviewLayananModel::find($id);

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'isi' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataReviewLayanan = collect($request)->only(ReviewLayananModel::filters())->all();
        $data->update($dataReviewLayanan);

        return response()->json(['Success'=> true, 'message'=> 'ReviewLayanan Successfully Changed']);
    }

    public function delete($id)
    {
        $data = ReviewLayananModel::where('id', $id)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'ReviewLayanan Successfully Deleted']);
    }
}
