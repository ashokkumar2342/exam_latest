<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Voter List Management | Log in</title> 
  <meta name="viewport" content="width=device-width, initial-scale=1"> 
  <meta name="_token" content="{!! csrf_token() !!}" /> 
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/fontawesome-free/css/all.min.css')}}"> 
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bbootstrap 4 --> 
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
  <!-- iCheck -->
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
  <!-- JQVMap -->
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/jqvmap/jqvmap.min.css')}}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('admin_asset/dist/css/AdminLTE.min.css')}}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/daterangepicker/daterangepicker.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> 
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/summernote/summernote-bs4.css')}}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  {{-- <link rel="stylesheet" href="{{ asset('admin_asset/plugins/multiple-select/css/multi-select.css')}}"> --}}

  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link href="{!! asset('admin_asset/bootstrap-select/dist/css/bootstrap-select.css') !!}"  rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
</head>
<body> 
  <div class="login-logo">
    <b>Search Voter</b>
    <a href="{{ route('admin.login') }}" title="" class="btn btn-info" style="margin-left: 100px">Login</a>
  </div> 
  <div class="card">
    <div class="card-body">  
      <div class="btn-group btn-group-xs">
        <button type="button" class="btn btn-default" onclick="callAjax(this,'{{ route('admin.search.voter.form',1) }}','search_voter_form')">Search By Voter Card No.</button>
        <button type="button" class="btn btn-default" onclick="callAjax(this,'{{ route('admin.search.voter.form',2) }}','search_voter_form')">Search By Voter Details</button> 
      </div>
      <div id="search_voter_form" style="margin-top: 20px">
         
       </div> 
    </div> 
  </div>  
  <script src="{{ asset('admin_asset/plugins/jQuery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('admin_asset/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<!-- Bootstrap 4 -->
<script src="{{ asset('admin_asset/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('admin_asset/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
{{-- <script src="{{ asset('admin_asset/plugins/sparklines/sparkline.js') }}"></script> --}}
<!-- JQVMap -->
{{-- <script src="{{ asset('admin_asset/plugins/jqvmap/jquery.vmap.min.js') }}"></script> --}}
{{-- <script src="{{ asset('admin_asset/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script> --}}
<!-- jQuery Knob Chart -->
{{-- <script src="{{ asset('admin_asset/plugins/jquery-knob/jquery.knob.min.js') }}"></script> --}}
<!-- daterangepicker -->
{{-- <script src="{{ asset('admin_asset/plugins/moment/moment.min.js') }}"></script> --}}
<script src="{{ asset('admin_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
{{-- <script src="{{ asset('admin_asset/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script> --}}
<!-- Summernote -->
<script src="{{ asset('admin_asset/plugins/summernote/summernote-bs4.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('admin_asset/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<script src="{{ asset('admin_asset/dist/js/toastr.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('admin_asset/dist/js/adminlte.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{-- <script src="{{ asset('admin_asset/dist/js/pages/dashboard.js') }}"></script> --}}
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('admin_asset/dist/js/demo.js') }}"></script>
<script src={!! asset('admin_asset/dist/js/validation/common.js?ver=1') !!}></script>
    <script src={!! asset('admin_asset/dist/js/customscript.js?ver=1') !!}></script>
    <script src={!! asset('admin_asset/bootstrap-select/dist/js/bootstrap-select.js')!!}> 
    </script>
  

{{-- <script src="{{ asset('admin_asset/plugins/select2/select2.full.min.js') }}"></script> --}}
{{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
<script type="text/javascript" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
 {{-- <script src="{{ asset('admin_asset/plugins/select2/select2.full.min.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> 
 <script src={!! asset('admin_asset/dist/js/summernote.js?ver=1') !!}></script>
 <script src={!! asset('admin_asset/dist/js/croppie.js?ver=1') !!}></script>

<script src="{{ asset('admin_asset/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>





    

</body>
</html>
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
