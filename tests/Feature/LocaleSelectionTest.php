<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocaleSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_locale_and_it_is_saved(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('locale.update'), ['locale' => 'fr'])
            ->assertRedirect();

        $this->assertEquals('fr', session('locale'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'preferred_locale' => 'fr']);
    }
}
