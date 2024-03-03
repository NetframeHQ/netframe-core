<?php
namespace App\Http\Controllers\Instance;

use App\Http\Controllers\BaseController;
use Symfony\Component\HttpFoundation\Response;
use App\Instance;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Imagine\Image\Point\Center;
use Imagine\Image\Metadata\ExifMetadataReader;
use App\MediasFolder;
use App\Helpers\ColorsHelper;

class GraphicalController extends BaseController
{
    private $imagine;

    public function __construct(ImagineInterface $imagine)
    {
        $this->middleware('instanceManager', ['except' => 'download']);
        parent::__construct();
        $this->imagine = $imagine;
    }

    /*
     *
     * change instance theme
     *
     */
    public function selectTheme($slug)
    {
        $themes = config('themes.themes');
        $instance = Instance::find(session('instanceId'));
        if (isset($themes[$slug])) {
            $instance->setParameter('css_theme', $slug);
        }

        // recompile custom css of theme
        $instance->compileCustomCss();

        return redirect()->route('instance.graphical');
    }

    public function graphical($customType = null)
    {
        $data = [];
        $instance = Instance::find(session('instanceId'));
        $data['instance'] = $instance;

        $theme = $instance->getParameter('css_theme');
        if ($theme == null) {
            $instance->setParameter('css_theme', 'standard');
            $theme = 'standard';
        }

        /*
         * get themes for choosen theme or default
         * get cursom colors or default
         *
         */
        $mainTheme = ($instance->getParameter('css_theme') == null) ? 'standard' : $instance->getParameter('css_theme');
        $paramCss = json_decode($instance->getParameter('css_colors_2018'), true);
        $customCss = $instance->getParameter('custom_css_2018');

        $defaultCss = $themeParameters = config('themes.themes.' . $mainTheme);
        $cssColors = [];
        $cssColorsTheme = []; // used to reset colors
        if ($paramCss != null && $customCss && isset($paramCss[$theme])) {
            // manage custom color, with dark mode
            foreach ($paramCss[$theme] as $keyMode => $themeDefaults) {
                if (in_array($keyMode, ['dark', 'light'])) {
                    foreach ($themeDefaults as $cssClass => $defaultValue) {
                        $cssClass = ($keyMode == 'dark') ? $cssClass . 'Dark' : $cssClass;
                        $cssColors[$cssClass] = ColorsHelper::convertRgbToHex($defaultValue);
                    }
                }
            }
        }

        // add default colors if not set
        foreach ($defaultCss['baseColors'] as $keyMode => $themeDefaults) {
            foreach ($themeDefaults as $cssClass => $defaultValue) {
                $cssClass = ($keyMode == 'dark') ? $cssClass . 'Dark' : $cssClass;
                $cssColorsTheme[$cssClass] = ColorsHelper::convertRgbToHex($defaultValue);
                if (!isset($cssColors[$cssClass])) {
                    $cssColors[$cssClass] = ColorsHelper::convertRgbToHex($defaultValue);
                }
            }
        }

        $navTheme = $instance->getParameter('nav_theme');
        $navTheme = ($navTheme == null) ? 'light' : $navTheme;

        /*
         *
         * instance cover
         *
         */
        $coverImage = $instance->getParameter('cover_image');
        if ($coverImage == null) {
            $coverImage = '';
        } else {
            $coverImage = url()->route('instance.download', ['parametername' => 'cover_image']);
        }

        /*
         *
         * square logo light and dark
         *
         */
        $menuLogo = $instance->getParameter('menu_logo_2018');
        if ($menuLogo == null) {
            $menuLogo = asset('assets/img/logo.png');
        } else {
            $data['menuLogoFile'] = $menuLogo;
            $menuLogo = url()->route('instance.download', ['parametername' => 'menu_logo_2018']);
        }

        $menuLogoDark = $instance->getParameter('menu_logo_2018_dark');
        if ($menuLogoDark == null) {
            $menuLogoDark = $menuLogo;
        } else {
            $data['menuLogoFile'] = $menuLogoDark;
            $menuLogoDark = url()->route('instance.download', ['parametername' => 'menu_logo_2018_dark']);
        }

        /*
         *
         * rectangle logo light and dark
         *
         */
        $mainLogoDb = $instance->getParameter('main_logo_2018');
        if ($mainLogoDb == null) {
            $mainLogo = asset('assets/img/widget-logo.png');
        } else {
            $data['mainLogoFile'] = $mainLogoDb;
            $mainLogo = url()->route('instance.download', ['parametername' => 'main_logo_2018']);
        }

        $mainLogoDark = $instance->getParameter('main_logo_2018_dark');
        if ($mainLogoDark == null && $mainLogoDb == null) {
            $mainLogoDark = asset('assets/img/widget-logo-dark.png');
        } elseif ($mainLogoDark == null && $mainLogoDb != null) {
            $data['mainLogoFile'] = $mainLogoDb;
            $mainLogoDark = url()->route('instance.download', ['parametername' => 'main_logo_2018']);
        } else {
            $data['mainLogoFile'] = $mainLogoDark;
            $mainLogoDark = url()->route('instance.download', ['parametername' => 'main_logo_2018_dark']);
        }

        // background parameters
        $bgScreenInstance = $instance->getParameter('background_login_2018');
        if ($bgScreenInstance != null) {
            $data['bgScreenFile'] = $bgScreenInstance;
            $bgScreen = url()->route('instance.download', ['parametername' => 'background_login_2018']);
        } else {
            $bgScreen = null;
        }

        if (request()->isMethod('POST')) {
            if ($customType == 'theme') {
                $mainTheme = request()->get('theme');
                $instance->setParameter('css_theme', $mainTheme);
                $instance->setParameter('custom_css_2018', 0);
                // check for custom login image
            } elseif ($customType=='buttons') {
                $values = request()->get('reactions');
                $reactions = str_split($values, 4);
                if (count($reactions)!=5) {
                    return redirect()->route('instance.graphical')
                        ->withInput()
                        ->withErrors(['reactions'=>'buttonsSize']);
                }

                $emojis = \App\Emoji::selectRaw('id')
                    ->whereRaw("value in ('".implode('\',\'', $reactions)."' COLLATE utf8mb4_bin)")
                    ->get();
                $emojis = array_map(function ($emoji) {
                    return $emoji['id'];
                }, $emojis->toArray());

                    $instance->setParameter('like_buttons', json_encode($emojis));

                    return redirect()->route('instance.graphical');
            } else {
                if ($customType == 'colors') {
                    // get instance theme
                    $cssColors = json_decode($instance->getParameter('css_colors_2018'), true);
                    if (!isset($cssColors[$theme])) {
                        $cssColors[$theme] = [];
                    }
                    if (!isset($cssColors[$theme]['light'])) {
                        $cssColors[$theme]['light'] = [];
                    }
                    if (!isset($cssColors[$theme]['dark'])) {
                        $cssColors[$theme]['dark'] = [];
                    }
                    $cssColors[$theme]['disableMode'] = request()->get('disableMode');

                    $primaryColor = ColorsHelper::convertHexToRgb(request()->get('primaryColor'));
                    $accentColor = ColorsHelper::convertHexToRgb(request()->get('accentColor'));
                    $bgColor = ColorsHelper::convertHexToRgb(request()->get('bgColor'));
                    $primaryColorDark = ColorsHelper::convertHexToRgb(request()->get('primaryColorDark'));
                    $accentColorDark = ColorsHelper::convertHexToRgb(request()->get('accentColorDark'));
                    $bgColorDark = ColorsHelper::convertHexToRgb(request()->get('bgColorDark'));

                    $cssColors[$theme]['light']['bgColor'] = $bgColor;
                    $cssColors[$theme]['light']['primaryColor'] = $primaryColor;
                    $cssColors[$theme]['light']['accentColor'] = $accentColor;
                    $cssColors[$theme]['dark']['bgColor'] = $bgColorDark;
                    $cssColors[$theme]['dark']['primaryColor'] = $primaryColorDark;
                    $cssColors[$theme]['dark']['accentColor'] = $accentColorDark;
                    $paramCss = $instance->setParameter('custom_css_2018', 1);
                }

                if ($customType == 'backgrounds') {
                    // get active parameter for background
                    $activeBgLogin = request()->get('activeBgLogin');
                    $activeBgScreen = request()->get('activeBgScreen');
                    if ($activeBgLogin == 1) {
                        if ($bgLoginInstance != null) {
                            $cssColors['loginBackground'] = url()->route(
                                'instance.download',
                                ['parametername' => 'background_login_2018']
                            );
                        } else {
                            $cssColors['loginBackground'] = '../img/login-background.jpg';
                        }
                    }
                    $paramCss = $instance->setParameter('active_bg_login_2018', $activeBgLogin);

                    if ($activeBgScreen == 1) {
                        if ($bgScreenInstance != null) {
                            $cssColors['screenBackground'] = url()->route(
                                'instance.download',
                                ['parametername' => 'background_screen_2018']
                            );
                        } else {
                            $cssColors['screenBackground'] = '../img/page-background.gif';
                        }
                    }
                    $paramCss = $instance->setParameter('active_bg_screen_2018', $activeBgScreen);

                    $cssColors['loginBgColor'] = request()->get('loginBgColor');
                    $cssColors['screenBgColor'] = request()->get('screenBgColor');
                    $paramCss = $instance->setParameter('custom_css_2018', 1);
                }

                $paramCss = $instance->setParameter('css_colors_2018', json_encode($cssColors));

                $instance->compileCustomCss();
            }
            return redirect()->route('instance.graphical');
        }

        $defaultMenuLogo = asset('assets/img/logo.png');
        $defaultMainLogo = asset('assets/img/widget-logo.png');
        $defaultMainLogoDark = asset('assets/img/widget-logo-dark.png');
        $data['cssColors'] = $cssColors;
        $data['paramCss'] = (isset($paramCss[$mainTheme])) ? $paramCss[$mainTheme] : null;
        $data['cssColorsTheme'] = $cssColorsTheme;
        $data['coverImage'] = $coverImage;
        $data['mainLogo'] = $mainLogo;
        $data['menuLogo'] = $menuLogo;
        $data['menuLogoDark'] = $menuLogoDark;
        $data['mainLogoDark'] = $mainLogoDark;
        $data['defaultMainLogo'] = $defaultMainLogo;
        $data['defaultMainLogoDark'] = $defaultMainLogoDark;
        $data['defaultMenuLogo'] = $defaultMenuLogo;
        $data['bgScreen'] = $bgScreen;
        $data['navTheme'] = $navTheme;
        $data['mainThemeCss'] = $mainTheme;
        $data['themeParameters'] = $themeParameters;
        $data['allThemes'] = config('themes');
        $data['currentTheme'] = $theme;

        return view('instances.graphical', $data);
    }



    public function upload()
    {
        $data = [];
        $instance = Instance::find(session('instanceId'));
        $mediasConfig = config('instances.medias');
        $data['instance'] = $instance;

        $mediaType = request()->get('mediaType');
        $file = request()->file('file');
        $mimeType = $file->getMimeType();
        if (in_array($mimeType, $mediasConfig['supportedMimes'])) {
            $uploadConfig = $mediasConfig['mediaTypes'][$mediaType];

            $storage_dir = env('NETFRAME_DATA_PATH', base_path())
                . '/storage/uploads/instances/'
                . $instance->id
                . '/' . $uploadConfig['storageDir'];
            if (!file_exists($storage_dir)) {
                $result = \File::makeDirectory($storage_dir, 0775, true);
            }

                // save file to disk
                $extension = strtolower($file->getClientOriginalExtension());
                $newFileName = hash('sha1', $file->getClientOriginalName().microtime()) . '.' . $extension;
                $file->move($storage_dir, $newFileName);
                $this->resizeImage($storage_dir.'/'.$newFileName, $uploadConfig['maxSize']);

                //store in instances parameter with $uploadConfig['parameterName']
                $mediaParameters = json_encode([
                    "mime_type" => $mimeType,
                    "filename" => $newFileName,
                    "directory" => $uploadConfig['storageDir']
                ]);
                $paramCss = $instance->setParameter($uploadConfig['parameterName'], $mediaParameters);

                //return file name with download route including target parameter ($uploadConfig['parameterName'])
                return response()->json([
                    'filePreview' => '.'.$mediaType,
                    'filename' => url()->route(
                        'instance.download',
                        ['parametername' => $uploadConfig['parameterName'] ]
                    ),
                ]);
        }
    }

    public function removeMedia()
    {
        $instance = Instance::find(session('instanceId'));
        $mediasConfig = config('instances.medias');

        $mediaType = request()->get('mediaType');
        if (isset($mediasConfig['mediaTypes'][$mediaType])) {
            $instance->deleteParameter($mediasConfig['mediaTypes'][$mediaType]['parameterName']);
        }
        return response()->json(['result' => 'removed']);
    }

    private function resizeImage($imagePath, $size)
    {
        $image = $this->imagine
        ->setMetadataReader(new ExifMetadataReader())
        ->open($imagePath);

        $metaData = $image->metadata();
        if (isset($metaData['ifd0.Orientation'])) {
            switch ($metaData['ifd0.Orientation']) {
                case 3:
                    $image->rotate(180);
                    break;
                case 6:
                    $image->rotate(90);
                    break;
                case 8:
                    $image->rotate(-90);
                    break;
                default:
                    break;
            }
        }

        $image->strip();
        $box = $image->getSize();

        if ($box->getWidth() <= $box->getHeight() && $box->getHeight() > $size) {
            $ratio = $box->getWidth() / $box->getHeight();
            $box = new Box($size * $ratio, $size);
        } elseif ($box->getWidth() > $size) {
            $ratio = $box->getHeight() / $box->getWidth();
            $box = new Box($size, $size * $ratio);
        } else {
            return;
        }

        $options = array(
            'resolution-units' => ImageInterface::RESOLUTION_PIXELSPERINCH,
            'resolution-x' => 72,
            'resolution-y' => 72,
            'jpeg_quality' => 90,
        );

        $image->resize($box);
        $image->save($imagePath, $options);
    }

    public function download($parameterName, $filename = null)
    {
        $instance = Instance::find(session('instanceId'));
        if ($parameterName == 'instance_css') {
            $fileName = $instance->id . '-' . $instance->slug . '.css';
            $customCssFilePath = env('NETFRAME_DATA_PATH', base_path())
            . '/storage/uploads/instances-css/'
                . $fileName;
                $timestamp = \File::lastModified($customCssFilePath);
                $response = new Response();
                $response->headers->set('Content-Type', 'text/css');
                $response->headers->set(
                    'Content-Disposition',
                    sprintf('attachment; filename="%s"?v="%s"', $fileName, $timestamp)
                );
                $response->headers->set(config('media.proxy_file_sending_header'), $customCssFilePath);
                return $response;
        } else {
            $config = config('instances.medias');
            if (in_array($parameterName, $config['allowedDownload'])) {
                //get filename in database
                $parameter = $instance->getParameter($parameterName);
                if ($parameter != null) {
                    $imageAttributes = json_decode($parameter, true);

                    $path = env('NETFRAME_DATA_PATH', base_path())
                    . '/storage/uploads/instances/' . $instance->id
                    . '/' . $imageAttributes['directory']
                    . '/' . $imageAttributes['filename'];

                    $response = new Response();
                    $response->headers->set('Content-Type', $imageAttributes['mime_type']);
                    $response->headers->set(
                        'Content-Disposition',
                        sprintf('attachment; filename="%s"', $imageAttributes['filename'])
                    );
                    $response->headers->set(config('media.proxy_file_sending_header'), $path);
                    return $response;
                }
            }
        }
    }
}
