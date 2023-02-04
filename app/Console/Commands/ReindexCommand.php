<?php

namespace App\Console\Commands;

use App\Offer;
use App\Media;
use App\TEvent;
use App\News;
use App\User;
use App\Project;
use App\Channel;
use App\House;
use App\Community;
use Elasticsearch\Client as ElasticsearchClient;
use Illuminate\Console\Command;
use Illuminate\Console\OutputStyle;

class ReindexCommand extends Command
{
    /**
     * @var array
     */
    public static $defaultClasses = [
        'media'     => Media::class,
        'event'     => TEvent::class,
        'offer'     => Offer::class,
        'news'      => News::class,
        'project'   => Project::class,
        'channel'   => Channel::class,
        'house'     => House::class,
        'community' => Community::class,
        'user'      => User::class,
    ];

    /**
     * @var string
     */
    protected $signature = "search:reindex {classes?* : the types of models to reindex} "
                         . "{--rebuild : Rebuild types mapping from scratch} "
                         . "{--dry-run : Run the command without real effects on Elasticsearch}";

    /**
     * @var string
     */
    protected $description = "Indexes all search classes to elasticsearch";

    /**
     * @var ElasticsearchClient
     */
    private $search;

    /**
     * @param ElasticsearchClient $search
     */
    public function __construct(ElasticsearchClient $search)
    {
        parent::__construct();

        $this->search = $search;
    }

    public function handle()
    {
        $this->line('Indexing searchable classes to elasticsearch. Might take a while...');

        $classes = !empty($this->argument('classes'))

            // if a custom set of classes is specified
            ? array_reduce($this->argument('classes'), function ($carry, $item) {
                if (array_key_exists($item, ReindexCommand::$defaultClasses)) {
                    $carry[] = ReindexCommand::$defaultClasses[$item];
                }
                return $carry;
            })

            // index all searchable classes by default
            :  array_values(ReindexCommand::$defaultClasses);


        // make a simple count of all model to be indexed
        $count = 0;
        foreach ($classes as $class) {
            foreach ($class::cursor() as $model) {
                $count++;
            }
        }

        $displayProgressBar = OutputStyle::VERBOSITY_NORMAL === $this->output->getVerbosity();
        $displayDetails = OutputStyle::VERBOSITY_VERBOSE <= $this->output->getVerbosity();

        if ($displayProgressBar) {
            $bar = $this->output->createProgressBar($count);
        }

        $affected = 0;
        foreach ($classes as $class) {
            /* rebuild index settings if ask */
            if ($this->option('rebuild') && !$this->option('dry-run') && $class::first()) {
                try {
                    $indices = $this->search->indices();
                    $settings = $class::mapping();

                    $indices->delete(['index' => $settings['index']]);
                    $indices->create($settings);
                } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
                    $message = json_decode($e->getMessage());
                    if ('index_not_found_exception' === $message->error->type) {
                        // index does not exists
                    } else {
                        throw $e;
                    }
                }
            }

            /* reindex all entities */
            foreach ($class::cursor() as $model) {
                if (!isset($model->active) || $model->active==1) {
                    $line = sprintf($class."\t".$model->id."\t");

                    try {
                        if (!$this->option('dry-run')) {
                            $this->search->index([
                                'index' => $model->getSearchIndex(),
                                'type'  => $model->getSearchType(),
                                'id'    => $model->id,
                                'body'  => $model->toSearchArray(),
                            ]);
                        }
                        $this->lineIf($displayDetails, $line."[OK]");
                        $affected++;
                    } catch (\Exception $e) {
                        $this->lineIf($displayDetails, $line."[ERR]");
                        $this->error($e->getMessage());
                    }

                    if ($displayProgressBar) {
                        $bar->advance();
                    }
                }
            }
        }

        if ($displayProgressBar) {
            $bar->finish();
        }

        if ($affected===$count) {
            $this->info(PHP_EOL.'Done!');
            return true;
        } else {
            $this->info(PHP_EOL.sprintf('Done with %d errors!', $count-$affected));
            return false;
        }
    }

    private function lineif(bool $condition, string $text)
    {
        if ($condition) {
            $this->line($text);
        }
    }
}
