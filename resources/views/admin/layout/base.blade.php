
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Eageskool Exam&nbsp;</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <meta name="_token" content="{!! csrf_token() !!}" /> 
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/fontawesome-free/css/all.min.css')}}"> 
  <!-- Ionicons -->
  
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
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">  --}}
  <!-- Ionicons -->
  {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css"> --}}
{{-- <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css"> --}}
  <!-- summernote -->
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/summernote/summernote-bs4.css')}}">
  <!-- Google Font: Source Sans Pro -->
  {{-- <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet"> --}}
  {{-- <link rel="stylesheet" href="{{ asset('admin_asset/plugins/multiple-select/css/multi-select.css')}}"> --}}

  
  <link rel="stylesheet" type="text/css" href="{{ asset('admin_asset/dist/css/toastr.min.css')}}">
  <link href="{!! asset('admin_asset/bootstrap-select/dist/css/bootstrap-select.css') !!}"  rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/select2/css/select2.min.css')}}">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
  <link href="{!! asset('admin_asset/dist/css/summernote.css') !!}"  rel="stylesheet" type="text/css">
  <link href="{!! asset('admin_asset/dist/css/croppie.css') !!}"  rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('admin_asset/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{ asset('admin_asset/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{ asset('admin_asset/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{ asset('admin_asset/plugins/datatables-buttons/css/buttons.bootstrap4.css')}}">
  

   
  
   
  
  <style type="text/css" media="screen">
    th {
    text-transform: uppercase;
     }
     .content-header>.breadcrumb>li+li:before {
         content: '|\00a0';
    }
     .text-white{
      
     }    

      .fa-asterisk {
     color: red;
     font-size:7px; 
     vertical-align: super;
   }
   .cursor:hover{
    cursor: pointer !important; 
    color:#2d4e94 !important;
    text-decoration: underline !important;
   }
   .cursor{
    cursor: pointer !important; 
    color:#2d4e94 !important;
    text-decoration: underline !important;
   }
   .select2-container .select2-selection--single{
    box-sizing: border-box;
    cursor: pointer;
    display: block;
    height: 38px;
    user-select: none;
    -webkit-user-select: none;
   }

   fieldset.fieldset_border{
    border: 5px solid #ddd !important;
    margin: 1px;
    xmin-width: 0;
    padding: 5px;
    position: relative;
    border-radius: 4px;
    padding-right: 22px !important;
    padding-right: 22px !important;
    width: 100%;
   }

   .card{
    border-radius: 1.5rem;
   }
   .modal-content{
    border-radius: 1rem;
   }

   table, thead{
    /*background-color: #212529;*/
    color: #fff;
   }

   
   



  </style>

  @stack('links')
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body class="hold-transition skin-red sidebar-mini" id="body_id">
    <!-- Site wrapper -->
    <div class="wrapper">
      @include('admin.include.header')
      @include('admin.include.sidebar')
        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            @yield('body')
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="pull-right hidden-xs">
           
            </div>
            <strong>Copyright &copy; {{ date('Y') }} <a href="#"></a>.</strong> All rights reserved.
        </footer>

        <!-- Add the sidebar's background. This div must be placed
        immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->
<script src="{{ asset('admin_asset/plugins/jQuery/jquery.min.js') }}"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{ asset('admin_asset/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->

<!-- Bootstrap 4 -->
<script src="{{ asset('admin_asset/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
{{-- <script src="{{ asset('admin_asset/plugins/chart.js/Chart.min.js') }}"></script> --}}
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
<script src="{{ asset('admin_asset/dist/js/adminlte.min.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
{{-- <script src="{{ asset('admin_asset/dist/js/pages/dashboard.js') }}"></script> --}}
<!-- AdminLTE for demo purposes -->
<script src="{{ asset('admin_asset/dist/js/demo.js') }}"></script>
<script src={!! asset('admin_asset/dist/js/validation/common.js?ver=1') !!}></script>
    <script src={!! asset('admin_asset/dist/js/customscript.js?ver=1') !!}></script>
    <script src={!! asset('admin_asset/bootstrap-select/dist/js/bootstrap-select.js')!!}> 
    </script>
  <script>
  $.widget.bridge('uibutton', $.ui.button)
</script>

{{-- <script src="{{ asset('admin_asset/plugins/select2/select2.full.min.js') }}"></script> --}}
{{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}
{{-- <script type="text/javascript" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script> --}}
{{-- <script type="text/javascript" src="//cdn.datatables.net/2.0.3/js/dataTables.js"></script> --}}
 {{-- <script src="{{ asset('admin_asset/plugins/select2/select2.full.min.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> 
 <script src={!! asset('admin_asset/dist/js/summernote.js?ver=1') !!}></script>
 <script src={!! asset('admin_asset/dist/js/croppie.js?ver=1') !!}></script>

<script src="{{ asset('admin_asset/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js') }}"></script>


<script src="{{ asset('admin_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('admin_asset/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<script>window.MathJax = { MathML: { extensions: ["mml3.js", "content-mathml.js"]}};</script>
<script type="text/javascript" async src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.0/MathJax.js?config=MML_HTMLorMML"></script>
<script src={!! asset('admin_asset/ckeditor4/ckeditor.js')!!}> </script>
<script src={!! asset('admin_asset/ckfinder/ckfinder.js')!!}> </script>
    
</script> 
@include('admin.include.message')
@include('admin.include.model')
    @stack('scripts')
    <script>
      $( ".datepicker").datepicker({dateFormat:'dd-mm-yy'}); 
      $('.select2').select2();
       //$('.multiselect').selectpicker(); 
       
      var editor = CKEDITOR.replace( 'ckfinder' );
      CKFinder.setupCKEditor( editor );

    function doc_keyUp(e) {

      // this would test for whichever key is 40 (down arrow) and the ctrl key at the same time
      if (e.key === 'F2') {
          // call your function to do the thing
          $('.wrs_tickContainer').hide()
          
      }
    
    }
    // register the handler
    document.addEventListener('keyup', doc_keyUp, false);
       
    </script>
    <script>
      /** add active class and stay opened when selected */
      var url = window.location;

      // for sidebar menu entirely but not cover treeview
      $('ul.nav-sidebar a').filter(function() {
          return this.href == url;
      }).addClass('active');

      // for treeview
      $('ul.nav-treeview a').filter(function() {
          return this.href == url;
      }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');
    </script>
</body>
</html>
