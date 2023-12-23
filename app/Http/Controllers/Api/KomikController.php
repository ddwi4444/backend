<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\KomikModel;
use App\Models\SubKomikModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use Validator;

class KomikController extends Controller
{
    // Untuk membuat komik
    public function create(Request $request)
    {
        $storeData = $request->all();

        $validator = Validator::make($storeData, [
            'judul' => 'required',
            'genre' => 'required',
            'thumbnail' => 'required|mimes:jpg,bmp,png',
            'volume' => 'required',
            'instagram_author' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = KomikModel::orderBy('created_at', 'DESC')->first();
        if (is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex() . 'Comic' . date('ymd') . '-' . sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex() . 'Comic' . date('ymd') . '-' . sprintf('%09d', $increment); // toString();
        }

        $nama_author = auth()->user()->nama_persona;
        $user_id = auth()->user()->id;

        $dataKomik = collect($request)->only(KomikModel::filters())->all();

        $image_name = \Str::random(15);
        $file = $dataKomik['thumbnail'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->thumbnail->storeAs(
            'komik_thumbnail',
            $image_name . '.' . $extension,
            ['disk' => 'public']
        );

        $dataKomik['uuid'] = $uuid;
        $dataKomik['thumbnail'] = $uploadDoc;
        $dataKomik['post_by'] = $nama_author;
        $dataKomik['user_id'] = $user_id;

        // Add the line to generate and store the slug
        $dataKomik['slug'] = Str::slug($dataKomik['judul']);

        $komik = KomikModel::create($dataKomik);

        return response([
            'message' => 'Komik Successfully Added',
            'data' => $komik,
        ], 200);
    }


    // Menampilkan komik pada single page
    public function read($uuid)
    {
        $data = KomikModel::where('uuid', $uuid)->first();

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
        $data = KomikModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'judul' => 'required',
            'genre' => 'required',
            'volume' => 'required',
            'instagram_author' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataKomik = collect($request)->only(KomikModel::filters())->all();

        // Check if the judul field is being updated
        if ($data->judul !== $request->judul) {
            // Update the slug if judul is changed
            $dataKomik['slug'] = Str::slug($request->judul);
        }

        if (isset($request->thumbnail)) {
            if (!empty($data->thumbnail)) {
                Storage::delete("public/" . $data->thumbnail);
            }
            $image_name = \Str::random(15);
            $file = $dataKomik['thumbnail'];
            $extension = $file->getClientOriginalExtension();

            $uploadDoc = $request->thumbnail->storeAs(
                'komik_thumbnail',
                $image_name . '.' . $extension,
                ['disk' => 'public']
            );

            $dataKomik['thumbnail'] = $uploadDoc;
        }

        $data->update($dataKomik);

        return response()->json(['Success' => true, 'message' => 'Komik Successfully Changed']);
    }


    // Menghapus Komik
    public function delete($uuid)
    {
        $data = KomikModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success' => true, 'message' => 'Komik Successfully Deleted']);
    }

    public function editStatusKomik($uuid)
    {
        $data = KomikModel::where('uuid', $uuid)->first();

        if ($data->status == 3) {
            $data->update(['status' => 1]);
            $isSuspen = 0;
        }
        else {
            $data->update(['status' => 3]);
            $isSuspen = 1;
        }

        return response()->json(['Success' => true, 'message' => 'Komik Successfully Deleted', 'isSuspen' => $isSuspen]);
    }

    // Show all Komik for Admin
    public function getAll($user_id)
    {
        $user = User::where('id', $user_id)->first();

        if($user->role == 'admin'){
            $data = KomikModel::orderBy('created_at', 'desc')->get();
        }
        else if($user->role == 'student' || $user->role == 'osis'){
            $data = KomikModel::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        }


        return response([
            'message' => 'Comic is succesfully show',
            'data' => $data,
        ], 200);
    }

    // Show all for komik in landingpage
    public function getDataKomik()
    {
        $dataLatest = KomikModel::orderBy('created_at', 'desc')->where('status', 1)->take(14)->get();

        $startDate = Carbon::now()->subDays(7);

        $dataPopular = KomikModel::where('created_at', '>=', $startDate)->where('status', 1)
            ->orderBy('jumlah_view', 'desc')
            ->take(14)
            ->get();

        $today = Carbon::now()->format('Y-m-d');

        $dataToday = KomikModel::whereDate('created_at', $today)->where('status', 1)
            ->orderBy('jumlah_view', 'desc')
            ->get();

        $dataComics = KomikModel::orderBy('updated_at', 'desc')->where('status', 1)->get();

        return response([
            'message' => 'Comic is succesfully show',
            'dataLatest' => $dataLatest,
            'dataPopular' => $dataPopular,
            'dataToday' => $dataToday,
            'dataComics' => $dataComics
        ], 200);
    }

    // Show all for komik today in landingpage
    public function getDataKomikTodayShow()
    {
        $today = now()->format('Y-m-d'); // Get the current date in 'Y-m-d' format

        $dataKomikTodaysShow = KomikModel::whereDate('created_at', $today)->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->get();

        return response([
            'message' => 'Comics for today are successfully shown',
            'dataKomikTodaysShow' => $dataKomikTodaysShow,
        ], 200);
    }

    public function addJumlahView($komik_uuid)
    {
        $data = KomikModel::where('uuid', $komik_uuid)->first();

        $data->update(['jumlah_view' => $data->jumlah_view + 1]);

        return response([
            'response' => 'Comic is succesfully show',
        ], 200);
    }

    public function getComicByCategori($category)
    {
        $dataKomiksByCategory = KomikModel::where('genre', $category)->where('status', 1)->get();
        $category = $category;

        return response([
            'response' => 'Comic is succesfully show',
            'dataKomiksByCategory' => $dataKomiksByCategory,
            'category' => $category,
        ], 200);
    }

    public function getDataKomikCategorysShow($category1, $category2, $category3)
    {
        $dataKomikCategorys1 = KomikModel::where('genre', $category1)->where('status', 1)->take(14)->get();
        $dataKomikCategorys2 = KomikModel::where('genre', $category2)->where('status', 1)->take(14)->get();
        $dataKomikCategorys3 = KomikModel::where('genre', $category3)->where('status', 1)->take(14)->get();

        return response([
            'response' => 'Comic is succesfully show',
            'dataKomikCategorys1' => $dataKomikCategorys1,
            'dataKomikCategorys2' => $dataKomikCategorys2,
            'dataKomikCategorys3' => $dataKomikCategorys3,
        ], 200);
    }

    public function getDataKomikSinglePost($slug, $uuid)
    {
        $dataComic = KomikModel::where('slug', $slug)
        ->where('uuid', $uuid)
        ->first();

        $dataSubComics = SubKomikModel::where('komik_id', $dataComic->id)
        ->orderBy('created_at', 'desc')
        ->get();
        
        return response([
            'response' => 'Comic is succesfully show',
            'dataComic' => $dataComic,
            'dataSubComics' => $dataSubComics,
        ], 200);
    }
}
