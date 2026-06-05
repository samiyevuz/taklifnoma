<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Support\TemplateCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $slugs = $user->favoriteSlugs();

        $templates = collect(TemplateCatalog::all())
            ->filter(fn (array $template) => in_array($template['slug'], $slugs, true))
            ->values()
            ->all();

        return view('account.favorites', [
            'title' => 'Yoqtirganlar — Taklifnoma',
            'templates' => $templates,
        ]);
    }

    public function store(Request $request, string $templateSlug): JsonResponse|RedirectResponse
    {
        abort_unless(TemplateCatalog::find($templateSlug), 404);

        Favorite::query()->firstOrCreate([
            'user_id' => $request->user()->id,
            'template_slug' => $templateSlug,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'favorited' => true,
                'message' => 'Shablon yoqtirganlarga qo\'shildi.',
            ]);
        }

        return back()->with('success', 'Shablon yoqtirganlarga qo\'shildi.');
    }

    public function destroy(Request $request, string $templateSlug): JsonResponse|RedirectResponse
    {
        $request->user()
            ->favorites()
            ->where('template_slug', $templateSlug)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'favorited' => false,
                'message' => 'Shablon yoqtirganlardan olib tashlandi.',
            ]);
        }

        return back()->with('success', 'Shablon yoqtirganlardan olib tashlandi.');
    }
}
