<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KomikModel;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SubKomikModel;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Validator;

class SubKomikController extends Controller
{
    // Untuk membuat komik
    public function create(Request $request, $id)
    {
        $dataKomik = KomikModel::where('id', $id)->first();

        if(is_null($dataKomik)){
            return response()->json(['Failure'=> true, 'message'=> 'Data not found']);
        }

        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'judul' => 'required',
            'thumbnail' => 'required|mimes:jpg,bmp,png',
            'content' => 'required|mimes:jpg,bmp,png',
            'chapter' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = SubKomikModel::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex().'SubComic'.date('ymd').'-'.sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex().'SubComic'.date('ymd').'-'.sprintf('%09d', $increment); // toString();
        }

        $nama_author = auth()->user()->nama_persona;
        $user_id = auth()->user()->id;
        $komik_id = $id;

        $dataKomik = collect($request)->only(SubKomikModel::filters())->all();

        // Image Thumbnail
        $image_name = \Str::random(15);
        $file = $dataKomik['thumbnail'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->thumbnail->storeAs(
            'subkomik_thumbnail',
            $image_name . '.' . $extension,
            ['disk' => 'public']
        );

        // Image Konten
        $image_name_content = \Str::random(15);
        $file_content = $dataKomik['content'];
        $extension = $file_content->getClientOriginalExtension();

        $uploadDocContent = $request->content->storeAs(
            'subkomik_content',
            $image_name_content . '.' . $extension,
            ['disk' => 'public']
        );

        $dataKomik['uuid'] = $uuid;
        $dataKomik['content'] = $uploadDocContent;
        $dataKomik['thumbnail'] = $uploadDoc;
        $dataKomik['post_by'] = $nama_author;
        $dataKomik['nama_author'] = $nama_author;
        $dataKomik['user_id'] = $user_id;
        $dataKomik['komik_id'] = $komik_id;
        $komik = SubKomikModel::create($dataKomik);

        return response([
            'message' => 'Komik Successfully Added',
            'data' => $komik,
        ], 200);
    }

    // Menampilkan komik pada single page
    public function read($uuid)
    {
        $data = SubKomikModel::where('uuid', $uuid)->first();

        if (!is_null($data)) {
            return response([
                'message' => 'Komik Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Komik Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    // Untuk mengupdate komik
    public function update(Request $request, $uuid)
    {
        $data = SubKomikModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'judul' => 'required',
            'chapter' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataKomik = collect($request)->only(SubKomikModel::filters())->all();

        if (isset($request->thumbnail)) {
            if (!empty($data->thumbnail)) {
                Storage::delete("public/" . $data->thumbnail);
            }
            // Image Thumbnail
            $image_name = \Str::random(15);
            $file = $dataKomik['thumbnail'];
            $extension = $file->getClientOriginalExtension();

            $uploadDoc = $request->thumbnail->storeAs(
                'subkomik_thumbnail',
                $image_name . '.' . $extension,
                ['disk' => 'public']
            );

            $dataKomik['thumbnail'] = $uploadDoc;
        }

        if (isset($request->content)) {
            if (!empty($data->content)) {
                Storage::delete("public/" . $data->content);
            }
            // Image Content
            $image_name_content = \Str::random(15);
            $file_content = $dataKomik['content'];
            $extension = $file_content->getClientOriginalExtension();

            $uploadDocContent = $request->content->storeAs(
                'subkomik_content',
                $image_name_content . '.' . $extension,
                ['disk' => 'public']
            );
            $dataKomik['content'] = $uploadDocContent;
        }

        $data->update($dataKomik);

        return response()->json(['Success' => true, 'message' => 'Komik Successfully Changed']);
    }

    // Menghapus Komik
    public function delete($uuid)
    {
        $data = SubKomikModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success' => true, 'message' => 'Komik Successfully Deleted']);
    }

    // Show all Komik for Admin
    public function getAll($id){
        $data = SubKomikModel::where('komik_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

        return response([
            'message' => 'Sub Comic is succesfully show',
            'data' => $data,
        ], 200);
    }
}
