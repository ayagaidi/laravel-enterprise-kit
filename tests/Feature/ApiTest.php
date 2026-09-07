<?php
namespace Tests\Feature;

use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_settings_endpoint_only_returns_public_settings(): void
    {
        SystemSetting::query()->create(['key'=>'public.key','value'=>'yes','type'=>'string','group'=>'general','is_public'=>true]);
        SystemSetting::query()->create(['key'=>'private.key','value'=>'secret','type'=>'string','group'=>'security','is_public'=>false]);
        $this->getJson('/api/v1/settings/public')->assertOk()->assertJsonPath('data.public.key','yes')->assertJsonMissing(['private.key'=>'secret']);
    }

    public function test_profile_endpoint_requires_sanctum_authentication(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_authenticated_api_user_can_read_profile(): void
    {
        $user = User::query()->create(['name'=>'API User','email'=>'api@example.test','password'=>'password1234','is_active'=>true,'locale'=>'en']);
        $token = $user->createToken('test')->plainTextToken;
        $this->withToken($token)->getJson('/api/v1/me')->assertOk()->assertJsonPath('data.email','api@example.test');
    }
}
