@extends('admin.layout.base')
@section('body')
@php
    Session::put('CryptoRandom',App\Helper\MyFuncs::generateId());
    Session::put('CryptoRandomInfo',App\Helper\MyFuncs::generateRandomIV());
@endphp
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Change Password</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"> 
                </ol>
            </div>
        </div> 
        <div class="card card-info"> 
            <div class="card-body">
                <form  action="{{ route('admin.account.change.password.store') }}"   class="add_form" method="post" autocomplete="off" button-click="btn_logout" onsubmit="return hashPasswordEncryption();">
                    {{ csrf_field()}}
                    <div class="form-body overflow-hide">
                        <div class="form-group">
                            <label class="control-label mb-10">Old Password</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="icon-lock"></i></div>
                                <input type="password" class="form-control" name="oldpassword" id="oldpassword" placeholder="Enter Old Password" required="">
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="control-label mb-10" for="exampleInputpwd_01">New Password</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="icon-lock"></i></div>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Enter New Password"  title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" required>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="control-label mb-10" for="exampleInputpwd_01">Confirm password</label>
                            <div class="input-group">
                                <div class="input-group-addon"><i class="icon-lock"></i></div>
                                <input type="password" name="passwordconfirmation" class="form-control" id="passwordconfirmation" placeholder="Enter Confirm Password"  title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters" oninput="check(this)" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-actions mt-10">            
                        <button type="submit" class="btn btn-success mr-10 mb-30">Update Password</button>
                    </div>              
                </form>

            </div> 
        </div>
    </div><!-- /.container-fluid -->
</section>
@endsection 
@push('scripts')
<script src={!! asset('admin_asset/dist/js/crypto-js.min.js?ver=') !!}{{date('Y-m-d')}}></script>
<script>
    function hashPasswordEncryption(){
        var password = jQuery("#password").val();
        var passwordconfirmation = jQuery("#passwordconfirmation").val();
        var oldpassword = jQuery("#oldpassword").val();

        var Cryptoksduid = '<?php echo Session::get('CryptoRandom');?>';
        var Cryptoikeywords = '<?php echo Session::get('CryptoRandomInfo');?>';
        var Cryptokfydsdyg = CryptoJS.enc.Utf8.parse(Cryptoksduid);
        
        var encrypted = CryptoJS.DES.encrypt(password,
        Cryptokfydsdyg, {
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7,
            iv: CryptoJS.enc.Utf8.parse(Cryptoikeywords)
        });

        var c_encrypted = CryptoJS.DES.encrypt(passwordconfirmation,
        Cryptokfydsdyg, {
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7,
            iv: CryptoJS.enc.Utf8.parse(Cryptoikeywords)
        });

        var o_encrypted = CryptoJS.DES.encrypt(oldpassword,
        Cryptokfydsdyg, {
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7,
            iv: CryptoJS.enc.Utf8.parse(Cryptoikeywords)
        });

        var hexstr = encrypted.ciphertext.toString();
        jQuery("#password").val(hexstr);

        var c_hexstr = c_encrypted.ciphertext.toString();
        jQuery("#passwordconfirmation").val(c_hexstr);

        var o_hexstr = o_encrypted.ciphertext.toString();
        jQuery("#oldpassword").val(o_hexstr);
    }
</script>

@endpush 


