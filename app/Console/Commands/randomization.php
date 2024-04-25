<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use App\Helper\MODutyFuncs;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class randomization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'randomization:start {district_id} {phase_id} {randomization_no} {randomization_for}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'randomization process ';

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
        $randomization_for = $this->argument('randomization_for'); 

        if($randomization_for == 1){
          if($randomization_no == 1){
              $this->doFirstRandomization($d_id, $phase_id, $randomization_no);    
          }else{
              $this->doSecondRandomization($d_id, $phase_id);
          }
        }elseif($randomization_for == 2){
          if($randomization_no == 1){
              $this->doMOFirstRandomization($d_id, $phase_id, $randomization_no);    
          }else{
              // $this->doSecondRandomization($d_id, $phase_id);
          }
        }
          
        
    }


  public function doMOFirstRandomization($d_id, $phase_id, $randomization_no)
  {
    $admin=Auth::guard('admin')->user();
    $user_id = $admin->id;

    $state_id = 0;      
    $from_ip = "a";

    $randomization_type = 2;
    $l_remarks = MyFuncs::checkRandomStatus($d_id, $phase_id, $randomization_no, $randomization_type);
    if($l_remarks != ""){
      return $l_remarks;
    }

    $rs_fetch = DB::select(DB::raw("SELECT `state_id` from `districts` where `id` = $d_id limit 1;"));
    $state_id = $rs_fetch[0]->state_id;

    //Clearing Previous Randomization Data if any
    $rs_save=DB::select(DB::raw("call `up_clear_first_micro_randomization`($d_id, $phase_id);"));

    //Randomization Started
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Started', 0, 0, 0);"));      
    $rs_save=DB::select(DB::raw("INSERT into `randomization_status` (`state_id`, `district_id`, `phase_id`, `randomization_no`, `status`, `start_time`, `updated_on`, `updated_by`, `updated_ip`, `randomization_type`) values ($state_id, $d_id, $phase_id, $randomization_no, 1, now(), now(), $user_id, '$from_ip', $randomization_type);"));

    //Blank Parties Created
    $reserve = 10;
    $rs_fetch = DB::select(DB::raw("SELECT `reserve_in_percent` from `randomization_setting` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch)>0){
      $reserve = $rs_fetch[0]->reserve_in_percent;
    }
    $rs_save = DB::select(DB::raw("call `up_create_mo_parties_blank`($d_id, $phase_id, $reserve);"));

    
    //MO Randomization
    $rs_save = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Micro Observer Randomization Started', 0, 0, 0);"));    
    $previous_complete = 0;
    $random_for_officer = "Micro Observer";
    
    //Prepare PO Data
    $eligibility = 7;
    $po_number = 1;
    $result = MyFuncs::prepare_POData($d_id, $phase_id, $randomization_no, $eligibility, $po_number, 0, 0);
    $gender_id = $result;
    $only_female = 0;
    if($result == 2){
      $only_female = 1;
    }

    //Preparing MO Parties
    $l_print_order = 1;
    $is_extra_reserve = 0;
    MODutyFuncs::prepare_MO_Polling_parties($d_id, $phase_id, $eligibility, $l_print_order, $previous_complete, $only_female, $gender_id, $random_for_officer, $is_extra_reserve);

    $rs_save=DB::select(DB::raw("DELETE From `po_data` where `duty_alloted` = 0 and `district_id` = $d_id;"));

    

    //Prepareing Check List----------
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Preparing Check List', 0, 0, 0);"));
    $this->prepare_check_list_MO_first($d_id, $phase_id);
    
    //Set Seat Sr. No.
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Updating Seat Sr. No.', 0, 0, 0);"));
    $rs_save=DB::select(DB::raw("call `up_set_mo_sr_no_first`($d_id, $phase_id, 0);"));

    //Completing The Process
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Completed', 1, 0, 100);"));      
    $rs_save=DB::select(DB::raw("UPDATE `randomization_status` set `status` = 2, `finish_time` = now() where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `randomization_type` = $randomization_type limit 1;"));

    return "";
  }

  public function prepare_check_list_MO_first($d_id, $phase_id)
  { 
    $rs_block = DB::select(DB::raw("SELECT `id`, `name_e`, `code` from `blocks_mcs` where `districts_id` = $d_id and `phase_no` = $phase_id;"));
    
    foreach ($rs_block as $key => $val_block){
      $block_id = $val_block->id;
      $block_name = $val_block->code.' - '.$val_block->name_e;

      $rs_fetch = DB::select(DB::raw("SELECT `name_e` from `districts` where `id` = $d_id limit 1;"));
      $dist_name = $rs_fetch[0]->name_e;

      $election_name = MyFuncs::election_name();
      $elect_type = MyFuncs::elect_type();
             

      $path=Storage_path('fonts/');
      $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
      $fontDirs = $defaultConfig['fontDir']; 
      $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
      $fontData = $defaultFontConfig['fontdata']; 
      
      $mpdf_check_list = new \Mpdf\Mpdf([
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

      $rs_fetch = DB::select(DB::raw("SELECT `pp`.`mo_no`, `pp`.`po1_id`, `pp`.`po1_name`, `pp`.`po1_dpt`, `pp`.`po1_off`, `pp`.`po1_desig`, `pp`.`po1_mobile`, `pp`.`po1_whatsapp` from `mo_buildings_detail` `pp` where `pp`.`block_id` = $block_id order by  `pp`.`mo_no`;"));
      
      $html_check_list = view('admin.report.mofirstrandomizationReport.checklist',compact('rs_fetch', 'election_name', 'block_name', 'elect_type'));
      
      
      $mpdf_check_list->WriteHTML($html_check_list);
      
      $documentUrl = Storage_path() . '/app/moreport/'.$d_id.'/'.$phase_id.'/first';  
      @mkdir($documentUrl, 0755, true);  
      
      $mpdf_check_list->Output($documentUrl.'/check_list_'.$block_id.'.pdf', 'F');    
    }
              
  }

  public function doFirstRandomization($d_id, $phase_id, $randomization_no)
  {
    $admin=Auth::guard('admin')->user();
    $user_id = $admin->id;

    $state_id = 0;      
    $from_ip = "a";

    $randomization_type = 1;
    $l_remarks = MyFuncs::checkRandomStatus($d_id, $phase_id, $randomization_no, $randomization_type);
    if($l_remarks != ""){
      return $l_remarks;
    }

    $rs_fetch = DB::select(DB::raw("SELECT `state_id` from `districts` where `id` = $d_id limit 1;"));
    $state_id = $rs_fetch[0]->state_id;

    //Clearing Previous Randomization Data if any
    $rs_save=DB::select(DB::raw("call `up_clear_first_randomization`($d_id, $phase_id);"));

    //Randomization Started
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Started', 0, 0, 0);"));      
    $rs_save=DB::select(DB::raw("INSERT into `randomization_status` (`state_id`, `district_id`, `phase_id`, `randomization_no`, `status`, `start_time`, `updated_on`, `updated_by`, `updated_ip`, `randomization_type`) values ($state_id, $d_id, $phase_id, $randomization_no, 1, now(), now(), $user_id, '$from_ip', $randomization_type);"));

    //Blank Parties Created
    $reserve = 10;
    $rs_fetch = DB::select(DB::raw("SELECT `reserve_in_percent` from `randomization_setting` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch)>0){
      $reserve = $rs_fetch[0]->reserve_in_percent;
    }
    $rs_save = DB::select(DB::raw("call `up_create_polling_parties_blank`($d_id, $phase_id, $reserve);"));

    
    $counter = 1;
    while($counter <= 2){
      if($counter == 1){
        //Presiding Randomization
        $rs_save = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Presiding Officer Randomization Started', 0, 0, 0);"));    
        $previous_complete = 0;
        $random_for_officer = "Presiding Officer";
      } else{
        //APO Randomization
        $rs_save = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'APO Randomization Started', 0, 0, 0);"));
        $previous_complete = 1;
        $random_for_officer = "APO";
      }

      //Prepare PO Data
      $eligibility = $counter;
      $po_number = 0;
      $result = MyFuncs::prepare_POData($d_id, $phase_id, $randomization_no, $eligibility, $po_number, 0, 0);
      $gender_id = $result;
      $only_female = 0;
      if($result == 2){
        $only_female = 1;
      }

      //Preparing Polling Parties
      $l_print_order = $counter;
      $this->prepare_Polling_parties_first($d_id, $phase_id, $eligibility, $l_print_order, $previous_complete, $only_female, $gender_id, $random_for_officer);

      // if($l_print_order == 1){
        $rs_save=DB::select(DB::raw("DELETE From `po_data` where `duty_alloted` = 0 and `district_id` = $d_id;"));  
      // }
      

      $counter++;
    }
    

    $rs_parties = DB::select(DB::raw("INSERT into `polling_parties_first_random` (`state_id`, `district_id`, `block_id`, `phase_id`, `party_no`, `po1_id`, `po1_name`, `po1_dpt`, `po1_off`, `po1_desig`, `po1_mobile`, `po1_whatsapp`, `po2_id`, `po2_name`, `po2_dpt`, `po2_off`, `po2_desig`, `po2_mobile`, `po2_whatsapp`) select `state_id`, `district_id`, `block_id`, `phase_id`, `party_no`, `po1_id`, `po1_name`, `po1_dpt`, `po1_off`, `po1_desig`, `po1_mobile`, `po1_whatsapp`, `po2_id`, `po2_name`, `po2_dpt`, `po2_off`, `po2_desig`, `po2_mobile`, `po2_whatsapp` from `polling_parties` where `district_id` = $d_id and `phase_id` = $phase_id;")); 

    //Prepareing Check List----------
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Preparing Check List', 0, 0, 0);"));
    $this->prepare_check_list_first($d_id, $phase_id);
    
    //Set Seat Sr. No.
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Updating Seat Sr. No.', 0, 0, 0);"));
    $rs_save=DB::select(DB::raw("call `up_set_sr_no_first`($d_id, $phase_id, 0);"));
    $rs_save=DB::select(DB::raw("call `up_set_sr_no_first`($d_id, $phase_id, 2);"));

    //Completing The Process
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Completed', 1, 0, 100);"));      
    $rs_save=DB::select(DB::raw("UPDATE `randomization_status` set `status` = 2, `finish_time` = now() where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `randomization_type` = $randomization_type limit 1;"));

    return "";
  }

  

  public function prepare_Polling_parties_first($d_id, $phase_id, $eligibility, $l_print_order, $previous_complete, $only_female, $gender_id, $random_for_officer)
  {
    
    $h_ac = 1; $p_ac = 1; $n_ac = 1; $c_dept = 1; $c_off = 1; $max_female = 0;
    $rs_fetch = DB::select(DB::raw("SELECT * from `randomization_setting` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch)>0){
      $h_ac = $rs_fetch[0]->h_ac; $p_ac = $rs_fetch[0]->p_ac; $n_ac = $rs_fetch[0]->n_ac; 
      $c_dept = $rs_fetch[0]->department_2; $c_off = $rs_fetch[0]->office_2; $max_female = $rs_fetch[0]->max_female;
    }

    $l_counter = 1;
    
    $condition = "";
    if($l_print_order >= 3){
      $condition = " and `pp`.`party_count` >= ".$l_print_order;
    }
    $rs_parties = DB::select(DB::raw("SELECT `pp`.`id`, `pp`.`block_id`, `pp`.`party_no`, `pp`.`party_count` From `polling_parties`  `pp` inner join `blocks_mcs` `bl` on `bl`.`id` = `pp`.`block_id` Where `pp`.`district_id` = $d_id and `pp`.`phase_id` = $phase_id $condition order by `bl`.`randamization_priority`, `pp`.`party_no`;"));
    $l_total_party = count($rs_parties);
    

    $condition = " where `district_id` = ".$d_id." and `phase_id` = ".$phase_id." and `duty_alloted` = 0 ";
    $condition_except_assembly = $condition." and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";

      
    foreach ($rs_parties as $key => $val_parties) {
      $l_party_complete = 0;
      $l_emp_id = 0;
      $l_block_id = $val_parties->block_id;
      $l_pp_id = $val_parties->id;
      
      $all_condition = $condition." and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 and `uf_assembly_condition`(`id`, `gender_id`, `h_ac`, `n_ac`, `p_ac`, $l_block_id, $h_ac, $n_ac, $p_ac) > 0 ";
      $condition_except_assembly = $condition." and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";
      
      if($l_print_order > 1){
        if($c_off > 0){
          $all_condition = $all_condition." and `office_id` not in (select distinct `off_id` from `deployed_data` where `party_id` = ".$l_pp_id.") ";
      
          $condition_except_assembly = $condition_except_assembly." and `office_id` not in (select distinct `off_id` from `deployed_data` where `party_id` = ".$l_pp_id.") ";
        }

        if($gender_id != 1 && $gender_id !=2){
          if($max_female > 0){
            $all_condition = $all_condition." and `uf_max_female_condition`($gender_id, $l_pp_id, $max_female) >= 1 ";
        
            $condition_except_assembly = $condition_except_assembly." and `uf_max_female_condition`($gender_id, $l_pp_id, $max_female) >= 1 ";
          }
        }
      }

      if($l_print_order > 2){
        if($c_dept > 0){
          $all_condition = $all_condition." and `department_id` not in (select `dept_id` from `deployed_data` where `party_id` = ".$l_pp_id." group by `dept_id` having count(*) >= 2) ";
      
          $condition_except_assembly = $condition_except_assembly." and `department_id` not in (select `dept_id` from `deployed_data` where `party_id` = ".$l_pp_id." group by `dept_id` having count(*) >= 2) ";
        }
      }

      
      // if($l_print_order == 5){
      //   $rs_fetch = DB::select(DB::raw("INSERT into `temp_query` (`query_text`) values ('$all_condition');")); 
      // }

      $l_emp_id = MyFuncs::find_suitable_emp($all_condition, $l_pp_id, $phase_id);
      if($l_emp_id > 0){
        $l_party_complete = 1;
        $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order, $only_female);"));
      }

      // Relax Assembly Condition
      if($l_emp_id == 0){
        // $rs_fetch = DB::select(DB::raw("INSERT into `temp_query` (`query_text`) values ('$condition_except_assembly');"));
        // error_log($condition_except_assembly);
        // log.console($condition_except_assembly);
        // dd($condition_except_assembly);
        $l_emp_id = MyFuncs::find_suitable_emp($condition_except_assembly, $l_pp_id, $phase_id);
        if($l_emp_id > 0){
          $l_party_complete = 1;
          $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order, $only_female);"));
          
        }
      }
      
      //Updating Party Status and randomization Process------
      $rs_update = DB::select(DB::raw("UPDATE `polling_parties` set `completed_party` = $l_party_complete where `id` = $l_pp_id and `completed_party` = $previous_complete limit 1;"));
      
      $percent_processed = (int)($l_counter/$l_total_party*100);
      // $percent_processed = 0;
      $message = "Processed ".$random_for_officer." ".$l_counter." of ".$l_total_party;
      $rs_update = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
      
      $l_counter = $l_counter + 1;

    }

    
  }

  

  // public function find_suitable_emp_from_already_deployed($condition, $l_pp_id, $eligibility, $ignore_previous_duty, $phase_id, $l_block_id, $max_female)
  // {
  //   $l_emp_text = "";
  //   $l_emp_id = 0;
  //   $query = "select `id`, `block_id`, `party_id` from `po_data` ".$condition." and `party_id` <> ".$l_pp_id." order by `random_no`";
  //   $query = str_replace("and `duty_alloted` = 0", "and `duty_alloted` = 1", $query);
  //   $query = str_replace("and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0", "", $query);
  //   $rs_suitable_records = DB::select(DB::raw($query));
  //   foreach ($rs_suitable_records as $key => $val_suitable){
  //     $new_block = $val_suitable->block_id;
  //     $new_pp_id = $val_suitable->party_id;
  //     $deployed_emp_id = $val_suitable->id;
  //     if($new_block != $l_block_id){
  //       $new_condition = str_replace("and `h_ac` <> ".$l_block_id, "and `h_ac` <> ".$new_block, $condition);  
  //       $new_condition = str_replace("and `p_ac` <> ".$l_block_id, "and `p_ac` <> ".$new_block, $condition);  
  //       $new_condition = str_replace("and `n_ac` <> ".$l_block_id, "and `n_ac` <> ".$new_block, $condition);  
        
  //       $new_condition = str_replace("and `department_id` not in (select distinct `dept_id` from `deployed_data` where `party_id` = ".$l_pp_id.")", "and `department_id` not in (select distinct `dept_id` from `deployed_data` where `party_id` = ".$new_pp_id." and `emp_id` <> ".$deployed_emp_id.")", $condition);  
  //     }

  //     $str_pos = strpos($new_condition, "and `gender_id` = 1");
  //     $str_pos = (int)$str_pos;
  //     if($str_pos == 0){
  //       $rs_fetch = DB::select(DB::raw("select count(*) as `tcount` from `deployed_data` where `party_id` = $new_pp_id and `gender_id` > 1; "));
  //       if ($rs_fetch[0]->tcount >= $max_female) {
  //         $condition = $condition." and `gender_id` = 1";
  //       }
  //     }
  //     $l_emp_id = $this->find_suitable_emp($new_condition, $new_pp_id, $eligibility, $ignore_previous_duty, $phase_id);
  //     if($l_emp_id > 0){
  //       $l_emp_text = $deployed_emp_id."_".$l_emp_id;
  //       return $l_emp_text;
  //     }
  //   }

  //   return $l_emp_text;
  // }

  // public function setReplacedEmployees($phase_id, $new_emp_id, $new_pp_id, $new_print_order, $new_block_id, $replaced_old_emp)
  // { 
  //  try {
  //   $rs_fetch = DB::select(DB::raw("select `block_id`, `party_id`, `duty_order_no` from `deployed_data` where `emp_id` = replaced_old_emp and `phase_id` = phase_id limit 1;"));
  //   $old_pp_id = $rs_fetch[0]->party_id;
  //   $old_block = $rs_fetch[0]->block_id;
  //   $old_print_order = $rs_fetch[0]->duty_order_no;

  //   $rs_delete = DB::select(DB::raw("delete from `deployed_data` where `phase_id` = $phase_id and `emp_id` = $replaced_old_emp limit 1;"));
    
  //   $rs_update = DB::select(DB::raw("call `up_update_polling_party`($new_block_id, $phase_id, $new_pp_id, $replaced_old_emp, $new_print_order);"));
    
  //   $rs_update = DB::select(DB::raw("call `up_update_polling_party`($old_block, $phase_id, $old_pp_id, $new_emp_id, $old_print_order);"));
        
  //   } catch (Exception $e) {}
  // }




  //--------------Second Randomization --------------------------------------

  /////------Second Randomization Functions -----------------------
  public function doSecondRandomization($d_id, $phase_id)
  {
    $admin=Auth::guard('admin')->user();
    $user_id = $admin->id;

    $state_id = 0;      
    $randomization_no = 2;
    $from_ip = "a";

    $randomization_type = 1;
    $l_remarks = MyFuncs::checkRandomStatus($d_id, $phase_id, $randomization_no, $randomization_type);
    if($l_remarks != ""){
      return $l_remarks;
    }

    $rs_fetch = DB::select(DB::raw("SELECT `state_id` from `districts` where `id` = $d_id limit 1;"));
    $state_id = $rs_fetch[0]->state_id;

    //Clearing Previous Randomization Data if any
    $rs_save=DB::select(DB::raw("call `up_clear_second_randomization`($d_id, $phase_id);"));

    //Randomization Started
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Started', 0, 0, 0);"));      
    $rs_save=DB::select(DB::raw("INSERT into `randomization_status` (`state_id`, `district_id`, `phase_id`, `randomization_no`, `status`, `start_time`, `updated_on`, `updated_by`, `updated_ip`, `randomization_type`) values ($state_id, $d_id, $phase_id, $randomization_no, 1, now(), now(), $user_id, '$from_ip', $randomization_type);"));

    //Blank Parties Created
    $reserve = 10;
    $rs_fetch = DB::select(DB::raw("SELECT `reserve_in_percent` from `randomization_setting` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch)>0){
      $reserve = $rs_fetch[0]->reserve_in_percent;
    }
    $rs_save = DB::select(DB::raw("call `up_create_polling_parties_blank`($d_id, $phase_id, $reserve);"));

    $counter = 1;
    while($counter <= 2){
      if($counter == 1){
        //Presiding Randomization
        $rs_save = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Presiding Officer Randomization Started', 0, 0, 0);"));    
        $previous_complete = 0;
        $random_for_officer = "Presiding Officer";
      } else{
        //APO Randomization
        $rs_save = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'APO Randomization Started', 0, 0, 0);"));
        $previous_complete = 1;
        $random_for_officer = "APO";
      }

      //Prepare PO Data
      $eligibility = $counter;
      $po_number = 0;
      $gender_id = 1;
      $rs_fetch = DB::select(DB::raw("SELECT `pro_gender`, `apo_gender` from `randomization_setting` where `district_id` = $d_id limit 1;"));
      if(count($rs_fetch)>0){
        if($eligibility == 1){
          $gender_id = $rs_fetch[0]->pro_gender;
        }elseif($eligibility == 2){
          $gender_id = $rs_fetch[0]->apo_gender;
        }
      }
      $only_female = 0;
      if($gender_id == 2){
        $only_female = 1;
      }

      //Preparing Polling Parties
      $l_print_order = $counter;
      $this->prepare_Polling_parties_second($d_id, $phase_id, $eligibility, $l_print_order, $previous_complete, $only_female, $gender_id, $random_for_officer);

      $counter++;
    }


    //PO Randomization
    $counter = 3;
    while($counter <= 6){
      //APO Randomization
      $po_number = $counter - 2;
      $rs_save = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'PO ($po_number) Randomization Started', 0, 0, 0);"));
      $previous_complete = 1;
      $random_for_officer = "PO ".$po_number." - ";
      
      //Prepare PO Data
      $eligibility = 3;
      
      $result = MyFuncs::prepare_POData($d_id, $phase_id, $randomization_no, $eligibility, $po_number, 0, 0);
      $gender_id = $result;
      $only_female = 0;
      if($result == 2){
        $only_female = 1;
      }

      //Preparing Polling Parties
      $l_print_order = $counter;
      $this->prepare_Polling_parties_first($d_id, $phase_id, $eligibility, $l_print_order, $previous_complete, $only_female, $gender_id, $random_for_officer);

      $rs_save=DB::select(DB::raw("DELETE From `po_data` where `duty_alloted` = 0 and `district_id` = $d_id;"));

      $counter++;
    }

    //Assigining first Assembly to extra reserve of 1st randomization
    $block_id = 0;
    $rs_block = DB::select(DB::raw("SELECT `id` from `blocks_mcs` where `districts_id` = $d_id and `phase_no` = $phase_id order by `randamization_priority` limit 1;"));
    if (count($rs_block) > 0){
      $block_id = $rs_block[0]->id;
    }
    $rs_update = DB::select(DB::raw("UPDATE `po_data` set `block_id` = $block_id, `is_extra_reserve_2` = 1 where `duty_alloted` = 1 and `district_id` = $d_id and `block_id` = 0;"));


    //Mannual Party Formed
    $result = $this->Assign_Party_No_Mannual($d_id, $phase_id);


    //Assigining Seat Sr.No.
    $rs_block = DB::select(DB::raw("SELECT `id` from `blocks_mcs` where `districts_id` = $d_id and `phase_no` = $phase_id;"));
    foreach ($rs_block as $key => $val_block) {
      $block_id = $val_block->id;
      $rs_update = DB::select(DB::raw("call `up_set_sr_no_second`($d_id, $phase_id, $block_id, 0);"));
      $rs_update = DB::select(DB::raw("call `up_set_sr_no_second`($d_id, $phase_id, $block_id, 2);"));
      $rs_update = DB::select(DB::raw("call `up_set_sr_no_second`($d_id, $phase_id, $block_id, 1);"));
    }


    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Preparing Check List', 0, 0, 0);"));
    $this->prepare_check_list_second($d_id, $phase_id);


    //Completing The Process
    $rs_save=DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, 'Completed', 1, 0, 100);"));      
    $rs_save=DB::select(DB::raw("UPDATE `randomization_status` set `status` = 2, `finish_time` = now() where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no and `randomization_type` = $randomization_type limit 1;"));

    // return "";
  }

  public function prepare_Polling_parties_second($d_id, $phase_id, $eligibility, $l_print_order, $previous_complete, $only_female, $gender_id, $random_for_officer)
  {
    $h_ac = 1; $p_ac = 1; $n_ac = 1; $c_dept = 1; $c_off = 1; $max_female = 0;
    $rs_fetch = DB::select(DB::raw("SELECT * from `randomization_setting` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch)>0){
      $h_ac = $rs_fetch[0]->h_ac; $p_ac = $rs_fetch[0]->p_ac; $n_ac = $rs_fetch[0]->n_ac; 
      $c_dept = $rs_fetch[0]->department_2; $c_off = $rs_fetch[0]->office_2; $max_female = $rs_fetch[0]->max_female;
    }

    $l_counter = 1;

    $rs_parties = DB::select(DB::raw("SELECT `pp`.`id`, `pp`.`block_id`, `pp`.`party_no`, `pp`.`party_count` From `polling_parties`  `pp` inner join `blocks_mcs` `bl` on `bl`.`id` = `pp`.`block_id` Where `pp`.`district_id` = $d_id and `pp`.`phase_id` = $phase_id order by `bl`.`randamization_priority`, `pp`.`party_no`;"));
    $l_total_party = count($rs_parties);

    $condition = " where `district_id` = ".$d_id." and `phase_id` = ".$phase_id." and `block_id` = 0 and `eligibility` = ".$eligibility;
    $condition_except_assembly = $condition." and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";

    foreach ($rs_parties as $key => $val_parties) {
      $rs_fetch = DB::select(DB::raw("SELECT * from `randomization_kill` where `district_id` = $d_id and `is_kill` = 1 limit 1;"));
      if(count($rs_fetch)>0){
        $percent_processed = 100;
        $message = "Processing Stopped";
        $rs_update = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
        $rs_delete = DB::select(DB::raw("DELETE from `randomization_kill` where `district_id` = $d_id limit 1;"));
        return;
      }
      
      $l_party_complete = 0;
      $l_emp_id = 0;
      $l_block_id = $val_parties->block_id;
      $l_pp_id = $val_parties->id;

      $all_condition = $condition." and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 and `uf_assembly_condition`(`id`, `gender_id`, `h_ac`, `n_ac`, `p_ac`, $l_block_id, $h_ac, $n_ac, $p_ac) > 0 ";
      $condition_except_assembly = $condition." and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";
      
      if($l_print_order > 1){
        if($c_off > 0){
          $all_condition = $all_condition." and `office_id` not in (select distinct `off_id` from `deployed_data` where `party_id` = ".$l_pp_id.") ";
      
          $condition_except_assembly = $condition_except_assembly." and `office_id` not in (select distinct `off_id` from `deployed_data` where `party_id` = ".$l_pp_id.") ";
        }

        if($gender_id != 1 && $gender_id !=2){
          if($max_female > 0){
            $all_condition = $all_condition." and `uf_max_female_condition`($gender_id, $l_pp_id, $max_female) >= 1 ";
        
            $condition_except_assembly = $condition_except_assembly." and `uf_max_female_condition`($gender_id, $l_pp_id, $max_female) >= 1 ";
          }
        }
      }

      if($l_print_order > 2){
        if($c_dept > 0){
          $all_condition = $all_condition." and `department_id` not in (select `dept_id` from `deployed_data` where `party_id` = ".$l_pp_id." group by `dept_id` having count(*) >= 2) ";
      
          $condition_except_assembly = $condition_except_assembly." and `department_id` not in (select `dept_id` from `deployed_data` where `party_id` = ".$l_pp_id." group by `dept_id` having count(*) >= 2) ";
        }
      }

      $l_emp_id = MyFuncs::find_suitable_emp($all_condition, $l_pp_id, $phase_id);
      if($l_emp_id > 0){
        $l_party_complete = 1;
        $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order, $only_female);"));
      }

      // Relax Assembly Condition
      if($l_emp_id == 0){
        $l_emp_id = MyFuncs::find_suitable_emp($condition_except_assembly, $l_pp_id, $phase_id);
        if($l_emp_id > 0){
          $l_party_complete = 1;
          $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order, $only_female);"));
        }
      }
      
      //Updating Party Status and randomization Process------
      $rs_update = DB::select(DB::raw("UPDATE `polling_parties` set `completed_party` = $l_party_complete where `id` = $l_pp_id and `completed_party` = $previous_complete limit 1;"));
      
      $percent_processed = (int)($l_counter/$l_total_party*100);
      // $percent_processed = 0;
      $message = "Processed ".$random_for_officer." ".$l_counter." of ".$l_total_party;
      $rs_update = DB::select(DB::raw("INSERT into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
      
      $l_counter = $l_counter + 1;

    }
    
  }


  // public function find_suitable_emp_second_from_already_deployed($condition, $l_pp_id, $eligibility, $ignore_previous_duty, $phase_id, $l_block_id, $max_female)
  // {
  //   $l_emp_text = "";
  //   $l_emp_id = 0;
  //   $query = "select `id`, `block_id`, `party_id` from `po_data` ".$condition." and `party_id` <> ".$l_pp_id." order by `random_no`";
  //   $query = str_replace("and `block_id` = 0", "and `block_id` > 0", $query);
  //   $rs_suitable_records = DB::select(DB::raw($query));
  //   foreach ($rs_suitable_records as $key => $val_suitable){
  //     $new_block = $val_suitable->block_id;
  //     $new_pp_id = $val_suitable->party_id;
  //     $deployed_emp_id = $val_suitable->id;
  //     $new_condition = $condition;
  //     if($new_block != $l_block_id){
  //       $new_condition = str_replace("and `h_ac` <> ".$l_block_id, "and `h_ac` <> ".$new_block, $condition);  
  //       $new_condition = str_replace("and `p_ac` <> ".$l_block_id, "and `p_ac` <> ".$new_block, $condition);  
  //       $new_condition = str_replace("and `n_ac` <> ".$l_block_id, "and `n_ac` <> ".$new_block, $condition);  
        
  //       $new_condition = str_replace("and `department_id` not in (select distinct `dept_id` from `deployed_data` where `party_id` = ".$l_pp_id.")", "and `department_id` not in (select distinct `dept_id` from `deployed_data` where `party_id` = ".$new_pp_id." and `emp_id` <> ".$deployed_emp_id.")", $condition);  
  //     }

  //     $str_pos = strpos($new_condition, "and `gender_id` = 1");
  //     $str_pos = (int)$str_pos;
  //     if($str_pos == 0){
  //       $rs_fetch = DB::select(DB::raw("select count(*) as `tcount` from `deployed_data` where `party_id` = $new_pp_id and `gender_id` > 1; "));
  //       if ($rs_fetch[0]->tcount >= $max_female) {
  //         $new_condition = $new_condition." and `gender_id` = 1";
  //       }
  //     }

  //     $l_emp_id = $this->find_suitable_emp($new_condition, $new_pp_id, $eligibility, $ignore_previous_duty, $phase_id);
  //     if($l_emp_id > 0){
  //       $l_emp_text = $deployed_emp_id."_".$l_emp_id;
  //       return $l_emp_text;
  //     }
  //   }

  //   return $l_emp_text;
  // }


  public function prepare_check_list_first($d_id, $phase_id)
  { 
    $rs_block = DB::select(DB::raw("SELECT `id`, `name_e`, `code` from `blocks_mcs` where `districts_id` = $d_id and `phase_no` = $phase_id;"));
    
    foreach ($rs_block as $key => $val_block){
      $block_id = $val_block->id;
      $block_name = $val_block->code.' - '.$val_block->name_e;

      $rs_fetch = DB::select(DB::raw("SELECT `name_e` from `districts` where `id` = $d_id limit 1;"));
      $dist_name = $rs_fetch[0]->name_e;

      $election_name = MyFuncs::election_name();
      $elect_type = MyFuncs::elect_type();
             

      $path=Storage_path('fonts/');
      $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
      $fontDirs = $defaultConfig['fontDir']; 
      $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
      $fontData = $defaultFontConfig['fontdata']; 
      
      $mpdf_check_list = new \Mpdf\Mpdf([
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

      $rs_fetch = DB::select(DB::raw("SELECT `pp`.`party_no`, `pp`.`po1_id`, `pp`.`po1_name`, `pp`.`po1_dpt`, `pp`.`po1_off`, `pp`.`po1_desig`, `pp`.`po1_mobile`, `pp`.`po1_whatsapp`, `pp`.`po2_id`, `pp`.`po2_name`, `pp`.`po2_dpt`, `pp`.`po2_off`, `pp`.`po2_desig`, `pp`.`po2_mobile`, `pp`.`po2_whatsapp` from `polling_parties` `pp` inner join `blocks_mcs` `bl` on `bl`.`id` = `pp`.`block_id` where `pp`.`block_id` = $block_id order by `pp`.`party_no`;"));
      
      $html_check_list = view('admin.report.firstrandomizationReport.checklist',compact('rs_fetch', 'election_name', 'block_name', 'elect_type'));
      
      
      $mpdf_check_list->WriteHTML($html_check_list);
      
      $documentUrl = Storage_path() . '/app/report/'.$d_id.'/'.$phase_id.'/first';  
      @mkdir($documentUrl, 0755, true);  
      
      $mpdf_check_list->Output($documentUrl.'/check_list_'.$block_id.'.pdf', 'F');    
    }
              
  }

  public function prepare_check_list_second($d_id, $phase_id)
  { 
    $rs_fetch = DB::select(DB::raw("SELECT `name_e` from `districts` where `id` = $d_id limit 1;"));
    $dist_name = $rs_fetch[0]->name_e;

    $election_name = MyFuncs::election_name();
    $elect_type = MyFuncs::elect_type();
    
    $rs_block = DB::select(DB::raw("SELECT * from `blocks_mcs` where `districts_id` = $d_id and `phase_no` = $phase_id;"));       
    foreach ($rs_block as $key => $val_block){
      $path=Storage_path('fonts/');
      $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
      $fontDirs = $defaultConfig['fontDir']; 
      $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
      $fontData = $defaultFontConfig['fontdata']; 
      
      $mpdf_check_list = new \Mpdf\Mpdf([
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

      $block_name = $val_block->code.' - '.$val_block->name_e;

      $rs_fetch = DB::select(DB::raw("SELECT ifnull(max(`pb`.`po_count`),0) as `max_po` from `polling_booths` `pb` where `pb`.`blocks_id` = $val_block->id;"));
      $max_po_count = $rs_fetch[0]->max_po;

      $rs_fetch = DB::select(DB::raw("select `pp`.`party_no`, `pp`.`po1_id`, `pp`.`po1_name`, `pp`.`po1_dpt`, `pp`.`po1_off`, `pp`.`po1_desig`, `pp`.`po1_mobile`, `pp`.`po1_whatsapp`, `pp`.`po2_id`, `pp`.`po2_name`, `pp`.`po2_dpt`, `pp`.`po2_off`, `pp`.`po2_desig`, `pp`.`po2_mobile`, `pp`.`po2_whatsapp`, `pp`.`po3_id`, `pp`.`po3_name`, `pp`.`po3_dpt`, `pp`.`po3_off`, `pp`.`po3_desig`, `pp`.`po3_mobile`, `pp`.`po3_whatsapp`, `pp`.`po4_id`, `pp`.`po4_name`, `pp`.`po4_dpt`, `pp`.`po4_off`, `pp`.`po4_desig`, `pp`.`po4_mobile`, `pp`.`po4_whatsapp`, `pp`.`po5_id`, `pp`.`po5_name`, `pp`.`po5_dpt`, `pp`.`po5_off`, `pp`.`po5_desig`, `pp`.`po5_mobile`, `pp`.`po5_whatsapp`, `pp`.`po6_id`, `pp`.`po6_name`, `pp`.`po6_dpt`, `pp`.`po6_off`, `pp`.`po6_desig`, `pp`.`po6_mobile`, `pp`.`po6_whatsapp` from `polling_parties` `pp` where `pp`.`block_id` = $val_block->id and `pp`.`phase_id` = $phase_id order by  `pp`.`party_no`;"));
      
      
      $html_check_list = view('admin.report.firstrandomizationReport.checklist_second',compact('rs_fetch', 'election_name', 'dist_name', 'elect_type', 'max_po_count', 'block_name'));
      
      
      $mpdf_check_list->WriteHTML($html_check_list);
      
      $documentUrl = Storage_path() . '/app/report/'.$d_id.'/'.$phase_id.'/second';  
      @mkdir($documentUrl, 0755, true);  
      
      $mpdf_check_list->Output($documentUrl.'/check_list_'.$val_block->id.'.pdf', 'F');    
    }
  }




  //Manuual Duty Polling Party Formed
  public function prepare_Polling_parties_second_mannual($d_id, $phase_id, $eligibility, $l_print_order)
  {
    
    $only_female = 0;
    $rs_parties = DB::select(DB::raw("SELECT `pp`.`id`, `pp`.`block_id`, `pp`.`party_no`, `pp`.`final_booth_id` From `polling_parties_mannual`  `pp` inner join `blocks_mcs` `bl` on `bl`.`id` = `pp`.`block_id` Where `pp`.`district_id` = $d_id and `pp`.`phase_id` = $phase_id order by `pp`.`party_no`;"));
    
    $condition = " where `district_id` = ".$d_id." and `phase_id` = ".$phase_id." and `eligibility` = ".$eligibility;
    
    foreach ($rs_parties as $key => $val_parties) {
      $l_emp_id = 0;
      $l_block_id = $val_parties->block_id;
      $l_pp_id = $val_parties->id;
      $booth_id = $val_parties->final_booth_id;

      $all_condition = $condition." and `booth_id` = $booth_id and `party_id` = 0 ";
      
      $l_emp_id = MyFuncs::find_suitable_emp($all_condition, $l_pp_id, $phase_id);
      if($l_emp_id > 0){
        $rs_fetch = DB::select(DB::raw("call `up_update_mannual_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order);"));
      }

    }
    
  }

  public function Assign_Party_No_Mannual($d_id, $phase_id)
  {
    $rs_save = DB::select(DB::raw("call `up_create_mannual_polling_parties_blank`($d_id, $phase_id);"));

    $counter = 1;
    while($counter <= 2){
      
      $eligibility = $counter;
      $l_print_order = $counter;
      $this->prepare_Polling_parties_second_mannual($d_id, $phase_id, $eligibility, $l_print_order);

      $counter++;
    }

    $counter = 3;
    while($counter <= 6){
      
      $eligibility = 3;
      $l_print_order = $counter;
      $this->prepare_Polling_parties_second_mannual($d_id, $phase_id, $eligibility, $l_print_order);

      $counter++;
    }

  }

}



