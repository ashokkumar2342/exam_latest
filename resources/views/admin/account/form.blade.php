@extends('admin.layout.base')
@section('body')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Add New User</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"> 
                </ol>
            </div>
        </div> 
        <div class="card card-info"> 
            <div class="card-body">
                <form action="{{ route('admin.account.post') }}" method="post" class="add_form" autocomplete="off">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Name</label>
                            <span class="fa fa-asterisk"></span>
                            <input Name="first_name" class="form-control"  placeholder="Enter First Name" required="" maxlength="50">
                        </div>                                
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>Role</label>
                            <span class="fa fa-asterisk"></span>
                            <select class="form-control select2" name="role_id">
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach 
                            </select>
                        </div>                               
                    </div>
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Email ID</label>
                            <span class="fa fa-asterisk"></span> 
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                </div>
                                <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter Email" maxlength="50">
                            </div> 
                        </div>
                    </div> 
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Password (Min 6 Max 15 Characters )</label>
                            <span class="fa fa-asterisk"></span> 
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password" maxlength="15" min="6">
                            </div> 
                        </div>
                    </div> 
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label>Mobile No.</label>
                            <span class="fa fa-asterisk"></span> 
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                </div>
                                <input type="text" Name="mobile" class="form-control" maxlength="10" onkeypress='return event.charCode >= 48 && event.charCode <= 57' placeholder="Enter Mobile No.">
                            </div> 
                        </div>
                    </div> 
                     
                </div>   
                <div class="box-footer text-center" style="margin-top: 30px">
                    <button type="submit" class="btn btn-primary form-control">Submit</button>
                </div> 
              </form>  <!-- /.card-body -->
            </div> 
        </div><!-- /.container-fluid -->
    </section>
    @endsection 

