<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\BaseController;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use App\Media;
use App\Playlist;
use App\PlaylistItem;
use App\PlaylistItemProfile;
use App\Profile;
use App\Project;
use App\House;
use App\Community;
use App\user;
use App\Events\NewAction;
use App\Events\InterestAction;
use App\Events\SocialAction;

class PlaylistController extends BaseController
{
    public function __construct()
    {
        $this->middleware('checkAuth');
        parent::__construct();
    }

    /**
     * Deletes a playlist item it.
     *
     * @param integer $itemId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteItem($id)
    {
        $user = auth()->guard('web')->user();
        $item = PlaylistItem::findOrFail($id);
        $playlist = $item->playlist;

        if ($playlist->users_id !== $user->id) {
            throw new UnauthorizedHttpException();
        }

        $item->delete();
        \App\Helpers\ActionMessageHelper::success(trans('playlist.item_success_delete'));

        return response()->json(array(
            'delete' => true,
            'targetId' => "#playlist-item-{$id}"
        ));
    }

    /**
     * Shows the given playlist.
     *
     * @param integer|string $playlistIdOrInstant
     *
     * @return \Illuminate\View\View
     *
     * @throws AccessDeniedException
     */
    public function show($id)
    {
        if ('instant' !== $id) {
            $user = auth()->guard('web')->user();
            if ('instant' === $id) {
                $playlist = Playlist::where('instant_playlist', '=', 1)
                    ->where('users_id', '=', $user->id)
                    ->first();

                // The playlist does not exists because no item was bookmarked yet
                if (null === $playlist) {
                    //throw new NotFoundHttpException();
                    return view('playlist.show.main', array(
                        'noplaylist' => 1
                    ));
                }
            } else {
                $playlist = Playlist::findOrFail($id);
            }

            if (auth()->guard('web')->check() && $playlist->users_id == auth()->guard('web')->user()->id) {
                $playlist->items()
                ->update(['read_owner' => 1]);
            }

            if ($playlist->instances_id != session('instanceId')) {
                return response(view('errors.403'), 403);
            }

            // Gets the media type filter value
            $mediaTypeFilter = request()->get('filter_media_type', 'all');

            if (null !== $mediaTypeFilter && 'all' !== $mediaTypeFilter) {
                switch ($mediaTypeFilter) {
                    case 'audios':
                        $mediaType = Media::TYPE_AUDIO;
                        break;

                    case 'videos':
                        $mediaType = Media::TYPE_VIDEO;
                        break;

                    case 'images':
                    default:
                        $mediaType = Media::TYPE_IMAGE;
                        break;
                }

                $playlistItems = $playlist->items()
                    ->join('medias', 'medias.id', '=', 'playlists_items.medias_id')
                    ->where('medias.type', '=', $mediaType);
            } else {
                $playlistItems = $playlist->items();
            }

            $playlistItems->orderBy('playlists_items.created_at', 'DESC');

            // Get the other playlists created by the user
            if (auth()->guard('web')->check()) {
                $otherPlaylists = Playlist::where('instances_id', '=', session('instanceId'))
                    ->where('users_id', '=', $user->id)
                    ->where('id', '!=', $playlist->id)
                    ->orderBy('created_at', 'DESC')
                    ->get();
            } else {
                $otherPlaylists = null;
            }

            return view('playlist.show.main', array(
                'noplaylist' => 0,
                'playlist' => $playlist,
                'otherPlaylists' => $otherPlaylists,
                'playlistItems' => $playlistItems->getResults(),
                'mediaTypeFilter' => $mediaTypeFilter,
            ));
        }
    }

    /**
     * Bookmarks a profile as the current user in the instant playlist.
     *
     * @param string  $profileType The profile type Profile::TYPE_* to bookmark
     * @param integer $profileId   The profile id to bookmark
     */
    public function instantBookmarkProfileAsUser($profileType, $profileId)
    {
        $this->ensureProfileExists(studly_case($profileType), $profileId);

        $profile = Profile::gather($profileType)->find($profileId);
        if ($profile->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $dataJson = array();

        $instant = $this->createOrGetUserInstantPlaylist();

        // Add the item to the playlist if not already existing
        $item = PlaylistItem::where('playlists_id', '=', $instant->id)
            ->where('users_id', '=', auth()->guard('web')->user()->id)
            ->where('instances_id', '=', session('instanceId'))
            ->where('profile_id', '=', $profileId)
            ->where('profile_type', '=', "App\\".studly_case($profileType))
            ->where('medias_id', '=', '0')
            ->get();

        if (null === $item || count($item) == 0) {
            $item = new PlaylistItem();
            $item->instances_id = session('instanceId');
            $item->users_id = auth()->guard('web')->user()->id;
            $item->playlists_id = $instant->id;
            $item->profile_id = $profileId;
            $item->profile_type = "App\\".studly_case($profileType);
            $item->medias_id = '0';
            $item->save();

            //get cat of bookmarked profile and insert interest
            event(new NewAction(
                'bookmark',
                $profileId,
                studly_case($profileType),
                auth()->guard('web')->user()->id,
                'user'
            ));
            event(new InterestAction(auth()->guard('web')->user(), $profile->tags, 'profile.like'));

            //insert notification
            $profile = $item->profile;
            event(new SocialAction($profile->users_id, $profile->id, get_class($profile), 'clipProfile'));

            $dataJson['result'] = 'add';
        } else {
            //remove instant bookmark

            foreach ($item as $pl) {
                $pl->delete();
            }
            $dataJson['result'] = 'remove';
        }

        return response()->json($dataJson);
    }

    /**
     * Bookmarks a profile media as user.
     *
     * @param string  $profileType The profile type Profile::TYPE_*
     * @param integer $profileId   The profile id
     * @param integer $mediaId     The id of the media to bookmark
     */
    public function instantBookmarkProfileMediaAsUser($profileType, $profileId, $mediaId)
    {
        $this->ensureProfileExists(studly_case($profileType), $profileId);

        // Ensure the media exists
        $media = Media::findOrFail($mediaId);
        if ($media->instances_id != session('instanceId')) {
            return response(view('errors.403'), 403);
        }

        $dataJson = array();

        $instant = $this->createOrGetUserInstantPlaylist();

        // Add the item to the playlist if not already existing
        $item = PlaylistItem::where('playlists_id', '=', $instant->id)
            ->where('users_id', '=', auth()->guard('web')->user()->id)
            ->where('instances_id', '=', session('instanceId'))
            ->where('profile_id', '=', $profileId)
            ->where('profile_type', '=', "App\\".studly_case($profileType))
            ->where('medias_id', '=', $mediaId)
            ->get();

        if (null === $item || count($item) == 0) {
            $item = new PlaylistItem();
            $item->users_id = auth()->guard('web')->user()->id;
            $item->instances_id = session('instanceId');
            $item->playlists_id = $instant->id;
            $item->profile_id = $profileId;
            $item->profile_type = "App\\".studly_case($profileType);
            $item->medias_id = $mediaId;
            $item->save();

            event(new NewAction('bookmark_media', $mediaId, 'Media', auth()->guard('web')->user()->id, 'user'));

            //get cat of bookmarked profile and insert interest
            $profile = Profile::gather($profileType)->find($profileId);
            if (strtolower($profileType) != 'user') {
                event(new InterestAction(auth()->guard('web')->user(), $profile->tags, 'profile.like'));
            }
            $dataJson['result'] = 'add';

            //insert notification
            $media = $item->media;
            event(new SocialAction($media->users_id, $media->id, 'App\\Media', 'clipMedia'));
        } else {
            //remove instant bookmark
            foreach ($item as $pl) {
                $pl->delete();
            }
            $dataJson['result'] = 'remove';
        }

        return response()->json($dataJson);
    }

    /**
     * Ensure the given profile exists.
     *
     * @param string  $profileType
     * @param integer $profileId
     */
    private function ensureProfileExists($profileType, $profileId)
    {
        switch ($profileType) {
            case studly_case(Profile::TYPE_HOUSE):
                House::findOrFail($profileId);
                break;

            case studly_case(Profile::TYPE_COMMUNITY):
                Community::findOrFail($profileId);
                break;

            case studly_case(Profile::TYPE_PROJECT):
                Project::findOrFail($profileId);
                break;

            case studly_case(Profile::TYPE_USER):
                User::findOrFail($profileId);
                break;

            default:
                throw new NotFoundHttpException();
        }
    }

    /**
     * Creates or get the user instant playlist.
     *
     * @return \Playlist
     */
    private function createOrGetUserInstantPlaylist()
    {
        $user = auth()->guard('web')->user();

        $instant = Playlist::where('instances_id', '=', session('instanceId'))
            ->where('users_id', '=', $user->id)
            ->where('author_id', '=', $user->id)
            ->where('author_type', '=', 'User')
            ->where('instant_playlist', '=', 1)
            ->first();

        if (null === $instant) {
            $instant = new Playlist();
            $instant->users_id = $user->id;
            $instant->instances_id = session('instanceId');
            $instant->author_id = $user->id;
            $instant->author_type = 'User';
            $instant->name = 'Playlist';
            $instant->instant_playlist = 1;

            $instant->save();
        }

        return $instant;
    }

    /**
     * Creates a new playlist.
     */
    public function create()
    {
        $user = auth()->guard('web')->user();
        $inputs = request()->all();

        $validator = validator($inputs, array(
            'name' => 'required',
            'author_id' => 'required',
            'author_type' => 'required',
        ));

        $data['inputs'] = $inputs;

        if ($validator->fails()) {
            return response()->json(array(
                'view' => view('playlist.show.create-modal', $data)->render(),
            ));
        }

        $playlist = new Playlist();
        $playlist->users_id = $user->id;
        $playlist->instances_id = session('instanceId');
        $playlist->author_id = $inputs['author_id'];
        $playlist->author_type = studly_case($inputs['author_type']);
        $playlist->name = $inputs['name'];
        $playlist->description = $inputs['description'];
        $playlist->instant_playlist = 0;

        $playlist->save();

        \App\Helpers\ActionMessageHelper::success(trans('playlist.playlist_success_create'));

        return response()->json(array(
                        'view' => view('playlist.show.create-modal', $data)->render(),
                        'redirect' => request()->get('httpReferer')
            ));
    }

    /**
     * Adds an item to a playlist.
     *
     * @param integer $id     The playlist id
     * @param integer $itemId The item id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addItem($id, $itemId)
    {
        $playlist = Playlist::findOrFail($id);
        $item = PlaylistItem::findOrFail($itemId);
        $user = auth()->guard('web')->user();
        $redirectPlaylistId = request()->get('redirect_playlist_id', $id);

        if ($playlist->users_id !== $user->id
            || $playlist->instances_id != session('instanceId')
            || $item->instances_id != session('instanceId')) {
            throw new AccessDeniedException();
        }

        $item->playlists_id = $id;
        $item->read_owner = $id;
        $item->save();

        \App\Helpers\ActionMessageHelper::success(
            sprintf(trans('playlist.item_add_success'), $playlist->name)
        );

        return response()->json(array(
            'move' => true,
            'targetId' => "#playlist-item-{$itemId}"
        ));
    }

    /**
     * Deletes a playlist.
     *
     * @param integer $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        $user = auth()->guard('web')->user();
        $playlist = Playlist::findOrFail($id);
        $redirectPlaylistId = request()->get('redirect_playlist_id', $id);

        if ($playlist->users_id !== $user->id) {
            throw new UnauthorizedHttpException();
        }

        $items = $playlist->items;
        foreach ($items as $item) {
            $item->delete();
        }

        $playlist->delete();

        \App\Helpers\ActionMessageHelper::success(trans('playlist.delete_success'));

        return response()->json(array(
            'delete' => true,
            'targetId' => "#playlist-{$id}"
        ));
    }

    public function edit($id = null)
    {
        return view('playlist.show.create-modal');
    }
}
