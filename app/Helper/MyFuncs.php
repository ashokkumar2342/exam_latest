<?php

namespace App\Helper;


use Illuminate\Support\Facades\Auth;
use Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class MyFuncs {

  
  public static function check_password_strength($password, $user_id) 
  {
    $passwordError = "";
    if (strlen($password) <= 8) {
      $passwordError .= "Password must have 8 characters at least.<br>";
    }
    if (!preg_match("#[0-9]+#", $password)) {
      $passwordError .= "Password must have 1 Number at least.<br>";
    }
    if (!preg_match("#[A-Z]+#", $password)) {
      $passwordError .= "Password must have 1 uppercase letter at least.<br>";
    }
    if (!preg_match("#[a-z]+#", $password)) {
      $passwordError .= "Password must have 1 lowercase letter at least.<br>";
    }
    if (!preg_match('@[^\w]@', $password)) {
      $passwordError .= "Password must have 1 special character letter at least.<br>";
    }

    
    $rs_fetch = DB::select(DB::raw("SELECT * from `password_change_history` where `user_id` = $user_id order by `id` desc limit 3;"));
    $found = 0;
    foreach ($rs_fetch as $key => $value) {
      if(password_verify($password,$value->new_password)){
        if($found == 0){
          $passwordError .= "You used this password recently, please choose a different password.";
          $found = 1;
        }
      }
    }
    

    return $passwordError;
    
  }

  public static function generateId() 
  {

    return rand(1111111111,100000000);
    
  }

  public static function generateRandomIV() 
  {

    return substr(uniqid(), 1, 8) ;
    
  }

  public static function removeSpacialChr($strValue)
  {
    $newString = trim(str_replace('\'', '', $strValue));
    $newString = trim(str_replace('\\', '', $newString));
   
    return $newString;
  }
  
  public  static function deletePermission(){
    $admin=Auth::guard('admin')->user();      
    return $roles = DB::select(DB::raw("select `role_id` from `admins` where `id` =$admin->id;")); 
  }


  public static function mainMenu($menu_type_id){ 
    $user_rs=Auth::guard('admin')->user();  
    $user_role = $user_rs->role_id;

    return $subMenus = DB::select(DB::raw("select `sm`.`id`, `sm`.`name`, `sm`.`status`, `sm`.`url` from `default_role_menu` `drm` inner join `sub_menus` `sm` on `sm`.`id` = `drm`.`sub_menu_id` where `drm`.`role_id` = $user_role and `drm`.`status` = 1 and `sm`.`menu_type_id` = $menu_type_id order by `sm`.`sorting_id` ;"));
  }


  public static function userHasMinu(){ 
    $user_rs=Auth::guard('admin')->user();  
    $user_role = $user_rs->role_id;
    return $menuTypes = DB::select(DB::raw("select * from `minu_types` where `id` in (select Distinct `sm`.`menu_type_id` from `default_role_menu` `drm` inner join `sub_menus` `sm` on `sm`.`id` = `drm`.`sub_menu_id` where `drm`.`role_id` = $user_role and `drm`.`status` = 1) order by `sorting_id` ;"));
  }

  public static function isPermission(){ 
    $user = Auth::guard('admin')->user();
    if(!empty($user)){ 
      $role_id = $user->role_id;
      $routeName = Route::currentRouteName();
      $rs_fetch = DB::select(DB::raw("select `id` from `sub_menus` where `url` = '$routeName' and `status` = 1;"));
      if (count($rs_fetch)>0){
        $menu_id = $rs_fetch[0]->id;
        $rs_fetch = DB::select(DB::raw("select `id` from `default_role_menu` where `role_id` = $role_id and `status` = 1 and `sub_menu_id` = $menu_id;"));
        if(count($rs_fetch) == 0){
          return false;    
        }
      }  
    }
    return true;
  }


  
  
    
}

