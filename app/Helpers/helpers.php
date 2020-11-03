<?php

/**
 * Returns the translations array.
 * These locales will be sent to Vue via the Inertia's share method.
 * @param string $locale - The locale whose translations you want to find
 * @return array
 */
function translations(string $locale): array
{
    $translationFiles = File::files(base_path("resources/lang/${locale}"));

    // This will return something like:
    // [
    //     'auth' => [ 'throttle' => 'Too many login attempts', ... ],
    //     'validation' => ['accepted' => 'The :attribute must be accepted.', ...],
    //  ]

    return collect($translationFiles)
        ->map(fn($file) => [$file->getFilenameWithoutExtension() => require($file)])
        ->collapse()
        ->toArray();
}

//function translations(): void
//{
//    foreach (config('app.locales') as $locale) {
//        $translationFiles = File::files(base_path("resources/lang/${locale}"));
//        $data = json_encode(collect($translationFiles)
//            ->map(fn($file) => [$file->getFilenameWithoutExtension() => require($file)])
//            ->collapse()
//            ->toArray());
//        file_put_contents(base_path("resources/lang/${locale}.json"), $data);
//    }
//}
