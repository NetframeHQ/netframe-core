<?php

namespace App\Helpers;

use App\Profile;
use App\User;

class StringHelper
{
    /**
     * Transforms a comma separated string to an array.
     *
     * @param string $string
     *
     * @return array
     */
    public static function toArray($string)
    {
        if (false !== strpos($string, ',')) {
            return explode(',', $string);
        } elseif (!empty($string)) {
            return array($string);
        }

        return array();
    }


    /**
     * Convert Array in string separate by comma or another symbol
     *
     * @param array $array
     * @param string $arrayKey name key from array if exist
     * @param string $symbol
     * @return string formated separate by symbol given
     */
    public static function separateBy($array, $arrayKey = null, $symbol = ", ")
    {
        $html = implode($symbol, array_map(function ($o) use ($arrayKey) {

            if (is_null($arrayKey)) {
                return $o;
            } else {
                return $o[$arrayKey];
            }
        }, $array));

        return $html;
    }



    /**
     * Build uri Home for user and return a string for an url
     *
     * @param int $id default null
     * @return string
     */
    public static function uriHomeUser($id = null)
    {
        if ($id) {
            $user = User::find($id);

            return "user/" . $user->slug
                . '/' . strtolower(str_slug($user->firstname)
                . '-' . str_slug($user->name));
        } else {
            if (auth()->guard('web')->check()) {
                $slug = auth()->guard('web')->user()->slug;
                $fullname = str_slug(auth()->guard('web')->user()->firstname)
                    . '-' . str_slug(auth()->guard('web')->user()->name);

                return "user/" . $slug .'/'.strtolower($fullname);
            }
        }
    }


    public static function uriHomeUserObject($user)
    {
        return "/user/". $user->slug.'/'.strtolower(str_slug($user->firstname).'-'.str_slug($user->name));
    }

    public static function getUrlUnitPost($profile, $postId)
    {
        switch ($profile->getType()) {
            case Profile::TYPE_HOUSE:
                return url()->route('page.house', [$profile->id, str_slug($profile->name), $postId]);
                break;

            case Profile::TYPE_COMMUNITY:
                return url()->route('page.community', [$profile->id, str_slug($profile->name), $postId]);
                break;

            case Profile::TYPE_PROJECT:
                return url()->route('page.project', [$profile->id, str_slug($profile->title), $postId]);
                break;

            case Profile::TYPE_USER:
                $userFullName = str_slug($profile->firstname).'-'.str_slug($profile->name);
                return url()->route('profile.user', [$profile->slug, $userFullName, $postId]);
                break;
        }
    }


    public static function formatPostText($content)
    {
        $content = nl2br($content);
        $content = html_entity_decode($content);

        // Link attributes
        $attr = '';
        $attributes = array();
        $protocols = array('http', 'mail', 'https', 'twitter', 'netframe');

        foreach ($attributes as $key => $val) {
            $attr = ' ' . $key . '="' . htmlentities($val) . '"';
        }

        $links = array();

        // Extract existing links and tags
        $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
            return '<' . array_push($links, $match[1]) . '>';
        }, $content);

        // Extract text links for each protocol
        foreach ((array)$protocols as $protocol) {
            switch ($protocol) {
                case 'http':
                case 'https':
                    $content = preg_replace_callback(
                        '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i',
                        function ($match) use ($protocol, &$links, $attr) {
                            if ($match[1]) {
                                $protocol = $match[1];
                            }
                            $link = $match[2] ?: $match[3];
                            $linkText = (mb_strlen($link, mb_detect_encoding($link)) > 25)
                                ? mb_substr($link, 0, 25, mb_detect_encoding($link))."..."
                                : $link;
                            return '<' . array_push(
                                $links,
                                "<a $attr href=\"$protocol://$link\" target=\"_blank\">$linkText</a>"
                            ) . '>';
                        },
                        $content
                    );
                    break;
                case 'mail':
                    $content = preg_replace_callback(
                        '~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~',
                        function ($match) use (&$links, $attr) {
                            return '<' . array_push(
                                $links,
                                "<a $attr href=\"mailto:{$match[1]}\" target=\"_blank\">{$match[1]}</a>"
                            ) . '>';
                        },
                        $content
                    );
                    break;
                //case 'twitter':
                //    $content = preg_replace_callback(
                //        '~(?<!\w)[@#](\w++)~',
                //        function ($match) use (&$links, $attr) {
                //            return '<' . array_push(
                //                $links,
                //                "<a $attr href=\"https://twitter.com/"
                //                    . ($match[0][0] == '@' ? '' : 'search/%23')
                //                    . $match[1]
                //                    . "\" target=\"_blank\">{$match[0]}</a>"
                //            ) . '>';
                //        },
                //        $content
                //    );
                //    break;
                //case 'twitter':
                //    $content = preg_replace_callback(
                //        '~(?<!\w)[@](\w++)~',
                //        function ($match) use (&$links, $attr) {
                //            return '<' . array_push(
                //                $links,
                //                "<a $attr href=\"https://twitter.com/"
                //                    . ($match[0][0] == '@' ? '' : 'search/%23')
                //                    . $match[1]
                //                    . "\" target=\"_blank\">{$match[0]}</a>"
                //            ) . '>';
                //        },
                //        $content
                //    );
                //    break;
                case 'netframe':
                    $content = preg_replace_callback(
                        '~(?<!\w)[#](\w++)~',
                        function ($match) use (&$links, $attr) {
                            return '<' . array_push(
                                $links,
                                "<a $attr href=\"/search?query=%23"
                                    . $match[1]
                                    . "&loadFilters=0&hashtag="
                                    . $match[1]
                                    . "\">{$match[0]}</a>"
                            ) . '>';
                        },
                        $content
                    );
                    break;
                default:
                    $content = preg_replace_callback(
                        '~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i',
                        function ($match) use ($protocol, &$links, $attr) {
                            return '<' . array_push(
                                $links,
                                "<a $attr href=\"$protocol://{$match[1]}\" target=\"_blank\">{$match[1]}</a>"
                            ) . '>';
                        },
                        $content
                    );
                    break;
            }
        }

        // user tag old mentions (before implementation of GED finder into netfrme 2021-10)
        $content = preg_replace_callback('~(@\[(.*?)\]\(([0-9]*)\))~i', function ($match) use (&$links) {
            $user = User::find($match[3]);
            return '<a class="user-taggued" href="'.$user->getUrl().'">' . $match[2] . '</a>';
        }, $content);

        // profile tag
        $content = preg_replace_callback(
            '~(@\[(.*?)\]\(([user|house|community|project|channel|users|houses|projects|channels]*):([0-9]*)\))~i',
            function ($match) use (&$links) {
                $profile = Profile::gather($match[3]);
                $profile = $profile::find($match[4]);
                return '<a class="user-taggued" href="'.$profile->getUrl().'">' . $match[2] . '</a>';
            },
            $content
        );


        // Insert all link
        return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
            return $links[$match[1] - 1];
        }, $content);

        return $content;
    }

    public static function removeMentionsTag($content)
    {
        $content = preg_replace_callback(
            '~(@\[(.*?)\]\(([user|house|community|project|channel|users|houses|projects|channels]*):([0-9]*)\))~i',
            function ($match) use (&$links) {
                $user = User::find($match[3]);
                return $match[2];
            },
            $content
        );
        return $content;
    }

    public static function collapsePostText($content, $maxLength = 200)
    {
        $content = html_entity_decode($content);
        $contentLength = mb_strlen($content);
        if ($contentLength > $maxLength) {
            $uniqId = uniqid('moreContent');
            $resumeContent = self::resumeContent($content, $maxLength);
            $collapse = '<p class="post-content">'.$resumeContent;
            $collapse .= '...<a href="#" class="fn-switch-post-content" data-target="#'.$uniqId.'">'
                . trans('page.readMore')
                . '</a></p>';
            $collapse .= "<p id='" . $uniqId . "' class='collapse'>".
                self::formatPostText($content) . "</p>";
            return $collapse;
        } else {
            return self::formatPostText($content);
        }
    }

    public static function resumeContent($content, $maxLength)
    {
        $resumeContent = self::removeMentionsTag($content);
        $resumeContent = html_entity_decode($resumeContent);
        $resumeContent = mb_substr($resumeContent, 0, $maxLength);
        return $resumeContent;
    }

    public static function xsTextModal($content, $route, $maxLength = 200)
    {
        $contentLength = mb_strlen($content);
        if ($contentLength > $maxLength) {
            $resumeContent = mb_substr($content, 0, $maxLength);
            $text = '<p class="visible-xs">'.$resumeContent;
            $text .= '...<a href="'.$route.'" class="" data-toggle="modal" data-target="#modal-ajax">'
                . trans('page.readMore')
                . '</a></p>';
            //$collapse .= "<p id='".$uniqId."' class='collapse'>"
            //    . \App\Helpers\StringHelper::formatPostText($content)."</p>";
            $text .= '<p class="hidden-xs">'.$content.'</p>';
            return $text;
        } else {
            return $content;
        }
    }

    public static function formatMetaText($content, $maxLength = 200)
    {
        $contentLength = mb_strlen($content);
        if ($contentLength > $maxLength) {
            $content = str_replace(array("\n", "\r"), '', $content);
            $content = mb_substr($content, 0, $maxLength).'...';
        }
        return $content;
    }

    public static function formatCbExpDate($expDate)
    {
        $month = substr($expDate, 0, 2);
        $year = substr($expDate, 2, 2);
        return $month."/".$year;
    }

    public static function formatPhoneNumber($phoneNumber)
    {
        $i=0;
        $j=0;
        $formate = "";
        while ($i<strlen($phoneNumber)) { //tant qu il y a des caracteres
            if ($j < 2) {
                if (preg_match('/^[0-9]$/', $phoneNumber[$i])) { //si on a bien un chiffre on le garde
                    $formate .= $phoneNumber[$i];
                    $j++;
                }
                $i++;
            } else { //si on a mis 2 chiffres a la suite on met un espace
                $formate .= " ";
                $j=0;
            }
        }
        return $formate;
    }
}
