<?php

namespace App\Http\ViewComposers\Medias;

use Illuminate\View\View;

class ScreenImage
{

    public function __construct()
    {
    }

    public function compose(View $view)
    {
        $getData = $view->getData();
        $medias = (isset($getData['post']->post)) ?
            $getData['post']->post->medias :
            $getData['post']->medias;
        

        if (is_countable($medias)) {
            $i = count($medias);
            if (count($medias) == 1) {
                $viewScreenImage = 'page.type-content.medias.full-size';
            } else {
                /*
                if ( ($medias[0]->feed_width > $medias[0]->feed_height) == true){
                    if ( count($medias) == 2 ){
                        $viewScreenImage = 'page.type-content.medias.half-horizontal';

                    } elseif ( count($medias) == 3 ){
                        $i = 3;
                        $viewScreenImage = 'page.type-content.medias.many-img-horizontal';

                    } elseif ( count($medias) == 4 ){
                        $i = 4;
                        $viewScreenImage = 'page.type-content.medias.many-img-horizontal';

                    } else {
                        $viewScreenImage = 'page.type-content.medias.many-img-horizontal';
                    }
                } else {
                    if ( count($medias) == 2 ){
                        $viewScreenImage = 'page.type-content.medias.half-vertical';
                    } elseif ( count($medias) == 3 ){
                        $i = 3;
                        $viewScreenImage = 'page.type-content.medias.many-img-vertical';
                    } elseif ( count($medias) == 4 ){
                        $i = 4;
                        $viewScreenImage = 'page.type-content.medias.many-img-vertical';

                    } else {
                        $i = 5;
                        $viewScreenImage = 'page.type-content.medias.many-img-vertical';
                    }
                }
                */
                $viewScreenImage = 'page.type-content.medias.multi-medias';
            }

            return $view
                ->with('getData', $getData)
                ->with('viewScreenImage', $viewScreenImage)
                ->with('medias', $medias)
                ->with('i', $i);
        }
    }
}
