@extends('admin.layout.base')
@section('body')
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3>Question</h3>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right"> 
                </ol>
            </div>
        </div> 
        <div class="card card-info"> 
            <div class="card-body"> 
                <form action="{{ route('admin.question.store') }}" method="post" class="add_form">
                {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Question Type</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="question_type" id="question_type_select_box" class="form-control select2" required editor_question="4" onchange="callAjax(this, '{{ route('admin.question.option') }}', 'option_div')">
                                <option selected disabled>Select Question Type</option>
                                @foreach ($rs_question_type as $rs_question_type_val)
                                    <option value="{{$rs_question_type_val->id}}">{{$rs_question_type_val->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Class</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="class" id="class_select_box" class="form-control select2" required>
                                <option selected disabled>Select Class</option>
                                @foreach ($rs_class_types as $rs_class_type)
                                    <option value="{{$rs_class_type->id}}">{{$rs_class_type->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Subject</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="subject" id="subject_select_box" class="form-control select2" onchange="callAjax(this, '{{ route('admin.Master.subjectwisesection') }}', 'section_select_box')" required>
                                <option selected disabled>Select Subject</option>
                                @foreach ($rs_subjects as $rs_subject)
                                    <option value="{{$rs_subject->id}}">{{$rs_subject->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Section</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="section" class="form-control select2" id="section_select_box" required>
                                <option selected disabled>Select Option</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Topic</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="topic" id="topic_select_box" class="form-control select2" required>
                                <option selected disabled>Select Topic</option>
                                @foreach ($rs_topics as $rs_topic)
                                    <option value="{{$rs_topic->id}}">{{$rs_topic->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-4">
                            <label for="exampleInputEmail1">Difficulty Level</label>
                            <span class="fa fa-asterisk"></span>
                            <select name="difficulty_level" id="difficulty_level_select_box" class="form-control select2" required>
                                <option selected disabled>Select Difficulty Level</option>
                                @foreach ($rs_difficulty_levels as $rs_difficulty_level)
                                    <option value="{{$rs_difficulty_level->id}}">{{$rs_difficulty_level->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="exampleInputEmail1">Title</label>
                            <span class="fa fa-asterisk"></span>
                            <input type="text" name="title" class="form-control" placeholder="Enter Title" maxlength="250" required>
                        </div>
                        <div class="form-group col-lg-6">
                            <label for="exampleInputEmail1">Video URL</label>
                            <span class="fa fa-asterisk"></span>
                            <input type="text" name="video_url" class="form-control" placeholder="Enter Name" maxlength="250">
                        </div> 
                        <div class="col-lg-12 form-group ">
                            <label>Question</label> 
                            <textarea name="question" class="ckeditor form-control" id="question"></textarea>
                        </div>

                        <div class="row" id="option_div"></div>

                        <div class="col-md-12">
                            <div class="form-group" >
                                <label>Solution</label> 
                                <textarea name="solution" class="ckeditor form-control" id="solution"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <div class="form-group">
                                <input type="submit" class="btn btn-warning" value="Save as Draft">
                                <input type="submit" class="btn btn-success" value="Final Submit">
                            </div>
                        </div>
                    </div> 
                </form>
            </div> 
        </div>
    </div> 
</section>
@endsection
@push('scripts')
 <script>
   CKEDITOR.config.toolbar_Full =
       [
       { name: 'document', items : [ 'Source'] },
       { name: 'clipboard', items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
       { name: 'editing', items : [ 'Find'] },
       { name: 'basicstyles', items : [ 'Bold','Italic','Underline'] },
       { name: 'paragraph', items : [ 'JustifyLeft','JustifyCenter','JustifyRight'] }
       ];
   CKEDITOR.replace('question', { height: 200 });
   CKEDITOR.plugins.addExternal('divarea', '../extraplugins/divarea/', 'plugin.js');
   
   CKEDITOR.replace('question', {
        extraPlugins: 'base64image,divarea,ckeditor_wiris',
        language: 'en'
   });
  CKEDITOR.replace('solution', { height: 200 });
   CKEDITOR.plugins.addExternal('divarea', '../extraplugins/divarea/', 'plugin.js');
   
   CKEDITOR.replace('solution', {
        extraPlugins: 'base64image,divarea,ckeditor_wiris',
        language: 'en'
   });
 </script>
@endpush

