<?php

namespace App\Services;

use App\Models\Invitation;
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
