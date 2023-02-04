<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        //parent::boot();
        // Load macros
        require base_path() . '/app/Macros/Templates.php';
        require base_path() . '/app/Macros/Buttons.php';
        require base_path() . '/app/Macros/Images.php';
        require base_path() . '/app/Macros/Notifiers.php';
        require base_path() . '/app/Macros/FormFields.php';
        require base_path() . '/app/Macros/User.php';
        require base_path() . '/app/Macros/Icons.php';
        require base_path() . '/app/Macros/Workflow.php';
        require base_path() . '/app/Macros/Rights.php';
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Macros must be loaded after the HTMLServiceProvider's
        // register method is called. Otherwise, csrf tokens
        // will not be generated
        //parent::register();
    }
}
