<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForumController extends Controller
{
    public function create(Request $request)
    {
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'isi' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user_id = auth()->user()->id;

        $dataForum = collect($request)->only(ForumModel::filters())->all();

        $dataForum['user_id'] = $user_id;

        $forum = ForumModel::create($dataForum);

        return response([
            'message' => 'Forum Successfully Added',
            'data' => $forum,
        ], 200);
    }

    public function read($id)
    {
        $data = ForumModel::where('id', $id)->first();

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
        $data = ForumModel::find($id);

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

        $dataForum = collect($request)->only(ForumModel::filters())->all();
        $data->update($dataForum);

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Changed']);
    }

    public function delete($id)
    {
        $data = ForumModel::where('id', $id)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Deleted']);
    }
}
