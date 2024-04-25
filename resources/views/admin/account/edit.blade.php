<div class="modal-dialog modal-lg">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Edit</h4>
      <button type="button" id="btn_close" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <form action="{{ route('admin.account.edit.post',$accounts[0]->id) }}" method="post">
                {{ csrf_field() }}
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-6">
                               <div class="form-group">
                                 <label>Role</label>
                                 <span class="fa fa-asterisk"></span>
                                 <select class="form-control" name="role_id">
                                 @foreach($roles as $role)
                                   <option value="{{ $role->id }}" {{ $accounts[0]->role_id == $role->id ? 'selected="selected"' : '' }}>{{ $role->name }}</option>  
                                  @endforeach 
                                      </select>
                                </div>                               
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                  <label for="exampleInputEmail1">Name</label>
                                  <span class="fa fa-asterisk"></span>
                                  <input Name="first_name" class="form-control" value="{{ $accounts[0]->user_name }}"  maxlength="50" placeholder="Enter first name">
                                </div>                                
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                  <label for="exampleInputEmail1">E-mail ID</label>
                                  <span class="fa fa-asterisk"></span>
                                  <input type="text" disabled="" name="email" class="form-control" value="{{ $accounts[0]->email }}" id="exampleInputEmail1" maxlength="50" placeholder="Enter email">
                                </div>                                
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                  <label for="exampleInputEmail1">Mobile No.</label>
                                  <span class="fa fa-asterisk"></span>
                                  <input type="text" Name="mobile" class="form-control" maxlength="10" onkeypress='return event.charCode >= 48 && event.charCode <= 57'  value="{{ $accounts[0]->mobile }}  ">
                                </div>                                
                            </div>
                             
                        </div>                     
                                        
                    <!-- /.box-body -->
                    <div class="box-footer text-center" style="margin-top: 30px">
                      <button type="submit" class="btn btn-primary form-control">Update</button>
                    </div>
                </form>
    </div>
  </div>
</div>

