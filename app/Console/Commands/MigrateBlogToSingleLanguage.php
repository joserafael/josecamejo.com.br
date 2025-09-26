<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MigrateBlogToSingleLanguage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:migrate-to-single-language {--dry-run : Show what would be migrated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate multilingual blog data to single language structure';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 Running in DRY RUN mode - no changes will be made');
        } else {
            $this->info('🚀 Starting blog migration to single language structure');
        }

        // Check if old multilingual columns exist
        $hasOldStructure = $this->checkForOldStructure();
        
        if (!$hasOldStructure) {
            $this->info('✅ No old multilingual structure found. Migration not needed.');
            return 0;
        }

        // Migrate each table
        $this->migrateBlogCategories($isDryRun);
        $this->migrateBlogSubcategories($isDryRun);
        $this->migrateBlogTags($isDryRun);

        if (!$isDryRun) {
            $this->info('✅ Migration completed successfully!');
        } else {
            $this->info('✅ Dry run completed. Use without --dry-run to apply changes.');
        }

        return 0;
    }

    /**
     * Check if old multilingual structure exists
     */
    private function checkForOldStructure(): bool
    {
        $tables = ['blog_categories', 'blog_subcategories', 'blog_tags'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                if (Schema::hasColumn($table, 'name_en') || 
                    Schema::hasColumn($table, 'name_es') || 
                    Schema::hasColumn($table, 'name_pt')) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Migrate blog categories
     */
    private function migrateBlogCategories(bool $isDryRun): void
    {
        if (!Schema::hasTable('blog_categories')) {
            return;
        }

        $this->info('📂 Processing blog categories...');

        // Check if old columns exist
        if (!Schema::hasColumn('blog_categories', 'name_en')) {
            $this->info('   ℹ️  No old multilingual columns found in blog_categories');
            return;
        }

        $oldCategories = DB::table('blog_categories')->get();
        $migratedCount = 0;

        foreach ($oldCategories as $category) {
            // Create entries for each language that has content
            $languages = ['pt', 'en', 'es'];
            
            foreach ($languages as $lang) {
                $nameField = "name_{$lang}";
                $descField = "description_{$lang}";
                
                if (!empty($category->$nameField)) {
                    $newData = [
                        'name' => $category->$nameField,
                        'slug' => $category->slug . ($lang !== 'pt' ? "-{$lang}" : ''),
                        'description' => $category->$descField ?? null,
                        'language' => $lang,
                        'is_active' => $category->is_active ?? true,
                        'sort_order' => $category->sort_order ?? 0,
                        'created_at' => $category->created_at,
                        'updated_at' => $category->updated_at,
                    ];

                    if ($isDryRun) {
                        $this->line("   Would create: {$newData['name']} ({$lang})");
                    } else {
                        DB::table('blog_categories_new')->insert($newData);
                    }
                    
                    $migratedCount++;
                }
            }
        }

        $this->info("   ✅ Processed {$migratedCount} category entries");
    }

    /**
     * Migrate blog subcategories
     */
    private function migrateBlogSubcategories(bool $isDryRun): void
    {
        if (!Schema::hasTable('blog_subcategories')) {
            return;
        }

        $this->info('📁 Processing blog subcategories...');

        // Check if old columns exist
        if (!Schema::hasColumn('blog_subcategories', 'name_en')) {
            $this->info('   ℹ️  No old multilingual columns found in blog_subcategories');
            return;
        }

        $oldSubcategories = DB::table('blog_subcategories')->get();
        $migratedCount = 0;

        foreach ($oldSubcategories as $subcategory) {
            // Create entries for each language that has content
            $languages = ['pt', 'en', 'es'];
            
            foreach ($languages as $lang) {
                $nameField = "name_{$lang}";
                $descField = "description_{$lang}";
                
                if (!empty($subcategory->$nameField)) {
                    $newData = [
                        'blog_category_id' => $subcategory->blog_category_id,
                        'name' => $subcategory->$nameField,
                        'slug' => $subcategory->slug . ($lang !== 'pt' ? "-{$lang}" : ''),
                        'description' => $subcategory->$descField ?? null,
                        'language' => $lang,
                        'is_active' => $subcategory->is_active ?? true,
                        'sort_order' => $subcategory->sort_order ?? 0,
                        'created_at' => $subcategory->created_at,
                        'updated_at' => $subcategory->updated_at,
                    ];

                    if ($isDryRun) {
                        $this->line("   Would create: {$newData['name']} ({$lang})");
                    } else {
                        DB::table('blog_subcategories_new')->insert($newData);
                    }
                    
                    $migratedCount++;
                }
            }
        }

        $this->info("   ✅ Processed {$migratedCount} subcategory entries");
    }

    /**
     * Migrate blog tags
     */
    private function migrateBlogTags(bool $isDryRun): void
    {
        if (!Schema::hasTable('blog_tags')) {
            return;
        }

        $this->info('🏷️  Processing blog tags...');

        // Check if old columns exist
        if (!Schema::hasColumn('blog_tags', 'name_en')) {
            $this->info('   ℹ️  No old multilingual columns found in blog_tags');
            return;
        }

        $oldTags = DB::table('blog_tags')->get();
        $migratedCount = 0;

        foreach ($oldTags as $tag) {
            // Create entries for each language that has content
            $languages = ['pt', 'en', 'es'];
            
            foreach ($languages as $lang) {
                $nameField = "name_{$lang}";
                $descField = "description_{$lang}";
                
                if (!empty($tag->$nameField)) {
                    $newData = [
                        'name' => $tag->$nameField,
                        'slug' => $tag->slug . ($lang !== 'pt' ? "-{$lang}" : ''),
                        'description' => $tag->$descField ?? null,
                        'language' => $lang,
                        'is_active' => $tag->is_active ?? true,
                        'color' => $tag->color ?? '#007bff',
                        'created_at' => $tag->created_at,
                        'updated_at' => $tag->updated_at,
                    ];

                    if ($isDryRun) {
                        $this->line("   Would create: {$newData['name']} ({$lang})");
                    } else {
                        DB::table('blog_tags_new')->insert($newData);
                    }
                    
                    $migratedCount++;
                }
            }
        }

        $this->info("   ✅ Processed {$migratedCount} tag entries");
    }
}
