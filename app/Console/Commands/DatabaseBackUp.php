<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $filename = "backup-".Carbon::now()->format('Y-m-d-h-i-s').".sql";


        $host = "127.0.0.1";
        $database = "timesheet";
        $username = "root";
        $password = "@@Sta11ing@";
        
        $command = "mysqldump -h'".$host."' -u'".$username."' -p'".$password."' ".$database." > ".storage_path()."/app/backup/".$filename;
        $returnVar = NULL;
        $output = NULL;


        exec($command, $output, $returnVar);
    }
}
