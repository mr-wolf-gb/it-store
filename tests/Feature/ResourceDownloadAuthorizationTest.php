<?php

namespace Tests\Feature;

use App\Models\SupportResource;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceDownloadAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_download_private_resource(): void
    {
        $resource = SupportResource::factory()->create([
            'visibility' => 'private',
        ]);

        $this->get(route('library.download', $resource))->assertForbidden();
    }

    public function test_user_with_private_download_permission_can_download_private_resource(): void
    {
        $this->seed(PermissionSeeder::class);
        $resource = SupportResource::factory()->create([
            'visibility' => 'private',
            'source_type' => 'link',
            'link_url' => 'https://example.com/private',
        ]);
        $user = User::factory()->create();
        $user->givePermissionTo('resources.download_private');

        $this->actingAs($user)->get(route('library.download', $resource))->assertRedirect('https://example.com/private');
    }
}
