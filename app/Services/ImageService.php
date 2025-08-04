<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageService
{
    /**
     * Process and optimize uploaded image
     */
    public static function processImage(UploadedFile $file, string $directory, array $sizes = []): string
    {
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $directory . '/' . $filename;
        
        // Create image instance
        $image = Image::make($file);
        
        // Apply sizes if provided
        if (!empty($sizes)) {
            $image->resize($sizes['width'] ?? null, $sizes['height'] ?? null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Optimize quality
        $image->encode('jpg', 85);
        
        // Store the image
        Storage::disk('public')->put($path, $image->stream());
        
        return $path;
    }
    
    /**
     * Process category image
     */
    public static function processCategoryImage(UploadedFile $file): string
    {
        return self::processImage($file, 'categories', [
            'width' => 200,
            'height' => 200
        ]);
    }
    
    /**
     * Process food item image
     */
    public static function processFoodItemImage(UploadedFile $file): string
    {
        return self::processImage($file, 'food-items', [
            'width' => 400,
            'height' => 300
        ]);
    }
    
    /**
     * Generate thumbnail for an image
     */
    public static function generateThumbnail(string $imagePath, int $width = 100, int $height = 100): string
    {
        $fullPath = Storage::disk('public')->path($imagePath);
        $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
        
        if (file_exists($fullPath)) {
            $image = Image::make($fullPath);
            $image->fit($width, $height);
            $image->encode('jpg', 85);
            
            Storage::disk('public')->put($thumbnailPath, $image->stream());
            
            return $thumbnailPath;
        }
        
        return $imagePath;
    }
    
    /**
     * Delete image and its thumbnails
     */
    public static function deleteImage(string $imagePath): bool
    {
        if (Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
            
            // Delete thumbnail if exists
            $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                Storage::disk('public')->delete($thumbnailPath);
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get optimized image URL
     */
    public static function getImageUrl(string $imagePath, string $size = 'original'): string
    {
        if (empty($imagePath)) {
            return '';
        }
        
        if ($size === 'thumbnail') {
            $thumbnailPath = str_replace('.', '_thumb.', $imagePath);
            if (Storage::disk('public')->exists($thumbnailPath)) {
                return Storage::disk('public')->url($thumbnailPath);
            }
        }
        
        return Storage::disk('public')->url($imagePath);
    }
} 