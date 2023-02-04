<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Tag;

class TagController extends BaseController
{
    public function page($tagId, $tagSlug)
    {
        $searchRoute = url()->route('search');
        return redirect()->to($searchRoute.'?loadFilters=0&query='.$tagSlug);

        /*
        $tag = Tag::find($tagId);

        if($tag->instances_id != session('instanceId')){
            return response(view('errors.403'), 403);
        }

        $tagRelations = [];
        foreach($tag->functionsRelations as $functionName=>$relatedObjects){
            $tagRelations[$relatedObjects] = $tag->$functionName->take(5);
        }

        //find tags relations
        $data = [];
        $data['tag'] = $tag;
        $data['tagRelations'] = $tagRelations;

        return view('tags.page', $data);
        */
    }

    public function autocomplete($tagType = null)
    {
        $searchTerms = htmlentities(request()->get('q'));
        $tags = Tag::select('id', 'name as text')
            ->where('instances_id', '=', session('instanceId'))
            ->where('name', 'like', request()->get('q').'%');

        if ($tags->count() == 0) {
            $tags= Tag::select('id', 'name as text')
                ->where('instances_id', '=', session('instanceId'))
                ->where('name', 'like', '%'.request()->get('q').'%');
        }

        $results = $tags->get(array('id', 'text'));

        $tabRes = [];
        $searchInRes = false;
        foreach ($results as $result) {
            $objTag = new \stdClass();
            $objTag->id = $result->id;
            $objTag->text = $result->text;

            if ($result->text == $searchTerms) {
                $searchInRes = true;
            }
            $tabRes[] = $objTag;
        }

        if ($searchInRes == false) {
            $objTag = new \stdClass();
            $objTag->id = $searchTerms;
            $objTag->text = $searchTerms;
            $tabRes[] = $objTag;
        }

        if ($tags->count() != 0) {
            $data['results'] = $tabRes;
            return response()->json($data);
        } else {
            $data['results'][0] = new \stdClass();
            $data['results'][0]->id = $searchTerms;
            $data['results'][0]->text = $searchTerms;
            return response()->json($data);
        }
    }
}
