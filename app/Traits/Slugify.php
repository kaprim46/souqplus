<?php

namespace App\Traits;


/**
 * Trait Slugify to make slug when create or update model
 */
trait Slugify {
    /**
     * Boot the trait
     */
    protected static function bootSlugify(): void
    {
        static::creating(function ($model) {
            $generatedSlug = $model->generateSlug($model->name);
            while (self::where('slug', $generatedSlug)->exists()) {
                $generatedSlug = $model->generateSlug($model->name) . '-' . self::count() + 1;
            }

            $model->slug = $generatedSlug;
        });

        static::updating(function ($model) {
            $generatedSlug = $model->generateSlug($model->name);
            while (self::where('slug', $generatedSlug)->where('id', '!=', $model->id)->exists()) {
                $generatedSlug = $model->generateSlug($model->name) . '-' . self::count() + 1;
            }

            $model->slug = $generatedSlug;
        });
    }

    /**
     * Generate a URL-friendly slug from a given string.
     *
     * @param string $string The input string.
     * @return string The generated slug.
     */
    private function generateSlug(string $string): string {
        // Convert to UTF-8 (in case it's not already)
        $string = mb_convert_encoding($string, 'UTF-8', mb_detect_encoding($string));

        // Remove any unwanted characters except Arabic, English letters, numbers, spaces, and hyphens
        $string = preg_replace('/[^\p{Arabic}\p{Latin}0-9\s-]/u', '', $string);

        // Replace multiple spaces or hyphens with a single hyphen
        $string = preg_replace('/[\s-]+/', '-', $string);

        // Trim leading and trailing hyphens
        $string = trim($string, '-');

        // Convert English characters to lowercase
        return mb_strtolower($string);
    }

    /**
     * Transliterate non-ASCII characters to ASCII equivalents.
     * This function is especially useful for handling Arabic characters.
     *
     * @param string $string The input string.
     * @return string The transliterated string.
     */
    private function transliterateToAscii(string $string) : string {
        // Transliteration map for Arabic to Latin
        $arabicToLatinMap = [
            'ا' => 'a', 'أ' => 'a', 'إ' => 'i', 'آ' => 'aa', 'ب' => 'b', 'ت' => 't', 'ث' => 'th', 'ج' => 'j',
            'ح' => 'h', 'خ' => 'kh', 'د' => 'd', 'ذ' => 'dh', 'ر' => 'r', 'ز' => 'z', 'س' => 's', 'ش' => 'sh',
            'ص' => 's', 'ض' => 'd', 'ط' => 't', 'ظ' => 'z', 'ع' => 'a', 'غ' => 'gh', 'ف' => 'f', 'ق' => 'q',
            'ك' => 'k', 'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ه' => 'h', 'و' => 'w', 'ي' => 'y', 'ى' => 'a',
            'ئ' => 'e', 'ء' => 'a', 'ؤ' => 'o', 'ة' => 'h'
        ];

        // Transliterate Arabic characters to Latin equivalents
        return str_replace(array_keys($arabicToLatinMap), array_values($arabicToLatinMap), $string);
    }

}