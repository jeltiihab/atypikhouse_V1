<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Role;
use DateTimeImmutable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use JetBrains\PhpStorm\ArrayShape;

class AuthController extends Controller
{
    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'lastName' => 'required|string|max:255',
            'firstName' => 'required|string|max:255',
            'userRole' => 'required|in:ROLE_LOC,ROLE_PROP',
            'sexe' => 'required|in:homme,femme',
            'birthDate' => 'required|date',
            'email' => 'required|string|email|max:255|unique:users',
            'phone'=>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15|unique:users',
            'password' => 'required|confirmed|string|min:8',

        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }


        $user = User::create([
            'name' => $request->lastName,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'firstName'=> $request->firstName,
            'lastName'=> $request->lastName,
            'sexe'=> $request->sexe,
            'birthDate' => new DateTimeImmutable($request->birthDate),
            'phone' => $request->phone
        ]);


        $role = Role::where("name",$request->userRole)->first() ;
        $user->attachRole($role) ;

        $role = $user->roles()->get(["name"]);
        $role =$role[0]?->name;
        $data = [
            "firstName"=>$user->firstName,
            "lastName"=>$user->lastName,
            "email"=>$user->email,
            "role" => $role
        ];

        $token = $user->createToken('auth_token')->plainTextToken;
        event(new Registered($user));


        return response()
            ->json(['data' => $data,'role'=> $user->roles()->get(["name"]) ,'access_token' => $token, 'token_type' => 'Bearer', ]);
    }

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors());
        }
        if (!Auth::attempt($request->only('email', 'password')))
        {
            return response()
                ->json(['message' => 'Unauthorized'], 200);
        }



        $user = User::where('email', $request['email'])->firstOrFail();

        if(is_null($user->email_verified_at))
            return response()
                ->json(['message' => 'email must verified'], 200);

        $token = $user->createToken('auth_token')->plainTextToken;
        $role = $user->roles()->get(["name"]);
        $role =$role[0]?->name;

        return response()
            ->json([
                'message' => 'login success',
                'access_token' => $token,
                'token_type' => 'Bearer',
                "user" => [
                    "firstName"=>$user->firstName,
                    "lastName"=>$user->lastName,
                    "email"=>$user->email,
                    "role" => $role
                ]
                ]);
    }

    // method for user logout and delete token
    #[ArrayShape(['message' => "string"])] public function logout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
