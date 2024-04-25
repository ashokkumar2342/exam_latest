<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backupmanagement:generate';
    


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'backupmanagement generate';

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
        ini_set('max_execution_time', '14400');
        ini_set('memory_limit','3000M');
        ini_set("pcre.backtrack_limit", "100000000");
        
        $folder = "1";
        /*Table Group 1*/
        $file_content = "";
        $fileName = "00_developer.sql";
        $file_content .= $this->prepareTableGroup00Backup();
        \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);

        /*Table Group 2*/
        $file_content = "";
        $fileName = "01_users.sql";
        $file_content .= $this->prepareTableGroup01Backup();
        \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);
        
        /*Table Group 3*/
        $file_content = "";
        $fileName = "02_master.sql";
        $file_content .= $this->prepareTableGroup02Backup();
        \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);
        
        /*Table Group 4*/
        $file_content = "";
        $fileName = "03_employee.sql";
        $file_content .= $this->prepareTableGroup03Backup();
        \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);
        
        /*Table Group 5*/
        $file_content = "";
        $fileName = "04_communication.sql";
        $file_content .= $this->prepareTableGroup04Backup();
        \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);
        
        /*Table Group 6*/
        $file_content = "";
        $fileName = "05_first_randomization.sql";
        $file_content .= $this->prepareTableGroup05Backup();
        \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);

        /*Table Group 7*/
        $file_content = "";
        $fileName = "06_group.sql";
        $file_content .= $this->prepareTableGroup06Backup();
        \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);

        //Employee Data District Wise
        $truncate = 1;
        $rs_district = DB::select(DB::raw("SELECT `id`, `code`, `name_e` from `districts` order by `id`;"));
        foreach ($rs_district as $key => $value) {
            $file_content = "";
            $fileName = "emp_".$value->code."_".$value->name_e.".sql";
            $condition = " where `district_id` = ".$value->id;
            

            $table_name = "employee_details";

            $file_content .= $this->prepareBackup($table_name, $condition, $truncate);
            \File::put(public_path('/backup/'.$folder.'/'.$fileName),$file_content);

            $truncate = 0;
        }
    }


        
    public function prepareTableGroup06Backup()
    {
        $file_content = "";

        $condition = "";
        $truncate = 1;

        $table_name = "election_setting_block";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "election_setting_district";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "emp_photo_upload_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "final_training_schedule";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "log_dept_hardcopy_received";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "log_office_transfer_other_district";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "pc_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "report_footer_district";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "report_header_sections";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "report_header_sections_district";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "ro_duty_types";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "ro_other_duty_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "ro_other_duty_history_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "second_training_schedule";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "training_schedule";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "user_state_assigns";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        return $file_content;   
    }

    public function prepareTableGroup05Backup()
    {
        $file_content = "";

        $condition = "";
        $truncate = 1;

        $table_name = "absent_notice_ro";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "attendance_marking_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "auto_attendance_setting";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "deployed_data";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "duty_report_footer";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "duty_report_header";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "duty_sector_magistrate_order_format";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "dutyreplaceddetail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "dutyreplaceddetail_log";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "first_training_schedule";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "log_randomization_status";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "po_data";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "polling_parties";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "polling_parties_first_random";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "randomization_setting";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "randomization_status";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "report_footer";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        return $file_content;   
    }

    public function prepareTableGroup04Backup()
    {
        $file_content = "";

        $condition = "";
        $truncate = 1;

        $table_name = "cp_block_officer_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_district_officer_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_dm_other_staff_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_eminent_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_pb_police_staff_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_pb_video_staff_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_pb_webcast_staff_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_ss_other_staff_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "cp_state_officer_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        return $file_content;   
    }

    public function prepareTableGroup03Backup()
    {
        $file_content = "";

        $condition = "";
        $truncate = 1;

        $table_name = "blo_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "blo_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "blo_locked";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "blo_locked_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "department_locked_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "dept_exemption_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "duty_magistrate_cluster";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "emp_deleted_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "emp_district_transfer_detail";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "emp_exemption_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "emp_transfer_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        // $table_name = "employee_details";
        // $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        // $table_name = "employee_history_details";
        // $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "log_handi_verified";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "log_hardcopy_received";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "nodal_officer_department";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "nodal_officer_office";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "office_exemption_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "office_locked_history";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "police_with_duty_magistrate";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "police_with_sec_magistrate";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "sector_supervisor_cluster";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        return $file_content;   
    }

    public function prepareTableGroup02Backup()
    {
        $file_content = "";

        $condition = "";
        $truncate = 1;

        $table_name = "ac_mas";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "basic_pay";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "blocks_mcs";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "booths_buildings";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "bus_routes";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "categorys";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "department_types";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "departments";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "designations";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "districts";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "eligibility";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "exempted_reason";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "extra_reserve";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "genders";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "grade_pay";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "offices";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "pay_matrix";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "pay_scale_mas";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "pay_scale_mas_7th";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "polling_booth_female";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "polling_booths";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "qualification_mas";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "recruitment_types";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "relation";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "report_types";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "states";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "villages";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        return $file_content;   
    }

    public function prepareTableGroup01Backup()
    {
        $file_content = "";

        $condition = "";
        $truncate = 1;

        $table_name = "admins";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "default_role_menu";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "default_role_quick_menu";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "minu_types";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "roles";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "sub_menus";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "user_block_assigns";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "user_district_assigns";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        return $file_content;   
    }

    public function prepareTableGroup00Backup()
    {
        $file_content = "";

        $condition = "";
        $truncate = 1;

        $table_name = "activities";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "default_app_values";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "default_values";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "duty_order_type";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "election_phases";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "election_setting";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "email_api";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "failed_jobs";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "jobs";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "migrations";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "print_setting";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        $table_name = "sms_api";
        $file_content .= $this->prepareBackup($table_name, $condition, $truncate);        

        return $file_content;   
    }

    public function prepareBackup($table_name, $condition, $truncate)
    {
        
        $backup_content = '';
        if($truncate == 1){
            $backup_content .= "\nTRUNCATE TABLE `$table_name`;\n\n";
        }
        
        $rs_result = DB::select(DB::raw("select * from `$table_name` $condition;"));
        // dd($rs_result);
        if(count($rs_result) > 0){
            $counter = 1;

            foreach ($rs_result as $rs_row){
                if($counter == 1){
                    $backup_content .= "INSERT INTO `$table_name` VALUES (";    
                }else{
                    $backup_content .= ",\n(";    
                }

                $first_col = 1; 
                foreach ($rs_row as $value){
                    if($first_col == 1){
                        $first_col = 0;    
                    }else{
                        $backup_content .= ",";   
                    }

                    if(is_null($value)){
                        $backup_content .= "null";    
                    }else{
                        $c_text = str_replace("'", "''", $value);
                        $backup_content .= "'$c_text'";    
                    }
                }
                $backup_content .= ")";

                $counter++;
                if($counter == 500){
                    $counter = 1;
                    $backup_content .= ";\n";
                }

            }
            if($counter != 1){
                $backup_content .= ";\n";
            }

        }
        
        return $backup_content;   
    }
}
