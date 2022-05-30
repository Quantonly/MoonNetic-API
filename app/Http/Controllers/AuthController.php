<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;
use App\Models\WebsiteUser;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class AuthController extends Controller
{
    public function deleteUser(Request $request) {
        $user = Auth::user();
        $website = WebsiteUser::where('user_id', '=', $user->id)->first();
        WebsiteUser::where('user_id', '=', $user->id)->delete();
        $process = new Process(['/usr/scripts/delete_website.sh', $website->server_ip, $website->sub_domain, $website->sftp_username, $website->sftp_password, $website->php_username, $website->php_password, $website->php_version]);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
        return response('Success', 200);
        User::where('id', '=', $user->id)->delete();
        return response('Success', 200);
    }

    public function editUser(Request $request) {
        $user = Auth::user();
        User::where('id', '=', $user->id)->update(['name' => $request->displayName]);
        return response('Success', 200);
    }

    public function register(Request $request) {
        if (User::where('email', '=', $request->email)->exists()) {
            return response(null, 500);
        }
        $user = User::create([
            'name' => $request->firstName . ' ' . $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return response()->json(compact('user'));
    }

    public function login(Request $request) {
        if (!$token = auth()->attempt($request->only('email', 'password'))) {
            return response(null, 401);
        }
        $user = Auth::user();
        $access_token = $token;
        return response()->json(compact('user', 'access_token'));
    }

    public function getUser(Request $request) {
        if (($user = Auth::user())) {
            return response()->json(compact('user'));
        }
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh() {
        try {
            $access_token = auth()->refresh();
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token expired'], 401);
        }
        return response()->json(compact('access_token'));
    }

    public function forgot(Request $request) {
        if(!$this->validEmail($request->email)) {
            return response()->json([
                'message' => 'Email not found.'
            ], Response::HTTP_NOT_FOUND);
        } else {
            $this->sendEmail($request->email);
            return response()->json([
                'message' => 'Password reset mail has been sent.'
            ], Response::HTTP_OK);            
        }
    }

    public function sendEmail($email){
        $token = $this->createToken($email);
        Mail::to($email)->send(new SendMail($token));
    }

    public function validEmail($email) {
       return !!User::where('email', $email)->first();
    }

    public function createToken($email){
      $isToken = DB::table('password_resets')->where('email', $email)->first();

      if($isToken) {
        return $isToken->token;
      }

      $token = Str::random(80);;
      $this->saveToken($token, $email);
      return $token;
    }

    public function saveToken($token, $email){
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()            
        ]);
    }

    public function update(Request $request){
        return $this->validateToken($request)->count() > 0 ? $this->changePassword($request) : $this->noToken();
    }

    private function validateToken($request){
        return DB::table('password_resets')->where([
            'token' => $request->input('passwordToken')
        ]);
    }

    private function noToken() {
        return response()->json([
          'error' => 'Email or token does not exist.'
        ],Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function changePassword($request) {
        $email = DB::table('password_resets')->where([
            'token' => $request->input('passwordToken')
        ])->get()[0]->email;
        $user = User::whereEmail($email)->first();
        $user->update([
          'password'=>bcrypt($request->input('password'))
        ]);
        $this->validateToken($request)->delete();
        return response()->json([
          'data' => 'Password changed successfully.'
        ],Response::HTTP_CREATED);
    }
}
