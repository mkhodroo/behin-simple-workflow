@php
    use Behin\SimpleWorkflow\Models\Entities;
    use Behin\SimpleWorkflow\Models\Entities\Case_customer;
    use Behin\SimpleWorkflow\Models\Entities\Devices;
    use Behin\SimpleWorkflow\Models\Entities\Case_extra_docs;
    use Behin\SimpleWorkflow\Models\Entities\Pre_invoice_items;
    use Behin\SimpleWorkflow\Models\Entities\Pre_invoices;

    $customer = Case_customer::where('case_number', $case->number)->first();
    $device = Devices::where('case_number', $case->number)->first();
    $extraDocs = Case_extra_docs::where('case_number', $case->number)->get();
    $preInvoice = Pre_invoices::where('case_number', $case->number)->first();
    $preInvoiceItems = Pre_invoice_items::where('case_number', $case->number)->get();
    $repairCosts = Entities\Repair_cost::where('case_number', $case->number)->get();
    $repairIncome = Entities\Repair_incomes::where('case_number', $case->number)->first();
    $saleUnitNotes = Entities\Sale_unit_notes::where('case_number', $case->number)
        ->orderBy('created_at')
        ->get();

    $lastStatusValue = $case->getVariable('last_status');
    $lastStatus = !empty($lastStatusValue)
        ? $lastStatusValue
        : 'هنوز وضعیت جدیدی برای این پرونده ثبت نشده است.';

    $customerName = ($customer && !empty($customer->fullname)) ? $customer->fullname : 'ثبت نشده';
    $deviceName = ($device && !empty($device->name)) ? $device->name : 'ثبت نشده';
    $deviceBrand = ($device && !empty($device->brand)) ? $device->brand : 'ثبت نشده';
    $devicePower = ($device && !empty($device->power)) ? $device->power : 'ثبت نشده';
    $deviceSerial = ($device && !empty($device->serial)) ? $device->serial : 'ثبت نشده';

    $counterParty = null;
    if ($preInvoice && method_exists($preInvoice, 'counterParty')) {
        $counterParty = $preInvoice->counterParty();
    }

    $accountOwnerName = ($counterParty && !empty($counterParty->account_owner_name))
        ? $counterParty->account_owner_name
        : 'ثبت نشده';
    $bankName = ($counterParty && !empty($counterParty->bank_name))
        ? $counterParty->bank_name
        : 'ثبت نشده';
    $cardNumber = ($counterParty && !empty($counterParty->card_number))
        ? $counterParty->card_number
        : 'ثبت نشده';
    $accountNumber = ($counterParty && !empty($counterParty->account_number))
        ? $counterParty->account_number
        : 'ثبت نشده';
    $shebaNumber = ($counterParty && !empty($counterParty->sheba_number))
        ? $counterParty->sheba_number
        : 'ثبت نشده';

    $fileUrl = function ($path) {
        if (empty($path)) {
            return null;
        }

        return strpos($path, 'http') === 0 ? $path : url('public/' . ltrim($path, '/'));
    };
@endphp

<style>
/* فونت IRANSansX */
@font-face {
    font-family: 'IRANSansX';
    src: url('https://opticpardaz.com/wp-content/themes/woodmart/theme-core/assets/fonts/IRSansx-Bold.woff2') format('woff2');
    font-weight: 700;
    font-style: normal;
}

@font-face {
    font-family: 'IRANSansX';
    src: url('https://opticpardaz.com/wp-content/themes/woodmart/theme-core/assets/fonts/IRSansx.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
}

.client-service-report,
.client-service-report * {
    box-sizing: border-box;
}

.client-service-report {
    --csr-primary: #176b87;
    --csr-primary-dark: #114f66;
    --csr-accent: #f2a900;
    --csr-success: #16855b;
    --csr-danger: #c73f4d;
    --csr-ink: #172033;
    --csr-muted: #697386;
    --csr-line: #e4e8ee;
    --csr-soft: #f6f8fa;
    --csr-radius: 17px;
    width: 100%;
    max-width: 1180px;
    margin: 20px auto 40px;
    padding: 0 14px;
    direction: rtl;
    color: var(--csr-ink);
    font-family: IRANSansX, Tahoma, Arial, sans-serif;
    line-height: 1.85;
    text-align: right;
}

.client-service-report a {
    text-decoration: none;
}

/* ==============================
   هدر موبایل
   ============================== */
.csr-mobile-header {
    display: none;
}

/* ==============================
   شل اصلی - دو ستونه در دسکتاپ
   ============================== */
.csr-shell {
    display: grid;
    grid-template-columns: 260px 1fr;
    overflow: hidden;
    border: 1px solid rgba(23, 32, 51, .08);
    border-radius: 24px;
    background: #fff;
    box-shadow: 0 18px 55px rgba(20, 31, 54, .09);
    min-height: 600px;
}

/* ==============================
   سایدبار
   ============================== */
.csr-sidebar {
    background: #f0f4f7;
    border-left: 1px solid var(--csr-line);
    padding: 24px 16px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.csr-sidebar__logo {
    text-align: center;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(23, 107, 135, .12);
    margin-bottom: 8px;
}

.csr-sidebar__logo img {
    max-width: 140px;
    height: auto;
}

.csr-nav-wrap {
    flex: 1;
}

.csr-tabs {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin: 0;
    padding: 0;
    list-style: none;
}

.csr-tab {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 44px;
    padding: 10px 14px;
    border-radius: 10px;
    color: #4a5c6e;
    font-size: 13px;
    border: solid 1px #11111120;
    font-weight: 700;
    transition: .2s ease;
    position: relative;
}

.csr-tab__content {
    display: flex;
    align-items: center;
    gap: 8px;
}

.csr-tab:hover:not(.active),
.csr-tab:focus:not(.active) {
    color: var(--csr-primary);
    background: rgba(23, 107, 135, .06);
    outline: none;
}

.csr-tab.active {
    color: #fff !important;
    background: var(--csr-primary);
    box-shadow: 0 7px 18px rgba(23, 107, 135, .20);
}

.csr-tab-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 22px;
    height: 22px;
    padding: 0 6px;
    border-radius: 999px;
    background: rgba(23, 107, 135, .12);
    color: var(--csr-primary);
    font-size: 11px;
    font-weight: 700;
}

.csr-tab.active .csr-tab-count {
    color: var(--csr-primary);
    background: #fff;
    font-weight: 800;
}

/* ==============================
   محتوای اصلی
   ============================== */
.csr-main {
    display: flex;
    flex-direction: column;
}

/* --- آلرت موفقیت --- */
.csr-success-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 0;
    padding: 13px 22px;
    border: 0;
    border-bottom: 1px solid #cceada;
    border-radius: 0;
    color: #0f6948;
    background: #eaf8f1;
    font-size: 14px;
    font-weight: 700;
}

/* --- هیرو (دسکتاپ همیشه نمایش داده می‌شود) --- */
.csr-hero {
    position: relative;
    overflow: hidden;
    padding: 28px 30px 24px;
    color: #fff;
    background:
        radial-gradient(circle at 12% 8%, rgba(242, 169, 0, .20), transparent 30%),
        linear-gradient(135deg, #101927 0%, #173348 55%, #1a5368 100%);
}

.csr-hero::after {
    content: '';
    position: absolute;
    right: 0;
    bottom: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--csr-primary), var(--csr-accent));
}

.csr-hero-top {
    position: relative;
    z-index: 2;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 20px;
}

.csr-hero-info {
    flex: 1;
    min-width: 0;
}

.csr-kicker {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 7px;
    color: rgba(255, 255, 255, .68);
    font-size: 13px;
    font-weight: 700;
}

.csr-title {
    margin: 0;
    color: #fff;
    font-size: 28px;
    font-weight: 900;
}

.csr-subtitle {
    margin: 6px 0 0;
    color: rgba(255, 255, 255, .69);
    font-size: 13px;
}

/* --- وضعیت و شماره پرونده در هیرو --- */
.csr-current-status-card {
    flex: 1;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    min-width: 0;
    padding: 15px 16px;
    border-radius: 14px;
    backdrop-filter: blur(8px);
    position: relative;
    overflow: hidden;
}

/* لایه چرخشی gradient */
.csr-current-status-card::before {
    content: '';
    position: absolute;
    width: 200%;
    height: 200%;
    top: -50%;
    left: -50%;
    background-image: conic-gradient(
        rgba(255, 255, 255, 0.9) 0deg,
        rgba(255, 255, 255, 0.3) 40deg,
        rgba(23, 107, 135, 0.4) 80deg,
        rgba(255, 255, 255, 0.3) 120deg,
        rgba(242, 169, 0, 0.4) 160deg,
        rgba(255, 255, 255, 0.3) 200deg,
        rgba(23, 107, 135, 0.4) 240deg,
        rgba(255, 255, 255, 0.3) 280deg,
        rgba(255, 255, 255, 0.9) 320deg,
        rgba(255, 255, 255, 0.3) 360deg
    );
    border-radius: 14px;
    animation: borderRotate 4s linear infinite;
    z-index: -1;
}

/* لایه داخلی سفید برای ایجاد حاشیه */
.csr-current-status-card::after {
    content: '';
    position: absolute;
    inset: 5px;
    background: white;
    border-radius: 12px;
    z-index: -1;
}

@keyframes borderRotate {
    100% {
        transform: rotate(360deg);
    }
}
.csr-status-label {
    display: block;
    margin-bottom: 5px;
    color: #176b87;
    font-size: 12px;
    font-weight: 700;
}

.csr-status-text {
    display: block;
    color: black;
    font-size: 16px;
    font-weight: 800;
    line-height: 1.9;
}

.csr-case-badge {
    flex: 0 0 auto;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    min-height: 39px;
    padding: 7px 13px;
    border: 1px solid rgba(255, 255, 255, .16);
    border-radius: 8px;
    color: #fff;
    background: #114f66;
    font-size: 13px;
    font-weight: 800;
    white-space: nowrap;
    backdrop-filter: blur(8px);
    align-self: flex-start;
}

/* --- سامری --- */
.csr-summary {
    position: relative;
    z-index: 2;
    display: grid;
    grid-template-columns: repeat(3, minmax(125px, 1fr));
    gap: 11px;
    margin-top: 16px;
}

.csr-summary-stat {
    min-height: 88px;
    padding: 15px 16px;
    border: 1px solid rgba(255, 255, 255, .11);
    border-radius: 14px;
    background: rgba(255, 255, 255, .075);
}

.csr-summary-label {
    display: block;
    margin-bottom: 6px;
    color: rgba(255, 255, 255, .57);
    font-size: 12px;
    font-weight: 700;
}

.csr-summary-value {
    display: block;
    color: #fff;
    font-size: 15px;
    font-weight: 800;
    overflow-wrap: anywhere;
}

/* --- هیرو اینلاین (فقط موبایل، داخل تب overview) --- */
.csr-hero-inline {
    display: none;
}

/* ==============================
   محتوای تب‌ها
   ============================== */
.csr-body {
    flex: 1;
    padding: 24px;
    background: #fbfcfd;
    overflow-y: auto;
}

.csr-panel {
    display: none;
    animation: csrFade .22s ease;
}

.csr-panel.active {
    display: block;
}

@keyframes csrFade {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}

.csr-section-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 15px;
}

.csr-section-title {
    margin: 0;
    color: var(--csr-ink);
    font-size: 18px;
    font-weight: 900;
}

.csr-section-note {
    margin: 3px 0 0;
    color: var(--csr-muted);
    font-size: 12px;
}

.csr-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    border-radius: 999px;
    color: var(--csr-primary-dark);
    background: #eaf5f8;
    font-size: 12px;
    font-weight: 800;
    white-space: nowrap;
}

.csr-info-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 12px;
}

.csr-info-card,
.csr-note-card,
.csr-payment-card,
.csr-bank-card,
.csr-empty {
    border: 1px solid var(--csr-line);
    border-radius: var(--csr-radius);
    background: #fff;
}

.csr-info-card {
    min-height: 93px;
    padding: 15px 16px;
}

.csr-info-card--wide {
    grid-column: span 2;
}

.csr-info-label {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 7px;
    color: var(--csr-muted);
    font-size: 12px;
    font-weight: 700;
}

.csr-info-label i {
    color: var(--csr-primary);
}

.csr-info-value {
    color: var(--csr-ink);
    font-size: 15px;
    font-weight: 800;
    overflow-wrap: anywhere;
}

.csr-device-files {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 16px;
}

.csr-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    min-height: 39px;
    padding: 7px 12px;
    border: 1px solid #dfe4e9;
    border-radius: 10px;
    color: #465163;
    background: #fff;
    font-size: 12px;
    font-weight: 800;
    transition: .2s ease;
}

.csr-action:hover,
.csr-action:focus {
    color: #fff;
    border-color: var(--csr-primary);
    background: var(--csr-primary);
    outline: none;
}

.csr-action--primary {
    color: #fff;
    border-color: var(--csr-primary);
    background: var(--csr-primary);
}

.csr-notes-list {
    display: grid;
    gap: 11px;
}

.csr-note-card {
    position: relative;
    padding: 16px 18px 16px 16px;
    overflow: hidden;
}

.csr-note-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 4px;
    background: var(--csr-primary);
}

.csr-note-author {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 7px;
    color: var(--csr-primary-dark);
    font-size: 13px;
    font-weight: 800;
}

.csr-note-text {
    color: #303a4b;
    font-size: 14px;
    font-weight: 500;
    line-height: 2;
    overflow-wrap: anywhere;
}

.csr-table-wrap {
    width: 100%;
    overflow-x: auto;
    border: 1px solid var(--csr-line);
    border-radius: var(--csr-radius);
    background: #fff;
    -webkit-overflow-scrolling: touch;
}

.csr-table {
    width: 100%;
    min-width: 680px;
    margin: 0 !important;
    border-collapse: separate;
    border-spacing: 0;
    color: var(--csr-ink);
    font-size: 13px;
}

.csr-table thead th {
    padding: 13px 15px;
    border: 0 !important;
    border-bottom: 1px solid var(--csr-line) !important;
    color: #677184;
    background: #f6f8fa;
    font-size: 12px;
    font-weight: 800;
    white-space: nowrap;
}

.csr-table tbody td {
    padding: 13px 15px;
    border: 0 !important;
    border-bottom: 1px solid #eef1f4 !important;
    vertical-align: middle;
}

.csr-table tbody tr:last-child td {
    border-bottom: 0 !important;
}

.csr-money {
    direction: ltr;
    text-align: right;
    white-space: nowrap;
    font-weight: 700;
}

.csr-invoice-toolbar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
    margin-bottom: 12px;
}

.csr-invoice-footer {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(260px, .65fr);
    gap: 13px;
    margin-top: 14px;
}

.csr-bank-card,
.csr-payment-card {
    padding: 17px;
}

.csr-bank-title,
.csr-payment-title {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0 0 12px;
    color: var(--csr-ink);
    font-size: 14px;
    font-weight: 800;
}

.csr-bank-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px 14px;
    margin: 0;
}

.csr-bank-item {
    display: grid;
    gap: 2px;
    padding-bottom: 7px;
    border-bottom: 1px dashed #e4e8ed;
}

.csr-bank-item dt {
    margin: 0;
    color: var(--csr-muted);
    font-size: 11px;
    font-weight: 700;
}

.csr-bank-item dd {
    margin: 0;
    color: var(--csr-ink);
    font-size: 13px;
    font-weight: 800;
    overflow-wrap: anywhere;
}

.csr-cost-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-bottom: 15px;
}

.csr-cost-item {
    padding: 14px;
    border: 1px solid #dfe7eb;
    border-radius: 13px;
    background: #f7fbfc;
}

.csr-cost-label {
    display: block;
    margin-bottom: 4px;
    color: var(--csr-muted);
    font-size: 11px;
    font-weight: 700;
}

.csr-cost-value {
    color: var(--csr-primary-dark);
    font-size: 19px;
    font-weight: 900;
}

.csr-payment-widget {
    padding-top: 15px;
    border-top: 1px solid var(--csr-line);
}

.csr-file-name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
}

.csr-file-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    border-radius: 10px;
    color: var(--csr-primary);
    background: #eaf5f8;
}

.csr-empty {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    min-height: 190px;
    padding: 24px;
    color: var(--csr-muted);
    text-align: center;
}

.csr-empty i {
    margin-bottom: 10px;
    color: #9099a7;
    font-size: 32px;
}

.csr-empty strong {
    display: block;
    margin-bottom: 4px;
    color: #3f4858;
    font-size: 14px;
}

/* --- نویگیشن موبایل --- */
.csr-nav-wrap.mobile-nav {
    display: none;
}

.csr-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 23px;
    border-top: 1px solid var(--csr-line);
    color: var(--csr-muted);
    background: #fff;
    font-size: 11px;
}

.csr-print {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #465163;
    font-weight: 800;
}

/* ==============================
   رسپانسیو - تبلت
   ============================== */
@media (max-width: 991.98px) {
    .csr-shell {
        grid-template-columns: 220px 1fr;
    }
    
    .csr-sidebar {
        padding: 20px 12px;
    }
    
    .csr-sidebar__logo img {
        max-width: 110px;
    }
    
    .csr-tab {
        padding: 8px 10px;
        font-size: 12px;
    }
    
    .csr-body {
        padding: 16px;
    }
    
    .csr-info-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    
    .csr-invoice-footer {
        grid-template-columns: 1fr;
    }
}

/* ==============================
   رسپانسیو - موبایل
   ============================== */
@media (max-width: 767.98px) {
    .client-service-report {
        margin-top: 0;
        padding: 0;
        max-width: 100%;
    }
    
    /* --- هدر ثابت موبایل --- */
    .csr-mobile-header {
        height: 8vh;
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        position: sticky;
        top: 0;
        z-index: 1000;
        background: #fff;
        padding: 10px 16px;
        border-bottom: 1px solid var(--csr-line);
        box-shadow: 0 2px 12px rgba(22, 31, 49, .06);
    }
    
    .csr-mobile-header__logo {
        flex-shrink: 0;
    }
    
    .csr-mobile-header__logo img {
        height: 24px;
        width: auto;
    }
    
    .csr-mobile-header__status {
        font-size: 11px;
        background: #114f66;
        font-weight: 700;
        color: white;
        display: flex;
        padding: 6px 10px;
        border-radius: 6px;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }
    
    .csr-mobile-header__status i {
        font-size: 14px;
    }
    
    .csr-shell {
        display: block;
        border-radius: 0;
        border: none;
        box-shadow: none;
    }
    
    .csr-sidebar {
        display: none;
    }
    
    /* مخفی کردن هیرو اصلی در موبایل */
    .csr-hero {
        display: none;
    }
    
    /* --- هیرو اینلاین (داخل تب overview) --- */
    .csr-hero-inline {
        display: block;
        background:
            radial-gradient(circle at 12% 8%, rgba(242, 169, 0, .20), transparent 30%),
            linear-gradient(135deg, #101927 0%, #173348 55%, #1a5368 100%);
        padding: 20px 16px 16px;
        border-radius: 16px;
        margin-bottom: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }
    
    .csr-hero-inline::after {
        content: '';
        position: absolute;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--csr-primary), var(--csr-accent));
    }
    
    .csr-hero-inline .csr-hero-top {
        flex-direction: column;
    }
    
    .csr-hero-inline .csr-title {
        font-size: 20px;
    }
    
    .csr-hero-inline .csr-subtitle {
        font-size: 11px;
        margin-top: 4px;
    }
    
    .csr-hero-inline .csr-current-status-card {
        width: 100%;
        flex-direction: row;
        gap: 12px;
    }
    
    .csr-hero-inline .csr-status-text {
        font-size: 14px;
    }
    
    .csr-hero-inline .csr-case-badge {
        align-self: flex-end;
    }
    
    .csr-hero-inline .csr-summary {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 8px;
        margin-top: 12px;
    }
    
    .csr-hero-inline .csr-summary-stat {
        min-height: 68px;
        padding: 10px;
    }
    
    .csr-hero-inline .csr-summary-label {
        font-size: 10px;
    }
    
    .csr-hero-inline .csr-summary-value {
        font-size: 12px;
    }
    
    .csr-body {
        padding: 16px 12px 100px;
        background: #fbfcfd;
        min-height: 75vh;
    }
    
    .csr-info-grid,
    .csr-bank-list,
    .csr-cost-list {
        grid-template-columns: 1fr 1fr;
    }
    
    .csr-info-card--wide {
        grid-column: auto;
    }
    
    .csr-section-head,
    .csr-invoice-toolbar {
        align-items: flex-start;
        flex-direction: column;
    }
    
    .csr-action {
        width: 100%;
    }
    
    .csr-invoice-footer {
        grid-template-columns: 1fr;
    }
    
    /* --- نویگیشن پایین صفحه --- */
    .csr-nav-wrap.mobile-nav {
        display: flex;
        position: fixed;
        bottom: 0;
        width: 100%;
        left: 0;
        height: 10vh;
        right: 0;
        z-index: 1000;
        padding: 6px 8px 10px;
        background: rgba(255, 255, 255, .98);
        backdrop-filter: blur(20px);
        border-top: 1px solid var(--csr-line);
        box-shadow: 0 -4px 20px rgba(22, 31, 49, .08);
        flex-direction: row;
        flex-wrap: wrap;
        align-content: flex-end;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tabs-scroll {
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
        width: 100%;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tabs-scroll::-webkit-scrollbar {
        display: none;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tabs {
        flex-direction: row;
        justify-content: space-between;
        gap: 4px;
        padding: 0;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tab {
        flex: 1;
        flex-direction: column;
        gap: 3px;
        min-height: 65px;
        padding: 8px 6px;
        font-size: 12px;
        text-align: center;
        justify-content: center;
        border-radius: 8px;
        position: relative;
        min-width: 65px;
        background: transparent;
        color: #8a8f9a;
        border: solid 1px #11111120;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tab__content {
        flex-direction: column;
        gap: 5px;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tab i {
        font-size: 18px;
        opacity: .5;
        transition: .2s ease;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tab.active {
        color: white;
        background: #114f66;
        box-shadow: none !important;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tab.active i {
        opacity: 1;
        color: white;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tab-count {
        position: absolute;
        top: 2px;
        right: 50%;
        transform: translateX(50%);
        min-width: 16px;
        height: 16px;
        font-size: 8px;
        padding: 0 4px;
        background: var(--csr-primary);
        color: #fff;
    }
    
    .csr-nav-wrap.mobile-nav .csr-tab.active .csr-tab-count {
        background: white;
        color: #114f66;
    }
    
    .csr-footer {
        padding: 13px 15px 100px;
        flex-direction: column;
        align-items: flex-start;
    }
}

/* ==============================
   پرینت
   ============================== */
@media print {
    .client-service-report {
        max-width: none;
        margin: 0;
        padding: 0;
    }

    .csr-shell {
        display: block;
        border: 0;
        box-shadow: none;
    }

    .csr-sidebar,
    .csr-nav-wrap,
    .csr-mobile-header,
    .csr-print {
        display: none !important;
    }
    
    .csr-hero {
        display: block !important;
    }
    
    .csr-hero-inline {
        display: none !important;
    }

    .csr-body {
        padding: 20px 0;
        background: #fff;
    }

    .csr-panel {
        display: block !important;
        margin-bottom: 28px;
        page-break-inside: avoid;
    }

    .csr-panel::before {
        content: attr(data-print-title);
        display: block;
        margin-bottom: 12px;
        font-size: 19px;
        font-weight: 800;
    }
}
</style>

<!-- هدر ثابت مخصوص موبایل -->
<header class="csr-mobile-header">
    <div class="csr-mobile-header__logo">
        <img src="https://opticpardaz.com/wp-content/uploads/2024/01/Header-logo-optic-pardaz.png" alt="لوگو" />
    </div>
    <div class="csr-mobile-header__status">
        <i class="fa fa-clock-o"></i>
        آخرین وضعیت: {{ $lastStatus }}
    </div>
</header>

<section class="client-service-report" dir="rtl" aria-label="گزارش پرونده خدمات">
    <div class="csr-shell">
        <!-- ========== سایدبار دسکتاپ ========== -->
        <aside class="csr-sidebar">
            <div class="csr-sidebar__logo">
                <img src="https://opticpardaz.com/wp-content/uploads/2024/01/Header-logo-optic-pardaz.png" alt="لوگو" />
            </div>
            <div class="csr-nav-wrap">
                <nav aria-label="بخش‌های گزارش">
                    <ul class="csr-tabs" role="tablist">
                        <li role="presentation">
                            <a href="#csr-overview" class="csr-tab active" data-csr-tab="csr-overview" role="tab" aria-selected="true">
                                <span class="csr-tab__content">
                                    <i class="fa fa-info-circle"></i>
                                    خلاصه پرونده
                                </span>
                            </a>
                        </li>

                        <li role="presentation">
                            <a href="#csr-notes" class="csr-tab" data-csr-tab="csr-notes" role="tab" aria-selected="false">
                                <span class="csr-tab__content">
                                    <i class="fa fa-commenting-o"></i>
                                    یادداشت‌های فروش
                                </span>
                                <span class="csr-tab-count">{{ $saleUnitNotes->count() }}</span>
                            </a>
                        </li>

                        <li role="presentation">
                            <a href="#csr-invoice" class="csr-tab" data-csr-tab="csr-invoice" role="tab" aria-selected="false">
                                <span class="csr-tab__content">
                                    <i class="fa fa-file-text-o"></i>
                                    پیش‌فاکتور
                                </span>
                                <span class="csr-tab-count">{{ $preInvoiceItems->count() }}</span>
                            </a>
                        </li>

                        @if ($repairCosts->count())
                            <li role="presentation">
                                <a href="#csr-payment" class="csr-tab" data-csr-tab="csr-payment" role="tab" aria-selected="false">
                                    <span class="csr-tab__content">
                                        <i class="fa fa-credit-card"></i>
                                        پرداخت
                                    </span>
                                </a>
                            </li>
                        @endif

                        <li role="presentation">
                            <a href="#csr-files" class="csr-tab" data-csr-tab="csr-files" role="tab" aria-selected="false">
                                <span class="csr-tab__content">
                                    <i class="fa fa-paperclip"></i>
                                    فایل‌ها
                                </span>
                                <span class="csr-tab-count">{{ $extraDocs->count() }}</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- ========== محتوای اصلی ========== -->
        <div class="csr-main">
            @isset($success)
                <div class="alert alert-success csr-success-alert">
                    <i class="fa fa-check-circle" aria-hidden="true"></i>
                    {{ $success }}
                </div>
            @endisset

            <!-- هیرو دسکتاپ (در موبایل مخفی می‌شود) -->
            <header class="csr-hero">
                <div class="csr-hero-top">
                    <div class="csr-hero-info">
                        <div class="csr-kicker">
                            <i class="fa fa-wrench" aria-hidden="true"></i>
                            سامانه پیگیری خدمات و تعمیرات
                        </div>
                        <h1 class="csr-title">گزارش وضعیت پرونده</h1>
                        <p class="csr-subtitle">مشخصات دستگاه، آخرین وضعیت، اطلاعات مالی و فایل‌های مرتبط</p>
                    </div>

                    <div class="csr-current-status-card">
                        <div>
                            <span class="csr-status-label">آخرین وضعیت اعلام‌شده</span>
                            <strong class="csr-status-text">{{ $lastStatus }}</strong>
                        </div>
                        
                        <div class="csr-case-badge">
                            <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                            پرونده {{ $case->number }}
                        </div>
                    </div>
                </div>

            </header>

            <main class="csr-body">
                <section id="csr-overview" class="csr-panel active" data-print-title="خلاصه پرونده" role="tabpanel">
                    <!-- --- هیرو اینلاین (فقط موبایل) --- -->
                    <div class="csr-hero-inline">
                        <div class="csr-hero-top">
                            <div class="csr-current-status-card">
                                <div>
                                    <span class="csr-status-label">آخرین وضعیت اعلام‌شده</span>
                                    <strong class="csr-status-text">{{ $lastStatus }}</strong>
                                </div>
                                
                                <div class="csr-case-badge">
                                    <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                    پرونده {{ $case->number }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="csr-section-head">
                        <div>
                            <h2 class="csr-section-title">مشخصات مشتری و دستگاه</h2>
                            <p class="csr-section-note">اطلاعات پایه‌ای که برای شناسایی و پیگیری پرونده لازم است.</p>
                        </div>
                        <span class="csr-chip"><i class="fa fa-database"></i> اطلاعات پرونده</span>
                    </div>

                    <div class="csr-info-grid">
                        <div class="csr-info-card csr-info-card--wide">
                            <span class="csr-info-label"><i class="fa fa-user-o"></i> نام مشتری</span>
                            <div class="csr-info-value">{{ $customerName }}</div>
                        </div>

                        <div class="csr-info-card">
                            <span class="csr-info-label"><i class="fa fa-folder-open-o"></i> شماره پرونده</span>
                            <div class="csr-info-value">{{ $case->number }}</div>
                        </div>

                        <div class="csr-info-card">
                            <span class="csr-info-label"><i class="fa fa-cog"></i> نام دستگاه</span>
                            <div class="csr-info-value">{{ $deviceName }}</div>
                        </div>

                        <div class="csr-info-card">
                            <span class="csr-info-label"><i class="fa fa-industry"></i> برند</span>
                            <div class="csr-info-value">{{ $deviceBrand }}</div>
                        </div>

                        <div class="csr-info-card">
                            <span class="csr-info-label"><i class="fa fa-bolt"></i> توان</span>
                            <div class="csr-info-value">{{ $devicePower }}</div>
                        </div>

                        <div class="csr-info-card csr-info-card--wide">
                            <span class="csr-info-label"><i class="fa fa-barcode"></i> شماره سریال</span>
                            <div class="csr-info-value" dir="ltr">{{ $deviceSerial }}</div>
                        </div>
                    </div>

                    @if ($device)
                        <div class="csr-device-files">
                            @if ($fileUrl($device->initial_pic))
                                <a class="csr-action" href="{{ $fileUrl($device->initial_pic) }}" target="_blank" rel="noopener" download>
                                    <i class="fa fa-picture-o"></i>
                                    تصویر اولیه دستگاه
                                </a>
                            @endif

                            @if ($fileUrl($device->plaque_pic))
                                <a class="csr-action" href="{{ $fileUrl($device->plaque_pic) }}" target="_blank" rel="noopener" download>
                                    <i class="fa fa-id-card-o"></i>
                                    تصویر پلاک دستگاه
                                </a>
                            @endif
                        </div>
                    @endif
                </section>

                <section id="csr-notes" class="csr-panel" data-print-title="یادداشت‌های واحد فروش" role="tabpanel">
                    <div class="csr-section-head">
                        <div>
                            <h2 class="csr-section-title">توضیحات واحد فروش</h2>
                            <p class="csr-section-note">پیام‌ها و توضیحات ثبت‌شده برای اطلاع مشتری.</p>
                        </div>
                        <span class="csr-chip">{{ $saleUnitNotes->count() }} یادداشت</span>
                    </div>

                    @if ($saleUnitNotes->count())
                        <div class="csr-notes-list">
                            @foreach ($saleUnitNotes as $note)
                                <article class="csr-note-card">
                                    <div class="csr-note-author">
                                        <i class="fa fa-user-circle-o"></i>
                                        واحد فروش
                                    </div>
                                    <div class="csr-note-text">{{ $note->note }}</div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="csr-empty">
                            <i class="fa fa-commenting-o"></i>
                            <strong>یادداشتی ثبت نشده است</strong>
                            <span>در حال حاضر توضیحی از طرف واحد فروش برای نمایش وجود ندارد.</span>
                        </div>
                    @endif
                </section>

                <section id="csr-invoice" class="csr-panel" data-print-title="پیش‌فاکتور" role="tabpanel">
                    <div class="csr-section-head">
                        <div>
                            <h2 class="csr-section-title">پیش‌فاکتور خدمات</h2>
                            <p class="csr-section-note">شرح خدمات، تعداد و مبالغ ثبت‌شده برای این پرونده.</p>
                        </div>
                        <span class="csr-chip">{{ $preInvoiceItems->count() }} ردیف</span>
                    </div>

                    @if (($preInvoice && !empty($preInvoice->invoice_pdf_file)))
                        <div class="csr-invoice-toolbar">
                            <a class="csr-action csr-action--primary" href="{{ $fileUrl($preInvoice->invoice_pdf_file) }}" target="_blank" rel="noopener">
                                <i class="fa fa-file-pdf-o"></i>
                                دانلود فایل پیش‌فاکتور
                            </a>
                        </div>
                    @endif

                    @if ($preInvoiceItems->count())
                        <div class="table-responsive csr-table-wrap">
                            <table class="table csr-table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">ردیف</th>
                                        <th>شرح کالا / خدمات</th>
                                        <th>قیمت واحد</th>
                                        <th>تعداد</th>
                                        <th>قیمت کل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($preInvoiceItems as $preInvoiceItem)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $preInvoiceItem->name ?? '—' }}</td>
                                            <td class="csr-money">{{ number_format($preInvoiceItem->unit_price ?? 0) }}</td>
                                            <td>{{ $preInvoiceItem->number ?? '—' }}</td>
                                            <td class="csr-money">{{ number_format($preInvoiceItem->price ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="csr-empty">
                            <i class="fa fa-file-text-o"></i>
                            <strong>ردیفی در پیش‌فاکتور ثبت نشده است</strong>
                            <span>پس از ثبت اقلام، جزئیات قیمت در این بخش نمایش داده می‌شود.</span>
                        </div>
                    @endif

                    @if ($preInvoice)
                        <div class="csr-invoice-footer">
                            <div class="csr-bank-card">
                                <h3 class="csr-bank-title"><i class="fa fa-bank"></i> اطلاعات واریز</h3>
                                <dl class="csr-bank-list">
                                    <div class="csr-bank-item">
                                        <dt>نام صاحب حساب</dt>
                                        <dd>{{ $accountOwnerName }}</dd>
                                    </div>
                                    <div class="csr-bank-item">
                                        <dt>نام بانک</dt>
                                        <dd>{{ $bankName }}</dd>
                                    </div>
                                    <div class="csr-bank-item">
                                        <dt>شماره کارت</dt>
                                        <dd dir="ltr">{{ $cardNumber }}</dd>
                                    </div>
                                    <div class="csr-bank-item">
                                        <dt>شماره حساب</dt>
                                        <dd dir="ltr">{{ $accountNumber }}</dd>
                                    </div>
                                    <div class="csr-bank-item" style="grid-column: 1 / -1;">
                                        <dt>شماره شبا</dt>
                                        <dd dir="ltr">{{ $shebaNumber }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="csr-bank-card">
                                <h3 class="csr-bank-title"><i class="fa fa-comment-o"></i> توضیحات پیش‌فاکتور</h3>
                                <div class="csr-note-text">
                                    {{ $preInvoice->pre_invoice_description ?: 'توضیحی ثبت نشده است.' }}
                                </div>
                            </div>
                        </div>
                    @endif
                </section>

                @if ($repairCosts->count())
                    <section id="csr-payment" class="csr-panel" data-print-title="اطلاعات پرداخت" role="tabpanel">
                        <div class="csr-section-head">
                            <div>
                                <h2 class="csr-section-title">اطلاعات پرداخت</h2>
                                <p class="csr-section-note">مبالغ ثبت‌شده و وضعیت رسید یا پرداخت مشتری.</p>
                            </div>
                            <span class="csr-chip"><i class="fa fa-credit-card"></i> امور مالی</span>
                        </div>

                        <div class="csr-payment-card">
                            <h3 class="csr-payment-title"><i class="fa fa-money"></i> مبالغ قابل پرداخت</h3>

                            <div class="csr-cost-list">
                                @foreach ($repairCosts as $cost)
                                    <div class="csr-cost-item">
                                        <span class="csr-cost-label">مبلغ ردیف {{ $loop->iteration }}</span>
                                        <strong class="csr-cost-value">{{ number_format($cost->cost ?? 0) }}</strong>
                                    </div>
                                @endforeach
                            </div>

                            <div class="csr-payment-widget">
                                @include('SimpleWorkflowView::Core.Form.field-generator', [
                                    'fieldName' => 'پرداخت هزینه',
                                    'fieldId' => 'repair_incomes',
                                    'fieldClass' => 'col-sm-12',
                                    'readOnly' => true,
                                    'required' => false,
                                    'fieldValue' => null,
                                    'fieldValueAlt' => null,
                                ])
                            </div>
                        </div>
                    </section>
                @endif

                <section id="csr-files" class="csr-panel" data-print-title="فایل‌های مرتبط با پرونده" role="tabpanel">
                    <div class="csr-section-head">
                        <div>
                            <h2 class="csr-section-title">فایل‌های مرتبط با پرونده</h2>
                            <p class="csr-section-note">تصاویر، گزارش‌ها و مستندات قابل دریافت.</p>
                        </div>
                        <span class="csr-chip">{{ $extraDocs->count() }} فایل</span>
                    </div>

                    @if ($extraDocs->count())
                        <div class="table-responsive csr-table-wrap">
                            <table class="table csr-table">
                                <thead>
                                    <tr>
                                        <th style="width: 60px;">ردیف</th>
                                        <th>نام فایل</th>
                                        <th style="width: 180px;">دریافت</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($extraDocs as $doc)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <span class="csr-file-name">
                                                    <span class="csr-file-icon"><i class="fa fa-file-o"></i></span>
                                                    {{ $doc->name }}
                                                </span>
                                            </td>
                                            <td>
                                                <a class="csr-action" href="{{ $fileUrl($doc->file) }}" target="_blank" rel="noopener" download>
                                                    <i class="fa fa-download"></i>
                                                    دانلود فایل
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="csr-empty">
                            <i class="fa fa-paperclip"></i>
                            <strong>فایلی برای نمایش وجود ندارد</strong>
                            <span>مستندات پرونده پس از ثبت در این قسمت قابل دریافت خواهند بود.</span>
                        </div>
                    @endif
                </section>
            </main>

            <footer class="csr-footer">
                <span>اطلاعات این صفحه براساس آخرین داده‌های ثبت‌شده در پرونده نمایش داده می‌شود.</span>
            </footer>
        </div>
    </div>
</section>

<!-- نویگیشن موبایل (sticky bottom) -->
<nav class="csr-nav-wrap mobile-nav" aria-label="بخش‌های گزارش">
    <div class="csr-tabs-scroll">
        <ul class="csr-tabs" role="tablist">
            <li role="presentation">
                <a href="#csr-overview" class="csr-tab active" data-csr-tab="csr-overview" role="tab" aria-selected="true">
                    <span class="csr-tab__content">
                        <i class="fa fa-info-circle"></i>
                        خلاصه
                    </span>
                </a>
            </li>

            <li role="presentation">
                <a href="#csr-notes" class="csr-tab" data-csr-tab="csr-notes" role="tab" aria-selected="false">
                    <span class="csr-tab__content">
                        <i class="fa fa-commenting-o"></i>
                        یادداشت‌ها
                    </span>
                    <span class="csr-tab-count">{{ $saleUnitNotes->count() }}</span>
                </a>
            </li>

            <li role="presentation">
                <a href="#csr-invoice" class="csr-tab" data-csr-tab="csr-invoice" role="tab" aria-selected="false">
                    <span class="csr-tab__content">
                        <i class="fa fa-file-text-o"></i>
                        فاکتور
                    </span>
                    <span class="csr-tab-count">{{ $preInvoiceItems->count() }}</span>
                </a>
            </li>

            @if ($repairCosts->count())
                <li role="presentation">
                    <a href="#csr-payment" class="csr-tab" data-csr-tab="csr-payment" role="tab" aria-selected="false">
                        <span class="csr-tab__content">
                            <i class="fa fa-credit-card"></i>
                            پرداخت
                        </span>
                    </a>
                </li>
            @endif

            <li role="presentation">
                <a href="#csr-files" class="csr-tab" data-csr-tab="csr-files" role="tab" aria-selected="false">
                    <span class="csr-tab__content">
                        <i class="fa fa-paperclip"></i>
                        فایل‌ها
                    </span>
                    <span class="csr-tab-count">{{ $extraDocs->count() }}</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<script>
    $(document).ready(function() {
        // مدیریت کلیک روی تب‌ها (هم سایدبار هم نویگیشن موبایل)
        $('[data-csr-tab]').on('click', function(event) {
            event.preventDefault();

            var targetId = $(this).attr('data-csr-tab');
            
            $('[data-csr-tab]')
                .removeClass('active')
                .attr('aria-selected', 'false');

            $('[data-csr-tab="' + targetId + '"]')
                .addClass('active')
                .attr('aria-selected', 'true');

            $('.csr-panel').removeClass('active');
            $('#' + targetId).addClass('active');
            
            if (window.innerWidth <= 767) {
                var headerHeight = $('.csr-mobile-header').outerHeight() || 0;
                $('html, body').animate({
                    scrollTop: $('#' + targetId).offset().top - headerHeight - 10
                }, 300);
            }
        });

        $('.client-service-report [data-csr-print]').on('click', function(event) {
            event.preventDefault();
            window.print();
        });
        
        function adjustMobilePadding() {
            if (window.innerWidth <= 767) {
                var navHeight = $('.csr-nav-wrap.mobile-nav').outerHeight() || 60;
                $('.csr-body').css('padding-bottom', (navHeight + 20) + 'px');
                $('.csr-footer').css('padding-bottom', (20) + 'px');
            } else {
                $('.csr-body').css('padding-bottom', '');
                $('.csr-footer').css('padding-bottom', '');
            }
        }
        
        adjustMobilePadding();
        $(window).resize(adjustMobilePadding);
    });

    function uploadPaymentReceipt() {
        var fd = new FormData($('#form')[0]);
        fd.append('api_key', 'DqsssZL3Ar4bxMbJ');
        fd.append('rowId', '{{ $repairIncome && isset($repairIncome->id) ? $repairIncome->id : '' }}');
        fd.append('caseId', '{{ $case->id }}');
        fd.append('viewModelId', '63fbee59-4624-4111-b2dc-ff3e5801b72e');
        fd.append('payment_method', 'بارگذاری توسط مشتری');

        var url = "{{ route('simpleWorkflow.view-model.update-record') }}";

        send_ajax_formdata_request(url, fd, function(res) {
            console.log(res);
            show_message(res);
            window.location.reload();
        });
    }
</script>