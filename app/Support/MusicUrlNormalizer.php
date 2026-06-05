<?php

namespace App\Support;

class MusicUrlNormalizer
{
    public static function normalize(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $url = trim($url);

        if (preg_match('/(?:youtube\.com|youtu\.be|spotify\.com)/i', $url)) {
            return null;
        }

        if (preg_match('/drive\.google\.com\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://drive.google.com/uc?export=open&id='.$matches[1];
        }

        if (preg_match('/drive\.google\.com\/open\?id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://drive.google.com/uc?export=open&id='.$matches[1];
        }

        if (str_contains($url, 'dropbox.com')) {
            $url = str_replace(['www.dropbox.com', 'dropbox.com'], 'dl.dropboxusercontent.com', $url);
            $url = str_replace('?dl=0', '?dl=1', $url);

            if (! str_contains($url, 'dl=')) {
                $url .= (str_contains($url, '?') ? '&' : '?').'dl=1';
            }
        }

        if (! preg_match('#^https?://#i', $url) && ! str_starts_with($url, '//')) {
            return asset(ltrim($url, '/'));
        }

        return $url;
    }

    public static function isDirectAudioUrl(?string $url): bool
    {
        if (blank($url)) {
            return false;
        }

        return (bool) preg_match('/\.(mp3|m4a|aac|ogg|wav)(\?.*)?$/i', $url);
    }
}
