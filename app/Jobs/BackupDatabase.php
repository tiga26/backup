<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $_databases = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_databases = config('database.backup.databases');

        if(empty($this->_databases)) {
            Log::error('No databases provided for backup. Please add database name to database config backup section');
            echo 'Error - Please see laravel.log file for more details.';
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->_databases as $databaseName) {
            $process = new Process(sprintf(
                'mysqldump -u%s -p%s %s > %s',
                config('database.connections.mysql.username'),
                config('database.connections.mysql.password'),
                $databaseName,
                storage_path('backups/'.$databaseName.'-'.date('Y-m-d H:i:s').'.sql')
            ));

            try {
                $process->mustRun();

                Log::info('Successfully processed backup for '.$databaseName.' database');
                echo 'Success!';
            } catch (ProcessFailedException $exception) {
                Log::error('Failed to backup '.$databaseName.' database. Reason:['.$exception->getMessage().']');
                echo 'Error - Please see laravel.log file for more details.';
            }
        }
    }
}
