<?php

namespace Database\Seeders;

use App\Models\ResourceCategory;
use App\Models\ResourceTag;
use App\Models\SupportResource;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ResourceLibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect(['Applications', 'Drivers', 'Scripts', 'Documentation', 'Security Patches'])
            ->map(fn (string $name) => ResourceCategory::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            ));

        $tags = collect(['windows', 'linux', 'network', 'security', 'automation', 'helpdesk'])
            ->map(fn (string $name) => ResourceTag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            ));

        $admin = User::query()->first();
        if ($admin === null) {
            return;
        }

        SupportResource::query()->firstOrCreate(
            ['slug' => 'vpn-client-installer'],
            [
                'user_id' => $admin->id,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
                'title' => 'VPN Client Installer',
                'description' => 'Latest installer for internal VPN access.',
                'resource_type' => 'app',
                'visibility' => 'private',
                'status' => 'published',
                'source_type' => 'link',
                'link_url' => '/downloads/vpn-client',
                'version' => '5.2.0',
                'category_id' => $categories->firstWhere('slug', 'applications')?->id,
                'is_featured' => true,
                'published_at' => now(),
            ]
        )->tags()->syncWithoutDetaching($tags->whereIn('slug', ['windows', 'security'])->pluck('id')->all());
    }
}
