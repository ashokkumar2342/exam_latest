<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class sqlServerDataTransfer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sqlServerDataTransfer:transfer {from_district} {to_district}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sqlServerDataTransfer Transfer ';

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
      
        $from_district = $this->argument('from_district');
        $to_district = $this->argument('to_district'); 
        
        echo "Porting Started \n";

        if($from_district == 0 && $to_district == 0){
            $this->import_master_data();
            echo "Porting Completed \n";
            return null;
        }



        // echo "Porting Departments \n";        
        // $this->import_dept_data($from_district, $to_district);      
        
        // echo "Porting Offices \n";        
        // $this->import_off_data($from_district, $to_district);      

        // echo "Porting Designations \n";        
        // $this->import_designation_data($from_district, $to_district);      

        echo "Porting Employee Data \n";        
        $this->import_emp_data($from_district, $to_district);      
        
        // echo "Updating Only Remarks \n";
        // $this->import_emp_data_only_remarks($from_district, $to_district);
        // echo "Porting Completed \n";
    }

    public function import_emp_data($from_district, $to_district)
    {   
        $counter = 0;
        $rs_fetch = DB::select(DB::raw("select `state_id`, `name_e` from `districts` where `id` = $to_district;"));
        $state_id = $rs_fetch[0]->state_id;
        echo "District :: ".$rs_fetch[0]->name_e." \n";
        $rs_source_data = DB::connection('sqlsrv')->select("select DeptCode, OffCode, EmpCode, EmpName, EmpFName, Desig, isnull(BPay,0) as s_bpay, PayScale, isnull(Quali,0) as qualification, Category, isnull(BLODuty,0) as blo, case Gazetted when 'G' then 1 else 0 end as is_gazetted, case Sex_Female when 'M' then 1 else 2 end as gender, isnull(Child,0) as child_pregnant, isnull(Handi,0) as handicapped, isnull(Eligible,0) as eligibility, isnull(Exem_Reason, '') as exempt_reason, Address, Contact_No, isnull(Age,0) as s_age, isnull(BirthDate,'2001-01-01') as birth_date, isnull(JoiningDate, '2020-01-01') as join_date, isnull(ServiceYears,0) as s_service_year, isnull(Deleted,0) as is_deleted, isnull(OffAddress,'') as officeaddress, isnull(Grade_Pay,0) as gradepay, isnull(PayBand,0) as pay_band, isnull(Ret_Date, '2025-01-01') as retdate, isnull(Is_Exempted, 0) as s_exempted, isnull(epicno,'') as epic_no, isnull(verified,0) as is_verified, isnull(Hblock,0) as hb, isnull(Pblock,0) as pb, isnull(Nblock,0) as nb, isnull(remarks,'') as s_remarks from Empmas_$from_district order by DeptCode, OffCode");
        $total = count($rs_source_data);
        
        foreach ($rs_source_data as $key => $val_source) {
            $counter++;
            if(fmod($counter, 1000) == 0){
                echo "Ported ".$counter." of ".$total." \n";    
            }
            $s_dept_code = MyFuncs::removeSpacialChr($val_source->DeptCode);
            $s_off_code = MyFuncs::removeSpacialChr($val_source->OffCode);
            $s_emp_code = MyFuncs::removeSpacialChr($val_source->EmpCode);
            $s_emp_name = substr(MyFuncs::removeSpacialChr($val_source->EmpName),0,50);
            $s_fname = substr(MyFuncs::removeSpacialChr($val_source->EmpFName), 0, 49);
            $s_desig = $val_source->Desig;
            $s_b_pay = $val_source->s_bpay;
            $s_pay_scale = MyFuncs::removeSpacialChr($val_source->PayScale);
            $s_qualification = $val_source->qualification;
            $s_category = $val_source->Category;
            $s_blo = $val_source->blo;
            $s_gazetted = $val_source->is_gazetted;
            $s_gender = $val_source->gender;
            $s_child = $val_source->child_pregnant;
            $s_handi = $val_source->handicapped;
            $s_eligibility = $val_source->eligibility;
            $s_exempt_reason = MyFuncs::removeSpacialChr($val_source->exempt_reason);
            $s_address = substr(MyFuncs::removeSpacialChr($val_source->Address),0,99);
            $s_contact = substr(MyFuncs::removeSpacialChr($val_source->Contact_No),0,10);
            $s_age = $val_source->s_age;
            if(strlen($s_age) == 0){
                $s_age = 0;
            }
            $s_birth = $val_source->birth_date;
            $s_join = $val_source->join_date;
            $s_service = $val_source->s_service_year;
            $s_deleted = $val_source->is_deleted;
            $s_off_address = substr(MyFuncs::removeSpacialChr($val_source->officeaddress), 0, 200);
            $s_grade_pay = $val_source->gradepay;
            $s_retired = $val_source->retdate;
            $s_is_exempted = $val_source->s_exempted;
            $s_epic = MyFuncs::removeSpacialChr($val_source->epic_no);
            $s_verified = $val_source->is_verified;
            $s_hb = $val_source->hb;
            $s_pb = $val_source->pb;
            $s_nb = $val_source->nb;
            $s_remarks = $val_source->s_remarks;
            
            $nb_id = 0;
            if($s_nb>0){
                $rs_fetch = DB::select(DB::raw("select `id` from `blocks_mcs` where `on_line_block_id` = '$s_nb' limit 1; "));
                if(count($rs_fetch)>0){
                    $nb_id = $rs_fetch[0]->id;
                }    
            }
            

            $pb_id = 0;
            if($s_pb > 0){
                $rs_fetch = DB::select(DB::raw("select `id` from `blocks_mcs` where `on_line_block_id` = '$s_pb' limit 1; "));
                if(count($rs_fetch)>0){
                    $pb_id = $rs_fetch[0]->id;
                }
            }
                

            $hb_id = 0;
            if($s_hb > 0){
                $rs_fetch = DB::select(DB::raw("select `id` from `blocks_mcs` where `on_line_block_id` = '$s_hb' limit 1; "));
                if(count($rs_fetch)>0){
                    $hb_id = $rs_fetch[0]->id;
                }
            }
                

            $grade_pay_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `grade_pay` where `grade_pay` = '$s_grade_pay' limit 1; "));
            if(count($rs_fetch)>0){
                $grade_pay_id = $rs_fetch[0]->id;
            }

            $status = 1;
            $verified = $s_verified;
            if($s_deleted == 1){
                $status = 2;
                $verified = 1;
            }

            $category_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `categorys` where `name_e` = '$s_category' limit 1; "));
            if(count($rs_fetch)>0){
                $category_id = $rs_fetch[0]->id;
            }

            $pay_scale_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `pay_scale_mas` where `pay_scale` = '$s_pay_scale' limit 1; "));
            if(count($rs_fetch)>0){
                $pay_scale_id = $rs_fetch[0]->id;
            }

            $dept_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `departments` where `district_id` = $to_district and `department_code` = '$s_dept_code' limit 1; "));
            if(count($rs_fetch)>0){
                $dept_id = $rs_fetch[0]->id;
            }

            $off_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `offices` where `department_id` = $dept_id and `office_code` = '$s_off_code' limit 1; "));
            if(count($rs_fetch)>0){
                $off_id = $rs_fetch[0]->id;
            }

            $desig_id = 0;
            // $rs_fetch = DB::select(DB::raw("select `id` from `designations` where `department_id` = $dept_id and `designation_code` = '$s_desig' limit 1; "));
            // if(count($rs_fetch)>0){
            //     $desig_id = $rs_fetch[0]->id;
            // }


            //For Kurushetra----------------
            $rs_fetch = DB::select(DB::raw("select `id` from `designations` where `department_id` = $dept_id and `designation_name_e` = '$s_desig' limit 1; "));
            if(count($rs_fetch)>0){
                $desig_id = $rs_fetch[0]->id;
            }

            $s_qualification = $val_source->qualification;
            $rs_fetch = DB::select(DB::raw("select `id` from `qualification_mas` where `qualification` = '$s_qualification' limit 1; "));
            if(count($rs_fetch)>0){
                $s_qualification = $rs_fetch[0]->id;
            }else{
              $s_qualification = 0;
            }
            //////---------Kurushetra End -------

            $b_pay_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `basic_pay` where `basic_pay` = $s_b_pay limit 1; "));
            if(count($rs_fetch)>0){
                $b_pay_id = $rs_fetch[0]->id;
            }

            $rs_insert = DB::select(DB::raw("INSERT into `employee_details` (`state_id`, `district_id`, `department_id`, `office_id`, `emp_code`, `employee_name_e`, `relation_name_e`, `gender_id`, `mobile_no`, `whatsapp_no`, `designation_id`, `eligibility`, `handicapped`, `joining_date`, `retirement_date`, `service_category`, `blo_duty`, `is_gazzeted`, `exempted`, `exempted_reason`, `handicapped_verified`, `epic_no`, `h_ac`, `p_ac`, `n_ac`, `status`, `verified`, `basic_pay`, `pay_scale`, `qualification`, `child_pregnant`, `emp_address`, `age`, `birth_date`, `service_yr`, `off_address`, `grade_pay`, `remarks`) values ($state_id, $to_district, $dept_id, $off_id, '$s_emp_code', '$s_emp_name', '$s_fname', $s_gender, '$s_contact', '$s_contact', $desig_id, $s_eligibility, $s_handi, '$s_join', '$s_retired', $category_id, $s_blo, $s_gazetted, $s_is_exempted, '$s_exempt_reason', $s_handi, '$s_epic', $hb_id, $pb_id, $nb_id, $status, $verified, $b_pay_id, $pay_scale_id, $s_qualification, $s_child, '$s_address', $s_age, '$s_birth', $s_service, '$s_off_address', $grade_pay_id, '$s_remarks');"));

            // $fresh = 0;
            // $found = 0;
            // if($fresh == 0){
            //     $rs_fetch = DB::select(DB::raw("select `id` from `employee_details` where `office_id` = $off_id and `emp_code` = '$s_emp_code' limit 1; ")); 
            //     if(count($rs_fetch) > 0){
            //         $found = 1;    
            //     }
            // }
            
            // if($found == 1){
            //     $id = $rs_fetch[0]->id;
            //     $rs_update = DB::select(DB::raw("UPDATE `employee_details` SET `employee_name_e` = '$s_emp_name', `relation_name_e` = '$s_fname', `gender_id` = $s_gender, `mobile_no` = '$s_contact', `whatsapp_no` = '$s_contact', `designation_id` = $desig_id, `handicapped` = $s_handi, `joining_date` = '$s_join', `retirement_date` = '$s_retired', `service_category` = $category_id, `blo_duty` = $s_blo, `is_gazzeted` = $s_gazetted, `handicapped_verified` = $s_handi, `epic_no` = '$s_epic', `h_ac` = $hb_id, `p_ac` = $pb_id, `n_ac` = $nb_id, `status` = $status, `verified` = $verified, `basic_pay` = $b_pay_id, `pay_scale` = $pay_scale_id, `qualification` = $s_qualification, `child_pregnant` = $s_child, `emp_address` = '$s_address', `age` = $s_age, `birth_date` = '$s_birth', `service_yr` = $s_service, `off_address` = '$s_off_address', `grade_pay` = $grade_pay_id, `remarks` = '$s_remarks' where `id` = $id limit 1;"));            
            // }else{
            //     $rs_insert = DB::select(DB::raw("INSERT into `employee_details` (`state_id`, `district_id`, `department_id`, `office_id`, `emp_code`, `employee_name_e`, `relation_name_e`, `gender_id`, `mobile_no`, `whatsapp_no`, `designation_id`, `eligibility`, `handicapped`, `joining_date`, `retirement_date`, `service_category`, `blo_duty`, `is_gazzeted`, `exempted`, `exempted_reason`, `handicapped_verified`, `epic_no`, `h_ac`, `p_ac`, `n_ac`, `status`, `verified`, `basic_pay`, `pay_scale`, `qualification`, `child_pregnant`, `emp_address`, `age`, `birth_date`, `service_yr`, `off_address`, `grade_pay`, `remarks`) values ($state_id, $to_district, $dept_id, $off_id, '$s_emp_code', '$s_emp_name', '$s_fname', $s_gender, '$s_contact', '$s_contact', $desig_id, $s_eligibility, $s_handi, '$s_join', '$s_retired', $category_id, $s_blo, $s_gazetted, $s_is_exempted, '$s_exempt_reason', $s_handi, '$s_epic', $hb_id, $pb_id, $nb_id, $status, $verified, $b_pay_id, $pay_scale_id, $s_qualification, $s_child, '$s_address', $s_age, '$s_birth', $s_service, '$s_off_address', $grade_pay_id, '$s_remarks');"));                
            // }
            
        }

    }

    public function import_emp_data_only_remarks($from_district, $to_district)
    {   
        $counter = 0;
        $rs_fetch = DB::select(DB::raw("select `state_id`, `name_e` from `districts` where `id` = $to_district;"));
        $state_id = $rs_fetch[0]->state_id;
        echo "District :: ".$rs_fetch[0]->name_e." \n";
        $rs_source_data = DB::connection('sqlsrv')->select("select DeptCode, OffCode, EmpCode, isnull(remarks,'') as s_remarks from Empmas_$from_district");
        $total = count($rs_source_data);
        
        foreach ($rs_source_data as $key => $val_source) {
            $counter++;
            if(fmod($counter, 1000) == 0){
                echo "Ported ".$counter." of ".$total." \n";    
            }
            $s_dept_code = MyFuncs::removeSpacialChr($val_source->DeptCode);
            $s_off_code = MyFuncs::removeSpacialChr($val_source->OffCode);
            $s_emp_code = MyFuncs::removeSpacialChr($val_source->EmpCode);
            $s_remarks = $val_source->s_remarks;
            
            $dept_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `departments` where `district_id` = $to_district and `department_code` = '$s_dept_code' limit 1; "));
            if(count($rs_fetch)>0){
                $dept_id = $rs_fetch[0]->id;
            }

            $off_id = 0;
            $rs_fetch = DB::select(DB::raw("select `id` from `offices` where `department_id` = $dept_id and `office_code` = '$s_off_code' limit 1; "));
            if(count($rs_fetch)>0){
                $off_id = $rs_fetch[0]->id;
            }

            $fresh = 0;
            $found = 0;
            if($fresh == 0){
                $rs_fetch = DB::select(DB::raw("select `id` from `employee_details` where `office_id` = $off_id and `emp_code` = '$s_emp_code' limit 1; ")); 
                if(count($rs_fetch) > 0){
                    $found = 1;    
                }
            }
            
            if($found == 1){
                $id = $rs_fetch[0]->id;
                $rs_update = DB::select(DB::raw("UPDATE `employee_details` SET `remarks` = '$s_remarks' where `id` = $id limit 1;"));            
            }
            
        }

    }

    public function import_designation_data($from_district, $to_district)
    {
        $rs_source_dept = DB::connection('sqlsrv')->select("select distinct emp.DeptCode from Empmas_$from_district emp");
        foreach ($rs_source_dept as $key => $val_dept_source) {
            $s_dept_code = MyFuncs::removeSpacialChr($val_dept_source->DeptCode);
            $rs_fetch = DB::select(DB::raw("select `id` from `departments` where `district_id` = $to_district and `department_code` = '$s_dept_code' limit 1; "));

            if(count($rs_fetch)>0){
                $dept_id = $rs_fetch[0]->id;
                $rs_source_rec = DB::connection('sqlsrv')->select("select distinct desig.ID, desig.desig from Empmas_$from_district emp inner join DesigMas desig on desig.ID = emp.Desig and emp.DCode = desig.dcode where emp.DeptCode = '$s_dept_code'");

                foreach ($rs_source_rec as $key => $val_source) {
                    $s_desig_id = $val_source->ID;
                    $s_desig_name = MyFuncs::removeSpacialChr($val_source->desig);
                    $rs_fetch = DB::select(DB::raw("select `id` from `designations` where `department_id` = $dept_id and `designation_name_e` = '$s_desig_name' and `designation_code` = '$s_desig_id' limit 1; "));
                    if(count($rs_fetch) == 0){
                        $rs_insert = DB::select(DB::raw("insert into `designations` (`department_id`, `designation_code`, `designation_name_e`, `designation_name_l`) values ($dept_id, '$s_desig_id', '$s_desig_name', '$s_desig_name'); "));    
                    }

                    
                }
                
            }

        }

    }

    public function import_block_data($from_district, $to_district)
    {
        $rs_source_rec = DB::connection('sqlsrv')->select("select ID, BlockName from BlockMas where Dcode = $from_district");
        
        foreach ($rs_source_rec as $key => $val_source) {
            $s_block_id = $val_source->ID;
            $s_block_name = MyFuncs::removeSpacialChr($val_source->BlockName);

            $rs_fetch = DB::select(DB::raw("update `blocks_mcs` set `on_line_block_id` = $s_block_id where `name_e` = '$s_block_name' and `districts_id` = $to_district limit 1; "));

        }
    }

    public function import_dept_data($from_district, $to_district)
    {
        $rs_fetch = DB::select(DB::raw("select `state_id` from `districts` where `id` = $to_district;"));
        echo "ok";
        $rs_dept_mas = DB::connection('sqlsrv')->select("select DeptName, Centre, DeptCode, isnull(Is_Exempted,0) as exempted from DeptMas where DCode = $from_district");
        
        $rs_fetch = DB::select(DB::raw("select `state_id` from `districts` where `id` = $to_district;"));
        $state_id = $rs_fetch[0]->state_id;

        foreach ($rs_dept_mas as $key => $val_dept_mas) {
            $dpt_code = MyFuncs::removeSpacialChr($val_dept_mas->DeptCode);
            $dpt_name = MyFuncs::removeSpacialChr($val_dept_mas->DeptName);
            $dpt_type = $val_dept_mas->Centre + 1;
            $exempted = $val_dept_mas->exempted;

            $rs_fetch = DB::select(DB::raw("select `id` from `departments` where `district_id` = $to_district and `department_code` = '$dpt_code' limit 1;"));
            if(count($rs_fetch)> 0){
                $id = $rs_fetch[0]->id;
                $rs_update = DB::select(DB::raw("UPDATE `departments` SET `state_id` = $state_id, `district_id` = $to_district, `department_code` = '$dpt_code', `department_name_e` = '$dpt_name', `department_type` = $dpt_type, `exempted` = $exempted where `id` = $id limit 1;"));
            }else{
                $rs_insert = DB::select(DB::raw("insert into `departments` (`state_id`, `district_id`, `department_code`, `department_name_e`, `department_type`, `exempted`) values ($state_id, $to_district, '$dpt_code',  '$dpt_name', $dpt_type, $exempted);"));
            }
            
        }
    }

    public function import_off_data($from_district, $to_district)
    {
        $rs_source_rec = DB::connection('sqlsrv')->select("select OffName, isnull(Locked, 0) as off_locked, isnull(Is_Exempted, 0) as exempted, DeptCode, OffCode, isnull(ExemptionRemarks, '') as exempt_reason from OffMas where DCode = $from_district");
        
        $rs_fetch = DB::select(DB::raw("select `state_id` from `districts` where `id` = $to_district limit 1;"));
        $state_id = $rs_fetch[0]->state_id;

        foreach ($rs_source_rec as $key => $val_source) {
            $dpt_code = MyFuncs::removeSpacialChr($val_source->DeptCode);
            $rs_fetch = DB::select(DB::raw("select `id` from `departments` where `district_id` = $to_district and `department_code` = '$dpt_code' limit 1;"));
            
            if(count($rs_fetch) > 0) {
                $dept_id = $rs_fetch[0]->id;

                $off_code = $val_source->OffCode;
                $off_name = MyFuncs::removeSpacialChr($val_source->OffName);
                $verified = $val_source->off_locked;
                $exempted = $val_source->exempted;
                $exempt_reason = MyFuncs::removeSpacialChr($val_source->exempt_reason);

                $rs_fetch = DB::select(DB::raw("select `id` from `offices` where `department_id` = $dept_id and `office_code` = '$off_code' limit 1;"));
                if(count($rs_fetch) > 0){
                    $id = $rs_fetch[0]->id;
                    $rs_update = DB::select(DB::raw("UPDATE `offices` set `office_name_e` = '$off_name', `verified_data` = $verified, `exempted` = $exempted, `exempted_reason` = '$exempt_reason' where `id` = $id limit 1;"));
                }else{
                    $rs_insert = DB::select(DB::raw("insert into `offices` (`state_id`, `district_id`, `department_id`, `office_code`, `office_name_e`, `verified_data`, `exempted`, `exempted_reason`) values ($state_id, $to_district, $dept_id, '$off_code', '$off_name', $verified, $exempted, '$exempt_reason');"));
                }
                
            }
                

        }

    }

    public function import_master_data()
    {

        //Ac Mas
        echo "AC Porting \n";
        $rs_source_rec = DB::connection('sqlsrv')->select("select * from acmas");
        
        $rs_delete = DB::select(DB::raw("truncate table `ac_mas`;"));

        foreach ($rs_source_rec as $key => $val_source) {
            $ac_id = $val_source->id;
            $pc_id = $val_source->pccode;
            $ac_code = $val_source->accode;
            $ac_name = $val_source->acname;
            
            $rs_insert = DB::select(DB::raw("insert into `ac_mas` (`id`, `pc_code`, `ac_code`, `ac_name`) values ($ac_id, '$pc_id', $ac_code, '$ac_name');"));

        }


        //Table Basic Pay
        echo "Basic Pay Porting \n";
        $rs_source_rec = DB::connection('sqlsrv')->select("select * from Basicpay where BPay is not null order by BPay");
        
        $rs_delete = DB::select(DB::raw("truncate table `basic_pay`;"));

        foreach ($rs_source_rec as $key => $val_source) {
            $bp_id = $val_source->ID;
            $bp_pay = $val_source->BPay;
            
            $rs_insert = DB::select(DB::raw("insert into `basic_pay` (`id`, `basic_pay`) values ($bp_id, $bp_pay);"));

        }

        //Category
        echo "Category Porting \n";
        $rs_source_rec = DB::connection('sqlsrv')->select("select * from Category");
        
        $rs_delete = DB::select(DB::raw("truncate table `categorys`;"));

        foreach ($rs_source_rec as $key => $val_source) {
            $cat_id = $val_source->cat_code;
            $cat_name = $val_source->cat_desc;
            
            $rs_insert = DB::select(DB::raw("insert into `categorys` (`id`, `code`, `name_e`, `name_l`) values ($cat_id, $cat_id, '$cat_name', '$cat_name');"));

        }


        // //Eligibility
        // echo "Eligibility Porting \n";
        // $rs_source_rec = DB::connection('sqlsrv')->select("select * from EligibilityMas");
        
        // $rs_delete = DB::select(DB::raw("truncate table `eligibility`;"));

        // foreach ($rs_source_rec as $key => $val_source) {
        //     $el_id = $val_source->ID;
        //     $el_name = $val_source->EligibleDuty;
            
        //     $rs_insert = DB::select(DB::raw("insert into `eligibility` (`id`, `eligibility_code`, `eligibility_name_e`, `eligibility_name_l`) values ($el_id, $el_id, '$el_name', '$el_name');"));

        // }


        //Grade Pay
        echo "Grade Pay Porting \n";
        $rs_source_rec = DB::connection('sqlsrv')->select("select * from GradePay");
        
        $rs_delete = DB::select(DB::raw("truncate table `grade_pay`;"));

        foreach ($rs_source_rec as $key => $val_source) {
            $pay_id = $val_source->ID;
            $pay_name = $val_source->GradePay;
            
            $rs_insert = DB::select(DB::raw("insert into `grade_pay` (`id`, `grade_pay`) values ($pay_id, $pay_name);"));

        }


        //Pay Scale
        echo "Pay Scale Porting \n";
        $rs_source_rec = DB::connection('sqlsrv')->select("select * from PayScaleMas");
        
        $rs_delete = DB::select(DB::raw("truncate table `pay_scale_mas`;"));

        foreach ($rs_source_rec as $key => $val_source) {
            $pay_id = $val_source->ID;
            $pay_name = $val_source->PayScale;
            
            $rs_insert = DB::select(DB::raw("insert into `pay_scale_mas` (`id`, `pay_scale`) values ($pay_id, '$pay_name');"));

        }


        //Qualification
        echo "Qualification Porting \n";
        $rs_source_rec = DB::connection('sqlsrv')->select("select * from QualiMas");
        
        $rs_delete = DB::select(DB::raw("truncate table `qualification_mas`;"));

        foreach ($rs_source_rec as $key => $val_source) {
            $pay_id = $val_source->ID;
            $pay_name = $val_source->Quali;
            
            $rs_insert = DB::select(DB::raw("insert into `qualification_mas` (`id`, `qualification`) values ($pay_id, '$pay_name');"));

        }

    }

   
}
