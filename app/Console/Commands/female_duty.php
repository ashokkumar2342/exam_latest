<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class female_duty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'female_duty:start {district_id} {phase_id} {randomization_no}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'female_duty process ';

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
    public function handle()
    { 
        ini_set('max_execution_time', '7200');
        ini_set('memory_limit','999M');
        ini_set("pcre.backtrack_limit", "100000000");
      
        $d_id = $this->argument('district_id');
        $phase_id = $this->argument('phase_id');
        $randomization_no = $this->argument('randomization_no'); 

        // dd("ok");
        if($randomization_no == 1){
          $this->set_female_duty_first($d_id, $phase_id, $randomization_no);  
        }else{
          $this->set_female_duty_second($d_id, $phase_id, $randomization_no);  
        }
        
        
    }


  // public function set_female_duty_first($d_id, $phase_id, $randomization_no)
  // {
  //   $admin=Auth::guard('admin')->user();
  //   $user_id = $admin->id;

  //   $state_id = 0;      
  //   $from_ip = MyFuncs::getIp();;

  //   // $l_remarks = $this->checkRandomStatus($d_id, $phase_id, $randomization_no);
  //   // if($l_remarks != ""){
  //   //   return $l_remarks;
  //   // }

  //   $rs_fetch = DB::select(DB::raw("select `state_id` from `districts` where `id` = $d_id limit 1;"));
  //   $state_id = $rs_fetch[0]->state_id;

  //   //Clearing Previous Randomization Data if any
  //   $rs_save=DB::select(DB::raw("call `up_clear_female_duty_po_apo`($d_id, $phase_id);"));

  //   //Randomization Started
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Started', 0, 0, 0);"));      
  //   // $rs_save=DB::select(DB::raw("insert into `randomization_status` (`state_id`, `district_id`, `phase_id`, `randomization_no`, `status`, `start_time`, `updated_on`, `updated_by`, `updated_ip`) values ($state_id, $d_id, $phase_id, $randomization_no, 1, now(), now(), $user_id, '$from_ip');"));

  //   //Prepare PO Data
  //   $this->prepare_POData($d_id, $phase_id, $randomization_no);      
    
  //   //Preparing Polling Parties
  //   $this->prepare_female_duty_first($d_id, $phase_id);

  //   //Prepareing Check List----------
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Preparing Check List', 0, 0, 0);"));
  //   $this->prepare_check_list_first($d_id, $phase_id, $randomization_no);
    
  //   //Set Seat Sr. No.
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Updating Seat Sr. No.', 0, 0, 0);"));
  //   $rs_save=DB::select(DB::raw("call `up_set_female_sr_no_first`($d_id, $phase_id);"));

  //   //Completing The Process
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Completed', 1, 0, 100);"));      
  //   $rs_save=DB::select(DB::raw("update `randomization_status` set `status` = 2, `finish_time` = now() where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;
  //       "));

  //   return "";
  // }

  // // public function checkRandomStatus($d_id, $phase_id, $randomization_no)
  // // {
  // //   $l_remarks = "";
  // //   $rs_save=DB::select(DB::raw("select * from `randomization_status` where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;"));
  // //   if(count($rs_save) > 0){
  // //     if($rs_save[0]->status == 1){
  // //       $l_remarks = "Randomization already in process";
  // //     }elseif($rs_save[0]->status == 3){
  // //       $l_remarks = "Randomization is already locked";
  // //     }

  // //   }
  // //   return $l_remarks;
  // // }

  // public function prepare_POData($d_id, $phase_id, $randomization_no)
  // {
    
  //   $rs_fetch = DB::select(DB::raw("select `election_date` from `election_phases` where `id` = $phase_id limit 1;"));
  //   $elect_date = $rs_fetch[0]->election_date;
    
  //   $j_days = 180; $r_days = 180; $h_ac = 1; $p_ac = 1; $n_ac = 1; $c_dept = 1; $blo = 1; $f_pro = 1; $f_apo = 1;
  //   $f_po = 1; $c_off = 1;
  //   $rs_fetch = DB::select(DB::raw("select * from `randomization_setting` where `district_id` = $d_id limit 1;"));
  //   if(count($rs_fetch)>0){
  //     $j_days = $rs_fetch[0]->joining_days; $r_days = $rs_fetch[0]->retirement_days; $h_ac = $rs_fetch[0]->h_ac; 
  //     $p_ac = $rs_fetch[0]->p_ac; $n_ac = $rs_fetch[0]->n_ac; $c_dept = $rs_fetch[0]->department_2;
  //     $blo = $rs_fetch[0]->blo; $c_off = $rs_fetch[0]->office_2;
  //   }

  //   $condition = " where `emp`.`district_id` = ".$d_id." and `emp`.`handicapped_verified` = 0 and `emp`.`exempted` = 0 and `emp`.`status` = 1 and `emp`.`verified` = 1 and `dpt`.`exempted` = 0 and `off`.`exempted` = 0 and duty_".$phase_id." = 0 and `emp`.`gender_id` = 2 and `emp`.`child_pregnant` = 0 ";
   
  //   if($randomization_no == 1){
  //     $condition = $condition." and `emp`.`eligibility` in (1,2) ";  
  //   }else{
  //     $condition = $condition." and `emp`.`eligibility` = 3 "; 
  //   } 
    
  //   if ($blo == 1){
  //     $condition = $condition." and `emp`.`blo_duty` = 0 ";
  //   }
  //   if ($j_days > 0){
  //     $condition = $condition." and datediff('".$elect_date."', `emp`.`joining_date`) > ".$j_days;
  //   }
  //   if ($r_days > 0){
  //     $condition = $condition." and datediff(`emp`.`retirement_date`, '".$elect_date."') > ".$r_days;
  //   }
    

  //   $rs_save = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Collecting Employees Data for Randomization', 0, 0, 0);"));
  //   $query = "insert into `po_female_data` (`id`, `state_id`, `district_id`, `department_id`, `office_id`, `eligibility`, `gender_id`, `joining`, `retirement`, `recruitment_type`, `h_ac`, `p_ac`, `n_ac`, `random_no`, `phase_id`, `block_id`, `party_id`, `party_no`, `duty_alloted`, `seat_sr_no`, `print_order`, `booth_id`) select `emp`.`id`, `emp`.`state_id`, `emp`.`district_id`, `emp`.`department_id`, `emp`.`office_id`, `emp`.`eligibility`, `emp`.`gender_id`, 0, 0, `emp`.`recruitment_type`, `emp`.`h_ac`, `emp`.`p_ac`, `emp`.`n_ac`, rand()*1000000, $phase_id, 0, 0, 0, 0, 0, 0, 0 from `employee_details` `emp` inner join `departments` `dpt` on `dpt`.`id` = `emp`.`department_id` inner join `offices` `off` on `off`.`id` = `emp`.`office_id`".$condition;
  //   $rs_save = DB::select(DB::raw($query));

  // }

  
  // public function prepare_female_duty_first($d_id, $phase_id)
  // {
    
  //   $h_ac = 1; $p_ac = 1; $n_ac = 1; $c_dept = 1; $f_pro = 1; $f_apo = 1; $f_po = 1; $c_off = 1; $max_female = 0;
  //   $rs_fetch = DB::select(DB::raw("select * from `randomization_setting` where `district_id` = $d_id limit 1;"));
  //   if(count($rs_fetch)>0){
  //     $h_ac = $rs_fetch[0]->h_ac; $p_ac = $rs_fetch[0]->p_ac; $n_ac = $rs_fetch[0]->n_ac; 
  //     $c_dept = $rs_fetch[0]->department_2; $c_off = $rs_fetch[0]->office_2; $max_female = $rs_fetch[0]->max_female;
  //   }

  //   $rs_fetch = DB::select(DB::raw("select count(*) as `tcount` from `polling_booth_female` where `block_id` in (select `id` from `blocks_mcs` where `district_id` = $d_id and `phase_no` = $phase_id);"));
  //   $l_total_party = $rs_fetch[0]->tcount;
  //   $l_counter = 1;

  //   $rs_parties = DB::select(DB::raw("select `pbf`.`block_id`, `pbf`.`polling_booth_id`, `pbf`.`pro_count`, `pbf`.`apo_count`, `pbf`.`po_count`, `pb`.`ss_id` from `polling_booth_female` `pbf` inner join `polling_booths` `pb` on `pb`.`id` = `pbf`.`polling_booth_id` where `pbf`.`district_id` = $d_id and `pbf`.`block_id` in (select `id` from `blocks_mcs` where `district_id` = $d_id and `phase_no` = $phase_id);"));
  //   foreach ($rs_parties as $key => $val_parties) {
  //     $pro_female = $val_parties->pro_count;
  //     if ($pro_female > 0){
  //       $l_print_order = 1;
  //       $l_emp_id = 0;
  //       $l_block_id = $val_parties->block_id;
  //       $polling_booth_id = $val_parties->polling_booth_id;
  //       $eligibility = 1;
  //       $gender = 2;

  //       $condition = " where `district_id` = ".$d_id." and `eligibility` = ".$eligibility." and `phase_id` = ".$phase_id." and `duty_alloted` = 0 and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";

  //       $l_emp_id = $this->find_suitable_emp($condition, $eligibility, 0, $phase_id, $polling_booth_id, $d_id);
  //       if($l_emp_id > 0){
  //         $rs_fetch = DB::select(DB::raw("call `up_update_female_polling_party`($l_block_id, $phase_id, $l_emp_id, $l_print_order, $polling_booth_id);"));    
  //       }
  //     }

  //     $apo_female = $val_parties->apo_count;
  //     if ($apo_female > 0){
  //       $l_print_order = 2;
  //       $l_emp_id = 0;
  //       $l_block_id = $val_parties->block_id;
  //       $polling_booth_id = $val_parties->polling_booth_id;
  //       $eligibility = 2;
  //       $gender = 2;

  //       $condition = " where `district_id` = ".$d_id." and `eligibility` = ".$eligibility." and `phase_id` = ".$phase_id." and `duty_alloted` = 0 and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";

  //       $l_emp_id = $this->find_suitable_emp($condition, $eligibility, 0, $phase_id, $polling_booth_id, $d_id);
  //       if($l_emp_id > 0){
  //         $rs_fetch = DB::select(DB::raw("call `up_update_female_polling_party`($l_block_id, $phase_id, $l_emp_id, $l_print_order, $polling_booth_id);"));    
  //       }
  //     }

  //     $percent_processed = (int)($l_counter/$l_total_party*100);
  //     $message = "Processed ".$l_counter." of ".$l_total_party;
  //     $rs_update = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
      
  //     $l_counter = $l_counter + 1;

  //   }
  // }

  // public function find_suitable_emp($condition, $eligibility, $ignore_previous_duty, $phase_id, $booth_id, $d_id)
  // {
  //   $l_emp_id = 0;

  //   $rs_fetch = DB::select(DB::raw("select `ss_id` from `polling_booths` where `id` = $booth_id limit 1;"));
  //   $ss_id = 0;
  //   $ss_id = $rs_fetch[0]->ss_id;
  //   if($ss_id > 0){
  //     $condition = $condition." and `office_id` in (select `id` from `offices` where `district_id` = $d_id and `village_id` in (select distinct `village_id` from `polling_booths` where `ss_id` = $ss_id))";


  //     $query = "select `id` from `po_female_data` ".$condition." order by `random_no` limit 1;";
  //     if($ignore_previous_duty > 0){
  //       $query = str_replace("and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0", "", $query);
  //     }
  //     // dd($query);
  //     $rs_fetch = DB::select(DB::raw($query));
  //     if(count($rs_fetch)>0){
  //       return $rs_fetch[0]->id;
  //     }
  //   }

  //   return $l_emp_id;
  // }

  // public function prepare_check_list_first($d_id, $phase_id, $randomization_no)
  // { 
  //   $rs_block = DB::select(DB::raw("select `id`, `name_e`, `code` from `blocks_mcs` where `districts_id` = $d_id and `phase_no` = $phase_id;"));
    
  //   foreach ($rs_block as $key => $val_block){
  //     $block_id = $val_block->id;
  //     $block_name = $val_block->code.' - '.$val_block->name_e;

  //     $rs_fetch = DB::select(DB::raw("select `name_e` from `districts` where `id` = $d_id limit 1;"));
  //     $dist_name = $rs_fetch[0]->name_e;

  //     $election_name = MyFuncs::election_name();
  //     $elect_type = MyFuncs::elect_type();
             

  //     $path=Storage_path('fonts/');
  //     $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
  //     $fontDirs = $defaultConfig['fontDir']; 
  //     $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
  //     $fontData = $defaultFontConfig['fontdata']; 
      
  //     $mpdf_check_list = new \Mpdf\Mpdf([
  //         'fontDir' => array_merge($fontDirs, [
  //                  __DIR__ . $path,
  //              ]),
  //              'fontdata' => $fontData + [
  //                  'frutiger' => [
  //                      'R' => 'FreeSans.ttf',
  //                      'I' => 'FreeSansOblique.ttf',
  //                  ]
  //              ],
  //              'default_font' => 'freesans',
  //          ]);

  //     $rs_fetch = DB::select(DB::raw("select `emp`.`employee_name_e`, `dpt`.`department_name_e`, `off`.`office_name_e`, `dsg`.`designation_name_e`, `emp`.`id`, `emp`.`mobile_no`, `elg`.`eligibility_name_e` from `po_female_data` `pd` inner join `employee_details` `emp` on `emp`.`id` = `pd`.`id` inner join `departments` `dpt` on `dpt`.`id` = `pd`.`department_id` inner join `offices` `off` on `off`.`id` = `pd`.`office_id` inner join `designations` `dsg` on `dsg`.`id` = `emp`.`designation_id` inner join `eligibility` `elg` on `elg`.`id` = `pd`.`eligibility` where `pd`.`duty_alloted` = 1 and `pd`.`district_id` = $d_id and `pd`.`phase_id` = $phase_id and `pd`.`block_id` = $block_id;"));
      
  //     $html_check_list = view('admin.report.firstrandomizationReport.female_checklist',compact('rs_fetch', 'election_name', 'block_name', 'elect_type', 'randomization_no'));
      
      
  //     $mpdf_check_list->WriteHTML($html_check_list);
      
  //     if($randomization_no == 1){
  //       $documentUrl = Storage_path() . '/app/report/'.$d_id.'/'.$phase_id.'/first';  
  //     }else{
  //       $documentUrl = Storage_path() . '/app/report/'.$d_id.'/'.$phase_id.'/second';  
  //     }
      
  //     @mkdir($documentUrl, 0755, true);  
      
  //     $mpdf_check_list->Output($documentUrl.'/female_check_list_'.$block_id.'.pdf', 'F');    
  //   }
              
  // }


  // public function set_female_duty_second($d_id, $phase_id, $randomization_no)
  // {
  //   $admin=Auth::guard('admin')->user();
  //   $user_id = $admin->id;

  //   $state_id = 0;      
  //   $from_ip = MyFuncs::getIp();;
  //   // dd("ok");
  //   // $l_remarks = $this->checkRandomStatus($d_id, $phase_id, $randomization_no);
  //   // if($l_remarks != ""){
  //   //   return $l_remarks;
  //   // }

  //   $rs_fetch = DB::select(DB::raw("select `state_id` from `districts` where `id` = $d_id limit 1;"));
  //   $state_id = $rs_fetch[0]->state_id;

  //   //Clearing Previous Randomization Data if any
  //   $rs_save=DB::select(DB::raw("call `up_clear_female_duty_complete_party`($d_id, $phase_id);"));

  //   //Randomization Started
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Started', 0, 0, 0);"));      
  //   // $rs_save=DB::select(DB::raw("insert into `randomization_status` (`state_id`, `district_id`, `phase_id`, `randomization_no`, `status`, `start_time`, `updated_on`, `updated_by`, `updated_ip`) values ($state_id, $d_id, $phase_id, $randomization_no, 1, now(), now(), $user_id, '$from_ip');"));

  //   //Prepare PO Data
  //   $this->prepare_POData($d_id, $phase_id, $randomization_no);      
    
  //   //Preparing Polling Parties
  //   $this->prepare_female_duty_second($d_id, $phase_id);

  //   //Prepareing Check List----------
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Preparing Check List', 0, 0, 0);"));
  //   $this->prepare_check_list_first($d_id, $phase_id, $randomization_no);
    
  //   //Set Seat Sr. No.
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Updating Seat Sr. No.', 0, 0, 0);"));
  //   $rs_save=DB::select(DB::raw("call `up_set_female_sr_no_second`($d_id, $phase_id);"));

  //   //Completing The Process
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Completed', 1, 0, 100);"));      
  //   $rs_save=DB::select(DB::raw("update `randomization_status` set `status` = 2, `finish_time` = now() where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;
  //       "));

  //   return "";
  // }



  // public function prepare_female_duty_second($d_id, $phase_id)
  // {
  //   // dd("ok - 1");
  //   $h_ac = 1; $p_ac = 1; $n_ac = 1; $c_dept = 1; $f_pro = 1; $f_apo = 1; $f_po = 1; $c_off = 1; $max_female = 0;
  //   $rs_fetch = DB::select(DB::raw("select * from `randomization_setting` where `district_id` = $d_id limit 1;"));
  //   if(count($rs_fetch)>0){
  //     $h_ac = $rs_fetch[0]->h_ac; $p_ac = $rs_fetch[0]->p_ac; $n_ac = $rs_fetch[0]->n_ac; 
  //     $c_dept = $rs_fetch[0]->department_2; $c_off = $rs_fetch[0]->office_2; $max_female = $rs_fetch[0]->max_female;
  //   }

  //   $rs_fetch = DB::select(DB::raw("select count(*) as `tcount` from `polling_booth_female` where `block_id` in (select `id` from `blocks_mcs` where `district_id` = $d_id and `phase_no` = $phase_id);"));
  //   $l_total_party = $rs_fetch[0]->tcount;
  //   $l_counter = 1;

  //   $rs_parties = DB::select(DB::raw("select `pbf`.`block_id`, `pbf`.`polling_booth_id`, `pbf`.`pro_count`, `pbf`.`apo_count`, `pbf`.`po_count`, `pb`.`ss_id` from `polling_booth_female` `pbf` inner join `polling_booths` `pb` on `pb`.`id` = `pbf`.`polling_booth_id` where `pbf`.`district_id` = $d_id and `pbf`.`block_id` in (select `id` from `blocks_mcs` where `district_id` = $d_id and `phase_no` = $phase_id);"));
  //   foreach ($rs_parties as $key => $val_parties) {
  //     $l_block_id = $val_parties->block_id;
  //     $polling_booth_id = $val_parties->polling_booth_id;
  //     $po_female = $val_parties->po_count;
  //     $l_counter = 1;
  //     while ($l_counter<=$po_female){
        
  //       $l_print_order = 2 + $l_counter;
  //       $l_emp_id = 0;  
  //       $eligibility = 3;
  //       $gender = 2;
  //       $l_counter++;
  //       $condition = " where `district_id` = ".$d_id." and `eligibility` = ".$eligibility." and `phase_id` = ".$phase_id." and `duty_alloted` = 0 and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";

  //       $l_emp_id = $this->find_suitable_emp($condition, $eligibility, 0, $phase_id, $polling_booth_id, $d_id);
  //       if($l_emp_id > 0){
  //         $rs_fetch = DB::select(DB::raw("call `up_update_female_polling_party`($l_block_id, $phase_id, $l_emp_id, $l_print_order, $polling_booth_id);"));    
  //       }
  //     }
      
  //     $percent_processed = (int)($l_counter/$l_total_party*100);
  //     $message = "Processed ".$l_counter." of ".$l_total_party;
  //     $rs_update = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
      
  //     $l_counter = $l_counter + 1;

  //   }
  // }

  

    
}



