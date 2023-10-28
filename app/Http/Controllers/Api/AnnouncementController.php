<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementModel;
use App\Models\imagesAnnouncementModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class AnnouncementController extends Controller
{
    public function create(Request $request)
    {
        $storeData = $request->all();
        $images = $request->file('images_announcement_path'); // Ganti 'images' sesuai dengan nama field yang digunakan dalam v-file-input

        $validator = Validator::make($storeData, [
            'isi' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = AnnouncementModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'Announcement'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'Announcement'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $post_by = auth()->user()->nama_persona;


        $dataAnnouncement = collect($request)->only(AnnouncementModel::filters())->all();

        $dataAnnouncement['uuid'] = $uuid;
        $dataAnnouncement['user_id'] = $user_id;
        $dataAnnouncement['post_by'] = $post_by;

        $announcement = AnnouncementModel::create($dataAnnouncement);

        // Store each image
        if($images != null){
            foreach($images as $image) {
                $imagePath = $image->store('/images-announcement-path', 'public');
                imagesAnnouncementModel::create([
                    'announcement_id' => $announcement->id,
                    'images_announcement_path' => $imagePath,
                ]);
            }
        }

        return response([
            'message' => 'Announcement Successfully Added',
            'data' => $announcement,
        ], 200);
    }

    public function read($uuid)
    {
        $data = AnnouncementModel::where('uuid', $uuid)->first();

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
        $data = AnnouncementModel::where('uuid', $uuid)->first();

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

    public function delete($uuid)
    {
        $data = AnnouncementModel::where('uuid', $uuid)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'Forum Successfully Deleted']);
    }

    // Show all NPC for Admin
    public function getAll(){
        $dataAnnouncement = AnnouncementModel::orderBy('created_at', 'desc')->get();
        $dataUser = User::orderBy('created_at', 'desc')->get();
        $dataImagesAnnouncement = imagesAnnouncementModel::orderBy('created_at', 'desc')->get();


        return response([
            'message' => 'Announcement is succesfully show',
            'dataAnnouncement' => $dataAnnouncement,
            'dataUser' => $dataUser,
            'dataImagesAnnouncement' => $dataImagesAnnouncement,
        ], 200);
    }
}
