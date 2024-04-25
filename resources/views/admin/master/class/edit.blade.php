<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Edit</h4>
      <button type="button" id="btn_close" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="modal-body">
      <form action="{{ route('admin.Master.class.store', Crypt::encrypt($rs_records[0]->id)) }}" method="post" class="add_form" content-refresh="example2" button-click="btn_close">
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-lg-12">
                    <label for="exampleInputEmail1">Code</label>
                    <span class="fa fa-asterisk"></span>
                    <input type="text" name="code" class="form-control" placeholder="Enter Code" maxlength="5" value="{{$rs_records[0]->code}}" required>
                </div>
                <div class="form-group col-lg-12">
                    <label for="exampleInputEmail1">Name</label>
                    <span class="fa fa-asterisk"></span>
                    <input type="text" name="name" class="form-control" placeholder="Enter Name" maxlength="100" value="{{$rs_records[0]->name}}" required>
                </div>
                <div class="form-group col-lg-12">
                    <label for="exampleInputEmail1">Display Order</label>
                    <input type="text" name="display_order" class="form-control" placeholder="Display Order" maxlength="3" onkeypress='return event.charCode >= 48 && event.charCode <= 57' value="{{$rs_records[0]->display_order}}">
                </div>
            </div> 
          <div class="modal-footer text-center">
            <button type="submit" class="btn btn-success form-control">Update</button>
            <button type="button" class="btn btn-danger form-control" data-dismiss="modal">Close</button>
          </div>
      </form>
    </div>
  </div>
</div>

