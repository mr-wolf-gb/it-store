<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resource_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('resource_tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('support_resource_tag', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_resource_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resource_tag_id')->constrained('resource_tags')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['support_resource_id', 'resource_tag_id']);
        });

        Schema::table('support_resources', function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique()->after('title');
            $table->string('version', 50)->nullable()->after('resource_type');
            $table->longText('changelog')->nullable()->after('version');
            $table->string('status', 20)->default('draft')->after('visibility');
            $table->string('thumbnail_path')->nullable()->after('link_url');
            $table->foreignId('category_id')->nullable()->after('user_id')->constrained('resource_categories')->nullOnDelete();
            $table->boolean('is_featured')->default(false)->after('status');
            $table->timestamp('published_at')->nullable()->after('is_featured');
            $table->foreignId('created_by')->nullable()->after('published_at')->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
        });

        DB::table('support_resources')
            ->orderBy('id')
            ->select(['id', 'title'])
            ->chunkById(100, function ($resources): void {
                foreach ($resources as $resource) {
                    $base = Str::slug((string) $resource->title);
                    $slug = $base !== '' ? $base : 'resource-'.$resource->id;
                    $counter = 1;

                    while (DB::table('support_resources')->where('slug', $slug)->where('id', '!=', $resource->id)->exists()) {
                        $slug = $base.'-'.$counter;
                        $counter++;
                    }

                    DB::table('support_resources')
                        ->where('id', $resource->id)
                        ->update([
                            'slug' => $slug,
                            'status' => 'published',
                            'published_at' => now(),
                            'created_by' => DB::table('support_resources')->where('id', $resource->id)->value('user_id'),
                            'updated_by' => DB::table('support_resources')->where('id', $resource->id)->value('user_id'),
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('support_resources', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('updated_by');
            $table->dropConstrainedForeignId('created_by');
            $table->dropConstrainedForeignId('category_id');
            $table->dropColumn([
                'slug',
                'version',
                'changelog',
                'status',
                'thumbnail_path',
                'is_featured',
                'published_at',
            ]);
        });

        Schema::dropIfExists('support_resource_tag');
        Schema::dropIfExists('resource_tags');
        Schema::dropIfExists('resource_categories');
    }
};
