<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($flag)
    {
        $query=User::select("name","email");
        if($flag==1){
            $query->where("status",1);

        }else if($flag==0){
            $query->where("status",0);
        }else{
            $response=[
                "message"=>"value can be either 0 or 1"];
                return response()->json($response,400);
        }
        $users=$query->get();
            $response=[
                "message"=>"Active ".count($users)." Users found",
                "status"=>1,
                "data"=>$users,
            ];
        return response()->json($response,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        $validator=Validator::make($request->all(),[
            'name'=>['required'],
            'email'=>['required','email','unique:users,email'],
            'password'=>['required','min:8','confirmed'],
            'password_confirmation'=>['required'],
            // 'pincode'=>['required','min:6']
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(),400);
        }else{
            $data=[
                "name"=>$request->name,
                "email"=>$request->email,
                "password"=>Hash::make($request->password),
                "pincode"=>$request->pincode
            ];
            DB::beginTransaction();
            try{
                $user=User::create($data);
                DB::commit();
            }catch(\Exception $e){
                DB::rollback();
                $user=null;
            }
            if($user!=null){
                return response()->json([
                    "message"=>"Successfully Registered"

                            ],200);
            }else{
                return response()->json(["message"=>"Internel Server Error"],500);
            }
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user=User::find($id);
        if(is_null($user)){
            $response=[
                "message"=>"User Not found to this id "];
        }else{
            $response=[
                "message"=>"User Find",
                "name"=>$user->name,
                "email"=>$user->email,
                "data"=>$user
            ];
        }
        return response()->json($response,200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user=User::find($id);
        if(!is_null($user)){
            DB::beginTransaction();
            try{
                $user->name=$request['name'];
                $user->email=$request['email'];
                $user->address=$request['address'];
                $user->pincode=$request['pincode'];
                $user->contact=$request['contact'];
                $user->save();
                DB::commit();
            }catch(\Exception $err){
                DB::rollback();
                $user=null;
                return response()->json(["status"=>0,
                "message"=>"Enternel Server Error"],500);

            }
        }else{
            return response()->json(["status"=>0,
                "message"=>"Can't Find The  User"],200);
        }
        if(!is_null($user)){
            return response()->json(["status"=>1,
                "message"=>"User Data Updated"],200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user=User::find($id);
        if(!is_null($user)){
            DB::beginTransaction();
            try{
                $user->delete();
                DB::commit();
                $response=[
                    "message"=>"User deleted Successfully",
                    "status"=>1
                ];
                $status_code=200;

            }catch(\Exception $err){
                DB::rollback();
                $response=[
                    "message"=>"Internel Server Error",
                    "status"=>0
                ];
                $status_code=500;


            }
        }else{
            $response=[
                    "message"=>"User no exist",
                    "status"=>0
                ];
            $status_code=404;
        }
        return response()->json($response,$status_code);
    }


    public function register(Request $request){
        $validatedData=$request->validate([
            'name'=>'required',
            'email'=>['required','email'],
            'password'=>['min:8','confirmed']]);
        $user=User::create($validatedData);
        $token=$user->createToken("auth_token")->accessToken;
        return response()->json([
            "message"=>"User Registered Succed",
            "token"=>$token,
            "status"=>1],200);
    }
    public function login(Request $request){
        $validatedData=$request->validate([
            'email'=>['required'],
            'password'=>['required']
        ]);
        $user=User::where(['email'=>$validatedData['email'],'password'=>$validatedData['password']])->first();
        if(!is_null($user)){
            $token=$user->createToken("auth_token")->accessToken;
            return response()->json([
                "message"=>"User Logged In  Succed",
                "token"=>$token,
                "status"=>1],200);
        }else{
            return response()->json([
                "message"=>"Can't Find The User",
                "status"=>1],200);
        }
    }
}
