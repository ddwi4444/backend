<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\imagesMerchandiseModel;
use App\Models\MerchandiseModel;
use App\Models\orderMerchandiseModel;
use App\Models\User;
use App\Models\orderProdukMerchandiseModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class MerchandiseController extends Controller
{
    public function create(Request $request)
    {
        $storeData = $request->all();
        $images = $request->file('images_merchandise_path'); // Ganti 'images' sesuai dengan nama field yang digunakan dalam v-file-input

        $validator = Validator::make($storeData, [
            'nama' => 'required',
            'deskripsi' => 'required',
            'thumbnail' => 'required|mimes:jpg,bmp,png',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Store UUID
        $get_data = MerchandiseModel::orderBy('created_at', 'DESC')->first();
        if (is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex() . 'Merchandise' . date('ymd') . '-' . sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex() . 'Merchandise' . date('ymd') . '-' . sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;

        $dataMerchandise = collect($request)->only(MerchandiseModel::filters())->all();

        $image_name = \Str::random(5) . str_replace(' ', '', $dataMerchandise['nama']) . \Str::random(5);
        $file = $dataMerchandise['thumbnail'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->thumbnail->storeAs(
            'merchandise_thumbnail',
            $image_name . '.' . $extension,
            ['disk' => 'public']
        );

        $dataMerchandise['uuid'] = $uuid;
        $dataMerchandise['thumbnail'] = $uploadDoc;
        $dataMerchandise['user_id'] = $user_id;

        $merchandise = MerchandiseModel::create($dataMerchandise);

        // Store each image
        foreach ($images as $image) {
            $imagePath = $image->store('/images-merchandise-path', 'public');
            imagesMerchandiseModel::create([
                'merchandise_id' => $merchandise->id,
                'images_merchandise_path' => $imagePath,
            ]);
        }

        return response([
            'message' => 'Merchandise Successfully Added',
            'data' => $merchandise,
        ], 200);
    }


    public function read($uuid)
    {
        $data = MerchandiseModel::where('uuid', $uuid)->first();

        if (!is_null($data)) {
            return response([
                'message' => 'Merchandise Succcessfully Showed',
                'data' => $data,
            ], 200);
        }

        return response([
            'message' => 'Merchandise Unsucccessfully Showed',
            'data' => null,
        ], 404);
    }

    public function update(Request $request, $uuid)
    {
        $data = MerchandiseModel::where('uuid', $uuid)->first();
        $images = $request->file('images_merchandise_path'); // Ganti 'images' sesuai dengan nama field yang digunakan dalam v-file-input

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'nama' => 'required',
            'deskripsi' => 'required',
            'harga' => 'required|numeric',
            'stok' => 'required|numeric',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataMerchandise = collect($request)->only(MerchandiseModel::filters())->all();

        if (isset($request->thumbnail)) {
            if (!empty($data->thumbnail)) {
                Storage::delete("public/" . $data->thumbnail);
            }
            $image_name = \Str::random(5) . $request->jenis_kendaraan_id . str_replace(' ', '', $dataMerchandise['nama']) . \Str::random(5);
            $file = $dataMerchandise['thumbnail'];
            $extension = $file->getClientOriginalExtension();

            $uploadDoc = $request->thumbnail->storeAs(
                'merchandise_thumbnail',
                $image_name . '.' . $extension,
                ['disk' => 'public']
            );

            $dataMerchandise['thumbnail'] = $uploadDoc;
        }

        $data->update($dataMerchandise);

        if (!empty($images)) {
            // Delete all records in imagesMerchandiseModel where merchandise_id is equal to $data->id
            imagesMerchandiseModel::where('merchandise_id', $data->id)->delete();
        
            // Delete the old thumbnail
            Storage::delete("public/" . $data->thumbnail);
        
            foreach ($images as $image) {
                // Store the new image
                $imagePath = $image->store('/images-merchandise-path', 'public');
        
                // Create a new record in imagesMerchandiseModel
                imagesMerchandiseModel::create([
                    'merchandise_id' => $data->id,
                    'images_merchandise_path' => $imagePath,
                ]);
            }
        }        

        return response()->json(['Success' => true, 'message' => 'Merchandise Successfully Changed']);
    }

    public function getImagesMerchandise($idMerchandise){
        $data = imagesMerchandiseModel::where('merchandise_id', $idMerchandise)->get();

        return response([
            'message' => 'Images merchandise is succesfully show',
            'data' => $data,
        ], 200);
    }

    public function delete($uuid)
    {
        $data = MerchandiseModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $data->delete();

        return response()->json(['Success' => true, 'message' => 'Merchandise Successfully Deleted']);
    }

    // Show all Merchandise for Admin
    public function getAll()
    {
        $data = MerchandiseModel::orderBy('updated_at', 'desc')->get();

        return response([
            'message' => 'Merchandise is succesfully show',
            'data' => $data,
        ], 200);
    }

    // Show all Merchandise for Admin
    public function getDataMerchandise()
    {
        $dataMerchandises = MerchandiseModel::orderBy('created_at', 'desc')->get();
        $dataImageMerchandises = imagesMerchandiseModel::orderBy('created_at', 'desc')->get();

        return response([
            'message' => 'Merchandise is succesfully show',
            'dataMerchandises' => $dataMerchandises,
            'dataImageMerchandises' => $dataImageMerchandises
        ], 200);
    }

    // Show order merchandise
    public function getDataOrderMerchandise($user_uuid)
    {
        $user = User::where('uuid', $user_uuid)->first();

        if ($user->role != 'admin') {
            $dataOrderMerchandises = OrderMerchandiseModel::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
        } else {
            $dataOrderMerchandises = OrderMerchandiseModel::orderBy('created_at', 'desc')->get();
        }


        // Assuming there's a relationship between uuidOrderMerchandise and orderMerchandiseModel
        $dataOrderProductsMerchandise = OrderProdukMerchandiseModel::whereIn('uuidOrderMerchandise', $dataOrderMerchandises->pluck('uuid'))->get();

        return response([
            'message' => 'Merchandise is successfully shown',
            'dataOrderMerchandises' => $dataOrderMerchandises,
            'dataOrderProductsMerchandise' => $dataOrderProductsMerchandise,
        ], 200);
    }

    public function getDataDetailOrderProductsMerchandise($uuidMerchandiseOrder)
    {
        // Assuming there's a relationship between user_id and orderMerchandiseModel
        $dataDetailOrderProductsMerchandise = orderProdukMerchandiseModel::where('uuidOrderMerchandise', $uuidMerchandiseOrder)->get();

        return response([
            'message' => 'Merchandise is successfully shown',
            'dataDetailOrderProductsMerchandise' => $dataDetailOrderProductsMerchandise,
        ], 200);
    }

    public function submitOrder(Request $request)
    {
        // Store UUID
        $get_data = orderMerchandiseModel::orderBy('created_at', 'DESC')->first();
        if (is_null($get_data)) {
            $uuid = Uuid::uuid4()->getHex() . 'OrderMrc' . date('ymd') . '-' . sprintf('%09d', 1); // toString();
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $uuid = Uuid::uuid4()->getHex() . 'OrderMrc' . date('ymd') . '-' . sprintf('%09d', $increment); // toString();
        }

        $user_id = auth()->user()->id;
        $order_by = auth()->user()->nama_persona;

        // Simpan data pesanan ke dalam database
        $order = new orderMerchandiseModel();
        $order->nama = $request->input('nama');
        $order->alamat = $request->input('alamat');
        $order->tlp = $request->input('tlp');
        $order->total_prices = $request->input('totalPrices');
        $order->uuid = $uuid;
        $order->user_id = $user_id;
        $order->order_by = $order_by;
        $order->status = 0;
        $order->save();

        $products = $request->input('products');

        foreach ($products as $productData) {
            $productData['uuidOrderMerchandise'] = $uuid;
            $productData['namaProduk'] = $productData['namaProduct'];
            $productData['UUIDProduk'] = $productData['uuidProduct'];
            $productData['quantity'] = $productData['totalPcsProduct'];
            $productData['notes'] = $productData['notesProduct'];

            $dataProduct = MerchandiseModel::where('uuid', $productData['uuidProduct'])->first();

            // Assuming 'orderProdukMerchandiseModel' is the Eloquent model for your database table
            orderProdukMerchandiseModel::create($productData);

            // Update stock if $dataProduct is found
            if ($dataProduct) {
                $dataProduct->update([
                    'stok' => $dataProduct->stok - $productData['quantity'],
                ]);
            }
        }


        return response()->json(['message' => 'Order berhasil disimpan'], 200);
    }

    public function submitFileBuktiTf(Request $request, $uuidMerchandise)
    {
        $data = orderMerchandiseModel::where('uuid', $uuidMerchandise)->first();

        $dataMerchandise = collect($request)->only(orderMerchandiseModel::filters())->all();

        $image_name = \Str::random(15);
        $file = $dataMerchandise['buktiTf'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->buktiTf->storeAs(
            'buktiTf_merchandise',
            $image_name . '.' . $extension,
            ['disk' => 'public']
        );

        $dataMerchandise['buktiTf'] = $uploadDoc;
        $dataMerchandise['status'] = 1;

        $data->update($dataMerchandise);

        return response()->json(['message' => 'Order berhasil disimpan'], 200);
    }

    public function submitAddNoResi(Request $request, $uuidMerchandise)
    {
        $data = orderMerchandiseModel::where('uuid', $uuidMerchandise)->first();

        $dataMerchandise['noResi'] = $request->noResi;

        $data->update($dataMerchandise);

        return response()->json(['message' => 'Order berhasil disimpan'], 200);
    }

    public function deleteOrder($uuid)
    {
        $data = orderMerchandiseModel::where('uuid', $uuid)->first();

        if (is_null($data)) {
            return response()->json(['Failure' => true, 'message' => 'Data not found']);
        }

        $data->delete();
    }

    public function confirmPayment($uuid)
    {
        // Find the record with the given UUID
        $order = orderMerchandiseModel::where('uuid', $uuid)->first();

        // Check if the record exists
        if ($order) {
            // Update the 'confirm_buktiTf' field to 1
            $order->update(['confirm_buktiTf' => 1]);

            // Return a JSON response indicating success
            return response()->json(['success' => true, 'message' => 'Merchandise Successfully Updated']);
        } else {
            // Return a JSON response indicating failure (record not found)
            return response()->json(['success' => false, 'message' => 'Merchandise not found'], 404);
        }
    }
}
