<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_active_user_can_sign_in(): void
    {
        $user = $this->createUserWithPermissions(['dashboard.view'], ['email' => 'admin@example.test']);
        $this->post('/login', ['email' => $user->email, 'password' => 'password1234'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
    }

    public function test_disabled_user_cannot_sign_in(): void
    {
        User::query()->create(['name'=>'Disabled','email'=>'disabled@example.test','password'=>'password1234','is_active'=>false,'locale'=>'en']);
        $this->post('/login', ['email'=>'disabled@example.test','password'=>'password1234'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
