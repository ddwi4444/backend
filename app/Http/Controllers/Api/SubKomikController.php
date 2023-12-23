<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KomikModel;
use App\Models\likeSubKomikModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $dataKomikPusat = KomikModel::where('id', $id)->first();

        if (is_null($dataKomikPusat)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
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
        $get_data = SubKomikModel::orderBy('created_at', 'DESC')->first();
        if (is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex() . 'SubComic' . date('ymd') . '-' . sprintf('%09d', 1);
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex() . 'SubComic' . date('ymd') . '-' . sprintf('%09d', $increment);
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

        // Add Slug
        $slug = \Str::slug($dataKomik['judul']); // Generating slug from the 'judul'
        $dataKomik['slug'] = $slug;

        $dataKomik['uuid'] = $uuid;
        $dataKomik['content'] = $uploadDocContent;
        $dataKomik['thumbnail'] = $uploadDoc;
        $dataKomik['post_by'] = $nama_author;
        $dataKomik['nama_author'] = $nama_author;
        $dataKomik['user_id'] = $user_id;
        $dataKomik['komik_id'] = $komik_id;

        $komik = SubKomikModel::create($dataKomik);
        $dataKomikPusat->update(['status' => 1]);

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

        // Check if 'judul' is changed
        if ($request->has('judul') && $request->judul !== $data->judul) {
            // Generate new slug from the updated 'judul'
            $slug = \Str::slug($request->judul);
            $dataKomik['slug'] = $slug;
        }

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
    public function getAll($id, $user_id)
    {

        $user = User::where('id', $user_id)->first();

        if($user->role == 'admin'){
            $data = SubKomikModel::where('komik_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        }
        else if($user->role == 'student' || $user->role == 'osis'){
            $data = SubKomikModel::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }

        

        return response([
            'message' => 'Sub Comic is succesfully show',
            'data' => $data,
        ], 200);
    }

    public function getDataSubComic($slug, $uuid)
    {
        $dataSubComic = SubKomikModel::where('slug', $slug)
        ->where('uuid', $uuid)->first();

        return response([
            'message' => 'Sub Comic is succesfully show',
            'dataSubComic' => $dataSubComic,
        ], 200);
    }

    public function addJumlahView($uuidSubComic)
    {
        $data = SubKomikModel::where('uuid', $uuidSubComic)->first();

        $data->update(['jumlah_view' => $data->jumlah_view + 1]);

        return response([
            'response' => 'Comic is succesfully show',
        ], 200);
    }

    public function klikLike($uuidSubKomik, $uuidUser)
    {
        $data = likeSubKomikModel::where('subKomik_uuid', $uuidSubKomik)
            ->where('user_uuid', $uuidUser)
            ->first();
        // If data does not exist, create a new record
        if (empty($data)) {
            // Create a new record in FavoriteKomikModel with 'komik_uuid' set
            likeSubKomikModel::create([
                'subKomik_uuid' => $uuidSubKomik,
                'user_uuid' => $uuidUser,
                // Other fields you may want to set
            ]);

            $dataJumlahLike = SubKomikModel::where('uuid', $uuidSubKomik)->first();
            $dataJumlahLike->update(['jumlah_like' => $dataJumlahLike->jumlah_like + 1]);

            $iyaLike = 1;

            // You can also do something after creating the record, if needed
        } else {
            $dataJumlahLike = SubKomikModel::where('uuid', $uuidSubKomik)->first();
            $dataJumlahLike->update(['jumlah_like' => $dataJumlahLike->jumlah_like - 1]);
            // Data already exists, delete the existing record
            $data->delete();

            $iyaLike = 0;

            // You can also do something after deleting the record, if needed
        }

        return response([
            'iyaLike' => $iyaLike,
        ], 200);
    }

    public function getDataLikeSubKomik($user_uuid)
    {
        $dataLikeSubComics = likeSubKomikModel::where('user_uuid', $user_uuid)->get();

        return response([
            'message' => 'Favorite comics is succesfully show',
            'dataLikeSubComics' => $dataLikeSubComics,
        ], 200);
    }
}
