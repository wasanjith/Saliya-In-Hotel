<?php

namespace App\Helpers;

use App\Services\ImageService;

class ImageHelper
{
    /**
     * Get optimized image URL for categories
     */
    public static function getCategoryImageUrl($category, $size = 'original'): string
    {
        if (!$category || !$category->image) {
            return '';
        }
        
        return ImageService::getImageUrl($category->image, $size);
    }
    
    /**
     * Get optimized image URL for food items
     */
    public static function getFoodItemImageUrl($foodItem, $size = 'original'): string
    {
        if (!$foodItem || !$foodItem->image) {
            return '';
        }
        
        return ImageService::getImageUrl($foodItem->image, $size);
    }
    
    /**
     * Get category image with fallback
     */
    public static function getCategoryImage($category, $size = 'original'): string
    {
        $imageUrl = self::getCategoryImageUrl($category, $size);
        
        if (empty($imageUrl)) {
            return asset('images/placeholder-category.svg');
        }
        
        return $imageUrl;
    }
    
    /**
     * Get food item image with fallback
     */
    public static function getFoodItemImage($foodItem, $size = 'original'): string
    {
        $imageUrl = self::getFoodItemImageUrl($foodItem, $size);
        
        if (empty($imageUrl)) {
            return asset('images/placeholder-food.svg');
        }
        
        return $imageUrl;
    }
    
    /**
     * Get image dimensions for responsive display
     */
    public static function getImageDimensions($type = 'food', $size = 'original'): array
    {
        $dimensions = [
            'category' => [
                'original' => ['width' => 200, 'height' => 200],
                'thumbnail' => ['width' => 100, 'height' => 100],
                'small' => ['width' => 50, 'height' => 50],
            ],
            'food' => [
                'original' => ['width' => 400, 'height' => 300],
                'thumbnail' => ['width' => 200, 'height' => 150],
                'small' => ['width' => 100, 'height' => 75],
            ]
        ];
        
        return $dimensions[$type][$size] ?? $dimensions[$type]['original'];
    }
} 