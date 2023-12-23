<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        // $validator = Validator::make($updateData, [
        //     'nama_persona' =>'required',
        //     'bio' => 'required',
        //     'umur' => 'required',
        //     'tanggal_lahir' => 'required',
        //     'zodiak' => 'required',
        //     'ras' => 'required',
        //     'tinggi_badan' => 'required',
        //     'berat_badan' => 'required',
        //     'MBTI' => 'required',
        //     'hobi' => 'required',
        //     'ig_acc' => 'required',
        //     'like' => 'required',
        //     'did_not_like' => 'required',
        //     'quotes' => 'required',
        //     'story_character' => 'required',
        // ]);

        //if validation fails
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }

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

        return response()->json(['myProfile' => $myProfile, 'Success' => true, 'message' => 'Successfully Updated Your Profile']);
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

    public function getDataStudents()
    {
        $dataStudents = User::orderBy('created_at', 'DESC')
            ->where('role', '!=', 'admin')
            ->select(
                'nama_persona',
                'bio',
                'image',
                'ig_acc',
                'umur',
                'tanggal_lahir',
                'zodiak',
                'ras',
                'tinggi_badan',
                'berat_badan',
                'MBTI',
                'hobi',
                'like',
                'did_not_like',
                'quotes',
                'story_character',
                'role',
            ) // Replace 'column1', 'column2', 'column3' with the columns you want
            ->get();

        return response()->json([
            'dataStudents' => $dataStudents,
            'Success' => true,
            'message' => 'Successfully Get Servicer Data'
        ]);
    }

    public function getDataUser($admin_uuid)
    {
        $user = User::where('uuid', $admin_uuid)->first();

        if ($user->role == 'admin') {
            $dataUser = User::orderBy('created_at', 'DESC')
                ->where('role', '!=', 'admin')
                ->select('uuid', 'image', 'nama_persona', 'role', 'is_active', 'is_servicer', 'created_at') // Replace 'column1', 'column2', 'column3' with the columns you want
                ->get();
        }

        return response()->json([
            'data' => $dataUser,
            'Success' => true,
            'message' => 'Successfully Get Servicer Data'
        ]);
    }

    public function editDataUser(Request $request, $admin_uuid)
    {
        $user = User::where('uuid', $admin_uuid)->first();

        // Ensure the user exists
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        Log::info($request->all());


        $role = ""; // Initialize $role variable

        if ($request->role == "Student") {
            $role = "student";
        } else if ($request->role == "Osis") {
            $role = "osis";
        } else if ($request->role == "User") {
            $role = "user";
        }

        $is_servicer = ($request->is_servicer == "Servicer") ? 1 : 0;

        $user->update([
            'role' => $role,
            'is_servicer' => $is_servicer
        ]);

        return response()->json([
            'role' => $request->roles,
            'success' => true,
            'message' => 'Successfully updated user data'
        ]);
    }

    public function userActive($uuidUser)
    {
        $user = User::where('uuid', $uuidUser)->first();

        // Ensure the user exists
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ]);
        }

        if ($user->is_active == 1) {
            $user->update(['is_active' => 0]);
            $isActive = 0;
        } else {
            $isActive = 1;
            $user->update(['is_active' => 1]);
        }

        return response()->json([
            'isActive' => $isActive,
            'success' => true,
            'message' => 'Successfully updated user data'
        ]);
    }
}
