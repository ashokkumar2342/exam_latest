@extends('admin.layout.base')
@section('body')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>List Users</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"> 
                </ol>
            </div>
        </div> 
        <div class="card card-info"> 
            <div class="card-body"> 
                <div class="col-lg-12">
                    <fieldset class="fieldset_border"> 
                        <div class="table-responsive"> 
                            <table id="example2" class="table table-bordered table-striped table-hover control-label">
                                <thead style="background-color: #6c757d;color: #fff">
                                    <tr>
                                        <th>Sr.No.</th> 
                                        <th>User Name</th>
                                        <th>Mobile No.</th>
                                        <th>Email Id</th>
                                        <th>Role</th> 
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $arrayId=1; 
                                    @endphp
                                    @foreach($accounts as $account) 
                                    <tr style="background-color:{{ $account->status==1?'#28a745!important':'#dc3545!important' }}">
                                        <td>{{ $arrayId ++ }}</td> 
                                        <td>{{ $account->user_name }}</td>
                                        <td>{{ $account->mobile }}</td> 
                                        <td>{{ $account->email }}</td>
                                        <td>{{ $account->role_id }}</td>
                                        <td> 
                                            <a href="#" type="button" onclick="callPopupLarge(this,'{{ route('admin.account.edit', Crypt::encrypt($account->id)) }}')" class="btn btn-primary btn-xs"><i class="fa fa-pencil"></i> Edit</a> 
                                            <a type="button" class="btn btn-xs btn-{{ $account->status==1?'danger':'success' }}" href="{{ route('admin.account.status', Crypt::encrypt($account->id)) }}" onclick="return confirm('Are you sure you want to change status?');">{{ $account->status==1?'Deactivate':'Active' }}</a> 
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
