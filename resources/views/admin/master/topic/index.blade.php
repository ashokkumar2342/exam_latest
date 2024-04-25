@extends('admin.layout.base')
@section('body')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Create Topic</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"> 
                </ol>
            </div>
        </div> 
        <div class="card card-info"> 
            <div class="card-body"> 
                <form action="{{ route('admin.Master.topic.store') }}" method="post" class="add_form" content-refresh="example2">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Class</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="class" id="class_select_box" class="form-control select2">
                                <option selected disabled>Select Class</option>
                                @foreach ($rs_class_types as $rs_class_type)
                                    <option value="{{$rs_class_type->id}}">{{$rs_class_type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Subject</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="subject" id="subject_select_box" class="form-control select2" onchange="callAjax(this, '{{ route('admin.Master.subjectwisesection') }}', 'section_select_box')">
                                <option selected disabled>Select Subject</option>
                                @foreach ($rs_subjects as $rs_subject)
                                    <option value="{{$rs_subject->id}}">{{$rs_subject->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Section</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="section" class="form-control select2" id="section_select_box">
                                <option selected disabled>Select Option</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="exampleInputEmail1">Topic Code</label>
                            <span class="fa fa-asterisk"></span>
                            <input type="text" name="code" class="form-control" placeholder="Enter Code" maxlength="5" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="exampleInputEmail1">Topic Name</label>
                            <span class="fa fa-asterisk"></span>
                            <input type="text" name="name" class="form-control" placeholder="Enter Name" maxlength="100" required>
                        </div> 
                        <div class="col-lg-12 form-group " style="margin-top:30px">
                            <button type="submit" class="btn btn-primary form-control">Save</button>
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
                                         <th>Class</th>
                                         <th>Subject</th>
                                         <th>Section</th> 
                                         <th>Topic Code</th> 
                                         <th>Topic Name</th> 
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
                                            <td>{{ $rs_value->class_id }}</td> 
                                            <td>{{ $rs_value->subject_id }}</td> 
                                            <td>{{ $rs_value->section_id }}</td> 
                                            <td>{{ $rs_value->code }}</td> 
                                            <td>{{ $rs_value->name }}</td> 
                                            <td>
                                                <a type="button" href="{{ route('admin.Master.topic.delete',Crypt::encrypt($rs_value->id)) }}" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>
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
