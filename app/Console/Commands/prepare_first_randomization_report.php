<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class prepare_first_randomization_report extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepareReportFirst:generate {district_id} {phase_id}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'prepareReportFirst generate';

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
        ini_set('memory_limit','1024');
        ini_set("pcre.backtrack_limit", "100000000");
        $d_id = $this->argument('district_id');
        $phase_id = $this->argument('phase_id');
        $randomization_no = 1;
        
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
            $pro_apo = 1;
            $this->prepare_attendance($d_id, $phase_id, $pro_apo);
            $pro_apo = 2;
            $this->prepare_attendance($d_id, $phase_id, $pro_apo);
        }
            
        
        if($is_only_attendance == 1){
            $rs_update = DB::select(DB::raw("update `pdf_report_process` set `status` = 2 where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;"));
            return "";
        }
        
        $is_dept_cover = $rs_fetch[0]->dept_cover;
        $is_off_cover = $rs_fetch[0]->off_cover;
        $is_id_card = $rs_fetch[0]->id_card;
        // $randomization_no = $rs_fetch[0]->randomization_no;

        $rs_update = DB::select(DB::raw("update `pdf_report_process` set `status` = 1 where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `status` = 0 limit 1;"));

        $max_per_page = 20; 

        $rs_fetch = DB::select(DB::raw("select `name_e` from `districts` where `id` = $d_id limit 1;"));
        $dist_name = $rs_fetch[0]->name_e;

        $election_name = MyFuncs::election_name();
        $rs_header = DB::select(DB::raw("select * from `report_header_sections_district` where `district_id` = $d_id and `status` = 1 and `duty_orders_type` = 1 limit 1;"));
        $block_id = 0;
        $act_section_text = "";
        $sign_authority = "";
        $image = "";
        if(count($rs_header)>0){
            $block_id = 0;
            $act_section_text = $rs_header[0]->rpt_header;
            $sign_authority = $rs_header[0]->rpt_sigining_auth;

            $act_section_text = trim(str_replace('#elect_name#', $election_name, $act_section_text));
            $image  =\Storage_path('/app/'.$rs_header[0]->rpt_sign_file_name);
        } 
        $rs_footer = DB::select(DB::raw("select * from `report_footer_district` where `district_id` = $d_id and `status` = 1 and `duty_orders_type` = 1 order by `print_order`;"));
               

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
        $rs_departments = DB::select(DB::raw("select distinct `pod`.`department_id`, `dept`.`department_name_e` from `po_data` `pod` inner join `departments` `dept` on `dept`.`id` = `pod`.`department_id` where `pod`.`district_id` = $d_id and `pod`.`phase_id` = $phase_id and `pod`.`duty_alloted` = 1 and `pod`.`eligibility` in (1,2) $dept_condition order by `dept`.`department_name_e`;"));

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

                $rs_fetch_cover = DB::select(DB::raw("select `off`.`office_name_e`, case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `elg`.`eligibility_name_e`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` inner join `offices` `off` on `off`.`id` = `pod`.`office_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`phase_id` = $phase_id and `pod`.`department_id` = $dept_id and `pod`.`eligibility` in (1,2) order by `off`.`office_name_e`, `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));
                if(count($rs_fetch_cover) > 0){
                    $html_cover_dept = view('admin.report.prepareDutyOrderReport.cover_header_dpt',compact('cover_type', 'dpt_off_name', 'rs_fetch_cover', 'max_per_page'));    
                    if($is_pre_page_portrait == 1){
                        $html_cover_dept = "<pagebreak>".$html_cover_dept;
                    }
                    $html_dept = $html_dept.$html_cover_dept;
                    $is_pre_page_portrait = 1;
                }
            }

            //For offices
            $rs_offices = DB::select(DB::raw("select distinct `pod`.`office_id`, `off`.`office_name_e` from `po_data` `pod` inner join `offices` `off` on `off`.`id` = `pod`.`office_id` where `pod`.`duty_alloted` = 1 and `pod`.`phase_id` = $phase_id and `pod`.`department_id` = $dept_id and `pod`.`eligibility` in (1,2) order by `off`.`office_name_e`;"));
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

                    $rs_fetch_cover = DB::select(DB::raw("select case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `elg`.`eligibility_name_e`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`phase_id` = $phase_id and `pod`.`office_id` = $office_id and `pod`.`eligibility` in (1,2) order by `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));

                    if(count($rs_fetch_cover)> 0){
                        $html_cover_off = view('admin.report.prepareDutyOrderReport.cover_header_off',compact('dpt_off_name', 'rs_fetch_cover', 'max_per_page'));
                        if($is_pre_page_portrait == 1){
                            $html_cover_off = "<pagebreak>".$html_cover_off;
                        }    
                        $html_office = $html_office.$html_cover_off;
                        $is_pre_page_portrait = 1;
                    }
                        
                }
                
                
                $rs_employees = DB::select(DB::raw("select case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e`, `bl`.`name_e` as `ac_name`, `bl`.`code` as `ac_code`, `pod`.`block_id`, 0 as `bl_id`, `elg`.`eligibility_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` left join `blocks_mcs` `bl` on `bl`.`id` = `pod`.`block_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`office_id` = $office_id and `pod`.`phase_id` = $phase_id and `pod`.`eligibility` in (1,2) order by `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));
                if(count($rs_employees)>0){
                    $html_return = view('admin.report.prepareDutyOrderReport.duty_order_pro_apo_all', compact('rs_employees', 'd_id', 'dept_name', 'office_Name', 'election_name', 'dist_name'));
                    if($is_pre_page_portrait == 1){
                        $html_return = "<pagebreak>".$html_return;
                    }
                    $html_office = $html_office.$html_return;
                    $is_pre_page_portrait = 1;
                }
                    


                if($is_id_card == 1){
                    $rs_employees = DB::select(DB::raw("select case when `pod`.`party_no` = 0 then 0 else 1 end as `is_reserved`, `pod`.`eligibility`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dsg`.`designation_name_e`, '' as `ac_name`, '' as `ac_code`, `elg`.`eligibility_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` inner join `eligibility` `elg` on `elg`.`id` = `pod`.`eligibility` where `pod`.`duty_alloted` = 1 and `pod`.`office_id` = $office_id and `pod`.`phase_id` = $phase_id and `pod`.`eligibility` in (1,2) order by `is_reserved` desc, `pod`.`eligibility`, `emp`.`employee_name_e`;"));
                    if(count($rs_employees)>0){
                        $html_return = view('admin.report.prepareDutyOrderReport.icard', compact('rs_employees', 'dept_name', 'office_Name', 'election_name'));
                        if($is_pre_page_portrait == 2){
                            $html_return = "<pagebreak>".$html_return;
                        }
                        $is_pre_page_portrait = 2;
                    }
                        
                }else{
                    $html_return = "";
                }
                $html_office = $html_office.$html_return;
                $html_write = "";
                if(substr($html_office,0,11) == "<pagebreak>"){
                    $html_write = substr($html_office,11);
                }else{
                    $html_write = $html_office;
                }
                $mpdf_office->WriteHTML($html_write);
                $mpdf_office->WriteHTML('</body></html>');
                $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/first/off';
                $documentUrl = Storage_path() . $file_path;  
                @mkdir($documentUrl, 0755, true);  
                $file_path = $file_path.'/'.$office_id.'.pdf';
                $mpdf_office->Output(Storage_path() .$file_path, 'F'); 
                $rs_insert = DB::select(DB::raw("insert into `pdf_report_detail` (`district_id`, `phase_id`, `randomization_no`, `pdf_type`, `dept_id`, `office_id`, `file_path`) values ($d_id, $phase_id, $randomization_no, 2, $dept_id, $office_id, '$file_path');"));
                $html_dept = $html_dept.$html_office;    
            }   //Loop Office End

            $html_write = "";
            if(substr($html_dept,0,11) == "<pagebreak>"){
                $html_write = substr($html_dept,11);
            }else{
                $html_write = $html_dept;
            }
            $mpdf_department->WriteHTML($html_write);
            $mpdf_department->WriteHTML('</body></html>');
            
            $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/first/dept';
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
            $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/first';
            $documentUrl = Storage_path() . $file_path; 
            @mkdir($documentUrl, 0755, true); 
            $file_path = $file_path.'/complete.pdf';
            $documentUrl = Storage_path() . $file_path; 
            $mpdf_complete->Output($documentUrl, 'F');   
            
            $rs_insert = DB::select(DB::raw("INSERT into `randomization_report_file` (`district_id`, `phase_id`, `randomization_no`, `extra_reserve_id`, `file_caption`, `file_path`) values ($d_id, $phase_id, $randomization_no, 0, '$file_caption', '$file_path');"));
        }
            
        $rs_update = DB::select(DB::raw("update `pdf_report_process` set `status` = 2 where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;"));
      
    }

    public function prepare_attendance($d_id, $phase_id, $pro_apo)
    { 
        $rs_fetch = DB::select(DB::raw("select `name_e` from `districts` where `id` = $d_id limit 1;"));
        $dist_name = $rs_fetch[0]->name_e;

        $election_name = MyFuncs::election_name();
        $randomization_no = 1;      

        $path=Storage_path('fonts/');
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir']; 
        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata']; 
        
        $mpdf_att = new \Mpdf\Mpdf([
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

        $rs_fetch = DB::select(DB::raw("select `pod`.`eligibility`, `pod`.`seat_sr_no`, `emp`.`id`, `emp`.`employee_name_e`, `emp`.`mobile_no`, `dept`.`department_name_e`, `off`.`office_name_e`, `dsg`.`designation_name_e` from `po_data` `pod` inner join `employee_details` `emp` on `emp`.`id` = `pod`.`id` inner join `departments` `dept` on `dept`.`id` = `emp`.`department_id` inner join `offices` `off` on `off`.`id` = `emp`.`office_id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` where `pod`.`district_id` = $d_id and `pod`.`phase_id` = $phase_id and `pod`.`eligibility`  =  $pro_apo and `pod`.`duty_alloted` = 1 order by `pod`.`seat_sr_no`;"));
        if($pro_apo == 1){
            $elect_desig = "Presiding Officer";
        }else{
            $elect_desig = "Alternate Presiding Officer";
        }
        $html_att = view('admin.report.prepareDutyOrderReport.att_pro_apo',compact('rs_fetch', 'elect_desig', 'election_name', 'dist_name'));
        
        
        $mpdf_att->WriteHTML($html_att);
        
        $file_path = '/app/report/'.$d_id.'/'.$phase_id.'/first';
        $file_caption = "";
        $documentUrl = Storage_path() . $file_path;  
        @mkdir($documentUrl, 0755, true);  
        if($pro_apo == 1){
            $file_path = $file_path.'/att_pro.pdf';
            $file_caption = "Attendance (Presiding Officer)";
        }else{
            $file_path = $file_path.'/att_apo.pdf';
            $file_caption = "Attendance (Alternate Presiding Officer)";
        }
        $documentUrl = Storage_path() . $file_path; 
        $mpdf_att->Output($documentUrl, 'F');
        
        $rs_insert = DB::select(DB::raw("INSERT into `randomization_report_file` (`district_id`, `phase_id`, `randomization_no`, `extra_reserve_id`, `file_caption`, `file_path`) values ($d_id, $phase_id, $randomization_no, 0, '$file_caption', '$file_path');"));
      
    }
       
}
