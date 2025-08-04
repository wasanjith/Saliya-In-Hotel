<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class GeneratePlaceholderImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:generate-placeholders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate placeholder images for categories and food items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating placeholder images...');

        // Create category placeholder
        $this->createPlaceholderImage(
            'public/images/placeholder-category.png',
            200,
            200,
            'Category',
            '#3b82f6'
        );

        // Create food item placeholder
        $this->createPlaceholderImage(
            'public/images/placeholder-food.png',
            400,
            300,
            'Food Item',
            '#10b981'
        );

        $this->info('✅ Placeholder images generated successfully!');
        $this->info('📁 Images saved in: public/images/');
    }

    private function createPlaceholderImage($path, $width, $height, $text, $color)
    {
        // Create image manager
        $manager = new ImageManager(new Driver());
        
        // Create image
        $image = $manager->create($width, $height);
        $image->fill('#f3f4f6');
        
        // Add border
        $image->rectangle(0, 0, $width - 1, $height - 1, function ($draw) use ($color) {
            $draw->border(2, $color);
        });
        
        // Add text
        $image->text($text, $width / 2, $height / 2, function ($font) use ($color) {
            $font->size(16);
            $font->color($color);
            $font->align('center');
            $font->valign('middle');
        });
        
        // Save image
        $image->save($path);
        
        $this->line("Created: {$path}");
    }
}
