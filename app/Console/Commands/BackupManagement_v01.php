<?php

namespace App\Console\Commands;

use App\Admin;
use App\Helper\MyFuncs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackupManagement_v01 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'BackupManagement_v01:generate';
    


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'BackupManagement_v01 generate';

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
        

        $folder = "edms";        
        $rs_tables = DB::connection('mysql_live_backup')->select(DB::raw("SELECT table_name FROM `information_schema`.tables where `table_schema` = '$folder' order by table_name;"));
        foreach ($rs_tables as $key => $tbl_value) {
            $table_name = $tbl_value->table_name;
            
            $result = $this->prepareBackup($folder, $table_name);
            
        }

        
    }

    public function prepareBackup($folder, $table_name)
    {
        $truncate = 1;
        $backup_content = '';
        if($truncate == 1){
            $backup_content .= "\nTRUNCATE TABLE `$table_name`;\n\n";
        }

        $l_off_set_size = 80000;
        $file_counter = 10;
        $l_off_set_counter = 0;
        $l_flag = 1;

        while ($l_flag == 1) {
            $l_off_set_records = $l_off_set_size*$l_off_set_counter;
            $sub_query = " limit $l_off_set_size offset ".$l_off_set_records;
            $rs_result = DB::connection('mysql_live_backup')->select(DB::raw("SELECT * from `$table_name` $sub_query;"));

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


                \File::put(public_path('/backup/'.$folder.'/'.$table_name.'_'.$file_counter.'.sql'),$backup_content);
                $backup_content = "";

            }else{
                $l_flag = 0;
            }

            $file_counter++;
            $l_off_set_counter++;
        }
        
    }
        
    
}
