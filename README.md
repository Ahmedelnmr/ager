# 🏢 Smart Flexible Rental Management System

نظام إدارة إيجارات داخلي (Back-Office) مبني بـ **Laravel 11** و **MySQL** مع واجهة عربية RTL احترافية.

---

## ✨ المزايا الرئيسية

- 🏢 **إدارة الأبراج والوحدات** مع إعدادات مرنة لكل مبنى
- 📄 **عقود مرنة** — كل عقد بشروطه الخاصة (زيادة سنوية، غرامة تأخير، تأمين، إلخ)
- 📅 **جدول الاستحقاقات** — يُولَّد تلقائياً عند إنشاء العقد
- 💰 **تسجيل المدفوعات وإصدار إيصالات PDF**
- 🔔 **إشعارات تلقائية** — عقود قرب الانتهاء، استحقاقات متأخرة
- 📊 **لوحة تحكم** مع مؤشرات KPI وChart.js
- 📤 **تصدير Excel** للمدفوعات والعقود
- 🔐 **أدوار ومصاحيح** (Spatie) — owner, admin, accountant, maintenance
- 📝 **سجل الأحداث** (Spatie Activity Log)
- 🌐 **واجهة عربية RTL** مع Bootstrap 5 RTL وخط Cairo

---

## ⚙️ متطلبات النظام

| المتطلب | الإصدار |
|---------|---------|
| PHP     | ≥ 8.2   |
| Composer | ≥ 2.x  |
| MySQL   | ≥ 8.0   |
| Node.js | ≥ 18    |

---

## 🚀 تثبيت المشروع

```bash
# 1. استنساخ المشروع
cd c:\Users\DELL\Videos\ager

# 2. تثبيت حزم PHP
composer install

# 3. إعداد ملف البيئة
cp .env.example .env
php artisan key:generate

# 4. إنشاء قاعدة البيانات
# في MySQL:
# CREATE DATABASE rental_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 5. تهيئة قاعدة البيانات مع البيانات الأولية
php artisan migrate:fresh --seed

# 6. ربط مجلد التخزين
php artisan storage:link

# 7. تثبيت حزم Node وبناء Assets
npm install
npm run dev

# 8. تشغيل السيرفر
php artisan serve
```

---

## 👤 حسابات الدخول الافتراضية (بعد Seeding)

| الدور      | البريد الإلكتروني       | كلمة المرور |
|------------|------------------------|-------------|
| المالك (owner)      | owner@rental.com       | password    |
| المشرف (admin)     | admin@rental.com       | password    |
| المحاسب (accountant) | accountant@rental.com | password    |

---

## 🗓️ الجدولة التلقائية (Scheduler)

لتشغيل المهام المجدولة يومياً، أضف هذا لـ Windows Task Scheduler أو cron:

```bash
# Linux cron (كل دقيقة)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Windows — PowerShell (شغّل كل ساعة)
php artisan schedule:run
```

**المهام المجدولة:**
- `08:00` — `MarkOverdueSchedulesJob` — تحديد الاستحقاقات المتأخرة وإرسال إشعار
- `08:05` — `GenerateContractEndingNotificationsJob` — إشعار العقود خلال 30 يوم

---

## 📬 قوائم الانتظار (Queues)

```bash
# تشغيل worker للقوائم
php artisan queue:work --queue=default --tries=3
```

---

## 🧪 الاختبارات

```bash
# تشغيل كل الاختبارات
php artisan test

# أو مع composer
composer test

# اختبار محدد
php artisan test tests/Unit/ContractServiceTest.php
```

**الاختبارات المكتوبة:**
- `ContractServiceTest` — توليد جداول شهرية/ربعية/سنوية، زيادات سنوية
- `LatePenaltyServiceTest` — تطبيق غرامات التأخير بالنسبة والمبلغ الثابت
- `BuildingCrudTest` — CRUD كامل مع التحقق من الصلاحيات
- `PaymentRecordingTest` — تسجيل دفعة كاملة/جزئية والتحقق من الحالة

---

## 🏗️ البنية المعمارية

```
app/
├── Http/
│   ├── Controllers/     # 12 controller
│   └── Requests/        # Form Request validation
├── Models/              # 11 model with relationships
├── Services/            # Business logic layer
│   ├── ContractService
│   ├── RentScheduleService
│   ├── LatePenaltyService
│   ├── PaymentService
│   └── NotificationService
├── Jobs/                # Scheduled jobs
├── Exports/             # Excel exports
└── Providers/

database/
├── migrations/          # 11 migration files
└── seeders/             # Roles, Users, Sample data

resources/views/         # 40+ Blade views (Arabic RTL)
├── layouts/app.blade.php
├── dashboard/
├── buildings/
├── units/
├── tenants/
├── contracts/           # 6-step wizard
├── rent-schedules/
├── payments/            # includes PDF receipt
├── maintenance/
├── notifications/
├── reports/
└── users/
```

---

## 🔐 الأدوار والصلاحيات

| الصلاحية | owner | admin | accountant | maintenance |
|---------|-------|-------|------------|-------------|
| إدارة المباني | ✅ | ✅ | 👁 | 👁 |
| إدارة العقود | ✅ | ✅ | 👁 | ❌ |
| تسجيل المدفوعات | ✅ | ✅ | ✅ | ❌ |
| التقارير | ✅ | ✅ | ✅ | ❌ |
| الصيانة | ✅ | ✅ | ❌ | ✅ |
| إدارة المستخدمين | ✅ | ❌ | ❌ | ❌ |
| سجل الأحداث | ✅ | ✅ | ❌ | ❌ |

---

## 📦 الحزم المستخدمة

| الحزمة | الغرض |
|--------|--------|
| `laravel/breeze` | مصادقة Blade |
| `spatie/laravel-permission` | أدوار وصلاحيات |
| `spatie/laravel-activitylog` | سجل الأحداث |
| `maatwebsite/excel` | تصدير Excel |
| `barryvdh/laravel-dompdf` | إصدار PDF |
| `laravel/sanctum` | API tokens |

---

## 📝 ملاحظات الإعداد

1. **MySQL** — تأكد من تشغيل MySQL Server ثم أنشئ قاعدة البيانات `rental_db`
2. **Storage** — شغّل `php artisan storage:link` لربط مجلد الصور والمرفقات
3. **Queue** — شغّل `php artisan queue:work` لمعالجة الإشعارات
4. **Scheduler** — سجّل `php artisan schedule:run` في Task Scheduler لتشغيل المهام اليومية
5. **APP_URL** — عدّل `.env` لتستخدم رابطك الحقيقي عند النشر
