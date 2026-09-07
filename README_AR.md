# Laravel Enterprise Kit

Starter Kit مفتوح المصدر مبني بـ **Laravel 13** للأنظمة الإدارية وأنظمة الشركات، ويركز من البداية على الأشياء التي يصعب إضافتها لاحقًا: **الصلاحيات، سجل التدقيق، الإعدادات، الـAPI، تعدد اللغة والاختبارات**.

## النسخة الأولى v0.1

- تسجيل دخول مع Rate Limiting
- مستخدم نشط/موقوف
- Roles & Permissions
- صلاحيات واضحة على Routes
- Super Admin محمي من تعديل الصلاحيات
- إدارة المستخدمين والأدوار
- System Settings مع Cache invalidation
- Audit Trail يسجل المستخدم والعملية والقيم القديمة والجديدة وIP وRequest ID
- إخفاء كلمات المرور والتوكنات من سجل التدقيق
- Laravel Sanctum API
- `/api/v1`
- عربي/إنجليزي وRTL/LTR
- Feature Tests + Laravel Pint + GitHub Actions
- لا توجد كلمة مرور Admin ثابتة داخل المشروع

## تشغيل سريع

```bash
cp .env.example .env
composer install
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

لإنشاء Admin تجريبي محليًا فقط، ضعي `SEED_ADMIN_PASSWORD` في `.env` ثم شغلي `php artisan db:seed`.

النسخة الأولى هي **Enterprise Foundation**. المراحل التالية موجودة في [`ROADMAP.md`](ROADMAP.md).
