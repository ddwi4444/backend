<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReviewLayananModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReviewLayananController extends Controller
{
    public function create(Request $request, $idStudent)
    {

        $dataStudent = User::where('id', $idStudent)->first();

        if(is_null($dataStudent)){
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

        $dataReviewLayanan = collect($request)->only(ReviewLayananModel::filters())->all();

        $dataReviewLayanan['user_id_reviewer'] = $user_id;
        $dataReviewLayanan['user_id_student'] = $idStudent;
        $dataReviewLayanan['post_by'] = $post_by;

        $forum = ReviewLayananModel::create($dataReviewLayanan);

        return response([
            'message' => 'Forum Successfully Added',
            'data' => $forum,
        ], 200);
    }

    public function read($id)
    {
        $data = ReviewLayananModel::where('id', $id)->first();

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

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Changed']);
    }

    public function delete($id)
    {
        $data = ReviewLayananModel::where('id', $id)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Deleted']);
    }
}
