<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title">Image Update</h4>
            <button type="button" id="btn_close" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <form action="{{ route('admin.emp.photo.update.webcam.store', $emp_id) }}" method="post" class="add_form" button-click="btn_close" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-6 form-group">
                        <div id="my_camera"></div>
                        <br/>
                        <input type=button value="Take Snapshot" onclick="take_snapshot()">
                        <input type="hidden" name="image" class="image-tag" required>
                    </div>
                    <div class="col-md-6" style="margin-top: 30px">
                        <div id="results">Your captured image will appear here...</div>
                    </div>
                    <div class="col-lg-12 form-group text-center" id="btn_save">
                        {{-- <input type="submit" class="btn btn-success" value="Save"> --}}
                    </div>
                    {{-- @if ($filename !='')
                      <div class="form-group col-lg-12">
                        <label>Uploaded Image</label>
                        <img width="50px" height="50px"  src="{{ route('admin.election.setting.district.image.show',Crypt::encrypt($filename)) }}">
                      </div>
                    @else

                    @endif --}}
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script language="JavaScript">
        Webcam.set({
            width: 250,
            height: 250,
            image_format: 'jpeg',
            jpeg_quality: 90
        });
     
        // Webcam.attach( '#my_camera' );

        // Webcam.snap( function(data_uri) {
        //     document.getElementById('my_result').innerHTML = '<img src="'+data_uri+'"/>';
        //   } );
      takeSnapShot = function () {
            Webcam.snap(function (data_uri) {
                document.getElementById('snapShot').innerHTML =
                    '<img src="' + data_uri + '" width="70px" height="50px" />';
            });
        }
         function take_snapshot() {
            Webcam.snap( function(data_uri) {
                $(".image-tag").val(data_uri);
                document.getElementById('results').innerHTML = '<img src="'+data_uri+'" width="220px" height="200px" />';
                document.getElementById('btn_save').innerHTML = ' <input type="submit" class="btn btn-success" value="Save">';

            } );
        }
</script>

