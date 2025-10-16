# 🤖 AI Agent Quick Start Guide

> **برای AI Agents که می‌خواهند روی این پروژه کار کنند**

## ⚡ 30-Second Overview

```
پروژه: ContentCraft AI - WordPress AI Content Generator
وضعیت: 60% Complete, Core 100% Functional
Backend: FastAPI ✅ (آماده)
Frontend: WordPress Plugin ✅ (95% آماده)
نیاز: JavaScript handlers + Tests (اختیاری)
```

---

## 📖 مسیر یادگیری (10 دقیقه)

### گام 1: بخوان (5 دقیقه)
```
1. این فایل (AI_AGENT_QUICKSTART.md)     ← همین
2. todo-management/PROJECT_STATUS.md      ← وضعیت دقیق
3. AI_AGENT_INSTRUCTIONS.md               ← راهنمای کامل
4. SETUP_GUIDE.md                         ← نحوه اجرا
```

### گام 2: اجرا کن (3 دقیقه)
```bash
# Backend
cd infra && docker-compose up -d

# Check
curl http://localhost:8000/api/health
# {"status":"healthy","version":"1.0.0",...}

# WordPress plugin
cp -r plugin /path/to/wp-content/plugins/contentcraft-ai
# Activate در WordPress admin
```

### گام 3: تست کن (2 دقیقه)
```
1. WordPress Admin → ContentCraft AI → Settings
2. Test Connection → ✅ Success
3. Content Studio → Generate → کار می‌کنه! 🎉
```

---

## 🎯 چی کار کنم؟ (Priority List)

### 🔥 HIGH (باید انجام بشه)

#### 1. Brand Voice JavaScript
**فایل**: `plugin/admin/assets/js/brand-voice.js`
**زمان**: 30 دقیقه

```javascript
// Template:
(function($) {
  // Form submit → /wp-json/contentcraft/v1/brand/train
  // Show progress
  // Display result
  // Enable/disable toggle
})(jQuery);

// الگو: content-studio.js را ببین
```

#### 2. Admin General Functions
**فایل**: `plugin/admin/assets/js/admin.js`
**زمان**: 20 دقیقه

```javascript
// Test connection button (settings page)
// Notice dismissal
// Common AJAX wrapper
// Settings save notification
```

#### 3. Product Writer JS
**فایل**: `plugin/admin/assets/js/product.js`
**زمان**: 40 دقیقه

```javascript
// WooCommerce meta box handler
// Generate → /wp-json/contentcraft/v1/product/generate
// Apply to product fields
// Loading states
```

---

### 📊 MEDIUM (خوبه انجام بشه)

#### 4. Bulk Generator
**فایل**: `plugin/admin/views/bulk-generator.php` + JS
**زمان**: 2 ساعت

- Product selection UI
- Chunked AJAX processing
- Progress bar
- CSV report

#### 5. Tests
**فایل**: `backend/tests/` + `plugin/tests/`
**زمان**: 3 ساعت

- pytest برای FastAPI
- PHPUnit برای WordPress
- Mock LLM responses

---

### 🔵 LOW (اختیاری)

- CI/CD pipeline
- Translation .pot
- WordPress.org readme
- Uninstaller

---

## 🚫 چی کار نکنم؟ (مهم!)

### ❌ تغییر نده:
- Core architecture
- API contracts (schemas)
- Security implementations
- Database schema
- Docker config
- Existing services

### ❌ ننویس از اول:
- FastAPI backend (کامله!)
- WordPress core classes (کامله!)
- CSS (600+ line زیباست!)
- Existing JavaScript

### ❌ نادیده نگیر:
- WordPress nonce
- Capability checks
- Input sanitization
- Existing patterns

---

## 📂 ساختار سریع

```
contentcraft-ai/
├── 📚 Docs (همه کامله ✅)
│   ├── README.md
│   ├── SETUP_GUIDE.md
│   ├── AI_AGENT_INSTRUCTIONS.md
│   └── docs/
│
├── 🔧 WordPress Plugin (95% ✅)
│   ├── plugin/contentcraft-ai.php ✅
│   ├── plugin/includes/*.php ✅ (9 classes)
│   ├── plugin/admin/views/*.php ✅ (4 pages)
│   ├── plugin/admin/assets/css/admin.css ✅
│   ├── plugin/admin/assets/js/content-studio.js ✅
│   └── plugin/admin/assets/js/*.js ⏳ (3 files needed)
│
├── 🐍 FastAPI Backend (100% ✅)
│   ├── backend/app/main.py ✅
│   ├── backend/app/routers/*.py ✅ (5 files)
│   ├── backend/app/services/*.py ✅ (6 files)
│   └── backend/tests/*.py ⏳ (empty)
│
└── 🐳 Docker (100% ✅)
    └── infra/docker-compose.yml ✅
```

---

## 💻 کد نمونه (الگو برای JavaScript)

### Pattern: AJAX با WordPress REST API

```javascript
(function($) {
  'use strict';

  const MyFeature = {
    init: function() {
      this.bindEvents();
    },

    bindEvents: function() {
      $('#my-button').on('click', this.handleClick.bind(this));
    },

    handleClick: function(e) {
      e.preventDefault();
      
      // Disable button
      const $btn = $(e.currentTarget);
      $btn.prop('disabled', true);
      
      // Show loading
      this.showLoading();
      
      // AJAX call
      $.ajax({
        url: contentcraftAdmin.restUrl + '/my-endpoint',
        method: 'POST',
        headers: {
          'X-WP-Nonce': contentcraftAdmin.nonce
        },
        data: JSON.stringify({
          // data
        }),
        contentType: 'application/json',
        success: (response) => {
          if (response.success) {
            this.showSuccess(response.data);
          } else {
            this.showError(response.error);
          }
        },
        error: (xhr) => {
          this.showError('Request failed');
        },
        complete: () => {
          $btn.prop('disabled', false);
          this.hideLoading();
        }
      });
    },

    showLoading: function() {
      // Show spinner
    },

    hideLoading: function() {
      // Hide spinner
    },

    showSuccess: function(data) {
      // Show success message
    },

    showError: function(message) {
      // Show error
    }
  };

  $(document).ready(function() {
    MyFeature.init();
  });

})(jQuery);
```

**الگوی کامل در**: `plugin/admin/assets/js/content-studio.js`

---

## 🔍 چک‌لیست قبل از Commit

```
قبل از commit:
□ کد تست شد؟
□ در WordPress کار می‌کنه؟
□ Nonce validation درسته؟
□ Input sanitized شده؟
□ Output escaped شده؟
□ Console error نداره؟
□ Loading state داره؟
□ Error handling داره؟
□ PROJECT_STATUS.md آپدیت شد؟
□ مستند شده؟

همه ✅ → Ready to commit!
```

---

## 🎓 یادگیری سریع

### WordPress REST API در این پروژه:

**Endpoints موجود**:
```
POST /wp-json/contentcraft/v1/content/generate
POST /wp-json/contentcraft/v1/product/generate
POST /wp-json/contentcraft/v1/image/analyze
POST /wp-json/contentcraft/v1/seo/optimize
POST /wp-json/contentcraft/v1/brand/train
POST /wp-json/contentcraft/v1/bulk/process
GET  /wp-json/contentcraft/v1/settings/test
```

**JavaScript global object**:
```javascript
contentcraftAdmin = {
  ajaxUrl: '/wp-admin/admin-ajax.php',
  restUrl: '/wp-json/contentcraft/v1',
  nonce: 'abc123...',
  ajaxNonce: 'xyz789...',
  strings: { /* translated strings */ },
  tones: { /* tone options */ },
  languages: { /* language options */ }
}
```

### FastAPI Endpoints:
```
POST /api/content/generate
POST /api/product/generate
POST /api/image/analyze
POST /api/seo/optimize
POST /api/brand/train
GET  /api/health
```

---

## 🐛 Debug Tips

### Frontend (WordPress):
```javascript
// Browser console:
console.log(contentcraftAdmin);  // Check globals
console.log($('#my-element'));   // Check jQuery

// Network tab:
// Check REST API calls
// Verify nonce header
```

### Backend (FastAPI):
```bash
# Container logs:
docker-compose logs -f fastapi

# Direct logs:
tail -f backend/logs/app.log

# Health check:
curl http://localhost:8000/api/health
```

### Common Issues:

**"Nonce verification failed"**:
```javascript
// Fix: Use correct nonce
headers: {
  'X-WP-Nonce': contentcraftAdmin.nonce  // ✅
}
```

**"Permission denied"**:
```php
// Fix: Check capability
current_user_can('edit_posts')  // ✅
```

**"FastAPI not responding"**:
```bash
# Check if running:
docker-compose ps
curl http://localhost:8000/api/health
```

---

## 📊 آمار فعلی

```
📁 Files: 60+
📝 Code: 8,000+ lines
✅ Core: 100%
✅ Backend: 100%
✅ Plugin: 95%
⏳ Tests: 0%

Next: Complete 3 JavaScript files (~ 90 minutes)
```

---

## 🚀 Mission Statement

```
هدف: تکمیل JavaScript handlers
زمان: ~2 ساعت
نتیجه: Plugin 100% functional

بعد از این:
→ Tests (optional)
→ WordPress.org submission
→ 🎉 Version 1.0.0 Release!
```

---

## 💬 سوالات متداول

**Q: از کجا شروع کنم؟**
A: Brand Voice JavaScript - ساده‌ترین task

**Q: الگوی کد چیه؟**
A: content-studio.js را ببین - همه چیز اونجاست

**Q: اگر Backend رو خراب کردم؟**
A: هیچ چی خراب نمی‌شه! Backend صرفاً خوانده می‌شه

**Q: تست کجا بنویسم؟**
A: `backend/tests/` برای pytest، `plugin/tests/` برای PHPUnit

**Q: مستندات کافیه؟**
A: آره! 10+ صفحه doc + inline comments

---

## 🎯 TL;DR (خیلی خلاصه!)

```
1. بخون: PROJECT_STATUS.md
2. اجرا کن: docker-compose up -d
3. بساز: 3 تا فایل JavaScript
4. تست کن: در WordPress
5. Commit کن: با update PROJECT_STATUS.md

Done! 🎉
```

---

## 📞 Need Help?

```
1. Read: AI_AGENT_INSTRUCTIONS.md (کامل‌ترین منبع)
2. Check: docs/api-contracts.md (API examples)
3. See: plugin/admin/assets/js/content-studio.js (pattern)
4. Run: docker-compose logs -f (debug)

Still stuck? Code is self-documented! 📖
```

---

**حالا بریم! کد بنویسیم! 💪**

**آخرین به‌روزرسانی**: 16 اکتبر 2024  
**برای**: AI Agents  
**وضعیت**: Ready to Code! 🚀

