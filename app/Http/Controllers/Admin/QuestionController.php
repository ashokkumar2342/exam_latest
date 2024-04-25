<?php

namespace App\Http\Controllers\Admin;

use App\Helper\MyFuncs;
use App\Helper\MailHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use PDF;

class QuestionController extends Controller
{
	public function index()
	{
		$rs_question_type = DB::select(DB::raw("SELECT * from `question_types`;"));
		$rs_class_types = DB::select(DB::raw("SELECT * from `class_types`;"));
        $rs_subjects = DB::select(DB::raw("SELECT * from `subject_types`;"));
        $rs_topics = DB::select(DB::raw("SELECT * from `topics`;"));
        $rs_difficulty_levels = DB::select(DB::raw("SELECT * from `difficulty_levels`;"));
		return view('admin.question.index', compact('rs_question_type', 'rs_class_types', 'rs_subjects', 'rs_topics', 'rs_difficulty_levels'));
	}

	public function option(Request $request)
	{
		$question_type_id = intval($request->id);
		return view('admin.question.option_form', compact('question_type_id'));
	}

	public function store(Request $request)
    {  
    	// return $request;
        $rules=[
            'question_type' => 'required', 
            'class' => 'required', 
            'subject' => 'required', 
            'section' => 'required', 
            'topic' => 'required', 
            'difficulty_level' => 'required', 
            'title' => 'required', 
            'video_url' => 'nullable', 
            'question' => 'required', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $user_id = Auth::guard('admin')->user()->id;
        $question_type_id = intval($request->question_type);
        $class_id = intval($request->class);
        $subject_id = intval($request->subject);
        $section_id = intval($request->section);
        $topic_id = intval($request->topic);
        $difficulty_level_id = intval($request->difficulty_level);
        $title = MyFuncs::removeSpacialChr($request->title);
        $video_url = MyFuncs::removeSpacialChr($request->video_url);
        $question = MyFuncs::removeSpacialChr($request->question);
        $solution = MyFuncs::removeSpacialChr($request->solution);
        // $correct_answer = $request->correct_answer;
        $marking = $request->marking;
        $option = $request->option;

        $rs_save = DB::select(DB::raw("INSERT into `questions` (`question_type_id`, `class_id`, `subject_id`, `section_id`, `topic_id`, `difficulty_level_id`, `title`, `video_url`, `question_details`, `solution_details`, `status`, `entry_by`, `verified_by`) values ('$question_type_id' , '$class_id' , '$subject_id' , '$section_id', '$topic_id', '$difficulty_level_id', '$title', '$video_url', '$question', '$solution', '0', '$user_id', '0');")); 

        $rs_fatch = DB::select(DB::raw("SELECT max(`id`) as `q_id` from `questions` limit 1;"));
        if (count($rs_fatch) > 0) {
        	$question_id = $rs_fatch[0]->q_id;
        }else{
        	$question_id = 0;
        }
        foreach ($marking as $key => $value) {
        	$marking_val = intval($value);
        	if ($marking_val!=0) {
        		$correct_answer = 1;
        	}else{
        		$correct_answer = 0;
        	}
        	$option_val = MyFuncs::removeSpacialChr($option[$key]);
        	$rs_save = DB::select(DB::raw("INSERT into `options` (`question_id`, `description`, `is_correct_ans`, `marking`) values ('$question_id' , '$option_val' , '$correct_answer' , '$marking_val');"));
        }
       
		$response=['status'=>1,'msg'=>'Save Successfully'];
        return response()->json($response);
    }
}