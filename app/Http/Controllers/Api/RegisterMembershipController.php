<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserMembership;
use Illuminate\Support\Facades\Validator;

class RegisterMembershipController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required|email|unique:users',
            'username'     => 'required|unique:users',
            'password'  => 'required|min:8|confirmed',
            'firstname'      => 'required',
            'lastname'      => 'required',
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create userMembership
        $user = User::create([
            'email'     => $request->email,
            'username'     => $request->username,
            'password'  => bcrypt($request->password),
            'firstname'      => $request->firstname,
            'lastname'      => $request->lastname,
        ]);

        //return response JSON user is created
        if($user) {
            return response()->json([
                'success' => true,
                'user'    => $user,  
            ], 201);
        }

        //return JSON process insert failed 
        return response()->json([
            'success' => false,
        ], 409);
    }
}
