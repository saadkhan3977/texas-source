<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\BaseController as BaseController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use Carbon\Carbon;
use Mail;
use App\Models\User;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Mail\SendVerifyCode;

class RegisterController extends BaseController
{
    public function register(Request $request)
    {
		$validator = Validator::make($request->all(), [
            'fullname' => 'required|string',
            'email' => 'required|email|unique:users',			
            // 'phone' => 'required|numeric|unique:users',
			// 'photo' => 'image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
            // 'address' => 'required|string',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ]);      
        if($validator->fails())
        {
		 return $this->sendError($validator->errors()->first());

        }
		$profile = null;
        if($request->hasFile('photo')) 
        {
            $file = request()->file('photo');
            $fileName = md5($file->getClientOriginalName() . time()) . "PayMefirst." . $file->getClientOriginalExtension();
            $file->move('uploads/user/profiles/', $fileName);  
            $profile = asset('uploads/user/profiles/'.$fileName);
        }
        $input = $request->except(['confirm_password'],$request->all());
        $input['password'] = bcrypt($input['password']);
        $input['photo'] = $profile;
		//$input['email_verified_at'] = Carbon::now();
		$input['email_code'] = mt_rand(9000, 9999);
        $user = User::create($input);
        
        Mail::to($user->email)->send(new SendVerifyCode($input['email_code']));
        $token =  $user->createToken('token')->plainTextToken;
		$users = $user;
		return response()->json(['success'=>true,'message'=>'User register successfully' ,'token'=>$token,'user_info'=>$users]);
    }


    public function login(Request $request)
    {   
        if(!empty($request->email) || !empty($request->password))
        {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:users',
                'password' => 'required',        
            ]);  
            if($validator->fails()){
				return $this->sendError($validator->errors()->first());
            }
            $user = User::firstWhere('email',$request->email);
            if($user->status == 'inactive')
            {
                return $this->sendError('Your account was deactivate');
            }
            if(Auth::attempt(['email' => $request->email, 'password' => $request->password,'status'=>'active']))
            { 
                $users = Auth::user(); 
                $token =  $users->createToken('token')->plainTextToken; 
                return response()->json(['success'=>true,'message'=>'Logged In successfully' ,'token'=>$token,'user_info'=>$user]);
            } 
            else
            { 
            return $this->sendError('Unauthorised User');

            } 
        }
        else{
		return $this->sendError('Email & Password are Required');

        }
      
    }


    public function user(Request $request)
    {
        if(Auth::check())
        {
            $success['users'] = User::where('role','!=','admin')->get();
            return $this->sendResponse($success, 'Current user successfully.');
        }
        else{
            return $this->sendError('No user in Session .');
        }
    }

    public function products(Request $request)
    {
        $success['products'] = Product::with('product_image')->get();
        return $this->sendResponse($success, 'Products Lists');
    }

    public function user_update(Request $request)
    {
        $user = User::find($request->id);
        $user->update([
            'status' => $request->status
        ]);
        return $this->sendResponse('success','User update successfully.');
    }

    public function profile(Request $request)
    {
        try
        {
            $user = User::findOrFail(Auth::id());
            // $validator = Validator::make($request->all(),[
            //     'name' =>'required|string',
            //     'email' =>'required|string',
            //     'designation'=>'required|string',
			// 	'image' => 'image|mimes:jpeg,png,jpg,bmp,gif,svg|max:2048',
            // ]);
            
            // if($validator->fails())
            // {
            //     return $this->sendError($validator->errors()->first());
            // }
            
            $profile = $user->photo;
			if($request->hasFile('photo')) 
			{
				$file = request()->file('photo');
				$fileName = md5($file->getClientOriginalName() . time()) . "Bussinessup." . $file->getClientOriginalExtension();
				$file->move('uploads/user/profiles', $fileName);  
				$profile = asset('uploads/user/profiles'.$fileName);
			}
            
            $user->name = $request->name ? $request->name : $user->name ;
            $user->email = $request->email ? $request->email : $user->email ;
            $user->phone = $request->phone ? $request->phone : $user->phone ;
            $user->address = $request->address ? $request->address : $user->address ;
            $user->photo = $profile;
            $user->save();
            return response()->json(['success'=>true,'message'=>'Profile Updated Successfully','user_info'=>$user]);
        }
        catch(\Eception $e)
        {
                return $this->sendError($e->getMessage());
        }   
    }
}
