<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Admin;
use App\Helpers\MailHelper;
use App\Helper\MyFuncs;
use App\Http\Controllers\Controller;
use App\Model\BlocksMc;
use App\Model\District;
use App\Model\Village;
use App\Student;
use Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Session;
class LoginController extends Controller
{
/*
|--------------------------------------------------------------------------
| Login Controller
|--------------------------------------------------------------------------
|
| This controller handles authenticating users for the application and
| redirecting them to your home screen. The controller uses a trait
| to conveniently provide its functionality to your applications.
|
*/

use AuthenticatesUsers;

/**
* Where to redirect users after login.
*
* @var string
*/

protected $redirectTo = '/admin/dashboard';

/**
* Create a new controller instance.
*
* @return void
*/
public function __construct()
{
  $this->middleware('admin.guest')->except('logout');
}

public function login(){
  return view('admin.auth.login');

} 

public function loginPost(Request $request){ 

  $this->validate($request, [
              'email' => 'required', 
              'password' => 'required',
              'captcha' => 'required|captcha' 
          ]);
          $admins=Admin::where('email',$request->email)->first();
          if (!empty($admins)) { 
            if ($admins->status==2) {
            return redirect()->route('student.resitration.verification',Crypt::encrypt($admins->id)); 
            }
          }
          $key = Session::get('CryptoRandom');
          $iv = Session::get('CryptoRandomInfo');
          $data = hex2bin($request['password']);
          $decryptedpass = openssl_decrypt($data, 'DES-CBC', $key, OPENSSL_RAW_DATA, $iv);

          // $email_mobile = MyFuncs::removeSpacialChr($request->email);
          // $from_ip = MyFuncs::getIp();
          // $rs_records = DB::select(DB::raw("call `up_check_login_attempt_status`('$email_mobile');"));
          // if($rs_records[0]->s_status == 0){
          //   return Redirect()->back()->with(['message'=>$rs_records[0]->result,'class'=>'error']);  
          // }
          // $user_id = $rs_records[0]->user_id;
              
          $credentials = [
                     'email' => $request['email'],
                     'password' => $decryptedpass,
                     'status' => 1,
                 ]; 
            if(auth()->guard('admin')->attempt($credentials)) {
              // $rs_update = DB::select(DB::raw("call `up_login_attempt_action`($user_id, '$from_ip', 1);"));
              if (Auth::guard('admin')->user()->user_type==1) {
                return redirect()->route('admin.dashboard');
              }else{
                return redirect()->route('admin.dashboard');
              }
                   
            } 
            // if($user_id > 0){
            //   $rs_update = DB::select(DB::raw("call `up_login_attempt_action`($user_id, '$from_ip', 0);"));  
            // }
            
            // $student = Student::orWhere('username',$request->email)->first();
            //  if (!empty($student)) {
            //      if (Hash::check($request->password, $student->password)) {
            //          auth()->guard('student')->loginUsingId($student->id);
            //          return redirect()->route('student.dashboard');

            //      } else {
            //          return Redirect()->back()->with(['message'=>'Invalid User or Password','class'=>'error']);
            //      }
            //  }
            
            // if (auth()->guard('student')->attempt($credentials)) {
            //   return redirect()->route('student.dashboard');
            // }
            return Redirect()->back()->with(['message'=>'Invalid User or Password','class'=>'error']); 


}
public function refreshCaptcha()
{  
  return  captcha_img('math');
}



// Logout method with guard logout for admin only
public function logout(Request $request)
{
  $this->guard()->logout();
  $request->session()->flush();
  $request->session()->regenerate();
  return redirect()->route('admin.login');
}

// defining auth  guard
protected function guard()
{
  return Auth::guard('admin');
}


public function forgetPassword()
{
  return view('admin.auth.forget_password');
}
public function forgetPasswordSendLink(Request $request)
{
  $AppUsers=new Admin();
  $u_detail=$AppUsers->getdetailbyemail($request->email);
  $up_u=array();
  $up_u['token'] = str_random(64);        
  $AppUsers->updateuserdetail($up_u,$u_detail->user_id);      
  $up_u['name']=$u_detail->name;
  $up_u['email']=$u_detail->email;
  $user=$u_detail->email;
// $up_u['otp']=$up_u['otp'];
  $up_u['logo']=url("img/logo.png");
  $up_u['link']=url("passwordreset/reset/".$up_u['token']);


  Mail::send('emails.forgotPassword', $up_u, function($message){
    $message->to('ashok@gmail.com')->subject('Password Reset');
  });

// $mailHelper = new MailHelper();
// $mailHelper->forgetmail($request->email); 
  $response=array();
  $response['status']=1;
  $response['msg']='Reset Link Sent successfully';
  return $response;

}

}
