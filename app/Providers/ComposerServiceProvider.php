<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('channel.main', 'App\Http\ViewComposers\Channels\MainMenu');
        View::composer('components.emojis.emojis', 'App\Http\ViewComposers\Emojis');
        View::composer('components.left-sidebar', 'App\Http\ViewComposers\Sidebar\LeftSidebar');
        View::composer('components.messages.new-with-list', 'App\Http\ViewComposers\Messages\NewWithList');
        View::composer('components.mosaic-feed', 'App\Http\ViewComposers\MosaicFeed');
        View::composer('components.navigation', 'App\Http\ViewComposers\Navigation');
        View::composer('components.sidebar', 'App\Http\ViewComposers\Sidebar\SidebarProfile');
        View::composer('components.sidebar.bookmark', 'App\Http\ViewComposers\Sidebar\Bookmark');
        View::composer('components.sidebar.event', 'App\Http\ViewComposers\Sidebar\Event');
        View::composer('components.sidebar.last-activity', 'App\Http\ViewComposers\Sidebar\LastActivity');
        View::composer('components.sidebar.mobile-sidebar-user', 'App\Http\ViewComposers\Sidebar\MobileSidebarUser');
        View::composer('components.sidebar.notifications-user', 'App\Http\ViewComposers\Sidebar\NotificationUser');
        View::composer('components.sidebar.playlist', 'App\Http\ViewComposers\Sidebar\Playlist');
        View::composer('components.sidebar.profile-mosaic', 'App\Http\ViewComposers\Sidebar\ProfileMosaic');
        View::composer('components.sidebar.profile-project', 'App\Http\ViewComposers\Sidebar\ProfileProject');
        View::composer('components.sidebar-user', 'App\Http\ViewComposers\Sidebar\SidebarUser');
        View::composer('components.thumbs.display', 'App\Http\ViewComposers\ThumbDisplay');
        View::composer('components.toolbar.mobile-sidebar', 'App\Http\ViewComposers\Toolbar\MobileSidebar');
        View::composer('page.type-content.profile', 'App\Http\ViewComposers\TypeContentProfile');
        View::composer('page.type-content.medias.medias', 'App\Http\ViewComposers\Medias\ScreenImage');
        View::composer('posting.content-types.link-preview', 'App\Http\ViewComposers\PostLinkPreview');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
