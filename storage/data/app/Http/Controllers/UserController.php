<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Mail\SendMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function  __construct() {
        $this->middleware(['auth:api']);
    }

    public function index(Request $request) {
        $user = $request->user();

        return response()->json(['email' => $user->email, 'name' => $user->name]);
    }

    public function getUsers(Request $request) {
        $users = User::all();
        foreach ($users as $user) {
            $roles = [];
            foreach($user->roles()->get() as $role) {
                array_push($roles, $role->name);
            }
            $user->roles = $roles;
        }
        return $users;
    }

    public function addUser(Request $request)
    {
        try {
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make(''),
            ]);
            $role = Role::first();
            $user->roles()->attach($role);
            $this->sendEmail($request->input('email'));
            $users = User::all();
            foreach ($users as $user) {
                $roles = [];
                foreach($user->roles()->get() as $role) {
                    array_push($roles, $role->name);
                }
                $user->roles = $roles;
            }
            return $users;
        } catch (Exception $e){
            if (isset($e->errorInfo)) {
                $errorCode = $e->errorInfo[1];
                if($errorCode == 1062) {
                    return response()->json(['error' => 'Emailadres is al in gebruik']);
                }
            }
            return response()->json(['error' => 'Er is iets misgegaan']);
        }
    }

    public function sendEmail($email){
        $token = $this->createToken($email);
        Mail::to($email)->send(new SendMail($token));
    }

    public function createToken($email){
      $isToken = DB::table('password_resets')->where('email', $email)->first();

      if($isToken) {
        return $isToken->token;
      }

      $token = Str::random(80);;
      DB::table('password_resets')->insert([
        'email' => $email,
        'token' => $token,
        'created_at' => Carbon::now()            
      ]);
      return $token;
    }

    public function editUser(Request $request, int $id)
    {
        try {
            $user = User::find($id);
            $user->name = $request->input('name');
            $user->roles()->detach();
            if ($request->input('roles')) {
                foreach ($request->input('roles') as $role) {
                    $role = Role::find($role);
                    $user->roles()->attach($role);
                }
            }
            $user->save();
            $users = User::all();
            foreach ($users as $user) {
                $roles = [];
                foreach($user->roles()->get() as $role) {
                    array_push($roles, $role->name);
                }
                $user->roles = $roles;
            }
            return $users;
        } catch (Exception $e){
            return response()->json(['error' => 'Er is iets misgegaan']);
        }
    }

    public function deleteUser(Request $request, int $id)
    {
        try {
            $user = User::find($id);
            $user->delete();
            $users = User::all();
            foreach ($users as $user) {
                $roles = [];
                foreach($user->roles()->get() as $role) {
                    array_push($roles, $role->name);
                }
                $user->roles = $roles;
            }
            return $users;
        } catch (Exception $e){
            return response()->json(['error' => 'Er is iets misgegaan']);
        }
    }
}
