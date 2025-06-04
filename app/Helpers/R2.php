<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('uploadToR2')) {
    function uploadToR2($file, $directory = 'tecidos')
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = $directory . '/' . uniqid() . '.' . $extension;

        $path = Storage::disk('cloudflare_r2')->put(
            $fileName,
            file_get_contents($file)
        );

        return Storage::disk('cloudflare_r2')->url($fileName);
    }
}

if (!function_exists('deleteFromR2')) {
    function deleteFromR2($url)
    {
        try {
            $path = parse_url($url, PHP_URL_PATH);
            return Storage::disk('cloudflare_r2')->delete(ltrim($path, '/'));
        } catch (\Exception $e) {
            return false;
        }
    }
}
