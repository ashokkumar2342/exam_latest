
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>DUTY MANAGEMENT | Log in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('admin_asset/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('admin_asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin_asset/dist/css/AdminLTE.min.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
<body class="hold-transition login-page bg-navy" style="background:url('{{ asset('images/curved14.jpg') }}');background-repeat: no-repeat, repeat;background-size: cover;background-position: center;">
    <div class="login-box">
        <div class="card">
            <div class="card-header text-center pt-4">
                {{-- <h2><strong>EAGESKOOL</strong></h2> --}}
                <img src="{{ asset('images/nic_logo.png')}}" alt="" style="text-align: center;width: 320px;padding-bottom: 20px;height: 70px">
                <img src="{{ asset('images/Capture.JPG')}}" alt="" style="text-align: center;width: 320px;padding-bottom: 20px;">
            </div>
            <div class="card-body">
                <form action="{{ route('admin.emp.photo.store', $emp_id) }}" method="post" class="add_form" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="">
                        <span style="color: black">Name :: <strong>{{$emp_name}}</strong></span><br>
                        <span style="color: black">Mobile :: <strong>{{$mobile}}</strong></span><br>
                        <span style="color: black">Designation :: <strong>{{$emp_desig}}</strong></span><br>
                        <span style="color: black">Department :: <strong>{{$emp_dept}}</strong></span><br>
                    </div>

                    <div class="input-group mb-3">
                        <span style="color: black">Photo Upload (Max. Size 20 KB)</span>
                    </div>
                    <div class="input-group mb-3"> 
                        <input type="file" name="image" class="form-control" required accept=".jpg, .jpeg">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span><i class="fa fa-attachment" style="font-size:20px;color:red"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3"> 
                        <a class="btn_web btn btn-dark btn-sm" my_camera="my_camera" onclick="callPopupLarge(this,'{{ route('admin.emp.photo.update.webcam', Crypt::encrypt($emp_id)) }}')"><i class="fa fa-camera" style="margin: 10px"></i></a>
                    </div>
                    <p class="text-danger">{{ $errors->first('image') }}</p>
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
                        <button type="submit" class="btn bg-gradient-danger w-100 my-4 mb-2">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>
    <script src="{{ asset('admin_asset/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin_asset/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('admin_asset/dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('admin_asset/dist/js/toastr.min.js') }}"></script>
    <script src={!! asset('admin_asset/dist/js/validation/common.js?ver=1') !!}></script>
    <script src={!! asset('admin_asset/dist/js/customscript.js?ver=1') !!}></script>
    @include('admin.include.message')
    @include('admin.include.model')
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
</body>
</html>
