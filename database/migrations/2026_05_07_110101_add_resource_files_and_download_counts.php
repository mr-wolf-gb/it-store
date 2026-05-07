<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('support_resources', function (Blueprint $table): void {
            $table->unsignedBigInteger('download_count')->default(0)->after('link_url');
        });

        Schema::create('support_resource_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_resource_id')->constrained()->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->unsignedBigInteger('download_count')->default(0);
            $table->timestamps();

            $table->index(['support_resource_id', 'created_at']);
        });

        $now = now();

        DB::table('support_resources')
            ->whereNotNull('file_path')
            ->where('file_path', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($resources) use ($now): void {
                $rows = [];

                foreach ($resources as $resource) {
                    $rows[] = [
                        'support_resource_id' => $resource->id,
                        'file_path' => $resource->file_path,
                        'file_name' => $resource->file_name ?: basename($resource->file_path),
                        'mime_type' => $resource->mime_type,
                        'file_size' => $resource->file_size,
                        'download_count' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                if ($rows !== []) {
                    DB::table('support_resource_files')->insert($rows);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_resource_files');

        Schema::table('support_resources', function (Blueprint $table): void {
            $table->dropColumn('download_count');
        });
    }
};
