<?php

namespace Netframe\Media\DocumentThumbnail;

use NcJoes\OfficeConverter\OfficeConverter;
use NcJoes\OfficeConverter\OfficeConverterException;
use Spatie\PdfToImage\Pdf;
use Netframe\Media\Model\Media;

class OfficeConverterGenerator implements Generator
{
    const PREVIEW_PATH = 'uploads/documents/preview/';
    const PDF_PATH = 'uploads/documents/pdf/';

    public function __construct()
    {
        $storages = [
            storage_path(self::PREVIEW_PATH),
            storage_path(self::PDF_PATH),
        ];

        array_walk(
            $storages,
            function ($path) {
                if (!file_exists($path)) {
                    \File::makeDirectory($path, 0775, true);
                }
                return $path;
            }
        );
    }

    public function execute(Media $media): Thumbnail
    {
        $unlink = false;
        $pdf_generated = false;
        $pdf_version = $media->file_path;
        $thumb_uniqid = uniqid();
        $pathinfo = pathinfo($media->file_path);
        $thumb_path = storage_path(self::PREVIEW_PATH."thumbs-".$thumb_uniqid.".jpg");

        try {
            /* Si le document n'est pas de base un PDF, on en fait une version PDF */
            if ('pdf' !== $pathinfo['extension']) {
                $pdf_version = sprintf('%s-%s.pdf', $pathinfo['filename'], $thumb_uniqid);

                $converter = new OfficeConverter($media->file_path);
                $converter->convertTo('pdf/'.$pdf_version);

                $pdf_version = storage_path(self::PDF_PATH.$pdf_version);
                $pdf_generated = $unlink = true; // supprimer le pdf généré si la vignette échoue
            }

            /* On génère une image à partir du document au format PDF */
            $pdf = new Pdf($pdf_version);
            $pdf->saveImage($thumb_path);
        } catch (OfficeConverterException $e) {
            throw new \Exception('File conversion to pdf not supported => no thumbnail');
        } finally {
            if ($pdf_generated && $pdf_generated) {
                // unlink($pdf_version);
            }
        }

        return new Thumbnail(
            $thumb_path,
            // si on a du créer un pdf pour générer la miniature, on le conserve
            $pdf_generated ? $pdf_version : null
        );
    }
}
