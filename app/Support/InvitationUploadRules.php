<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;

class InvitationUploadRules
{
    private const MUSIC_EXTENSIONS = ['mp3', 'm4a', 'aac', 'ogg', 'wav', 'mpeg', 'mp4'];

    private const MUSIC_MIMES = [
        'audio/mpeg',
        'audio/mp3',
        'audio/mp4',
        'audio/x-m4a',
        'audio/m4a',
        'audio/aac',
        'audio/aacp',
        'audio/ogg',
        'audio/vorbis',
        'application/ogg',
        'audio/wav',
        'audio/x-wav',
        'audio/wave',
        'audio/vnd.wave',
    ];

    /**
     * @return array<int, mixed>
     */
    public static function musicFile(): array
    {
        return [
            'nullable',
            'file',
            'max:15360',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if (! $value instanceof UploadedFile) {
                    return;
                }

                if (! $value->isValid()) {
                    $fail(__('builder.music_file_invalid'));

                    return;
                }

                if (! self::isAllowedMusicUpload($value)) {
                    $fail(__('builder.music_file_invalid'));
                }
            },
        ];
    }

    public static function isAllowedMusicUpload(UploadedFile $file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: '');
        $mime = strtolower((string) $file->getMimeType());

        if (in_array($mime, self::MUSIC_MIMES, true)) {
            return true;
        }

        if ($mime === 'application/octet-stream' && in_array($extension, self::MUSIC_EXTENSIONS, true)) {
            return true;
        }

        return in_array($extension, self::MUSIC_EXTENSIONS, true);
    }
}
