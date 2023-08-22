<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
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
        $post_by = auth()->user()->nama_persona;

        $dataAnnouncement = collect($request)->only(AnnouncementModel::filters())->all();

        $dataAnnouncement['user_id'] = $user_id;
        $dataAnnouncement['post_by'] = $post_by;

        $forum = AnnouncementModel::create($dataAnnouncement);

        return response([
            'message' => 'Forum Successfully Added',
            'data' => $forum,
        ], 200);
    }

    public function read($id)
    {
        $data = AnnouncementModel::where('id', $id)->first();

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
        $data = AnnouncementModel::find($id);

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

        $dataAnnouncement = collect($request)->only(AnnouncementModel::filters())->all();
        $data->update($dataAnnouncement);

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Changed']);
    }

    public function delete($id)
    {
        $data = AnnouncementModel::where('id', $id)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Deleted']);
    }
}
