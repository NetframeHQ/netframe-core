<?php

namespace App\Helpers;

use App\Tag;

class TagsHelper
{
    public static function displayTags($tags)
    {
        if ($tags != null) {
            $tagArray = [];
            foreach ($tags as $tag) {
                $tagArray[] = $tag->name;
            }

            $tagDisplay = implode(', ', $tagArray);
            return $tagDisplay;
        } else {
            return null;
        }
    }

    public static function getFromForm($tags)
    {
        $returnTags = [];
        if ($tags != null) {
            foreach ($tags as $formTag) {
                if (is_numeric($formTag)) {
                    $returnTags[$formTag] = Tag::find($formTag)->name;
                } else {
                    $returnTags[$formTag] = $formTag;
                }
            }
        }

        return $returnTags;
    }

    public static function attachPostedTags($tags, $element)
    {
        // detach tags of element
        $element->tags()->detach();

        if ($tags != null) {
            foreach ($tags as $newTag) {
                if (is_numeric($newTag) && Tag::find($newTag) != null) {
                    $tag = Tag::find($newTag);
                } else {
                    // search if tag already register on preview element
                    $searchTag = Tag::where('name', '=', $newTag)
                        ->where('instances_id', '=', session('instanceId'))
                        ->where('lang', '=', \Lang::locale())
                        ->first();
                    if ($searchTag != null) {
                        $tag = $searchTag;
                    } else {
                        $tag = new Tag();
                        $tag->instances_id = session('instanceId');
                        $tag->name = $newTag;
                        $tag->lang = \Lang::locale();
                        $tag->users_id = auth()->guard('web')->user()->id;
                        $tag->save();
                    }
                }
                $taggable = new \App\Taggable();
                $taggable->tag_id = $tag->id;
                $taggable->taggable_type = get_class($element);
                $taggable->taggable_id = $element->id;
                $taggable->save();
                // $element->tags()->save($tag);
            }
        }
    }

    public static function compareTags($oldTags, $newTags)
    {
        $tags1 = Tag::whereIn('id', [1,2,3])->get();
        $tags2 = Tag::whereIn('id', [2,3,4])->get();

        return $diff = $newTags->diff($oldTags);
    }
}
