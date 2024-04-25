<form action="{{ route('admin.search.voter.folter',1) }}" method="post" class="add_form" data-table="voter_datatable" success-content-id="voter_table" no-reset="true">
    {{ csrf_field() }}
    <div class="row">  
        <div class="col-lg-3 form-group">
            <label>Voter Card No.</label>
            <input type="text" name="voter_card_no" class="form-control">
        </div>

        <div class="col-lg-12 form-group">
            <input type="submit" id="btn_show" value="Search" class="form-control btn btn-success">
        </div>
    </div>
</form>
<div id="voter_table">

</div>