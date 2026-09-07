<?php
namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SystemSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_admin_can_update_existing_setting_and_audit_it(): void
    {
        $admin = $this->createUserWithPermissions(['settings.manage']);
        SystemSetting::query()->create(['key'=>'app.name','value'=>'Old','type'=>'string','group'=>'general','is_public'=>true]);
        $this->actingAs($admin)->put('/admin/settings',['settings'=>['app.name'=>'New Name']])->assertRedirect();
        $this->assertDatabaseHas('system_settings',['key'=>'app.name','value'=>'New Name']);
        $this->assertTrue(AuditLog::query()->where('action','setting.updated')->exists());
    }
}
