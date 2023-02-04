<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Instance;
use App\Application;

class EnableOnlyoffice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'enable:onlyoffice {instance}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable OnlyOffice for an instance';

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
        $app = Application::where('slug', 'office')->firstOrFail();

        if (!$instance->apps->contains($app->id)) {
            $instance->apps()->attach($app->id);
            $this->info('Enabled OnlyOffice for instance "' . $instance_slug . '"');
        } else {
            $this->info('OnlyOffice already was enabled for instance "' . $instance_slug . '"');
        }
    }
}
