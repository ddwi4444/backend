<?php

namespace App\Http\Controllers\Api;

use JWTAuth;
use Validator, DB, Hash, Mail;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use App\Mail\RegisterMail;
use App\Mail\RecoverPasswordMail;
use Ramsey\Uuid\Uuid;





class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }

    // Login
    public function register()
    {
        //set validation
        $validator = Validator::make(request()->all(), [
            'email'     => 'required|email|unique:users',
            'username'     => 'required',
            'password'  => 'required|min:8',
            'firstname'      => 'required',
            'lastname'      => 'required',
        ]);

        
        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $get_data = User::orderBy('created_at','DESC')->first();
        if(is_null($get_data)) {
            $id = 'User'.date('ymd').'-'.sprintf('%09d', 1);
        } else {
            $find = substr($get_data->id, -9);
            $increment = $find + 1;
            $id = 'User'.date('ymd').'-'.sprintf('%09d', $increment);
        }

        $email = request('email');
        $username = request('username');
        $password = request('password');
        $firstname = request('firstname');
        $lastname = request('lastname');

        //create userMembership
        // $user = User::create([
        //     'email'     => request('email'),
        //     'username'     => request('username'),
        //     'password'  => Hash::make(request('password')),
        //     'firstname'      => request('firstname'),
        //     'lastname'      => request('lastname'),
        // ]);

        // $verification_code = Str::random(30); //Generate verification code
        // DB::table('membership_verifications')->insert(['membership_id'=>$user->id,'token'=>$verification_code]);

        $subject = "Please verify your email address.";
        // Mail::send('email.verify', ['name' => $firstname, 'verification_code' => $verification_code],
        //     function($mail) use ($email, $firstname, $subject){
        //         $mail->from(getenv('FROM_EMAIL_ADDRESS'), "From Historical Art Fantasia");
        //         $mail->to($email, $firstname);
        //         $mail->subject($subject);
        //     });

        // $token = \Str::random(25);

        

        $user = User::create([
            'id'     => $id,
            'uuid'       => Uuid::uuid4()->getHex(), // toString();
            'email'     => request('email'),
            'username'     => request('username'),
            'password'  => Hash::make(request('password')),
            'firstname'      => request('firstname'),
            'lastname'      => request('lastname'),
        ]);

        $verification_code = Str::random(30); //Generate verification code
        DB::table('user_verifications')->insert(['user_id'=>$user->id,'token'=>$verification_code]);

            $this->sendEmail($email, $verification_code, $firstname);

        return response()->json(['success'=> true, 'message'=> 'Thanks for signing up! Please check your email to complete your registration.']);
    }

    // TO send email
    public function sendEmail($email, $verification_code, $firstname){
        $mailData = [
            "title" => "Register Email Verifikasi",
            "firstname" => "Hello!, ".$firstname,
            "body1" => "Thank you for creating an account with us.",
            "body2" => "Please click on the link below or copy it into the address bar of your browser to confirm your email address : ",
            "verification_code" => $verification_code
        ];

        Mail::to($email)->send(new RegisterMail($mailData));
    }

    /*
    * API Verify User
    *
    * @param Request $request
    * @return \Illuminate\Http\JsonResponse
    */
   public function verifyUser($verification_code)
   {
       $check = DB::table('user_verifications')->where('token',$verification_code)->first();

       if(!is_null($check)){
           $user = User::find($check->user_id);

           if($user->is_verified == 1){
               return response()->json([
                   'success'=> true,
                   'message'=> 'Account already verified..'
               ]);
           }

           $user->update(['is_verified' => 1]);
           DB::table('user_verifications')->where('token',$verification_code)->delete();

           return response()->json([
               'success'=> true,
               'message'=> 'You have successfully verified your email address.'
           ]);
       }

       return response()->json(['success'=> false, 'error'=> "Verification code is invalid."]);

   }

    /****
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        $credentials['is_verified'] = 1;

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 404);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }

        // all good so return the token
        return response()->json(['success' => true, 'data'=> [ 'token' => $token ]], 200);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['succes' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


    /**
     * API Recover Password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recover(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $error_message = "Your email address was not found.";
            return response()->json(['success' => false, 'error' => ['email'=> $error_message]], 401);
        }

        try {
            $firstname = $user->firstname;
            $uuid = $user->uuid;
            $email = request('email');

            $this->sendEmailReset($email, $firstname, $uuid);
            
            // Password::sendResetLink($request->only('email'), function (Message $message) {
            //     $message->subject('Your Password Reset Link');
            // });

        } catch (\Exception $e) {
            //Return with error
            $error_message = $e->getMessage();
            return response()->json(['successss' => false, 'error' => $error_message], 401);
        }

        return response()->json([
            'success' => true, 'data'=> ['message'=> 'A reset email has been sent! Please check your email.']
        ]);
    }

    // To send email reset
    public function sendEmailReset($email, $firstname, $uuid){
        $mailData = [
            "uuid" => $uuid,
            "title" => "Reset Password",
            "firstname" => "Hello!, ".$firstname,
            "body1" => "You are receiving this email because we are received a password reset request for your account",
            "body2" => "Please click on the link below or copy it into the address bar of your browser to change your password :",
        ];

        Mail::to($email)->send(new RecoverPasswordMail($mailData));
    }

    // Reset Password 
    public function resetPassword($uuid, Request $request)
   {

        //set validation
        $validator = Validator::make(request()->all(), [
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6'

        ]);

        
        //if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::where('uuid', $uuid)->first();

        $user->update([
            'password'  => Hash::make(request('password')),
        ]);

        if ($user) {
            return response()->json([
                'success'=> true,
                'message'=> 'You have successfully updated your password.'
            ]);
        } else {
            return response()->json([
                'success'=> true,
                'message'=> 'You have unsuccessfully updated your password.'
            ]);
        }
   }
}
