<?php

namespace App\Services;

use App\Models\Invitation;
use App\Support\PlanEntitlements;
use App\Support\StoryGallerySlots;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvitationMediaService
{
    public function sync(Request $request, Invitation $invitation): void
    {
        $updates = [];

        if ($request->hasFile('cover_image')) {
            $updates['cover_image'] = $this->storeCover($request->file('cover_image'), $invitation);
        }

        if ($request->hasFile('music_file')) {
            $updates['music_url'] = $this->storeMusic($request->file('music_file'), $invitation);
        }

        $storyImages = $this->syncStoryImages($request, $invitation);
        if ($storyImages !== null) {
            $updates['story_images'] = $storyImages;
        }

        if ($updates !== []) {
            $invitation->update($updates);
        }
    }

    private function storeCover(UploadedFile $file, Invitation $invitation): string
    {
        $this->deleteIfExists($invitation->cover_image);

        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'cover-'.Str::uuid().'.'.$extension;

        $path = $file->storeAs("invitations/{$invitation->id}", $filename, 'public');

        return 'storage/'.$path;
    }

    private function storeMusic(UploadedFile $file, Invitation $invitation): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'mp3');
        $filename = 'music-'.Str::uuid().'.'.$extension;

        $path = $file->storeAs("invitations/{$invitation->id}", $filename, 'public');

        return asset('storage/'.$path);
    }

    private function storeStoryImage(UploadedFile $file, Invitation $invitation, string $slotKey): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'story-'.$slotKey.'-'.Str::uuid().'.'.$extension;

        $path = $file->storeAs("invitations/{$invitation->id}", $filename, 'public');

        return 'storage/'.$path;
    }

    /**
     * @return array<int, array<string, mixed>>|null
     */
    private function syncStoryImages(Request $request, Invitation $invitation): ?array
    {
        if (! PlanEntitlements::forInvitation($invitation)['story_gallery']) {
            if (filled($invitation->story_images)) {
                $this->deleteStoryImages($invitation->story_images);
            }

            return [];
        }

        $slug = $invitation->template_slug ?? 'nikoh';
        $existing = collect($invitation->story_images ?? [])->keyBy('slot');
        $merged = [];

        foreach (StoryGallerySlots::slotsForSlug($slug) as $slot) {
            $slotKey = $slot['key'];
            $inputName = "story_image_{$slotKey}";
            $captionName = "story_caption_{$slotKey}";
            $current = $existing->get($slotKey, []);
            $path = is_array($current) ? ($current['path'] ?? null) : null;
            $caption = $request->has($captionName)
                ? trim((string) $request->input($captionName, ''))
                : trim((string) (is_array($current) ? ($current['caption'] ?? '') : ''));

            if ($request->hasFile($inputName)) {
                $this->deleteIfExists($path);
                $path = $this->storeStoryImage($request->file($inputName), $invitation, $slotKey);
            }

            if (filled($path)) {
                $merged[] = [
                    'slot' => $slotKey,
                    'path' => $path,
                    'caption' => $caption,
                ];
            }
        }

        $removed = $existing
            ->keys()
            ->diff(collect($merged)->pluck('slot'))
            ->all();

        foreach ($removed as $slotKey) {
            $this->deleteIfExists($existing->get($slotKey)['path'] ?? null);
        }

        return $merged;
    }

    /**
     * @param  array<int, array<string, mixed>>|null  $items
     */
    private function deleteStoryImages(?array $items): void
    {
        foreach ($items ?? [] as $item) {
            $this->deleteIfExists($item['path'] ?? null);
        }
    }

    private function deleteIfExists(?string $publicPath): void
    {
        if (blank($publicPath) || ! str_starts_with($publicPath, 'storage/')) {
            return;
        }

        $storagePath = Str::after($publicPath, 'storage/');

        if (Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->delete($storagePath);
        }
    }
}
