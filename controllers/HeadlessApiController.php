<?php

namespace Jkchr1s\StaticPageBlocks\Controllers;

use Cms\Classes\CmsObjectCollection;
use Cms\Classes\Theme;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jkchr1s\StaticPageBlocks\Models\BlockSet as BlockSetModel;
use RainLab\Pages\Classes\Page;
use RainLab\Pages\Classes\PageList;

class HeadlessApiController extends Controller
{
    public function index(Request $request)
    {
        if (!empty($request->query('slug'))) {
            $slug = strtolower($request->query('slug'));
            $bySlug = BlockSetModel::find($slug);
            return empty($bySlug)
                ? response([
                    'error' => 'Blocks not found for specified slug',
                    'url' => $slug
                ], 404)
                : response([
                    'slug' => $slug,
                    'ts' => $bySlug->mtime ?: null,
                    'blocks' => $bySlug->blocks
                ]);
        }

        $theme = Theme::getActiveTheme();
        /** @var CmsObjectCollection $pageList */
        $pageList = (new PageList($theme))
            ->listPages()
            ->filter(function (Page $page) {
            return isset($page->headless)
                && $page->headless === '1'
                && isset($page->url)
                && isset($page->blocks)
                && is_array($page->blocks);
        });

        if (!empty($request->query('url'))) {
            $url = strtolower($request->query('url'));
            $page = $pageList->first(function (Page $page) use ($url) {
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

        return response([
            'blockSets' => [
                BlockSetModel::all()->filter(function (BlockSetModel $bs) {
                    return isset($bs->blocks) && is_array($bs->blocks);
                })->map(function (BlockSetModel $bs) {
                    return [
                        'slug' => $bs->slug,
                        'ts' => $bs->mtime ?: null,
                        'blocks' => $bs->blocks
                    ];
                })->values()
            ],
            'pages' => $pageList->filter(function (Page $page) {
                return isset($page->blocks) && is_array($page->blocks);
            })->map(function (Page $page) {
                return [
                    'url' => $page->url,
                    'ts' => $page->mtime ?: null,
                    'blocks' => $page->blocks
                ];
            })
        ]);
    }
}
