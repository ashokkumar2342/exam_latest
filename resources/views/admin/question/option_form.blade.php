@if ($question_type_id==1)
    <div class="row input_fields_wrap">
        @foreach ([0,1,2,3] as $key => $value)
        <div class="col-lg-6 form-group"> 
            <div id="div_{{ $key+1 }}">
                <input type="radio" id="answer" class="correct_id_{{ $key+1 }}" name="correct_answer" value="{{ $key+1 }}"> 
                <label> {{ $key+1 }}. Correct Answer</label>
                <label style="padding-left:10px"> Marking</label>
                <input type="number" name="marking[]" id="answer_value_id_{{ $key+1 }}" style="width: 3em"> 
                <div>
                    <textarea class="ckeditor" id="option_{{ $key+1 }}" name="option[]"></textarea>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <button class="add_field_button pull-right btn btn-success btn-xs" id="add_field_button">Add More Fields</button> 
@endif
@if ($question_type_id==2)
    @foreach ([0,1,2,3] as $key => $value)
    <div class="col-lg-6 form-group"> 
        <div id="div_{{ $key+1 }}">
            <input type="checkbox" id="answer" class="correct_id_{{ $key+1 }}" name="correct_answer" value="{{ $key+1 }}"> 
            <label> {{ $key+1 }}. Correct Answer</label>
            <label style="padding-left:10px"> Marking</label>
            <input type="number" name="marking[]" id="answer_value_id_{{ $key+1 }}" style="width: 3em"> 
            <div>
                <textarea class="ckeditor" id="option_{{ $key+1 }}" name="option[]"></textarea>
            </div>
        </div>
    </div>
@endforeach 
@endif
@if ($question_type_id==3)
    @foreach ([0,1] as $key => $value)
    <div class="col-lg-6 form-group"> 
        <div id="div_{{ $key+1 }}">
            <input type="radio" id="answer" class="correct_id_{{ $key+1 }}" name="correct_answer" value="{{ $key+1 }}"> 
            <label> {{ $key+1 }}. Correct Answer</label>
            <label style="padding-left:10px"> Marking</label>
            <input type="number" name="marking[]" id="answer_value_id_{{ $key+1 }}" style="width: 3em"> 
            <div>
                <textarea class="ckeditor" id="option_{{ $key+1 }}" name="option[]"></textarea>
            </div>
        </div>
    </div>
@endforeach 
@endif
@if ($question_type_id==4)
    <div class="col-lg-12 form-group"> 
        <div id="div_1">
            <input type="radio" id="answer" class="correct_id_1" name="correct_answer" value="1" checked=""> 
            <label> 1. Correct Answer</label>
            <label style="padding-left:10px"> Marking</label>
            <input type="number" name="marking[]" id="answer_value_id_1" style="width: 3em" value="1"> 
            <div>
                <textarea class="ckeditor" id="option_1" name="option[]"></textarea>
            </div>
        </div>
    </div>
@endif
<script>

  $(document).ready(function() { 
    var max_fields      = 10; //maximum input boxes allowed
    var wrapper         = $(".input_fields_wrap"); //Fields wrapper
    var add_button      = $(".add_field_button"); //Add button ID
    
    var x = 4; //initlal text box count
    $(add_button).click(function(e){ //on add input button click
        e.preventDefault();
        if(x < max_fields){ //max input box allowed
            x++; //text box increment
            var editorId = 'option_' +x;
            var div_x = 'div_' +x;
            $(wrapper).append('<div id="'+div_x+'"> <input type="radio" id="answer" name="correct_answer[]" value="'+x+'">  <label> '+x+'. Correct Answer</label>  <label style="padding-left:10px"> Marking</label> <input type="number" name="marking[]" style="width: 3em"><textarea id="'+editorId+'" class="ckeditor" name="option[]"></textarea></div></br>'); //add input box           
             
            CKEDITOR.config.toolbar_Full =
                [
                { name: 'document', items : [ 'Source'] },
                { name: 'clipboard', items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
                { name: 'editing', items : [ 'Find'] },
                { name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },
                { name: 'paragraph', items : [ 'JustifyLeft','JustifyCenter','JustifyRight'] }
                ];
            CKEDITOR.replace(editorId, { height: 100 });
            CKEDITOR.plugins.addExternal('divarea', '../extraplugins/divarea/', 'plugin.js');
            
            CKEDITOR.replace(editorId, {
                 extraPlugins: 'base64image,divarea,ckeditor_wiris',
                 language: 'en'
            });
        }
    });
    
    $('#btn_remove').on("click", function(e){ //user click on remove text
      if (x > 2) {  
         e.preventDefault(); $('#div_'+x).remove(); x--;
      }
       
    })
});
</script>
<script>
   if ({{$question_type_id}}==1) {
       $('.correct_id_1').on("click", function(e){
          
         $('#answer_value_id_1').val(1);
         $('#answer_value_id_2').val(0);
         $('#answer_value_id_3').val(0);
         $('#answer_value_id_4').val(0);
      })

      $('.correct_id_2').on("click", function(e){
          
         $('#answer_value_id_2').val(1);
         $('#answer_value_id_1').val(0);
         $('#answer_value_id_3').val(0);
         $('#answer_value_id_4').val(0);
      })

      $('.correct_id_3').on("click", function(e){
          
         $('#answer_value_id_3').val(1);
         $('#answer_value_id_2').val(0);
         $('#answer_value_id_1').val(0);
         $('#answer_value_id_4').val(0);
      })

      $('.correct_id_4').on("click", function(e){
          
         $('#answer_value_id_4').val(1);
         $('#answer_value_id_1').val(0);
         $('#answer_value_id_2').val(0);
         $('#answer_value_id_3').val(0);
      }) 
   }
   if ({{$question_type_id}}==2) {
       $('.correct_id_1').on("click", function(e){
         $('#answer_value_id_1').val(1);
      })

      $('.correct_id_2').on("click", function(e){
         $('#answer_value_id_2').val(1);
      })

      $('.correct_id_3').on("click", function(e){
        $('#answer_value_id_3').val(1);
      })

      $('.correct_id_4').on("click", function(e){
        $('#answer_value_id_4').val(1);
      }) 
   }
   if ({{$question_type_id}}==3) {
       $('.correct_id_1').on("click", function(e){
         $('#answer_value_id_1').val(1);
         $('#answer_value_id_2').val(0);
      })

      $('.correct_id_2').on("click", function(e){
         $('#answer_value_id_2').val(1);
         $('#answer_value_id_1').val(0);
      })
   }    
</script>
