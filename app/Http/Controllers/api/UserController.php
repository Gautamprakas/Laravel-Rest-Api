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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
