<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReviewLayananModel;
use App\Models\User;
use App\Models\TransaksiLayananModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

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

        // Store UUID
        $get_data = ReviewLayananModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'ReviewServices'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'ReviewServices'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $post_by = auth()->user()->nama_persona;
        $idServicer = $dataTransaksiLayanan->user_id_servicer;


        $dataReviewLayanan = collect($request)->only(ReviewLayananModel::filters())->all();

        $dataReviewLayanan['uuid'] = $uuid;
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

    public function read($uuid)
    {
        $data = ReviewLayananModel::where('uuid', $uuid)->first();

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

    public function update(Request $request, $uuid){
        $data = ReviewLayananModel::where('uuid', $uuid)->first();

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

    public function delete($uuid)
    {
        $data = ReviewLayananModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'ReviewLayanan Successfully Deleted']);
    }

    public function get_reviewLayanan($idTransaksiLayanan)
    {
        $dataReviewLayanan = ReviewLayananModel::where('transaksi_layanan_id', $idTransaksiLayanan)->first();

        if (is_null($dataReviewLayanan)) {
            return response()->json(['Failure' => true, 'message' => 'Service transaction servicer not found']);
        }

        return response()->json(['data' => $dataReviewLayanan, 'Success' => true, 'message' => 'Successfully Get Service Transaction Data']);
    }    

    public function getAll($idServicer){
        $data = ReviewLayananModel::where('user_id_servicer', $idServicer)->get();

        if ($data->isEmpty()) {
            return response()->json(['message' => 'No service reviews found', 'data' => []], 200);
        }

        // Menghitung jumlah total rating
        $totalRating = 0;

        // Menghitung jumlah data yang ada
        $count = $data->count();

        // Memeriksa apakah ada data yang ditemukan
        if ($count > 0) {
            // Mengakumulasi total rating
            foreach ($data as $review) {
                $totalRating += $review->rating;
            }

            // Menghitung rata-rata rating
            $rerataRating = $totalRating / $count;
        } else {
            // Menangani kasus jika tidak ada data yang ditemukan
            $rerataRating = 0;
            echo "Tidak ada data rating ditemukan.";
        }
    
        $reviewers = [];
    
        foreach ($data as $review) {
            $userId = $review->user_id_customer;
        
            // Check if the user with the current ID is not already in $reviewers
            if (!array_key_exists($userId, $reviewers)) {
                $dataReviewer = User::where('id', $userId)->first();
        
                // Check if the user is found before adding to the array
                if ($dataReviewer) {
                    $reviewers[$userId] = $dataReviewer;
                }
            }
        }
    
        return response([
            'message' => 'Service reviews are successfully shown',
            'dataReview' => $data,
            'dataReviewers' => $reviewers,
            'rerataRating' => $rerataRating,
        ], 200);
    }
}
