
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Eageskool Exam | Log in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('admin_asset/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_asset/dist/css/AdminLTE.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('admin_asset/dist/css/toastr.min.css')}}">
</head>
<style type="text/css">
    .card{
        border-radius:1rem;
    }
    .form-control{
        border-radius:1rem;  
    }
    .modal-content{
        border-radius:2rem;  
    }
    .btn{
        border-radius:0.90rem;  
    }
</style>
@php

Session::put('CryptoRandom',App\Helper\MyFuncs::generateId());
Session::put('CryptoRandomInfo',App\Helper\MyFuncs::generateRandomIV());
@endphp
<body class="hold-transition login-page bg-navy" {{-- style="background:url('{{ asset('images/curved14.jpg') }}');background-repeat: no-repeat, repeat;background-size: cover;background-position: center;" --}}>
    <div class="login-box">
        <div class="card">
            <div class="card-header text-center pt-4">
                <h2><strong style="color:#000;">EAGESKOOL EXAM</strong></h2>
               {{--  <img src="{{ asset('images/nic_logo.png')}}" alt="" style="text-align: center;width: 320px;padding-bottom: 20px;height: 70px">
                <img src="{{ asset('images/Capture.JPG')}}" alt="" style="text-align: center;width: 320px;padding-bottom: 20px;"> --}}
            </div>
            <div class="card-body">
                <form action="{{ route('admin.login.post') }}" method="post" class="add_form" autocomplete="off" onsubmit="return hashPasswordEncryption();">
                    {{ csrf_field() }}
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span><i class="fa fa-envelope-square" style="font-size:20px;color:red"></i></span>
                            </div>
                        </div>
                    </div>
                    <p class="text-danger">{{ $errors->first('email') }}</p>
                    <div class="input-group mb-3">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="off">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span><i class="fa fa-lock" style="font-size:20px;color:green"></i></span>
                            </div>
                        </div>
                    </div>
                    <p class="text-danger">{{ $errors->first('password') }}</p>
                    <div class="captcha input-group mb-3">
                        <span>{!! captcha_img('math') !!}</span>
                        <button type="button" class="btn btn-default" id="refresh"> <i class="fas fa-1x fa-sync-alt" ></i> </button>
                    </div>
                    <div class="input-group mb-3" style="margin-top: 5px">
                        <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha"> 
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span><i class="fa fa-align-justify" style="font-size:20px;"></i></span>
                            </div>
                        </div>
                    </div>
                    <p class="text-danger">{{ $errors->first('captcha') }}</p>
                    <div class="mb-2">
                        <button type="submit" class="btn bg-gradient-dark w-100 my-4 mb-2">Login</button>
                        <a href="#" class="btn bg-gradient-danger w-100 my-4 mb-2">Counting Dashboard</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="{{ asset('admin_asset/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_asset/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin_asset/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('admin_asset/dist/js/toastr.min.js') }}"></script>
    <script src={!! asset('admin_asset/dist/js/crypto-js.min.js?ver=') !!}{{date('Y-m-d')}}></script>

    @include('admin.include.message')
    <script type="text/javascript">
        $('#refresh').click(function(){
            $.ajax({
                type:'GET',
                url:'{{ route('admin.refresh.captcha') }}',
                success:function(data){
                    $(".captcha span").html(data);
                }
            });
        });
    </script>
    <script>
        function hashPasswordEncryption(){
            var password = jQuery("#password").val();
            var Cryptoksduid = '<?php echo Session::get('CryptoRandom');?>';
            var Cryptoikeywords = '<?php echo Session::get('CryptoRandomInfo');?>';
            var Cryptokfydsdyg = CryptoJS.enc.Utf8.parse(Cryptoksduid);
            var encrypted = CryptoJS.DES.encrypt(password,
            Cryptokfydsdyg, {
                mode: CryptoJS.mode.CBC,
                padding: CryptoJS.pad.Pkcs7,
                iv: CryptoJS.enc.Utf8.parse(Cryptoikeywords)
            });
            var hexstr = encrypted.ciphertext.toString();
            jQuery("#password").val(hexstr);
        }
    </script>
</body>
</html>
