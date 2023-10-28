<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ForumModel;
use App\Models\imagesKomenForumModel;
use App\Models\komenForumModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class KomenForumController extends Controller
{
    // Membuat komen
    public function create(Request $request, $idForum)
    {
        $data = ForumModel::where('id', $idForum)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $storeData = $request->all();
        $images = $request->file('images_komenForum_path'); // Ganti 'images' sesuai dengan nama field yang digunakan dalam v-file-input


        $validator = Validator::make($storeData, [
            'isi' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = komenForumModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'CommentForum'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'CommentForum'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $komen_by = auth()->user()->nama_persona;

        $dataKomen = collect($request)->only(komenForumModel::filters())->all();
        $dataKomen['uuid'] = $uuid;
        $dataKomen['user_id'] = $user_id;
        $dataKomen['komen_by'] = $komen_by;
        $dataKomen['forum_id'] = $idForum;

        $komenForum = komenForumModel::create($dataKomen);

        // Store each image
        if($images != null){
            foreach($images as $image) {
                $imagePath = $image->store('/images-komenForum-path', 'public');
                imagesKomenForumModel::create([
                    'komenForum_id' => $komenForum->id,
                    'images_komenForum_path' => $imagePath,
                ]);
            }
        }

        return response([
            'message' => 'Komen Forum Successfully Added',
            'data' => $komenForum,
        ], 200);
    }

    // Show all NPC for Admin
    public function getAll(){
        $dataKomenForum = komenForumModel::orderBy('created_at', 'desc')->get();
        $dataUser = User::orderBy('created_at', 'desc')->get();
        $dataImagesKomenForum = imagesKomenForumModel::orderBy('created_at', 'desc')->get();


        return response([
            'message' => 'Komen is succesfully show',
            'dataKomenForum' => $dataKomenForum,
            'dataUser' => $dataUser,
            'dataImagesKomenForum' => $dataImagesKomenForum,
        ], 200);
    }
}
