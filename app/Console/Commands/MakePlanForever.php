<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Instance;
use App\Application;

class MakePlanForever extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'plan:forever {instance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make plan forever for an instance';

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
        $instance_slug = $this->argument('instance');
        $instance = Instance::where('slug', $instance_slug)->firstOrFail();

        $instance->parameters()
          ->where([
            'parameter_name' => 'billing_offer'
          ])
          ->update([
            'parameter_value' => 'forever'
          ]);
    }
}
