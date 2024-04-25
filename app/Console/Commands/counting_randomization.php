<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class counting_randomization extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'counting_randomization:start {district_id}';


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
        
        $this->doCountingRandomization($d_id);
        
        
    }


  // public function doFirstRandomization($d_id, $phase_id, $randomization_no)
  // {
  //   $admin=Auth::guard('admin')->user();
  //   $user_id = $admin->id;

  //   $state_id = 0;      
  //   $from_ip = "a";

  //   $l_remarks = $this->checkRandomStatus($d_id, $phase_id, $randomization_no);
  //   if($l_remarks != ""){
  //     return $l_remarks;
  //   }

  //   $rs_fetch = DB::select(DB::raw("select `state_id` from `districts` where `id` = $d_id limit 1;"));
  //   $state_id = $rs_fetch[0]->state_id;

  //   //Clearing Previous Randomization Data if any
  //   $rs_save=DB::select(DB::raw("call `up_clear_first_randomization`($d_id, $phase_id);"));

  //   //Randomization Started
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Started', 0, 0, 0);"));      
  //   $rs_save=DB::select(DB::raw("insert into `randomization_status` (`state_id`, `district_id`, `phase_id`, `randomization_no`, `status`, `start_time`, `updated_on`, `updated_by`, `updated_ip`) values ($state_id, $d_id, $phase_id, $randomization_no, 1, now(), now(), $user_id, '$from_ip');"));

  //   //Prepare PO Data
  //   $this->prepare_POData($d_id, $phase_id, $randomization_no);      

  //   //Blank Parties Created
  //   $reserve = 10;
  //   $rs_fetch = DB::select(DB::raw("select `reserve_in_percent` from `randomization_setting` where `district_id` = $d_id limit 1;"));    
  //   if(count($rs_fetch)>0){
  //     $reserve = $rs_fetch[0]->reserve_in_percent;
  //   }
  //   $rs_save = DB::select(DB::raw("call `up_create_polling_parties_blank`($d_id, $phase_id, $reserve);"));


  //   //Preparing Polling Parties
  //   $this->prepare_Polling_parties_first($d_id, $phase_id);

  //   //Prepareing Check List----------
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Preparing Check List', 0, 0, 0);"));
  //   $this->prepare_check_list_first($d_id, $phase_id);
    
  //   //Set Seat Sr. No.
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Updating Seat Sr. No.', 0, 0, 0);"));
  //   $rs_save=DB::select(DB::raw("call `up_set_sr_no_first`($d_id, $phase_id);"));

  //   //Completing The Process
  //   $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Completed', 1, 0, 100);"));      
  //   $rs_save=DB::select(DB::raw("update `randomization_status` set `status` = 2, `finish_time` = now() where `district_id` = $d_id and `phase_id` = $phase_id and `randomization_no` = $randomization_no limit 1;
  //       "));

  //   return "";
  // }

  public function checkRandomStatus($d_id)
  {
    $l_remarks = "";
    $rs_fetch = DB::select(DB::raw("SELECT * from `counting_randomization_status` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch) > 0){
      if($rs_fetch[0]->status == 1){
        $l_remarks = "Randomization already in process";
      }elseif($rs_fetch[0]->status == 3){
        $l_remarks = "Randomization is already locked";
      }
    }
    return $l_remarks;
  }

  public function prepare_COData($d_id)
  {
    
    $rs_fetch = DB::select(DB::raw("select `election_date` from `election_phases` where `id` = 1 limit 1;"));
    $elect_date = $rs_fetch[0]->election_date;
    
    $j_days = 180; $r_days = 180; $blo = 1; 
    $rs_fetch = DB::select(DB::raw("select * from `randomization_setting` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch)>0){
      $j_days = $rs_fetch[0]->joining_days; $r_days = $rs_fetch[0]->retirement_days; $blo = $rs_fetch[0]->blo;
    }

    $condition = " where `emp`.`district_id` = ".$d_id." and `emp`.`handicapped_verified` = 0 and `emp`.`exempted` = 0 and `emp`.`status` = 1 and `emp`.`verified` = 1 and `dpt`.`exempted` = 0 and `off`.`exempted` = 0 and `emp`.`gender_id` = 1 ";
   
    $condition = $condition." and `emp`.`count_elegibility` in (10, 11) ";  
    
    if ($blo == 1){
      $condition = $condition." and `emp`.`blo_duty` = 0 ";
    }
    if ($j_days > 0){
      $condition = $condition." and datediff('".$elect_date."', `emp`.`joining_date`) > ".$j_days;
    }
    if ($r_days > 0){
      $condition = $condition." and datediff(`emp`.`retirement_date`, '".$elect_date."') > ".$r_days;
    }
    

    $rs_save = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Collecting Employees Data for Randomization', 0, 0, 0);"));
    $query = "insert into `co_data` (`id`, `state_id`, `district_id`, `department_id`, `office_id`, `eligibility`, `gender_id`, `joining`, `retirement`, `recruitment_type`, `h_ac`, `p_ac`, `n_ac`, `random_no`, `block_id`, `party_id`, `party_no`, `duty_alloted`, `seat_sr_no`, `print_order`, `table_id`) select `emp`.`id`, `emp`.`state_id`, `emp`.`district_id`, `emp`.`department_id`, `emp`.`office_id`, `emp`.`count_elegibility`, `emp`.`gender_id`, 0, 0, `emp`.`recruitment_type`, `emp`.`h_ac`, `emp`.`p_ac`, `emp`.`n_ac`, rand()*1000000, 0, 0, 0, 0, 0, 0, 0 from `employee_details` `emp` inner join `departments` `dpt` on `dpt`.`id` = `emp`.`department_id` inner join `offices` `off` on `off`.`id` = `emp`.`office_id`".$condition;
    // dd($query);
    $rs_save = DB::select(DB::raw($query));

  }

  // public function prepare_Polling_parties_first($d_id, $phase_id)
  // {
    
  //   $h_ac = 1; $p_ac = 1; $n_ac = 1; $c_dept = 1; $f_pro = 1; $f_apo = 1; $f_po = 1; $c_off = 1; $max_female = 0;
  //   $rs_fetch = DB::select(DB::raw("select * from `randomization_setting` where `district_id` = $d_id limit 1;"));
  //   if(count($rs_fetch)>0){
  //     $h_ac = $rs_fetch[0]->h_ac; $p_ac = $rs_fetch[0]->p_ac; $n_ac = $rs_fetch[0]->n_ac; 
  //     $c_dept = $rs_fetch[0]->department_2; $c_off = $rs_fetch[0]->office_2; $max_female = $rs_fetch[0]->max_female;
  //     $f_pro = $rs_fetch[0]->pro_female; $f_apo = $rs_fetch[0]->apo_female; $f_po = $rs_fetch[0]->po_female;
  //   }

  //   $rs_fetch = DB::select(DB::raw("Select count(*) as `tcount` From `polling_parties` Where `district_id` = $d_id and `phase_id` = $phase_id;"));
  //   $l_total_party = $rs_fetch[0]->tcount;
  //   $l_counter = 1;

  //   $rs_parties = DB::select(DB::raw("Select `pp`.`id`, `pp`.`block_id`, `pp`.`party_no`, `pp`.`party_count` From `polling_parties`  `pp` inner join `blocks_mcs` `bl` on `bl`.`id` = `pp`.`block_id` Where `pp`.`district_id` = $d_id and `pp`.`phase_id` = $phase_id order by `bl`.`randamization_priority`, `pp`.`party_no`;"));
  //   foreach ($rs_parties as $key => $val_parties) {
  //     $l_party_complete = 1;
  //     $l_print_order = 1;
  //     $l_emp_id = 0;
  //     $l_block_id = $val_parties->block_id;
  //     $l_pp_id = $val_parties->id;
  //     $eligibility = 1;
  //     $gender = 1;
  //     $condition = " where `district_id` = ".$d_id." and `eligibility` = ".$eligibility." and `phase_id` = ".$phase_id." and `duty_alloted` = 0 and `uf_duty_exists_previous_phases`(`id`, ".$phase_id.") = 0 ";
  //     if ($h_ac == 1){$condition = $condition." and `h_ac` <> ".$l_block_id;}
  //     if ($p_ac == 1){$condition = $condition." and `p_ac` <> ".$l_block_id;}
  //     if ($n_ac == 1){$condition = $condition." and `n_ac` <> ".$l_block_id;}
  //     if ($f_pro == 1){$condition = $condition." and `gender_id` = 1";}

  //     $l_emp_id = $this->find_suitable_emp($condition, $l_pp_id, $eligibility, 0, $phase_id);
  //     if($l_emp_id > 0){
  //       $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order);"));    
  //     }else{
  //       //Find from other Parties -----
  //       $l_emp_text = $this->find_suitable_emp_from_already_deployed($condition, $l_pp_id, $eligibility, 0, $phase_id, $l_block_id, $max_female);
  //       if($l_emp_text != ""){
  //         $emp_ids = explode("_", $l_emp_text);
  //         $old_emp_id = $emp_ids[0];
  //         $new_emp_id = $emp_ids[1];
  //         $this->setReplacedEmployees($phase_id, $new_emp_id, $l_pp_id, $l_print_order, $l_block_id, $old_emp_id);
  //       }else{
  //         $l_emp_id = $this->find_suitable_emp($condition, $l_pp_id, $eligibility, 1, $phase_id); 
  //         if($l_emp_id > 0){
  //           $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order);"));
  //         }else{
  //           $l_emp_text = $this->find_suitable_emp_from_already_deployed($condition, $l_pp_id, $eligibility, 1, $phase_id, $l_block_id, $max_female);
  //           if($l_emp_text != ""){
  //             $emp_ids = explode("_", $l_emp_text);
  //             $old_emp_id = $emp_ids[0];
  //             $new_emp_id = $emp_ids[1];
  //             $this->setReplacedEmployees($phase_id, $new_emp_id, $l_pp_id, $l_print_order, $l_block_id, $old_emp_id);
  //           }else{
  //             $l_party_complete = 0;
  //           }
  //         }
  //       }
  //     }


  //     $l_print_order = 2;
  //     $eligibility = 2;
  //     $condition = str_replace("and `gender_id` = 1", "", $condition);
  //     $condition = str_replace("and `eligibility` = 1", "and `eligibility` = 2", $condition)." and `department_id` not in (select distinct `dept_id` from `deployed_data` where `party_id` = ".$l_pp_id.")";
  //     if ($f_apo == 1){$condition = $condition." and `gender_id` = 1";}
  //     else{
  //       $rs_fetch = DB::select(DB::raw("select count(*) as `tcount` from `deployed_data` where `party_id` = $l_pp_id and `gender_id` > 1; "));
  //       if ($rs_fetch[0]->tcount >= $max_female) {
  //         $condition = $condition." and `gender_id` = 1";
  //       }
  //     }
      
  //     $l_emp_id = $this->find_suitable_emp($condition, $l_pp_id, $eligibility, 0, $phase_id);
  //     if($l_emp_id > 0){
  //       $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order);"));    
  //     }else{
  //       //Find from other Parties -----
  //       $l_emp_text = $this->find_suitable_emp_from_already_deployed($condition, $l_pp_id, $eligibility, 0, $phase_id, $l_block_id, $max_female);
  //       if($l_emp_text != ""){
  //         $emp_ids = explode("_", $l_emp_text);
  //         $old_emp_id = $emp_ids[0];
  //         $new_emp_id = $emp_ids[1];
  //         $this->setReplacedEmployees($phase_id, $new_emp_id, $l_pp_id, $l_print_order, $l_block_id, $old_emp_id);
  //       }else{
  //         $l_emp_id = $this->find_suitable_emp($condition, $l_pp_id, $eligibility, 1, $phase_id); 
  //         if($l_emp_id > 0){
  //           $rs_fetch = DB::select(DB::raw("call `up_update_polling_party`($l_block_id, $phase_id, $l_pp_id, $l_emp_id, $l_print_order);"));
  //         }else{
  //           $l_emp_text = $this->find_suitable_emp_from_already_deployed($condition, $l_pp_id, $eligibility, 1, $phase_id, $l_block_id, $max_female);
  //           if($l_emp_text != ""){
  //             $emp_ids = explode("_", $l_emp_text);
  //             $old_emp_id = $emp_ids[0];
  //             $new_emp_id = $emp_ids[1];
  //             $this->setReplacedEmployees($phase_id, $new_emp_id, $l_pp_id, $l_print_order, $l_block_id, $old_emp_id);
  //           }else{
  //             $l_party_complete = 0;
  //           }
  //         }
  //       }
  //     }

  //     //Updating Party Status and randomization Process------
  //     $rs_update = DB::select(DB::raw("update `polling_parties` set `completed_party` = $l_party_complete where `id` = $l_pp_id limit 1;"));
      
  //     $percent_processed = (int)($l_counter/$l_total_party*100);
  //     $message = "Processed ".$l_counter." of ".$l_total_party;
  //     $rs_update = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
      
  //     $l_counter = $l_counter + 1;

  //   }

  //   $rs_parties = DB::select(DB::raw("insert into `polling_parties_first_random` (`state_id`, `district_id`, `block_id`, `phase_id`, `party_no`, `po1_id`, `po1_name`, `po1_dpt`, `po1_off`, `po1_desig`, `po1_mobile`, `po1_whatsapp`, `po2_id`, `po2_name`, `po2_dpt`, `po2_off`, `po2_desig`, `po2_mobile`, `po2_whatsapp`) select `state_id`, `district_id`, `block_id`, `phase_id`, `party_no`, `po1_id`, `po1_name`, `po1_dpt`, `po1_off`, `po1_desig`, `po1_mobile`, `po1_whatsapp`, `po2_id`, `po2_name`, `po2_dpt`, `po2_off`, `po2_desig`, `po2_mobile`, `po2_whatsapp` from `polling_parties` where `district_id` = $d_id and `phase_id` = $phase_id;")); 
  // }

  public function find_suitable_emp($condition, $l_pp_id, $eligibility)
  {
    $l_emp_id = 0;
    $query = "select `id` from `co_data` ".$condition." order by `random_no` limit 1;";
    
    $rs_fetch = DB::select(DB::raw($query));
    if(count($rs_fetch)>0){
      return $rs_fetch[0]->id;
    }

    //Relaxing Different Department Condition
    if($eligibility == 11){
      $query = str_replace("and `department_id` not in (select distinct `dept_id` from `counting_deployed_data` where `party_id` = ".$l_pp_id.")", "and `department_id` not in (select `dept_id` from `counting_deployed_data` where `party_id` = ".$l_pp_id." group by `dept_id` having count(*) > 1) and `office_id` not in (select distinct `off_id` from `counting_deployed_data` where `party_id` = ".$l_pp_id.")", $query); 
      $rs_fetch = DB::select(DB::raw($query));
      if(count($rs_fetch)>0){
        return $rs_fetch[0]->id;
      }
    }

    return $l_emp_id;
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




  // //--------------Second Randomization --------------------------------------

  // /////------Second Randomization Functions -----------------------
  public function doCountingRandomization($d_id)
  {
    $admin=Auth::guard('admin')->user();
    $user_id = $admin->id;

    $state_id = 0;      
    $from_ip = MyFuncs::getIp();;

    $l_remarks = $this->checkRandomStatus($d_id);
    if($l_remarks != ""){
      return $l_remarks;
    }

    $rs_fetch = DB::select(DB::raw("select `state_id` from `districts` where `id` = $d_id limit 1;"));
    $state_id = $rs_fetch[0]->state_id;

    //Clearing Previous Randomization Data if any
    $rs_save = DB::select(DB::raw("call `up_clear_counting_randomization`($d_id);"));

    //Randomization Started
    $rs_save = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Started', 0, 0, 0);"));      
    $rs_save = DB::select(DB::raw("insert into `counting_randomization_status` (`state_id`, `district_id`, `status`, `start_time`, `updated_on`, `updated_by`, `updated_ip`) values ($state_id, $d_id, 1, now(), now(), $user_id, '$from_ip');"));

    //Prepare CO Data
    $this->prepare_COData($d_id);      
    // return "";

    //Blank Parties Created
    $reserve = 10;
    $rs_fetch = DB::select(DB::raw("select `reserve_in_percent` from `randomization_setting` where `district_id` = $d_id limit 1;"));    
    if(count($rs_fetch)>0){
      $reserve = $rs_fetch[0]->reserve_in_percent;
    }
    $rs_save = DB::select(DB::raw("call `up_create_counting_parties_blank`($d_id, $reserve);"));


    //Preparing Counting Parties
    $this->prepare_Counting_parties($d_id);


    //Assigining Seat Sr.No.
    $rs_block = DB::select(DB::raw("select `id` from `blocks_mcs` where `districts_id` = $d_id and `phase_no` > 0;"));
    foreach ($rs_block as $key => $val_block) {
      $block_id = $val_block->id;
      $rs_update = DB::select(DB::raw("call `up_set_sr_no_counting`($d_id, $block_id);"));
    }


    $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Preparing Check List', 0, 0, 0);"));
    $this->prepare_check_list_counting($d_id);


    //Completing The Process
    $rs_save=DB::select(DB::raw("insert into `randomization_progress` values ($d_id, 'Completed', 1, 0, 100);"));      
    $rs_save=DB::select(DB::raw("update `counting_randomization_status` set `status` = 2, `finish_time` = now() where `district_id` = $d_id limit 1;"));

    return "";
  }

  public function prepare_Counting_parties($d_id)
  {
    
    $c_off = 1; $c_dept = 1;
    $rs_fetch = DB::select(DB::raw("select * from `randomization_setting` where `district_id` = $d_id limit 1;"));
    if(count($rs_fetch)>0){
      $c_dept = $rs_fetch[0]->department_2; $c_off = $rs_fetch[0]->office_2;
    }

    $rs_fetch = DB::select(DB::raw("Select count(*) as `tcount` From `counting_parties` Where `district_id` = $d_id;"));
    $l_total_party = $rs_fetch[0]->tcount;
    $l_counter = 1;

    $rs_parties = DB::select(DB::raw("Select `pp`.`id`, `pp`.`block_id`, `pp`.`party_no`, `pp`.`party_count` From `counting_parties` `pp` inner join `blocks_mcs` `bl` on `bl`.`id` = `pp`.`block_id` Where `pp`.`district_id` = $d_id order by `bl`.`randamization_priority`, `pp`.`party_no`;"));
    foreach ($rs_parties as $key => $val_parties) {
      $rs_fetch = DB::select(DB::raw("select * from `randomization_kill` where `district_id` = $d_id and `is_kill` = 1 limit 1;"));
      if(count($rs_fetch)>0){
        $percent_processed = 100;
        $message = "Processing Stopped";
        $rs_update = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
        $rs_delete = DB::select(DB::raw("DELETE from `randomization_kill` where `district_id` = $d_id;"));
        return;
      }
        
      $l_party_complete = 1;
      $eligibility = 10;
      $l_print_order = 1;
      $l_emp_id = 0;
      $l_block_id = $val_parties->block_id;
      $l_pp_id = $val_parties->id;
      $l_party_size = $val_parties->party_count;

      $condition = " where `district_id` = ".$d_id." and `eligibility` = ".$eligibility." and `block_id` = 0 and `duty_alloted` = 0 ";
      
      $l_emp_id = $this->find_suitable_emp($condition, $l_pp_id, $eligibility);
      if($l_emp_id > 0){
        $rs_fetch = DB::select(DB::raw("call `up_update_counting_party`($l_block_id, $l_pp_id, $l_emp_id, $l_print_order);"));    
      }

      $l_po_counter = 2;
      $eligibility = 11;
      while(($l_po_counter <= $l_party_size) && ($l_party_complete >0)){
        $condition = " where `district_id` = ".$d_id." and `eligibility` = ".$eligibility." and `duty_alloted` = 0 ";
        if($c_dept == 1){
          // $condition = $condition." and `department_id` not in (select distinct `dept_id` from `counting_deployed_data` where `party_id` = ".$l_pp_id." group by `dept_id` having count(*) > 1) ";
        }
        if($c_off == 1){
          $condition = $condition." and `office_id` not in (select distinct `off_id` from `counting_deployed_data` where `party_id` = ".$l_pp_id.") ";
        }
        
        $condition = $condition." and `gender_id` = 1";
        
        $l_print_order = $l_po_counter;
        $l_emp_id = $this->find_suitable_emp($condition, $l_pp_id, $eligibility);
        if($l_emp_id > 0){
          $rs_fetch = DB::select(DB::raw("call `up_update_counting_party`($l_block_id, $l_pp_id, $l_emp_id, $l_print_order);"));    
        }else{
          $l_party_complete = 0;
        }
        $l_po_counter = $l_po_counter + 1;
      }
      

      //Updating Party Status and randomization Process------
      $rs_update = DB::select(DB::raw("update `counting_parties` set `completed_party` = $l_party_complete where `id` = $l_pp_id limit 1;"));
      
      $percent_processed = (int)($l_counter/$l_total_party*100);
      $message = "Processed ".$l_counter." of ".$l_total_party;
      $rs_update = DB::select(DB::raw("insert into `randomization_progress` values ($d_id, '$message', 0, 0, $percent_processed);"));
      
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


  // public function prepare_check_list_first($d_id, $phase_id)
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

  //     $rs_fetch = DB::select(DB::raw("select `pp`.`party_no`, `pp`.`po1_id`, `pp`.`po1_name`, `pp`.`po1_dpt`, `pp`.`po1_off`, `pp`.`po1_desig`, `pp`.`po1_mobile`, `pp`.`po1_whatsapp`, `pp`.`po2_id`, `pp`.`po2_name`, `pp`.`po2_dpt`, `pp`.`po2_off`, `pp`.`po2_desig`, `pp`.`po2_mobile`, `pp`.`po2_whatsapp` from `polling_parties` `pp` inner join `blocks_mcs` `bl` on `bl`.`id` = `pp`.`block_id` where `pp`.`block_id` = $block_id order by `pp`.`party_no`;"));
      
  //     $html_check_list = view('admin.report.firstrandomizationReport.checklist',compact('rs_fetch', 'election_name', 'block_name', 'elect_type'));
      
      
  //     $mpdf_check_list->WriteHTML($html_check_list);
      
  //     $documentUrl = Storage_path() . '/app/report/'.$d_id.'/'.$phase_id.'/first';  
  //     @mkdir($documentUrl, 0755, true);  
      
  //     $mpdf_check_list->Output($documentUrl.'/check_list_'.$block_id.'.pdf', 'F');    
  //   }
              
  // }

  public function prepare_check_list_counting($d_id)
  { 
    $rs_fetch = DB::select(DB::raw("select `name_e` from `districts` where `id` = $d_id limit 1;"));
    $dist_name = $rs_fetch[0]->name_e;

    $election_name = MyFuncs::election_name();
    $elect_type = MyFuncs::elect_type();
    
    $rs_block = DB::select(DB::raw("select * from `blocks_mcs` where `districts_id` = $d_id and `phase_no` > 0;"));
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

      $rs_fetch = DB::select(DB::raw("select ifnull(max(`pb`.`co_count`),0) as `max_po` from `counting_tables` `pb` where `pb`.`blocks_id` = $val_block->id;"));
      $max_po_count = $rs_fetch[0]->max_po;

      $rs_fetch = DB::select(DB::raw("select `pp`.`party_no`, `pp`.`co1_id`, `pp`.`co1_name`, `pp`.`co1_dpt`, `pp`.`co1_off`, `pp`.`co1_desig`, `pp`.`co1_mobile`, `pp`.`co1_whatsapp`, `pp`.`co2_id`, `pp`.`co2_name`, `pp`.`co2_dpt`, `pp`.`co2_off`, `pp`.`co2_desig`, `pp`.`co2_mobile`, `pp`.`co2_whatsapp`, `pp`.`co3_id`, `pp`.`co3_name`, `pp`.`co3_dpt`, `pp`.`co3_off`, `pp`.`co3_desig`, `pp`.`co3_mobile`, `pp`.`co3_whatsapp`, `pp`.`co4_id`, `pp`.`co4_name`, `pp`.`co4_dpt`, `pp`.`co4_off`, `pp`.`co4_desig`, `pp`.`co4_mobile`, `pp`.`co4_whatsapp` from `counting_parties` `pp` where `pp`.`block_id` = $val_block->id order by  `pp`.`party_no`;"));
      
      
      $html_check_list = view('admin.counting.report.firstRandomization.checkList',compact('rs_fetch', 'election_name', 'dist_name', 'elect_type', 'max_po_count', 'block_name'));
      
      
      $mpdf_check_list->WriteHTML($html_check_list);
      
      $documentUrl = Storage_path() . '/app/report/'.$d_id.'/counting';
      @mkdir($documentUrl, 0755, true);  
      
      $mpdf_check_list->Output($documentUrl.'/check_list_'.$val_block->id.'.pdf', 'F');    
    }
  }
  
}



