<?php

namespace App\Support;

class TextHelper
{
    /**
     * Convert URLs and location links in text into clickable anchor links.
     */
    public static function linkify(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // Escape HTML to prevent XSS
        $escaped = e($text);

        // Regex pattern to capture full URLs including Google Maps, short links, etc.
        $urlPattern = '/(https?:\/\/[^\s<]+)/i';

        $linked = preg_replace_callback($urlPattern, function ($matches) {
            $url = $matches[1];
            $isMap = str_contains(strtolower($url), 'maps') || str_contains(strtolower($url), 'goo.gl');
            $icon = $isMap 
                ? '<svg class="w-3.5 h-3.5 inline mr-1 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' 
                : '<svg class="w-3.5 h-3.5 inline mr-1 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>';
            
            $label = $isMap ? 'Open Maps Location' : $url;

            return '<a href="' . $url . '" target="_blank" rel="noopener noreferrer" class="inline-flex items-center font-bold text-secondary underline hover:text-primary transition break-all bg-secondary/10 px-2 py-0.5 rounded-lg text-xs mx-0.5">' 
                . $icon . '<span>' . $label . '</span></a>';
        }, $escaped);

        return nl2br($linked);
    }
}
