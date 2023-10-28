<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumModel;
use App\Models\imagesForumModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;


class ForumController extends Controller
{
    public function create(Request $request)
    {
        $storeData = $request->all();
        $images = $request->file('images_forum_path'); // Ganti 'images' sesuai dengan nama field yang digunakan dalam v-file-input

        $validator = Validator::make($storeData, [
            'isi' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = ForumModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'Forum'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'Forum'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $post_by = auth()->user()->nama_persona;

        $dataForum = collect($request)->only(ForumModel::filters())->all();

        $dataForum['uuid'] = $uuid;
        $dataForum['user_id'] = $user_id;
        $dataForum['post_by'] = $post_by;

        $forum = ForumModel::create($dataForum);

        // Store each image
        if($images != null){
            foreach($images as $image) {
                $imagePath = $image->store('/images-forum-path', 'public');
                imagesForumModel::create([
                    'forum_id' => $forum->id,
                    'images_forum_path' => $imagePath,
                ]);
            }
        }

        return response([
            'message' => 'Forum Successfully Added',
            'data' => $forum,
        ], 200);
    }

    public function read($uuid)
    {
        $data = ForumModel::where('uuid', $uuid)->first();

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

    public function update(Request $request, $uuid){
        $data = ForumModel::where('uuid', $uuid)->first();

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

    public function delete($uuid)
    {
        $data = ForumModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Deleted']);
    }

    // Show all Komik for Admin
    public function getAll(){
        $data = ForumModel::orderBy('updated_at', 'desc')->get();
        $imagesForum = imagesForumModel::orderBy('updated_at', 'desc')->get();

        return response([
            'message' => 'Forum is succesfully show',
            'data' => $data,
            'imagesForum' => $imagesForum
        ], 200);
    }
}
