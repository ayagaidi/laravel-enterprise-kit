<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;
    public function test_locale_switch_is_stored_in_session(): void
    {
        $this->get('/locale/ar')->assertRedirect();
        $this->assertSame('ar', session('locale'));
    }
}
