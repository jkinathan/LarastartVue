<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Hash;

class Usercontroller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required | string|max:190',
            'email'=>'required | string|email|max:190|unique:users',
            'password' => 'required|string|min:5'
        ]);

        return User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'type' => $request['type'],
            'bio' => $request['bio'],
            'photo'=> $request['photo'],
            'password' => Hash::make($request['password']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function profile()
    {
        return auth('api')->user();
    }
    public function updateprofile(Request $request)
    {
        $user = auth('api')->user();
        $currentphoto = $user->photo;
        
        if ($request->photo != $currentphoto){
            
            $name = time().'.'.explode('/', explode(':', \substr
            ($request->photo, 0, strpos($request->photo, ';')))[1])[1];
            \Image::make($request->photo)->save(\public_path
            ('img/profile/').$name);

            $request->merge(['photo' => $name]);
        }
        $user->update($request->all());
        return ['message' => 'Success, updated !'];
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
        $user = User::findOrFail($id);
        $this->validate($request,[
            'name'=>'required | string|max:190',
            'email'=>'required | string|email|max:190|unique:users,email,'.$user->id, //if user email is the same continue, otherwise fail.
            'password' => 'sometimes|string|min:5'
        ]);
        $user->update($request->all());
        return ['message' => 'Updated the user info'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        //delete the user
        $user->delete();

        //redirect back with message
        return ['message' => 'user Deleted'];
    }
}
