<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB; 
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        
        $input = $request->all();

        $validator = Validator::make($input,[
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'level' => 'required'
        ]);

        if($validator->fails()){
            
            return response()->json([
                'status' => 500,
                'message' => 'Bad Request',
                'error' => $validator->errors(),
            ],401);

        }else{

            try {
                  
                DB::beginTransaction();
                    
                $input ['password'] = Hash::make($input['password']);
                $query = User::create($input);
                $response['token'] = $query->createToken('users')->accessToken;
                $response['email'] = $query->email;
                
                DB::commit();
                
                return response()->json($response,200);
                
            }catch(\Exception $e){
                
                DB::rollback();
                return response()->json([
                    'status' => 500,
                    'message' => 'Bad Request',
                    'error' => $e->getmessage() .' Line '. $e->getLine(),
                ],401);
        
             }

        }
    }

    public function login(Request $request){

        $input = $request->all();

        
        $validator = Validator::make($input,[
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            
            return response()->json([
                'status' => 500,
                'message' => 'Bad Request',
                'error' => $validator->errors(),
            ],401);
        
        }else {
            
            $check_user = User::where('email',$input['email'])->first();
            
            if(@count($check_user) > 0){

                $password = $input['password'];
                if (Hash::check($password,$check_user['password'])) {
                    
                    $response['token'] = $check_user->createToken('users')->accessToken;
                    $response['status'] = 200;
                    $response['message'] = 'Login Successfull';

                    return response()->json($response,200);
                    
                }else {
                    
                    return response()->json([
                        'status' => 500,
                        'message' => 'Bad Request',
                        'error' => 'Password Salah !',
                    ],401);
                
                }
                
            }else {

                return response()->json([
                    'status' => 500,
                    'message' => 'Bad Request',
                    'error' => 'Email Tidak Terdaftar!',
                ],401);
            
            }


        }
        
    }







}
