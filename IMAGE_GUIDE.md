# 🖼️ Image Management Guide - Saliya In Hotel POS System

## 📋 Overview

This guide explains how to manage images in the POS system, including upload, sizing, and optimization.

## 🎯 Image Requirements

### Categories
- **Recommended Size:** 200x200 pixels (square)
- **Format:** JPG, PNG, WebP
- **Max File Size:** 2MB
- **Aspect Ratio:** 1:1 (square)

### Food Items
- **Recommended Size:** 400x300 pixels (landscape)
- **Format:** JPG, PNG, WebP
- **Max File Size:** 2MB
- **Aspect Ratio:** 4:3 (landscape)

## 📤 How to Upload Images

### Method 1: Filament Admin Panel (Recommended)

1. **Access Admin Panel:**
   - Go to: `http://localhost:8000/admin`
   - Login with: `superadmin@lawora.com`

2. **Upload Category Images:**
   - Navigate to **Categories**
   - Click **Edit** on any category
   - In the **Image** field, click **Choose File**
   - Select your image
   - The image will be automatically:
     - Resized to 200x200px
     - Optimized for web
     - Stored in `storage/app/public/categories/`

3. **Upload Food Item Images:**
   - Navigate to **Food Items**
   - Click **Edit** on any food item
   - In the **Image** field, click **Choose File**
   - Select your image
   - The image will be automatically:
     - Resized to 400x300px
     - Optimized for web
     - Stored in `storage/app/public/food-items/`

### Method 2: Direct File Upload

1. **Prepare Images:**
   - Resize categories to 200x200px
   - Resize food items to 400x300px
   - Save as JPG or PNG

2. **Upload Files:**
   - Categories: Place in `storage/app/public/categories/`
   - Food Items: Place in `storage/app/public/food-items/`

3. **Update Database:**
   - Use admin panel to update image paths
   - Or update database directly with filename

## 🔧 Image Optimization Features

### Automatic Features
- ✅ **Resizing:** Images automatically resized to optimal dimensions
- ✅ **Compression:** Images optimized for web (85% quality)
- ✅ **Format Conversion:** Converted to JPG for consistency
- ✅ **Aspect Ratio:** Maintained during resizing
- ✅ **File Size:** Limited to 2MB maximum

### Manual Features
- ✅ **Image Editor:** Built-in editor in admin panel
- ✅ **Crop Tool:** Crop images to exact dimensions
- ✅ **Preview:** See how image will look before saving

## 📁 File Structure

```
storage/app/public/
├── categories/
│   ├── fried-rice.jpg
│   ├── kottu.jpg
│   ├── bites.jpg
│   └── ...
├── food-items/
│   ├── chicken-fried-rice.jpg
│   ├── egg-kottu.jpg
│   ├── chicken-wings.jpg
│   └── ...
└── public/images/
    ├── placeholder-category.svg
    └── placeholder-food.svg
```

## 🌐 Image URLs

### Access Images
- **Categories:** `http://localhost:8000/storage/categories/filename.jpg`
- **Food Items:** `http://localhost:8000/storage/food-items/filename.jpg`
- **Placeholders:** `http://localhost:8000/images/placeholder-category.svg`

### In Code
```php
// Get category image
$imageUrl = asset('storage/' . $category->image);

// Get food item image
$imageUrl = asset('storage/' . $foodItem->image);

// Get placeholder
$placeholder = asset('images/placeholder-category.svg');
```

## 🎨 Best Practices

### Image Preparation
1. **Use High Quality:** Start with high-resolution images
2. **Square for Categories:** Crop categories to square format
3. **Landscape for Food:** Use landscape format for food items
4. **Good Lighting:** Ensure images are well-lit
5. **Clean Background:** Use simple, clean backgrounds

### File Management
1. **Descriptive Names:** Use descriptive filenames
2. **Consistent Format:** Use JPG for photos, PNG for graphics
3. **Backup Originals:** Keep original high-res images
4. **Regular Updates:** Update images regularly

## 🚀 Performance Tips

### Optimization
- ✅ Images are automatically compressed
- ✅ Responsive sizing for different screens
- ✅ Lazy loading in POS interface
- ✅ Caching for better performance

### Storage
- ✅ Images stored in public storage
- ✅ Automatic cleanup of old images
- ✅ Backup system for images

## 🔍 Troubleshooting

### Common Issues

**Image Not Displaying:**
- Check file permissions
- Verify storage link exists
- Ensure correct file path

**Poor Image Quality:**
- Start with higher resolution
- Use better lighting
- Avoid over-compression

**Upload Fails:**
- Check file size (max 2MB)
- Verify file format
- Ensure disk space available

### Commands
```bash
# Regenerate storage link
php artisan storage:link

# Clear cache
php artisan cache:clear

# Generate placeholders
php artisan images:generate-placeholders
```

## 📱 Mobile Optimization

### Responsive Images
- ✅ Images scale properly on mobile
- ✅ Touch-friendly interface
- ✅ Fast loading on mobile networks
- ✅ Optimized for different screen sizes

## 🎯 Next Steps

1. **Upload Real Images:** Replace placeholders with real food images
2. **Optimize for Brand:** Use consistent styling and colors
3. **Regular Updates:** Keep images fresh and current
4. **Performance Monitor:** Monitor image loading performance

---

**Need Help?** Contact the development team for assistance with image management. 