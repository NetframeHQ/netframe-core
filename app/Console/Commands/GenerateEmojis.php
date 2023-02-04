<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\EmojisGroup;
use File;

class GenerateEmojis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:emojis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate emojis json feed';

    private $imagine;

    /**
     * Create a new command instance.
     *
     * @return void
     */

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $emojisGroups = EmojisGroup::orderBy('order')->with('emojis')->get();

        $tabEmojis = [];
        foreach ($emojisGroups as $group) {
            $subTab = [];
            foreach ($group->emojis as $emoji) {
                $subTab[$emoji->id] = $emoji->value;
            }

            $tabEmojis[$group->id] = [
                'order' => $group->order,
                'name' => $group->name,
                'emojis' => $subTab,
            ];
        }

        File::put(storage_path('emojis.json'), json_encode($tabEmojis));
    }
}
