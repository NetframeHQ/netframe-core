<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Instance;

class GenerateInstancesCss extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'instances:css';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate instances custom css after master css changes';

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
        $instances = Instance::get();
        foreach ($instances as $instance) {
            //get instance css parameter
            $this->info('Generate css for '.$instance->name);
            $instance->compileCustomCss();
        }
    }
}
