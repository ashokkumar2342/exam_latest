@extends('admin.layout.base')
@section('body')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Mapping Subject Section</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"> 
                </ol>
            </div>
        </div> 
        <div class="card card-info"> 
            <div class="card-body"> 
                <form action="{{ route('admin.Master.subjectsection.store') }}" method="post" class="add_form" no-reset="true" select-triger="subject_select_box">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="exampleInputEmail1">Subject</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="subject" id="subject_select_box" class="form-control select2" onchange="callAjax(this, '{{ route('admin.Master.subjectsection.table') }}', 'rs_table')">
                                <option selected disabled>Select Subject</option>
                                @foreach ($rs_subjects as $rs_subject)
                                    <option value="{{$rs_subject->id}}">{{$rs_subject->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="exampleInputEmail1">Section</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="section" class="form-control select2">
                                <option selected disabled>Select Section</option>
                                @foreach ($rs_sections as $rs_section)
                                    <option value="{{$rs_section->id}}">{{$rs_section->name}}</option>
                                @endforeach
                            </select>
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
                <div class="row" id="rs_table">
                    
                </div> 
            </div>
        </div> 
    </div> 
</section>
@endsection
