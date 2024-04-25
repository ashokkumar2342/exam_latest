@extends('admin.layout.base')
@section('body')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Create Subject</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"> 
                </ol>
            </div>
        </div> 
        <div class="card card-info"> 
            <div class="card-body"> 
                <form action="{{ route('admin.Master.subject.store', Crypt::encrypt(0)) }}" method="post" class="add_form" content-refresh="example2">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Code</label>
                            <span class="fa fa-asterisk"></span>
                            <input type="text" name="code" class="form-control" placeholder="Enter Code" maxlength="5" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Name</label>
                            <span class="fa fa-asterisk"></span>
                            <input type="text" name="name" class="form-control" placeholder="Enter Name" maxlength="100" required>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Display Order</label>
                            <input type="text" name="display_order" class="form-control" placeholder="Display Order" maxlength="3" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
                        </div> 
                    <div class="col-lg-12 form-group " style="margin-top:30px">
                        <button type="submit" class="btn btn-primary form-control">Submit</button>
                    </div>
                    </div> 
                </form>
            </div> 
        </div>
        <div class="card card-info"> 
            <div class="card-body">
                <div class="col-lg-12">
                    <fieldset class="fieldset_border">
                        <div class="table-responsive"> 
                            <table id="example2" class="table table-striped table-bordered">
                                <thead>
                                     <tr>
                                         <th>Sr.No.</th>
                                         <th>Code</th>
                                         <th>Name</th>
                                         <th>Display Order</th> 
                                         <th>Action</th>
                                          
                                     </tr>
                                 </thead>
                                 <tbody>
                                    @php
                                        $sr_no = 1;
                                    @endphp
                                    @foreach ($rs_records as $rs_value)
                                        <tr>
                                            <td>{{ $sr_no++ }}</td> 
                                            <td>{{ $rs_value->code }}</td> 
                                            <td>{{ $rs_value->name }}</td> 
                                            <td>{{ $rs_value->display_order }}</td> 
                                            <td>
                                                <a type="button" onclick="callPopupLarge(this,'{{ route('admin.Master.subject.edit', Crypt::encrypt($rs_value->id)) }}')" title="" class="btn btn-info btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                                <a type="button" href="{{ route('admin.Master.subject.delete',Crypt::encrypt($rs_value->id)) }}" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>
                                            </td>
                                        </tr> 
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                   </fieldset>
                </div> 
            </div> 
        </div>
    </div> 
</section>
@endsection
@push('scripts')
    <script>
      $(function () {
        $("#example2").DataTable({
          "responsive": true, "lengthChange": false, "autoWidth": false,
          "buttons": ["excel", "copy", "csv", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      });
    </script>
@endpush