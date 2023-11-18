<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


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
            'nama_persona' =>'required',
            'umur' => 'required',
            'tanggal_lahir' => 'required',
            'zodiak' => 'required',
            'ras' => 'required',
            'tinggi_badan' => 'required',
            'berat_badan' => 'required',
            'MBTI' => 'required',
            'hobi' => 'required',
            'ig_acc' => 'required',
            'like' => 'required',
            'did_not_like' => 'required',
            'quotes' => 'required',
            'story_character' => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $dataUser = collect($request)->only(User::filters())->all();

        if (isset($request->image)) {
            if (!empty($user->image)) {
                Storage::delete("public/" . $user->image);
            }
        $image_name = \Str::random(15);
        $file = $dataUser['image'];
        $extension = $file->getClientOriginalExtension();

        $uploadDoc = $request->image->storeAs(
            'user_image',
            $image_name . '.' . $extension,
            ['disk' => 'public']
        );

        $dataUser['image'] = $uploadDoc;
    }

        $user->update($dataUser);

        $myProfile = User::where('uuid', $uuid)->first();

        return response()->json(['myProfile' => $myProfile,'Success' => true, 'message' => 'Successfully Updated Your Profile']);
    }

    // Untuk mengupdate komik
    public function getMyProfile($uuid)
    {
        $myProfile = User::where('uuid', $uuid)->first();

        if (is_null($myProfile)) {
            return response()->json(['Failure' => true, 'message' => 'User not found']);
        }

        return response()->json(['myProfile' => $myProfile, 'Success' => true, 'message' => 'Successfully Get Your Profile Data']);
    }

    // Untuk mengupdate komik
    public function getServicer()
    {
        $dataServicer = User::where('is_servicer', '1')->get();

        if (is_null($dataServicer)) {
            return response()->json(['Failure' => true, 'message' => 'User servicer not found']);
        }

        return response()->json(['dataServicer' => $dataServicer, 'Success' => true, 'message' => 'Successfully Get Servicer Data']);
    }
}
