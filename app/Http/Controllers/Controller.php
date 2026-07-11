<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

abstract class Controller
{
    /**
     * Upload an image, converting to WebP format.
     */
    protected function uploadImage(Request $request, string $field = 'image', string $path = 'products'): ?string
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $filename = Str::random(40) . '.webp';

        try {
            $image = \Intervention\Image\Laravel\Facades\Image::read($file->getRealPath());
            $image->toWebp(80);
            Storage::disk('public')->put($path . '/' . $filename, $image->encode());
            return $path . '/' . $filename;
        } catch (\Exception $e) {
            // Fallback to original format if WebP conversion fails
            $filename = $file->hashName();
            $file->storeAs($path, $filename, 'public');
            return $path . '/' . $filename;
        }
    }
}
