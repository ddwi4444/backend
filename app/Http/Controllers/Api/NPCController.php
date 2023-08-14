<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\NPCModel;
use Validator;

class NPCController extends Controller
{
    // Untuk membuat npc
    public function create(Request $request)
    {
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'my_profile' => 'required',
            'story' => 'required',
            'image_npc' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $nama_author = auth()->user()->nama_persona;
        $user_id = auth()->user()->id;

        $dataNPC = collect($request)->only(NPCModel::filters())->all();
        $dataNPC['nama_author'] = $nama_author;
        $dataNPC['user_id'] = $user_id;
        $npc = NPCModel::create($dataNPC);

        return response([
            'message' => 'NPC Successfully Added',
            'data' => $npc,
        ], 200);
    }

    // Menampilkan NPC pada single page
    public function read($id)
    {
        $data = NPCModel::where('id', $id)->first();

        if(!is_null($data)){
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
    public function update(Request $request, $id)
    {
        $data = NPCModel::find($id);

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'my_profile' => 'required',
            'story' => 'required',
            'image_npc' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataNPC = collect($request)->only(NPCModel::filters())->all();
        $data->update($dataNPC);

        return response()->json(['Success'=> true, 'message'=> 'NPC Successfully Changed']);
    }

    // Menghapus npc
    public function delete($id)
    {
        $data = NPCModel::where('id', $id)->first();

        if(is_null($data)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success'=> true, 'message'=> 'NPC Successfully Deleted']);
    }
}