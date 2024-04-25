<table class="table table-bordered" id="voter_datatable">
	<thead>
		<tr>
			<th>Name <br> Relation <br> Relation Name</th>
			<th>Gender<br>age<br>Mobile No.</th>
			<th>Block<br>Village</th>
			<th>Ward No.<br>Sr. No. in List</th>
			<th>EPIC No.<br>Booth No.</th>
			<th>Booth Name/Location</th>
		</tr>
	</thead>
	<tbody>
		@foreach ($voters as $voter)
		<tr>
			<td>{{$voter->name_e}}<br>{{$voter->relation_e}}<br>{{$voter->father_name_e}}</td>
			<td>{{$voter->genders}}<br>{{$voter->age}}<br>xxxxxx{{substr($voter->mobile_no,-4,4)}}</td>
			<td>{{$voter->block_name}}<br>{{$voter->village_name}}</td>
			<td>{{$voter->ward_no}}<br>{{$voter->print_sr_no}}</td>
			<td>{{$voter->voter_card_no}}<br>{{$voter->booth_no_comp}}</td>
			<td>{{$voter->polling_booth_name}}</td>
		</tr>
		@endforeach
	</tbody>
</table>