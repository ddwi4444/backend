<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NPCModel;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Validator;

class NPCController extends Controller
{
    // Untuk membuat npc
    public function create(Request $request)
    {
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'npc_name' => 'required',
            'npc_profile' => 'required',
            'npc_story' => 'required',
            'image_npc' => 'required|mimes:jpg,bmp,png',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = NPCModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'NPC'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'NPC'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $nama_author = auth()->user()->nama_persona;
        $user_id = auth()->user()->id;

        $dataNPC = collect($request)->only(NPCModel::filters())->all();

        $image_name = \Str::random(15);
        $file = $dataNPC['image_npc'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->image_npc->storeAs(
            'npc_image',
            $image_name . '.' . $extension,
            ['disk' => 'public']
        );

        $dataNPC['uuid'] = $uuid;
        $dataNPC['image_npc'] = $uploadDoc;
        $dataNPC['nama_author'] = $nama_author;
        $dataNPC['user_id'] = $user_id;
        $npc = NPCModel::create($dataNPC);

        return response([
            'message' => 'NPC Successfully Added',
            'data' => $npc,
        ], 200);
    }

    // Menampilkan NPC pada single page
    public function read($uuid)
    {
        $data = NPCModel::where('uuid', $uuid)->first();

        if (!is_null($data)) {
            return response([
                'message' => 'NPC Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'NPC Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    // Untuk mengupdate NPC
    public function update(Request $request, $uuid)
    {
        $data = NPCModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'npc_name' => 'required',
            'npc_profile' => 'required',
            'npc_story' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataNPC = collect($request)->only(NPCModel::filters())->all();

        if (isset($request->image_npc)) {
            if (!empty($data->image_npc)) {
                Storage::delete("public/" . $data->image_npc);
            }
            $image_name = \Str::random(15);
            $file = $dataNPC['image_npc'];
            $extension = $file->getClientOriginalExtension();

            $uploadDoc = $request->image_npc->storeAs(
                'npc_image',
                $image_name . '.' . $extension,
                ['disk' => 'public']
            );

            $dataNPC['image_npc'] = $uploadDoc;
        }

        $data->update($dataNPC);

        return response(['Success' => true, 'message' => 'NPC Successfully Changed']);
    }

    // Menghapus npc
    public function delete($uuid)
    {
        $data = NPCModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response(['Failure' => true, 'message' => 'Data not found']);
        }

        $data->delete();

        return response(['Success' => true, 'message' => 'NPC Successfully Deleted']);
    }

    // Show all NPC for Admin
    public function getAll(){
        $data = NPCModel::orderBy('updated_at', 'desc')->get();

        return response([
            'message' => 'NPC is succesfully show',
            'data' => $data,
        ], 200);
    }

    public function getAllForAbout(){
        $data = NPCModel::orderBy('updated_at', 'desc')
        ->select('npc_name', 'npc_profile', 'nama_author', 'npc_story', 'image_npc') // Replace 'column1', 'column2', 'column3' with the columns you want
        ->get();

        return response([
            'message' => 'NPC is succesfully show',
            'data' => $data,
        ], 200);
    }
}
