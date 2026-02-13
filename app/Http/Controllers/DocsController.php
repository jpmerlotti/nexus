<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class DocsController extends Controller
{

    public function show($page = 'index')
    {
        $path = resource_path("docs/{$page}.md");

        if (!File::exists($path)) {
            abort(404);
        }

        $content = File::get($path);

        // Configure CommonMark with Heading Permalinks
        $config = [
            'heading_permalink' => [
                'html_class' => 'heading-permalink',
                'id_prefix' => '',
                'fragment_prefix' => '',
                'insert' => 'none', // We just want the ID, not the symbol for now
                'min_heading_level' => 2,
                'max_heading_level' => 3,
            ],
        ];

        $environment = new \League\CommonMark\Environment\Environment($config);
        $environment->addExtension(new \League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension());
        $environment->addExtension(new \League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension());
        
        // Add other extensions if needed (e.g. GithubFlavoredMarkdownExtension)
        $environment->addExtension(new \League\CommonMark\Extension\GithubFlavoredMarkdownExtension());

        $converter = new \League\CommonMark\MarkdownConverter($environment);
        $html = $converter->convert($content);

        // Extract ToC manually from markdown (simple regex for now)
        // matches ## Title or ### Title
        preg_match_all('/^(##|###)\s+(.*)$/m', $content, $matches, PREG_SET_ORDER);
        
        $toc = [];
        foreach ($matches as $match) {
            $level = strlen($match[1]); // 2 or 3
            $title = trim($match[2]);
            $id = Str::slug($title); // CommonMark uses slug by default
            
            $toc[] = [
                'level' => $level,
                'title' => $title,
                'id' => $id
            ];
        }

        // Get index for sidebar
        $indexPath = resource_path('docs/index.md');
        $index = File::exists($indexPath) ? Str::markdown(File::get($indexPath)) : '';

        return view('docs.show', [
            'content' => $html,
            'index' => $index,
            'toc' => $toc,
            'title' => Str::title(str_replace('-', ' ', $page)),
        ]);
    }
}

