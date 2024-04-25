<option selected disabled>Select Option</option>
@foreach ($rs_records as $rs_value)
    <option value="{{$rs_value->rec_id}}">{{$rs_value->rec_name}}</option>
@endforeach