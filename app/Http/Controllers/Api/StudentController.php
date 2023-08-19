<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentController extends Controller
{
    // Untuk mengupdate komik
    public function update(Request $request, $uuid)
    {
        $user = User::where('uuid', $uuid)->first();

        if (is_null($user)) {
            return response()->json(['Failure' => true, 'message' => 'User not found']);
        }

        $updateData = $request->all();
        $validator = Validator::make($updateData, [
            'nama_OC' => 'required',
            'no_tlp' => 'required|numeric',
            'umur' => 'required',
            'tanggal_lahir' => 'required',
            'umur_rl' => 'required',
            'tanggal_lahir_rl' => 'required',
            'zodiak' => 'required',
            'ras' => 'required',
            'tinggi_badan' => 'required',
            'berat_badan' => 'required',
            'MBTI' => 'required',
            'hobi' => 'required',
            'like' => 'required',
            'did_not_like' => 'required',
            'quotes' => 'required',
            'story_character' => 'required',
            'eskul' => 'required',
            'image' => 'required|mimes:jpg,bmp,png',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataUser = collect($request)->only(User::filters())->all();
        $image_name = \Str::random(15);
        $file = $dataUser['image'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->image->storeAs(
            'user_image',
            $image_name . '.' . $extension,
            ['disk' => 'public']
        );

        $dataUser['image'] = $uploadDoc;
        $user->update($dataUser);

        return response()->json(['Success' => true, 'message' => 'Successfully Updated Your Profile']);
    }
}
