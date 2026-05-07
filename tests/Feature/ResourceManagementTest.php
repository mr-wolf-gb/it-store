<?php

namespace Tests\Feature;

use App\Models\ResourceCategory;
use App\Models\ResourceTag;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_resource_with_metadata(): void
    {
        $this->seed(PermissionSeeder::class);

        $user = User::factory()->create();
        $user->givePermissionTo('resources.manage');
        $category = ResourceCategory::create(['name' => 'Applications', 'slug' => 'applications']);
        $tag = ResourceTag::create(['name' => 'windows', 'slug' => 'windows']);

        $response = $this->actingAs($user)->post(route('admin.resources.store'), [
            'title' => 'Office Installer',
            'slug' => 'office-installer',
            'description' => 'Installer package',
            'resource_type' => 'app',
            'visibility' => 'private',
            'status' => 'published',
            'version' => '1.0.0',
            'changelog' => 'Initial release',
            'category_id' => $category->id,
            'tag_ids' => [$tag->id],
            'source_type' => 'link',
            'link_url' => 'https://example.com/office',
            'is_featured' => '1',
        ]);

        $response->assertRedirect(route('admin.resources.index'));
        $this->assertDatabaseHas('support_resources', ['slug' => 'office-installer', 'status' => 'published']);
        $this->assertDatabaseHas('support_resource_tag', ['resource_tag_id' => $tag->id]);
    }
}
