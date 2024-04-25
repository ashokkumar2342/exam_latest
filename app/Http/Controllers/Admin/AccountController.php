<?php

namespace App\Http\Controllers\Admin;

use App\Admin; 
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use App\Helper\MyFuncs;
use Session;
class AccountController extends Controller
{
    
    public function index()
    {
        $admin = Auth::guard('admin')->user();	
        $accounts = DB::select(DB::raw("SELECT * from `admins`;")); 
    	return view('admin.account.list',compact('accounts'));
    }

    public function edit(Request $request,$account_id)
    {
        $account_id = intval(Crypt::decrypt($account_id));
        $admin=Auth::guard('admin')->user();       
        $roles = DB::select(DB::raw("SELECT * from `roles`;"));
        $accounts = DB::select(DB::raw("SELECT * from `admins` where `id` = $account_id limit 1;")); 
        return view('admin.account.edit',compact('accounts','roles')); 
    }

    public function update(Request $request,$account_id)
    {

       $this->validate($request,[
           'first_name' => 'required|string|min:3|max:50', 
           "mobile" => 'required|numeric|digits:10',
           "role_id" => 'required',             
           ]);          
        
        $first_name = MyFuncs::removeSpacialChr($request->first_name);
        $mobile_no = MyFuncs::removeSpacialChr($request->mobile);

        $account = Admin::find($account_id);
        $account->user_name = $first_name;
        $account->role_id = $request->role_id; 
        $account->mobile = $mobile_no;
        
        if ($account->save())
        {
          return redirect()->route('admin.account.list')->with(['message'=>'Account Updated Successfully.','class'=>'success']);        
        }
        else{
            return redirect()->back()->with(['class'=>'error','message'=>'Whoops ! Look like somthing went wrong ..']);
        }
    }

    public function status($account_id)
    {
        $account_id = intval(Crypt::decrypt($account_id));
        $rs_fatch = DB::select(DB::raw("SELECT `status` from `admins` where `id` = $account_id limit 1;"));
        $status = $rs_fatch[0]->status;

        if ($status == 1) {
            $l_status = 0;
            $message = 'Account Deactivated Successfully';
        }else{
            $l_status = 1;
            $message = 'Account Activated Successfully';
        } 
        $accounts = DB::select(DB::raw("UPDATE `admins` set `status` = $l_status where `id` = $account_id limit 1;"));

        return redirect()->back()->with(['class'=>'success','message'=>$message]);
    }

    public function form(Request $request)
    {
        $admin=Auth::guard('admin')->user();       
    	$roles = DB::select(DB::raw("SELECT * from `roles`;"));
    	return view('admin.account.form',compact('roles'));
    }
   

    public function store(Request $request)
    { 
        $rules=[
            'first_name' => 'required|string|min:3|max:50',             
            'email' => 'required|email|unique:admins',
            "mobile" => 'required|unique:admins|numeric|digits:10',
            "role_id" => 'required',
            "password" => 'required|min:6|max:15', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }

        $admin = Auth::guard('admin')->user();
        $user_id = $admin->id;
        $role_id = $admin->role_id;

        $user_name = MyFuncs::removeSpacialChr($request->first_name);
        $email_id = MyFuncs::removeSpacialChr($request->email);
        $plain_pass = MyFuncs::removeSpacialChr($request->password);
        $mobile = MyFuncs::removeSpacialChr($request->mobile);
        $new_role_id = intval($request->role_id);
        
        $password=bcrypt($plain_pass);
        
        $accounts = DB::select(DB::raw("INSERT into `admins` (`user_name`, `role_id` , `email` , `password` , `mobile` , `password_plain` , `status`, `created_by`) values ('$user_name' , '$new_role_id', '$email_id', '$password', '$mobile', '$plain_pass', '1', $user_id);"));
        
        $response=['status'=>1,'msg'=>'Account Created Successfully'];
            return response()->json($response);   
    }

    public function changePassword()
    {
        return view('admin.account.change_password');
    }

    public function changePasswordStore(Request $request)
    { 
        $rules=[
            'oldpassword'=> 'required',
            'password'=> 'required|min:8',
            'passwordconfirmation'=> 'required|min:8|same:password',
        ];
        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }        
        $user=Auth::guard('admin')->user();
        $userid = $user->id; 

        $key = Session::get('CryptoRandom');
        $iv = Session::get('CryptoRandomInfo');
        
        $data = hex2bin($request['password']);
        $decryptedpass = openssl_decrypt($data, 'DES-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        $c_data = hex2bin($request['passwordconfirmation']);
        $c_decryptedpass = openssl_decrypt($c_data, 'DES-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        $o_data = hex2bin($request['oldpassword']);
        $o_decryptedpass = openssl_decrypt($o_data, 'DES-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        // $password_strength = MyFuncs::check_password_strength($decryptedpass, $userid);
        // if($password_strength != ''){
        //     $response=['status'=>0,'msg'=>$password_strength];
        //     return response()->json($response);// response as json
        // }

        $from_ip = MyFuncs::getIp();

        if(password_verify($o_decryptedpass,$user->password)){
            if ($o_decryptedpass == $decryptedpass) {
                $response=['status'=>0,'msg'=>'Old Password And New Password Cannot Be Same'];
                return response()->json($response);
            }else{
                $en_password = bcrypt($decryptedpass); 
                DB::select(DB::raw("UPDATE `admins` set `password` = '$en_password' where `id` = $userid limit 1;"));

                $response=['status'=>1,'msg'=>'Password Changed Successfully'];
                return response()->json($response);// response as json 
            }
        }else{               
            $response=['status'=>0,'msg'=>'Old Password Is Not Correct'];
            return response()->json($response);// response as json
        }        
    }


    public function resetPassWord($value='')
    {
        $user=Auth::guard('admin')->user();
        $userid = $user->id;
        $role_id = $user->role_id;
        if($role_id == 1){
            $admins = DB::select(DB::raw("SELECT * from `admins` where `role_id` in (4, 9, 10, 11, 13) order by `email`;"));    
        }else{
            $admins = DB::select(DB::raw("SELECT * from `admins` where `created_by` = $userid order by `email`;"));
        }
        
        return view('admin.account.reset_password',compact('admins'));
    }

    public function resetPassWordChange(Request $request)
    {  
        if ($request->new_pass!=$request->con_pass) {
            $response=['status'=>0,'msg'=>'Password Not Match'];
            return response()->json($response);
        }

        $resetPassWordChange=bcrypt($request['new_pass']);
        $reset_user_id = intval($request->email);
        $rs_update = DB::select(DB::raw("UPDATE `admins` set `password` = '$resetPassWordChange' where `id` = '$reset_user_id' limit 1;"));

        $response=['status'=>1,'msg'=>'Password Changed Successfully'];
        return response()->json($response); 
    }


}
