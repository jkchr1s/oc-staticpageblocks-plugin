<?php

namespace Jkchr1s\StaticPageBlocks\Controllers;

use Cms\Classes\CmsObjectCollection;
use Cms\Classes\Theme;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use October\Rain\Support\Collection;
use RainLab\Pages\Classes\Page;
use RainLab\Pages\Classes\PageList;

class HeadlessApiController extends Controller
{
    /**
     * @var Collection
     */
    private $pageList;

    public function __construct()
    {
        $theme = Theme::getActiveTheme();
        /** @var CmsObjectCollection $pageList */
        $pageList = (new PageList($theme))->listPages();
        $this->pageList = $pageList->filter(function (Page $page) {
            return isset($page->headless)
                && $page->headless === '1'
                && isset($page->url)
                && isset($page->blocks)
                && is_array($page->blocks);
        });
    }

    public function index(Request $request)
    {
        if (!empty($request->query('url'))) {
            $url = strtolower($request->query('url'));
            $page = $this->pageList->first(function (Page $page) use ($url) {
                return strtolower($page->url) === $url;
            });
            return empty($page)
                ? response([
                    'error' => 'Blocks not found for specified url',
                    'url' => $url
                ], 404)
                : response([
                    'url' => $page->url,
                    'ts' => $page->mtime ?: null,
                    'blocks' => $page->blocks
                ]);
        }

        return response(
            $this->pageList->filter(function (Page $page) {
                return isset($page->getViewBag()->blocks) && is_array($page->getViewBag()->blocks);
            })->map(function (Page $page) {
                return [
                    'url' => $page->url,
                    'ts' => $page->mtime ?: null,
                    'blocks' => $page->blocks
                ];
            })
        );
    }
}
