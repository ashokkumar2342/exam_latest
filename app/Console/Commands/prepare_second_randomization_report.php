<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class prepare_second_randomization_report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepareReportSecond:generate {district_id} {phase_id}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'prepareReportSecond generate';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    //\Log::info(date('Y-m-d H:i:s'));
    public function handle()
    { 
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit','999M');
        ini_set("pcre.backtrack_limit", "100000000");
        $d_id = $this->argument('district_id');
        $phase_id = $this->argument('phase_id');
        
        $randomization_no = 2;
        $html_icard = "";
        $html_cover_off = "";
        $html_cover_dept = "";

        $is_pre_page_portrait = 0;

        $rs_fetch = DB::select(DB::raw("select * from `pdf_report_process` where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `status` = 0 limit 1;"));
        
        if(count($rs_fetch)==0){
            return "";
        }

        $is_only_attendance = $rs_fetch[0]->only_attendance_sheet;       
        $dept_id = $rs_fetch[0]->dept_id;

        if($is_only_attendance == 0 && $dept_id == 0){
            $rs_delete = DB::select(DB::raw("delete from `pdf_report_detail` where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no;"));    
        }
        
        if($is_only_attendance == 0  && $dept_id == 0){
            $rs_delete = DB::select(DB::raw("delete from `randomization_report_file` where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no;"));    
        }elseif($is_only_attendance == 1){
            $rs_delete = DB::select(DB::raw("delete from `randomization_report_file` where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `file_caption` like 'Attendance%';"));
        }elseif($dept_id > 0){
            $rs_delete = DB::select(DB::raw("delete from `pdf_report_detail` where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `dept_id` = $dept_id;"));
        }

        if($dept_id == 0) {
            $rs_blocks = DB::select(DB::raw("select * from `blocks_mcs` where `districts_id` = $d_id and `phase_no` = $phase_id;"));
            foreach ($rs_blocks as $key => $val_blocks) {
                $this->prepare_attendance($d_id, $val_blocks->id);
            }
        }

        if($is_only_attendance == 1){
            $rs_update = DB::select(DB::raw("update `pdf_report_process` set `status` = 2 where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;"));
            return "";
        }


        $is_dept_cover = $rs_fetch[0]->dept_cover;
        $is_off_cover = $rs_fetch[0]->off_cover;
        $is_id_card = $rs_fetch[0]->id_card;

        $rs_update = DB::select(DB::raw("update `pdf_report_process` set `status` = 1 where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `status` = 0 limit 1;"));

        $max_per_page = 20; 

        $rs_fetch = DB::select(DB::raw("select `name_e` from `districts` where `id` = $d_id limit 1;"));
        $dist_name = $rs_fetch[0]->name_e;

        $election_name = MyFuncs::election_name();
        // $rs_header = DB::select(DB::raw("select * from `report_header_sections` where `district_id` = $d_id and `status` = 1 order by `block_id` limit 1;"));
        // $block_id = 0;
        // $act_section_text = "";
        // $sign_authority = "";
        // $image = "";
        // if(count($rs_header)>0){
        //     $block_id = $rs_header[0]->block_id;
        //     $act_section_text = $rs_header[0]->rpt_header;
        //     $sign_authority = $rs_header[0]->rpt_sigining_auth;

        //     $act_section_text = trim(str_replace('#elect_name#', $election_name, $act_section_text));
        //     $image  =\Storage_path('/app/'.$rs_header[0]->rpt_sign_file_name);
        // } 
        // $rs_footer = DB::select(DB::raw("select * from `report_footer` where `block_id` = $block_id and `status` = 1 order by `print_order`;"));
               

        $path=Storage_path('fonts/');
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir']; 
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata']; 
        
        $mpdf_complete = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                     __DIR__ . $path,
                 ]),
                 'fontdata' => $fontData + [
                     'frutiger' => [
                         'R' => 'FreeSans.ttf',
                         'I' => 'FreeSansOblique.ttf',
                     ]
                 ],
                 'default_font' => 'freesans',
             ]);

        $html = view('admin.report.prepareDutyOrderReport.start');
        $mpdf_complete->WriteHTML($html);


        $html_complete = "";
        $dept_condition = "";
        $request_dept_id = $dept_id;
        if($dept_id > 0){
            $dept_condition = " and `pod`.`department_id` = ".$dept_id;
        }
        $rs_departments = DB::select(DB::raw("select distinct `pod`.`department_id`, `dept`.`department_name_e` from `po_data` `pod` inner join `departments` `dept` on `dept`.`id` = `pod`.`department_id` where `pod`.`district_id` = $d_id and `pod`.`phase_id` = $phase_id and `pod`.`duty_alloted` = 1 $dept_condition order by `dept`.`department_name_e`;"));

        
        
        foreach ($rs_departments as $key => $val_departments) {
            $mpdf_department = new \Mpdf\Mpdf([
                'fontDir' => array_merge($fontDirs, [
                         __DIR__ . $path,
                     ]),
                     'fontdata' => $fontData + [
                         'frutiger' => [
                             'R' => 'FreeSans.ttf',
                             'I' => 'FreeSansOblique.ttf',
                         ]
                     ],
                     'default_font' => 'freesans',
            ]);
            $html_dept = "";
            $html = view('admin.report.prepareDutyOrderReport.start');
            $mpdf_department->WriteHTML($html);

            
            $dept_id = $val_departments->department_id;
            $dept_name = $val_departments->department_name_e;
            if($is_dept_cover == 1){
                $html_cover_dept = "";
                $cover_type = 1;
                $dpt_off_name = $dept_name;

                $rs_fetch_cover = DB::select(DB::raw("select `off`.`office_name_e`, case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `elg`.`eligibility_name_e`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` inner join `offices` `off` on `off`.`id` = `pod`.`office_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`phase_id` = $phase_id and `pod`.`department_id` = $dept_id order by `off`.`office_name_e`, `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));

                $html_cover_dept = view('admin.report.prepareDutyOrderReport.cover_header_dpt',compact('cover_type', 'dpt_off_name', 'rs_fetch_cover', 'max_per_page'));    
                if($is_pre_page_portrait == 1){
                    $html_cover_dept = "<pagebreak>".$html_cover_dept;
                }
                $html_dept = $html_dept.$html_cover_dept;
                $is_pre_page_portrait = 1;
            }

            //For offices
            $rs_offices = DB::select(DB::raw("select distinct `pod`.`office_id`, `off`.`office_name_e` from `po_data` `pod` inner join `offices` `off` on `off`.`id` = `pod`.`office_id` where `pod`.`duty_alloted` = 1 and `pod`.`phase_id` = $phase_id and `pod`.`department_id` = $dept_id order by `off`.`office_name_e`;"));
            foreach ($rs_offices as $key => $val_offices) {
                $html_office = "";
                $html_cover_off = "";
                $office_id = $val_offices->office_id;
                $office_Name = $val_offices->office_name_e;

                $mpdf_office = new \Mpdf\Mpdf([
                    'fontDir' => array_merge($fontDirs, [
                             __DIR__ . $path,
                         ]),
                         'fontdata' => $fontData + [
                             'frutiger' => [
                                 'R' => 'FreeSans.ttf',
                                 'I' => 'FreeSansOblique.ttf',
                             ]
                         ],
                         'default_font' => 'freesans',
                ]);
                $html = view('admin.report.prepareDutyOrderReport.start');
                $mpdf_office->WriteHTML($html);

                if($is_off_cover == 1){
                    $html_cover_off = "";
                    $dpt_off_name = $office_Name;

                    $rs_fetch_cover = DB::select(DB::raw("select case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `elg`.`eligibility_name_e`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`phase_id` = $phase_id and `pod`.`office_id` = $office_id order by `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));

                    $html_cover_off = view('admin.report.prepareDutyOrderReport.cover_header_off',compact('dpt_off_name', 'rs_fetch_cover', 'max_per_page'));
                    if($is_pre_page_portrait == 1){
                        $html_cover_off = "<pagebreak>".$html_cover_off;
                    }    
                    $html_office = $html_office.$html_cover_off;
                    $is_pre_page_portrait = 1;
                }
                
                $rs_employees = DB::select(DB::raw("select `pod`.`id`, `pod`.`party_no`, `pod`.`seat_sr_no`, `pod`.`print_order`, `pp`.`po1_id`, `pp`.`po1_name`, `pp`.`po1_dpt`, `pp`.`po1_off`, `pp`.`po1_desig`, `pp`.`po1_mobile`, `pp`.`po1_whatsapp`, `pp`.`po2_id`, `pp`.`po2_name`, `pp`.`po2_dpt`, `pp`.`po2_off`, `pp`.`po2_desig`, `pp`.`po2_mobile`, `pp`.`po2_whatsapp`, `pp`.`po3_id`, `pp`.`po3_name`, `pp`.`po3_dpt`, `pp`.`po3_off`, `pp`.`po3_desig`, `pp`.`po3_mobile`, `pp`.`po3_whatsapp`, `pp`.`po4_id`, `pp`.`po4_name`, `pp`.`po4_dpt`, `pp`.`po4_off`, `pp`.`po4_desig`, `pp`.`po4_mobile`, `pp`.`po4_whatsapp`, `pp`.`po5_id`, `pp`.`po5_name`, `pp`.`po5_dpt`, `pp`.`po5_off`, `pp`.`po5_desig`, `pp`.`po5_mobile`, `pp`.`po5_whatsapp`, `pp`.`po6_id`, `pp`.`po6_name`, `pp`.`po6_dpt`, `pp`.`po6_off`, `pp`.`po6_desig`, `pp`.`po6_mobile`, `pp`.`po6_whatsapp`, `bl`.`code`, `bl`.`name_e`, `elg`.`eligibility_name_e`, `pod`.`block_id` from `po_data` `pod` inner join `polling_parties` `pp` on `pp`.`id` = `pod`.`party_id` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `blocks_mcs` `bl` on `bl`.`id` = `pod`.`block_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`office_id` = $office_id and `pod`.`party_no` > 0 and `pod`.`phase_id` = $phase_id order by `pod`.`eligibility`, `emp`.`employee_name_e`;"));
                if(count($rs_employees)>0){
                    $html_return = view('admin.report.prepareDutyOrderReport.complete_party_duty_order', compact('rs_employees', 'd_id', 'dist_name', 'election_name'));
                    if($is_pre_page_portrait == 2){
                        $html_return = "<pagebreak>".$html_return;
                    }
                    $html_office = $html_office.$html_return;
                    $is_pre_page_portrait = 2;
                }

                $rs_employees = DB::select(DB::raw("select case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e`, `bl`.`name_e` as `ac_name`, `bl`.`code` as `ac_code`, `pod`.`block_id`, `pod`.`block_id` as `bl_id`, `elg`.`eligibility_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` left join `blocks_mcs` `bl` on `bl`.`id` = `pod`.`block_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`office_id` = $office_id and `pod`.`phase_id` = $phase_id and `pod`.`party_no` = 0 order by `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));
                if(count($rs_employees)>0){
                    $html_return = view('admin.report.prepareDutyOrderReport.duty_order_pro_apo_all', compact('rs_employees', 'd_id', 'dept_name', 'office_Name', 'election_name', 'dist_name'));
                    if($is_pre_page_portrait == 1){
                        $html_return = "<pagebreak>".$html_return;
                    }
                    $html_office = $html_office.$html_return;
                    $is_pre_page_portrait = 1;
                }
                    


                if($is_id_card == 1){
                    $rs_employees = DB::select(DB::raw("select case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e`, `bl`.`name_e` as `ac_name`, `bl`.`code` as `ac_code`, `pod`.`block_id`, `elg`.`eligibility_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` left join `blocks_mcs` `bl` on `bl`.`id` = `pod`.`block_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`office_id` = $office_id and `pod`.`phase_id` = $phase_id order by `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));
                
                    reset($rs_employees);
                    $html_return = view('admin.report.prepareDutyOrderReport.icard', compact('rs_employees', 'dept_name', 'office_Name', 'election_name'));
                    if($is_pre_page_portrait == 2){
                        $html_return = "<pagebreak>".$html_return;
                    }
                    $is_pre_page_portrait = 2;
                }else{
                    $html_return = "";
                }
                $html_office = $html_office.$html_return;


                $mpdf_office->WriteHTML($html_office);
                $mpdf_office->WriteHTML('</body></html>');

                $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/second/off';
                $documentUrl = Storage_path() . $file_path;  
                @mkdir($documentUrl, 0755, true);  
                $file_path = $file_path.'/'.$office_id.'.pdf';
                $mpdf_office->Output(Storage_path() .$file_path, 'F'); 
                $rs_insert = DB::select(DB::raw("insert into `pdf_report_detail` (`district_id`, `phase_id`, `randomization_no`, `pdf_type`, `dept_id`, `office_id`, `file_path`) values ($d_id, $phase_id, $randomization_no, 2, $dept_id, $office_id, '$file_path');"));

                $html_dept = $html_dept.$html_office;    
            }   //Loop Office End


            $mpdf_department->WriteHTML($html_dept);
            $mpdf_department->WriteHTML('</body></html>');


            $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/second/dept';
            $documentUrl = Storage_path() . $file_path;  
            @mkdir($documentUrl, 0755, true);  
            $file_path = $file_path.'/'.$dept_id.'.pdf';
            $mpdf_department->Output(Storage_path() .$file_path, 'F'); 
            $rs_insert = DB::select(DB::raw("insert into `pdf_report_detail` (`district_id`, `phase_id`, `randomization_no`, `pdf_type`, `dept_id`, `office_id`, `file_path`) values ($d_id, $phase_id, $randomization_no, 1, $dept_id, 0, '$file_path');"));
            
            
            $html_complete = $html_complete.$html_dept;
        }   //Loop Department End

        if($request_dept_id == 0){
            $mpdf_complete->WriteHTML($html_complete);
            $mpdf_complete->WriteHTML('</body></html>');
            
            $file_caption = "Complete Duty Orders";
            $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/second';
            $documentUrl = Storage_path() . $file_path; 
            @mkdir($documentUrl, 0755, true); 
            $file_path = $file_path.'/complete.pdf';
            $documentUrl = Storage_path() . $file_path; 
            $mpdf_complete->Output($documentUrl, 'F');   
            
            $rs_insert = DB::select(DB::raw("INSERT into `randomization_report_file` (`district_id`, `phase_id`, `randomization_no`, `extra_reserve_id`, `file_caption`, `file_path`) values ($d_id, $phase_id, $randomization_no, 0, '$file_caption', '$file_path');"));
        }

        $rs_update = DB::select(DB::raw("update `pdf_report_process` set `status` = 2 where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;"));
    }


    public function prepare_attendance($d_id, $block_id)
    { 
        $randomization_no = 2;
        $rs_fetch = DB::select(DB::raw("select `name_e` from `districts` where `id` = $d_id limit 1;"));
        $dist_name = $rs_fetch[0]->name_e;

        $rs_fetch = DB::select(DB::raw("select `name_e`, `phase_no` from `blocks_mcs` where `id` = $block_id limit 1;"));
        $block_name = $rs_fetch[0]->name_e;
        $phase_id = $rs_fetch[0]->phase_no;

        $election_name = MyFuncs::election_name();
        $elect_type = MyFuncs::elect_type();
               

        $path=Storage_path('fonts/');
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir']; 
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata']; 
        
        $mpdf_party_wise = new \Mpdf\Mpdf([
          'fontDir' => array_merge($fontDirs, [
                 __DIR__ . $path,
             ]),
             'fontdata' => $fontData + [
                 'frutiger' => [
                     'R' => 'FreeSans.ttf',
                     'I' => 'FreeSansOblique.ttf',
                 ]
             ],
             'default_font' => 'freesans',
        ]);

        // if($d_id != 14){
            $rs_fetch = DB::select(DB::raw("select * from `polling_parties` where `block_id` = $block_id order by `party_no`;"));
            $report_type = 1;
            $html_check_list = view('admin.report.common.attendance_complete_party',compact('rs_fetch', 'election_name', 'block_name', 'elect_type', 'report_type'));

            $mpdf_party_wise->WriteHTML($html_check_list);
            $mpdf_party_wise->WriteHTML("<pagebreak>");
        // }else{
        //     $report_type = 1;
        // }
            

        // if($d_id == 14){
        //     $l_date = date('Y-m-d');
        //     $condition = " and `pod`.`extra_reserve_date` = '".$l_date."' ";    
        // }else{
            $condition = "";
        // }
        
        $rs_fetch = DB::select(DB::raw("select `emp`.`employee_name_e`, `emp`.`id` as `emp_id`,  `emp`.`mobile_no`, `dsg`.`designation_name_e`, `dpt`.`department_name_e`, `off`.`office_name_e`, `elg`.`eligibility_name_e`, `pod`.`seat_sr_no` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` left join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` inner join `offices` `off` on `off`.`id` = `pod`.`office_id` inner join `departments` `dpt` on `dpt`.`id` = `emp`.`department_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`party_no` = 0 and `pod`.`block_id` = $block_id $condition order by `pod`.`seat_sr_no`;"));

        $html_check_list = view('admin.report.common.attendance_extra_reserve',compact('rs_fetch', 'election_name', 'block_name', 'elect_type', 'report_type'));        
        $mpdf_party_wise->WriteHTML($html_check_list);
        
        
        
        $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/second/attendance_'.$block_id.'.pdf';
        $dir_path = Storage_path() . '/app/report/'.$d_id.'/'.$phase_id.'/second/';
        $documentUrl = Storage_path() . $file_path;  
        @mkdir($dir_path, 0755, true);  
        $mpdf_party_wise->Output($documentUrl, 'F');

        $file_caption = "Attendance Sheet (".$block_name.")";
        $rs_insert = DB::select(DB::raw("INSERT into `randomization_report_file` (`district_id`, `phase_id`, `randomization_no`, `extra_reserve_id`, `file_caption`, `file_path`) values ($d_id, $phase_id, $randomization_no, 0, '$file_caption', '$file_path');"));
        
    }

       
}
