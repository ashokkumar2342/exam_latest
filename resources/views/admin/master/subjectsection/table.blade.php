<div class="col-lg-12">
    <fieldset class="fieldset_border">
        <div class="table-responsive"> 
            <table id="example2" class="table table-striped table-bordered">
                <thead>
                     <tr>
                         <th>Sr.No.</th>
                         <th>Section</th>
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
                            <td>{{ $rs_value->name }}</td> 
                            <td>
                                <a type="button" href="{{ route('admin.Master.subjectsection.delete',Crypt::encrypt($rs_value->id)) }}" onclick="return confirm('Are you sure you want to delete this item?');" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Delete</a>
                            </td>
                        </tr> 
                    @endforeach
                </tbody>
            </table>
        </div>
    </div> 
</div> 