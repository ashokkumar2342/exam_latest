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

class MasterController extends Controller
{
    public function subjectwisesection(Request $request)
    {   
        $subject_id = intval($request->id);
        $rs_records = DB::select(DB::raw("SELECT `ss`.`id` as `rec_id`, `st`.`name` as `rec_name` from `subject_section` `ss` inner join `section_types` `st` on `st`.`id` = `ss`.`section_id` where `ss`.`subject_id` = $subject_id;"));
        return view('admin.master.common.seclect_box', compact('rs_records'));
    }
    public function classIndex()
    {
        $rs_records = DB::select(DB::raw("SELECT * from `class_types`;"));
        return view('admin.master.class.index', compact('rs_records'));
    }

    public function classStore(Request $request, $rec_id)
    {  
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rules=[
            'code' => 'required|max:5|unique:class_types,code,'.$rec_id, 
            'name' => 'required|max:100', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $code = MyFuncs::removeSpacialChr($request->code);
        $name = MyFuncs::removeSpacialChr($request->name);
        $display_order = intval(MyFuncs::removeSpacialChr($request->display_order));

        if ($rec_id == 0) {
          $rs_save = DB::select(DB::raw("INSERT into `class_types` (`code`, `name`, `display_order`) values ('$code' , '$name' , '$display_order');")); 
          $response=['status'=>1,'msg'=>'Saved Successfully'];
        }elseif (empty($id)) { 
          $rs_save = DB::select(DB::raw("UPDATE `class_types` set `code` ='$code', `name` = '$name', `display_order` ='$display_order' where `id` = $rec_id limit 1;")); 
          $response=['status'=>1,'msg'=>'Updated Successfully'];
        }
        return response()->json($response);
    }

    public function classEdit($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("SELECT * from `class_types` where `id` = $rec_id limit 1;"));
        return view('admin.master.class.edit', compact('rs_records'));
    }

    public function classDelete($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("DELETE from `class_types` where `id` = $rec_id limit 1;"));
        return redirect()->back()->with(['message' => 'Deleted Successfully', 'class' => 'success']);
    }

//section

    public function sectionIndex()
    {
        $rs_records = DB::select(DB::raw("SELECT * from `section_types`;"));
        return view('admin.master.section.index', compact('rs_records'));
    }

    public function sectionStore(Request $request, $rec_id)
    {  
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rules=[
            'code' => 'required|max:5|unique:section_types,code,'.$rec_id, 
            'name' => 'required|max:100', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $code = MyFuncs::removeSpacialChr($request->code);
        $name = MyFuncs::removeSpacialChr($request->name);
        $display_order = intval(MyFuncs::removeSpacialChr($request->display_order));

        if ($rec_id == 0) {
          $rs_save = DB::select(DB::raw("INSERT into `section_types` (`code`, `name`, `display_order`) values ('$code' , '$name' , '$display_order');")); 
          $response=['status'=>1,'msg'=>'Saved Successfully'];
        }elseif (empty($id)) { 
          $rs_save = DB::select(DB::raw("UPDATE `section_types` set `code` ='$code', `name` = '$name', `display_order` ='$display_order' where `id` = $rec_id limit 1;")); 
          $response=['status'=>1,'msg'=>'Updated Successfully'];
        }
        return response()->json($response);
    }

    public function sectionEdit($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("SELECT * from `section_types` where `id` = $rec_id limit 1;"));
        return view('admin.master.section.edit', compact('rs_records'));
    }

    public function sectionDelete($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("DELETE from `section_types` where `id` = $rec_id limit 1;"));
        return redirect()->back()->with(['message' => 'Deleted Successfully', 'class' => 'success']);
    }

//subject

    public function subjectIndex()
    {
        $rs_records = DB::select(DB::raw("SELECT * from `subject_types`;"));
        return view('admin.master.subject.index', compact('rs_records'));
    }

    public function subjectStore(Request $request, $rec_id)
    {  
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rules=[
            'code' => 'required|max:5|unique:subject_types,code,'.$rec_id, 
            'name' => 'required|max:100', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $code = MyFuncs::removeSpacialChr($request->code);
        $name = MyFuncs::removeSpacialChr($request->name);
        $display_order = intval(MyFuncs::removeSpacialChr($request->display_order));

        if ($rec_id == 0) {
          $rs_save = DB::select(DB::raw("INSERT into `subject_types` (`code`, `name`, `display_order`) values ('$code' , '$name' , '$display_order');")); 
          $response=['status'=>1,'msg'=>'Saved Successfully'];
        }elseif (empty($id)) { 
          $rs_save = DB::select(DB::raw("UPDATE `subject_types` set `code` ='$code', `name` = '$name', `display_order` ='$display_order' where `id` = $rec_id limit 1;")); 
          $response=['status'=>1,'msg'=>'Updated Successfully'];
        }
        return response()->json($response);
    }

    public function subjectEdit($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("SELECT * from `subject_types` where `id` = $rec_id limit 1;"));
        return view('admin.master.subject.edit', compact('rs_records'));
    }

    public function subjectDelete($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("DELETE from `subject_types` where `id` = $rec_id limit 1;"));
        return redirect()->back()->with(['message' => 'Deleted Successfully', 'class' => 'success']);
    }

//questiontype

    public function questiontypeIndex()
    {
        $rs_records = DB::select(DB::raw("SELECT * from `question_types`;"));
        return view('admin.master.questiontype.index', compact('rs_records'));
    }

    public function questiontypeStore(Request $request, $rec_id)
    {  
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rules=[
            'code' => 'required|max:5|unique:question_types,code,'.$rec_id, 
            'name' => 'required|max:100', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $code = MyFuncs::removeSpacialChr($request->code);
        $name = MyFuncs::removeSpacialChr($request->name);
        $display_order = intval(MyFuncs::removeSpacialChr($request->display_order));

        if ($rec_id == 0) {
          $rs_save = DB::select(DB::raw("INSERT into `question_types` (`code`, `name`, `display_order`) values ('$code' , '$name' , '$display_order');")); 
          $response=['status'=>1,'msg'=>'Saved Successfully'];
        }elseif (empty($id)) { 
          $rs_save = DB::select(DB::raw("UPDATE `question_types` set `code` ='$code', `name` = '$name', `display_order` ='$display_order' where `id` = $rec_id limit 1;")); 
          $response=['status'=>1,'msg'=>'Updated Successfully'];
        }
        return response()->json($response);
    }

    public function questiontypeEdit($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("SELECT * from `question_types` where `id` = $rec_id limit 1;"));
        return view('admin.master.questiontype.edit', compact('rs_records'));
    }

    public function questiontypeDelete($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("DELETE from `question_types` where `id` = $rec_id limit 1;"));
        return redirect()->back()->with(['message' => 'Deleted Successfully', 'class' => 'success']);
    }

//difficultylevel

    public function difficultylevelIndex()
    {
        $rs_records = DB::select(DB::raw("SELECT * from `difficulty_levels`;"));
        return view('admin.master.difficultylevel.index', compact('rs_records'));
    }

    public function difficultylevelStore(Request $request, $rec_id)
    {  
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rules=[
            'code' => 'required|max:5|unique:difficulty_levels,code,'.$rec_id, 
            'name' => 'required|max:100', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $code = MyFuncs::removeSpacialChr($request->code);
        $name = MyFuncs::removeSpacialChr($request->name);
        $display_order = intval(MyFuncs::removeSpacialChr($request->display_order));

        if ($rec_id == 0) {
          $rs_save = DB::select(DB::raw("INSERT into `difficulty_levels` (`code`, `name`, `display_order`) values ('$code' , '$name' , '$display_order');")); 
          $response=['status'=>1,'msg'=>'Saved Successfully'];
        }elseif (empty($id)) { 
          $rs_save = DB::select(DB::raw("UPDATE `difficulty_levels` set `code` ='$code', `name` = '$name', `display_order` ='$display_order' where `id` = $rec_id limit 1;")); 
          $response=['status'=>1,'msg'=>'Updated Successfully'];
        }
        return response()->json($response);
    }

    public function difficultylevelEdit($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("SELECT * from `difficulty_levels` where `id` = $rec_id limit 1;"));
        return view('admin.master.difficultylevel.edit', compact('rs_records'));
    }

    public function difficultylevelDelete($rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("DELETE from `difficulty_levels` where `id` = $rec_id limit 1;"));
        return redirect()->back()->with(['message' => 'Deleted Successfully', 'class' => 'success']);
    }

//subject section

    public function subjectsectionIndex()
    {
        $rs_subjects = DB::select(DB::raw("SELECT * from `subject_types`;"));
        $rs_sections = DB::select(DB::raw("SELECT * from `section_types`;"));
        return view('admin.master.subjectsection.index', compact('rs_subjects', 'rs_sections'));
    }

    public function subjectsectionStore(Request $request)
    {  
        $rules=[
            'subject' => 'required', 
            'section' => 'required', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $subject_id = intval($request->subject);
        $section_id = intval($request->section);

        $rs_fatch = DB::select(DB::raw("SELECT * from `subject_section` where `subject_id` = $subject_id and `section_id` = $section_id limit 1;")); 
        if (count($rs_fatch) > 0) {
            $response=['status'=>0,'msg'=>'Record Already Exits'];
            return response()->json($response); 
        }
        $rs_save = DB::select(DB::raw("INSERT into `subject_section` (`subject_id`, `section_id`, `status`) values ('$subject_id' , '$section_id' , 1);")); 
        $response=['status'=>1,'msg'=>'Saved Successfully'];
        return response()->json($response);
    }

    public function subjectsectionTable(Request $request)
    {
        $subject_id = intval($request->id);
        $rs_records = DB::select(DB::raw("SELECT `ss`.`id`, `st`.`name` from `subject_section` `ss` inner join `section_types` `st` on `st`.`id` = `ss`.`section_id` where `ss`.`subject_id` = $subject_id;"));
        return view('admin.master.subjectsection.table', compact('rs_records'));
    }

    public function subjectsectionDelete(Request $request, $rec_id)
    {
        $rec_id = intval(Crypt::decrypt($rec_id));
        $rs_records = DB::select(DB::raw("DELETE from `subject_section` where `id` = $rec_id limit 1;"));
        $response=['status'=>1,'msg'=>'Deleted Successfully'];
        return response()->json($response);
    }

//topic
    public function topicIndex()
    {
        $rs_class_types = DB::select(DB::raw("SELECT * from `class_types`;"));
        $rs_subjects = DB::select(DB::raw("SELECT * from `subject_types`;"));
        $rs_records = DB::select(DB::raw("SELECT * from `topics`;"));
        return view('admin.master.topic.index', compact('rs_class_types', 'rs_subjects', 'rs_records'));
    }

    public function topicStore(Request $request)
    {  
        $rules=[
            'class' => 'required', 
            'subject' => 'required', 
            'section' => 'required', 
            'code' => 'required|max:5', 
            'name' => 'required|max:100', 
        ];

        $validator = Validator::make($request->all(),$rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            $response=array();
            $response["status"]=0;
            $response["msg"]=$errors[0];
            return response()->json($response);// response as json
        }
        $class_id = intval($request->class);
        $subject_id = intval($request->subject);
        $section_id = intval($request->section);
        $code = MyFuncs::removeSpacialChr($request->code);
        $name = MyFuncs::removeSpacialChr($request->name);

        $rs_save = DB::select(DB::raw("INSERT into `topics` (`class_id`, `subject_id`, `section_id`, `code`, `name`, `status`) values ('$class_id', '$subject_id' , '$section_id', '$code', '$name', 1);")); 
        $response=['status'=>1,'msg'=>'Saved Successfully'];
        return response()->json($response);
    }
}
