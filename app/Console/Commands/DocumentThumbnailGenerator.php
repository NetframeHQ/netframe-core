<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Netframe\Media\DocumentThumbnail\Generator;
use Netframe\Media\DocumentThumbnail\OfficeConverterGenerator;
use App\Media;

class DocumentThumbnailGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:thumbnail:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate missing document thumbnails';

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
     * @param Generator $thumbnailGenerator
     * @return mixed
     */
    public function handle(OfficeConverterGenerator $thumbnailGenerator)
    {
        $command = $this;
        $medias = Media::where('encoded', 0)
            ->where('type', Media::TYPE_DOCUMENT)
            // exclu les fichiers excel ou les feuilles de calcul
            ->where('mime_type', 'not like', '%excel%')
            ->where('mime_type', 'not like', '%sheet%')
            ->get();

        // vérouille les médias pour ne pas lancer deux fois la génération d'une miniature dessus
        $medias->each(function ($media) {
            $media->encoded = 2;
            $media->save();
        });

        $medias->each(function ($media) use ($thumbnailGenerator, $command) {

            try {
                $this->line(sprintf('Generate thumbnail for document #%s %s', $media->id, $media->file_path));
                $thumbnailGenerator
                    ->execute($media) // génère une miniature à partir du document
                    ->applyTo($media) // enrichie le document avec les infos de la miniature
                    ->save()
                ;
            } catch (\Exception $e) {
                $media->update(['encoded' => 3]);
                // impossible de génrer la miniature
                $this->error($e->getMessage());
            }
        });
    }
}
