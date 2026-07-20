<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        // Agent portal: agents need to see everything, public + internal.
        [$articles, $categories] = $this->buildArticlesAndCategories(publicOnly: false);

        return view('KnowledgeBase', compact('articles', 'categories'));
    }

    public function customerIndex()
    {
        // Customer portal: only ever expose publicly-visible articles.
        [$articles, $categories] = $this->buildArticlesAndCategories(publicOnly: true);

        return view('CustomerPortal', compact('articles', 'categories'));
    }

    /**
     * Shared query + formatting logic for both the agent and customer views.
     * Filtering happens here, server-side, before anything is sent to the browser -
     * internal articles never get embedded in the customer portal's page source.
     */
    private function buildArticlesAndCategories(bool $publicOnly = false)
    {
        // 1. Grab items out of your database tables
        $query = DB::table('kb_articles');

        if ($publicOnly) {
            $query->where('visibility', 'public');
        }

        $dbArticles = $query->get();
        $dbCategories = DB::table('article_categories')->get();

        // 2. Format articles collection for the UI
        $articles = $dbArticles->map(function($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'desc' => $article->desc,
                'category' => $article->category,
                'catId' => $article->cat_id, // Reads whatever is stored in cat_id column
                'views' => number_format($article->views),
                'updated' => Carbon::parse($article->updated_at)->format('m/d/Y'),
                'helpful' => $article->yes_votes + $article->no_votes > 0 
                    ? round(($article->yes_votes / ($article->yes_votes + $article->no_votes)) * 100) . '%' 
                    : '100%',
                'tags' => explode(',', $article->tags),
                'yesVotes' => $article->yes_votes,
                'noVotes' => $article->no_votes,
                'visibility' => $article->visibility,
            ];
        });

        // 3. Dynamically count how many articles belong to each category
        $categories = collect([
            [
                'id' => 'all',
                'name' => 'All Articles',
                'count' => $articles->count()
            ]
        ]);

        foreach ($dbCategories as $cat) {
            // Get a safe fallback ID matching what your frontend buttons use
            $cleanId = strtolower(explode(' ', $cat->category_name)[0]);
            $cleanId = str_replace('&', '', $cleanId);

            // Count matching rows
            $count = $articles->where('catId', $cleanId)->count();

            $categories->push([
                'id' => $cleanId,
                'name' => $cat->category_name,
                'count' => $count
            ]);
        }

        return [$articles, $categories];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'tags' => 'required|string',
            'desc' => 'required|string',
            'visibility' => 'required|string',
        ]);

        // Clean up text to match category filter ID prefix
        $cleanId = strtolower(explode(' ', $validated['category'])[0]);
        $cleanId = str_replace('&', '', $cleanId);

        DB::table('kb_articles')->insert([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'cat_id' => $cleanId,
            'tags' => $validated['tags'],
            'desc' => $validated['desc'],
            'visibility' => $validated['visibility'],
            'views' => 0,
            'yes_votes' => 0,
            'no_votes' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function vote(Request $request, $id)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:yes,no',
        ]);

        // One vote per article per visitor session, so refreshing/re-clicking can't stack votes
        $votedArticles = session('voted_articles', []);
        if (in_array((int) $id, $votedArticles, true)) {
            return response()->json(['success' => false, 'message' => 'You already voted on this article.'], 409);
        }

        $article = DB::table('kb_articles')->where('id', $id)->first();
        if (!$article) {
            return response()->json(['success' => false, 'message' => 'Article not found'], 404);
        }

        $column = $validated['type'] === 'yes' ? 'yes_votes' : 'no_votes';
        DB::table('kb_articles')->where('id', $id)->increment($column);

        $votedArticles[] = (int) $id;
        session(['voted_articles' => $votedArticles]);

        $yesVotes = $article->yes_votes + ($validated['type'] === 'yes' ? 1 : 0);
        $noVotes = $article->no_votes + ($validated['type'] === 'no' ? 1 : 0);
        $total = $yesVotes + $noVotes;
        $helpful = $total > 0 ? round(($yesVotes / $total) * 100) . '%' : '100%';

        return response()->json([
            'success' => true,
            'yesVotes' => $yesVotes,
            'noVotes' => $noVotes,
            'helpful' => $helpful,
        ]);
    }

    public function incrementView(Request $request, $id)
    {
        // One counted view per article per visitor session, so re-rendering the same
        // list (filtering, searching, etc.) doesn't inflate the count on every render
        $viewedArticles = session('viewed_articles', []);
        if (in_array((int) $id, $viewedArticles, true)) {
            return response()->json(['success' => true, 'alreadyCounted' => true]);
        }

        $updated = DB::table('kb_articles')->where('id', $id)->increment('views');
        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Article not found'], 404);
        }

        $viewedArticles[] = (int) $id;
        session(['viewed_articles' => $viewedArticles]);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'tags' => 'required|string',
            'desc' => 'required|string',
            'visibility' => 'required|string|in:public,internal',
        ]);

        // Clean up text to match category filter ID prefix, same rule used on create
        $cleanId = strtolower(explode(' ', $validated['category'])[0]);
        $cleanId = str_replace('&', '', $cleanId);

        $updated = DB::table('kb_articles')
            ->where('id', $id)
            ->update([
                'title' => $validated['title'],
                'category' => $validated['category'],
                'cat_id' => $cleanId,
                'tags' => $validated['tags'],
                'desc' => $validated['desc'],
                'visibility' => $validated['visibility'],
                'updated_at' => now(),
            ]);

        if (!$updated) {
            return response()->json(['success' => false, 'message' => 'Article not found'], 404);
        }

        return response()->json(['success' => true]);
    }

    
}