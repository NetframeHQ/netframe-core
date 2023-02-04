<?php

View::composer('media::attachment.main', function ($view) {
    $mediaManager = resolve('media.manager');
    $user = auth()->guard('web')->user();

    $talents = \DB::table('talents')
        ->leftJoin('ref_categories', 'ref_categories_id', '=', 'ref_categories.id')
        ->where('talents.users_id', '=', $user->id)
        ->get(array('talents.id', 'ref_categories.name'));

    $view->with('importers', $mediaManager->getImporters());
    $view->with('talents', $talents);
});

View::composer('media::attachment.upload', function ($view) {
    $mediaManager = resolve('media.manager');
    $view->with('importers', $mediaManager->getImporters());
});

View::composer('media::attachment.import', function ($view) {
    $mediaManager = resolve('media.manager');
    $view->with('importers', $mediaManager->getImporters());
});
