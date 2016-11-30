<?php

namespace DreamFactory\Enterprise\Installer\Console\Commands;

use Illuminate\Console\Command;

class setupDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the DFG Console Service DB';

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
        // Get the service database specified from Puppet.
        $svc_db = env('DB_DATABASE');
        \DB::select("CREATE DATABASE IF NOT EXISTS {$svc_db}");
        // need an empty dreamfactory database
        \DB::select("CREATE DATABASE IF NOT EXISTS dreamfactory");


        \Artisan::call('migrate');
        \Artisan::call('db:seed');
    }
}
