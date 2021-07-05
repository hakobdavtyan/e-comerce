<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function userLoginRegister(){
        return view('wayshop.users.login_register');
    }
    public function register(Request $request){
        if ($request->isMethod('post')){
        $data = $request->all();
        $userCount = User::where('email',$data['email'])->count();
        if ($userCount>0){
            return redirect()->back()->with('flash_message_error','Email is already exist');
        }else{
            //adding user in table
            $user = new User();
            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = bcrypt($data['password']);
            $user->save();
            if (Auth::attempt(['email'=>$data['email'],'password'=>$data['password']])){
                return redirect('/cart');
            }
        }
        }
    }

    public function logout(){
        Auth::logout();
        return redirect('/');
    }
}
