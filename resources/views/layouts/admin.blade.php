<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'NU Clark Asset Management' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Lexend:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy-950:#080D22; --navy-900:#0C1330; --navy-800:#141B42; --navy-700:#1D2657;
            --gold-600:#C9932E; --gold-500:#E3B04E; --gold-400:#F0C876; --gold-050:#FBF3E1;
            --canvas:#FFFFFF; --surface:#FFFFFF; --surface-2:#F2F2F2;
            --ink-900:#000000; --ink-700:#000000; --ink-500:#1A1A1A; --ink-400:#333333;
            --line:#000000; --line-2:#000000;
            --success-bg:#E6F6EE; --success-ink:#0F7A4E; --success-line:#BEE7D2;
            --danger-bg:#FCEBEC; --danger-ink:#C42A3B; --danger-line:#F5C6CB;
            --warning-bg:#FCF3E1; --warning-ink:#9C6B0B; --warning-line:#F2DBA8;
            --info-bg:#E9F1FE; --info-ink:#1E56B0; --info-line:#C6D9F7;
            --r-sm:10px; --r-md:14px; --r-lg:20px;
            --shadow-sm:0 1px 2px rgba(18,22,43,.04), 0 1px 1px rgba(18,22,43,.03);
            --shadow-md:0 8px 24px -8px rgba(18,22,43,.14), 0 2px 8px -2px rgba(18,22,43,.06);
            --shadow-lg:0 24px 48px -16px rgba(12,19,48,.28), 0 4px 16px -4px rgba(12,19,48,.10);
            --font-display:'Lexend',Inter,Segoe UI,Arial,sans-serif;
        }
        /* ---------- Gray theme: Bootstrap ships these components with a
           hardcoded white background, so they're overridden here in one
           place rather than in every view that uses .card/.table/etc. ---------- */
        .card,.table,.modal-content,.dropdown-menu,.list-group-item,.offcanvas,
        .popover,.toast,.accordion-item,.accordion-button,.nav-tabs .nav-link.active,
        .page-item .page-link{
            --bs-card-bg:var(--surface); --bs-table-bg:var(--surface);
            --bs-modal-bg:var(--surface); --bs-dropdown-bg:var(--surface);
            --bs-list-group-bg:var(--surface); --bs-offcanvas-bg:var(--surface);
            --bs-popover-bg:var(--surface); --bs-toast-bg:var(--surface);
            --bs-accordion-bg:var(--surface); --bs-nav-tabs-link-active-bg:var(--surface);
            background-color:var(--surface);
        }
        .card,.modal-content,.dropdown-menu,.list-group,.offcanvas,.popover,.toast,.accordion-item{
            border-color:var(--line) !important;
        }
        .table>:not(caption)>*>*{background-color:var(--surface);border-bottom-color:var(--line-2)}
        .modal-header,.modal-footer{border-color:var(--line)}
        *{box-sizing:border-box}
        html{scrollbar-width:thin;scrollbar-color:var(--line) transparent}
        *::-webkit-scrollbar{width:9px;height:9px}
        *::-webkit-scrollbar-track{background:transparent}
        *::-webkit-scrollbar-thumb{background:var(--line);border-radius:999px;border:2px solid transparent;background-clip:padding-box}
        *::-webkit-scrollbar-thumb:hover{background:var(--ink-400);background-clip:padding-box}
        *::-webkit-scrollbar-button{display:none;width:0;height:0}
        *::-webkit-scrollbar-corner{background:transparent}
        body{margin:0;background:var(--canvas);color:var(--ink-900);font-family:Inter,Segoe UI,Arial,sans-serif;-webkit-font-smoothing:antialiased;font-feature-settings:"tnum" 1,"cv05" 1;transition:background .2s ease,color .2s ease}
        h1,h2,h3,.module-title,.page-title{font-family:var(--font-display)}
        .app-shell{display:flex;min-height:100vh}

        /* ---------- Sidebar ---------- */
        .sidebar{width:158px;background:linear-gradient(165deg,var(--navy-800) 0%,var(--navy-900) 55%,var(--navy-950) 100%);color:#fff;position:sticky;top:0;height:100vh;border-right:1px solid rgba(255,255,255,.05);z-index:20;display:flex;flex-direction:column;overflow:hidden;box-shadow:var(--shadow-lg)}
        .nav-list{padding:10px 10px 16px;display:grid;gap:7px;overflow-y:auto;flex:1;scrollbar-width:thin;scrollbar-color:rgba(255,255,255,.2) transparent}
        .nav-list::-webkit-scrollbar{width:5px}
        .nav-list::-webkit-scrollbar-thumb{background:rgba(255,255,255,.2);border-radius:999px}
        .brand-wrap{padding:22px 14px 18px;position:relative}
        .brand-wrap::after{content:'';position:absolute;left:16px;right:16px;bottom:0;height:1px;background:linear-gradient(90deg,transparent,rgba(227,176,78,.35),transparent)}
        .brand-box{display:flex;align-items:center;gap:10px}
        .brand-mark{width:36px;height:36px;border-radius:11px;background:linear-gradient(150deg,var(--gold-400),var(--gold-600));color:var(--navy-950);display:grid;place-items:center;font-size:16px;font-weight:800;box-shadow:0 6px 16px rgba(227,176,78,.35),inset 0 1px 0 rgba(255,255,255,.4);font-family:var(--font-display)}
        .brand-title{font-weight:700;font-size:12.5px;line-height:1.25;font-family:var(--font-display);letter-spacing:.01em}
        .brand-sub{font-size:9.5px;color:#9FACD6;text-transform:uppercase;letter-spacing:.06em;font-weight:600;margin-top:1px}
        .nav-linkx{position:relative;display:flex;align-items:center;gap:11px;padding:14px 12px;margin:0 2px;border-radius:11px;text-decoration:none;color:#AEB8D6;font-size:12px;font-weight:500;transition:background .15s ease,color .15s ease;letter-spacing:.01em;min-height:20px}
        .nav-linkx span{overflow:hidden;text-overflow:ellipsis}
        .nav-linkx i{font-size:16px;min-width:17px;color:#8592BC;transition:color .15s ease}
        .nav-linkx:hover{background:rgba(255,255,255,.055);color:#fff}
        .nav-linkx:hover i{color:var(--gold-400)}
        .nav-linkx.active{background:rgba(227,176,78,.12);color:#fff;font-weight:700}
        .nav-linkx.active i{color:var(--gold-500)}
        .nav-linkx.active::before{content:'';position:absolute;left:-2px;top:20%;bottom:20%;width:3px;border-radius:0 4px 4px 0;background:linear-gradient(180deg,var(--gold-400),var(--gold-600))}

        /* ---------- Topbar ---------- */
        .main{flex:1;min-width:0}
        .topbar{min-height:72px;background:linear-gradient(120deg,var(--navy-800) 0%,var(--navy-900) 55%,var(--navy-950) 100%);position:relative;overflow:hidden;display:flex;align-items:center;justify-content:space-between;padding:0 28px;position:sticky;top:0;z-index:10;box-shadow:0 4px 16px -4px rgba(12,19,48,.25)}
        .topbar::before{content:'';position:absolute;right:60px;top:-50px;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle at center,rgba(227,176,78,.22),transparent 70%);pointer-events:none}
        /* Topbar label is now a gold eyebrow above the page title, matching the
           dashboard hero style used across every tab. */
        .page-title{font-size:10.5px;font-weight:700;margin:0;color:var(--gold-400);letter-spacing:.08em;text-transform:uppercase;position:relative}
        .page-subtitle{display:none}
        .page-subtitle-hero{font-family:var(--font-display);font-size:17px;font-weight:800;color:#fff;letter-spacing:-.01em;margin-top:3px;position:relative}
        .page-subtitle-note{font-size:11.5px;color:#AEB8D6;margin-top:3px;max-width:460px;line-height:1.4;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;position:relative}
        .top-actions{display:flex;align-items:center;gap:18px;position:relative}
        .page-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap;padding-right:18px;border-right:1px solid rgba(255,255,255,.14)}
        .page-actions .btn-primaryx,.page-actions .btn-soft{padding:8px 15px;font-size:11.5px}
        .top-icon{font-size:20px;color:#C7CEE8}
        .notif-link{position:relative;color:#C7CEE8;text-decoration:none;display:inline-flex;align-items:center;width:38px;height:38px;justify-content:center;border-radius:11px;transition:background .15s ease}
        .notif-link:hover{background:rgba(255,255,255,.08)}
        .notif-badge{position:absolute;top:4px;right:5px;background:var(--danger-ink);color:#fff;border-radius:999px;min-width:16px;height:16px;padding:0 4px;display:grid;place-items:center;font-size:9px;font-weight:700;border:2px solid var(--navy-900)}
        .user-chip{display:flex;align-items:center;gap:10px;padding-left:14px;border-left:1px solid rgba(255,255,255,.14)}
        .avatar{width:36px;height:36px;border-radius:11px;background:linear-gradient(150deg,var(--gold-400),var(--gold-600));color:var(--navy-950);display:grid;place-items:center;font-size:14px;font-weight:700;font-family:var(--font-display);box-shadow:var(--shadow-sm)}
        .user-meta{line-height:1.2}.user-name{font-weight:700;font-size:12px;color:#fff}.user-role{font-size:10.5px;color:#9FACD6}
        .logout-btn{color:#FF8A93;text-decoration:none;font-size:11.5px;font-weight:600;background:none;border:none;padding:0}

        /* ---------- Surfaces & layout ---------- */
        .content{padding:28px}
        .surface{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);box-shadow:var(--shadow-sm)}
        .module-head{display:flex;align-items:flex-end;justify-content:space-between;gap:12px;margin-bottom:18px;flex-wrap:wrap}
        .module-title{font-size:19px;font-weight:700;margin:0;color:var(--ink-900);letter-spacing:-.01em;padding-left:14px;position:relative}
        .module-title::before{content:'';position:absolute;left:0;top:3px;bottom:3px;width:4px;border-radius:4px;background:linear-gradient(180deg,var(--gold-400),var(--gold-600))}
        .module-note{font-size:12.5px;color:var(--ink-500);margin-top:3px;max-width:640px;line-height:1.5}

        /* ---------- Buttons ---------- */
        .btn-primaryx{background:linear-gradient(155deg,var(--navy-700),var(--navy-900));color:#fff;border:none;border-radius:var(--r-sm);padding:10px 18px;font-weight:600;font-size:12.5px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;box-shadow:0 4px 12px -2px rgba(12,19,48,.35);transition:transform .12s ease,box-shadow .12s ease}
        .btn-primaryx:hover{color:#fff;transform:translateY(-1px);box-shadow:0 8px 20px -4px rgba(12,19,48,.4)}
        .btn-approve,.btn-reject,.btn-soft{border:none;border-radius:var(--r-sm);padding:9px 18px;font-size:11.5px;font-weight:700;color:#fff;text-decoration:none;display:inline-flex;gap:7px;align-items:center;transition:transform .12s ease,box-shadow .12s ease;letter-spacing:.01em}
        .btn-approve{background:linear-gradient(155deg,#1DAA65,var(--success-ink));box-shadow:0 4px 12px -3px rgba(15,122,78,.4)}
        .btn-reject{background:linear-gradient(155deg,#DE4A57,var(--danger-ink));box-shadow:0 4px 12px -3px rgba(196,42,59,.4)}
        .btn-soft{background:#818181;box-shadow:var(--shadow-sm)}
        .btn-approve:hover,.btn-reject:hover,.btn-soft:hover{transform:translateY(-1px)}
        .btn-soft:hover{background:#6f6f6f}
        /* Buttons that live inside the navy/blue banners (topbar + dashboard
           hero) keep their original blue instead of the gray-theme color. */
        .topbar .btn-soft,.page-hero .btn-soft{background:var(--navy-800)}
        .topbar .btn-soft:hover,.page-hero .btn-soft:hover{background:var(--navy-700)}
        .small-btn{padding:7px 13px;border-radius:9px;font-size:11.5px;font-weight:700;line-height:1;overflow:hidden}
        /* Hard cap on icon glyph size — clips any icon that tries to render
           larger than its button (font-loading races, browser extensions,
           zoom/translate tools can otherwise blow an icon up to huge size). */
        .btn-primaryx,.btn-approve,.btn-reject,.btn-soft,.small-btn,.notif-link,.mobile-menu,.top-icon{line-height:1}
        .btn-primaryx i,.btn-approve i,.btn-reject i,.btn-soft i,.small-btn i,.notif-link i,.mobile-menu i,
        i[class^="bi-"],i[class*=" bi-"]{font-size:1em!important;line-height:1!important;display:inline-block;vertical-align:-.125em;max-width:1.4em;max-height:1.4em;overflow:hidden}

        /* ---------- Search / filter ---------- */
        .search-strip{display:flex;align-items:center;gap:10px;padding:11px 14px;border:1px solid var(--line);background:var(--surface);border-radius:var(--r-sm);box-shadow:var(--shadow-sm);margin-bottom:16px}
        .search-input{border:none;background:transparent;outline:none;width:100%;font-size:12.5px;color:var(--ink-700)}
        .filter-box{min-width:110px;border-left:1px solid var(--line);padding-left:12px;display:flex;align-items:center;gap:8px}.filter-box select{border:none;background:transparent;width:100%;font-size:12.5px;outline:none;color:var(--ink-700)}

        /* ---------- Stat cards ---------- */
        .stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:16px}
        .stat-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:18px 18px 14px;position:relative;min-height:118px;box-shadow:var(--shadow-sm);overflow:hidden;transition:box-shadow .15s ease,transform .15s ease}
        .stat-card:hover{box-shadow:var(--shadow-md);transform:translateY(-2px)}
        .stat-card::before{content:'';position:absolute;left:0;top:0;right:0;height:3px;background:var(--stat-accent,var(--gold-500))}
        .stat-icon{width:40px;height:40px;border-radius:12px;display:grid;place-items:center;color:#fff;font-size:18px;margin-bottom:16px;box-shadow:var(--shadow-sm)}
        .stat-mini{position:absolute;top:18px;right:20px;font-size:11px;font-weight:700}
        .stat-label{font-size:12.5px;color:var(--ink-500);margin-bottom:3px;font-weight:500}
        .stat-value{font-size:32px;font-weight:800;line-height:1;font-family:var(--font-display);color:var(--ink-900);letter-spacing:-.02em}
        .icon-cyan{background:linear-gradient(155deg,#3BC3E0,#1D8FAE);--stat-accent:#3BC3E0}
        .icon-green{background:linear-gradient(155deg,#2BC876,var(--success-ink));--stat-accent:#2BC876}
        .icon-amber{background:linear-gradient(155deg,var(--gold-400),var(--gold-600));--stat-accent:var(--gold-500)}
        .icon-red{background:linear-gradient(155deg,#E85D6A,var(--danger-ink));--stat-accent:#E85D6A}
        .mini-green{color:var(--success-ink)}.mini-red{color:var(--danger-ink)}

        /* ---------- Panels / charts / tables ---------- */
        .panel-grid-2,.report-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
        .chart-card,.report-box{padding:0;border-radius:var(--r-lg);overflow:hidden;background:var(--surface);border:1px solid var(--line);box-shadow:var(--shadow-sm)}
        .chart-head{height:42px;display:flex;align-items:center;gap:8px;padding:0 16px;border-bottom:1px solid var(--line);font-weight:700;font-size:13px;background:var(--surface-2);color:var(--ink-900)}
        .chart-body{padding:18px;background:var(--surface)}
        .chart-wrap{height:280px;position:relative}
        .data-panel{padding:16px;background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);box-shadow:var(--shadow-sm)}
        .report-accordion-toggle{cursor:pointer;user-select:none;align-items:center!important}
        .report-accordion-toggle .bi-chevron-down{font-size:16px;color:var(--ink-400);transition:transform .2s ease;flex-shrink:0}
        .report-accordion-toggle[aria-expanded="true"] .bi-chevron-down{transform:rotate(180deg);color:var(--gold-600)}
        .report-accordion-toggle:hover .module-title{color:var(--navy-800)}
        .data-table{width:100%;border-collapse:collapse;font-size:12.5px}
        .data-table thead th{padding:11px 14px;background:var(--surface-2);border-bottom:2px solid var(--line);color:var(--ink-500);text-transform:uppercase;font-size:10.5px;letter-spacing:.05em;font-weight:700;text-align:left}
        .data-table thead th:not(:last-child){border-right:1px solid var(--line)}
        .data-table tbody td{padding:12px 14px;border-top:1px solid var(--line);vertical-align:middle;color:var(--ink-700)}
        .data-table tbody td:not(:last-child){border-right:1px solid var(--line-2)}
        .data-table tbody tr:nth-child(even){background:var(--surface-2)}
        .data-table tbody tr{transition:background .12s ease}
        .data-table tbody tr:hover{background:var(--gold-050)}
        .data-table{border:1px solid var(--line);border-radius:var(--r-md);overflow:hidden}
        .img-fallback{width:64px;height:64px;min-width:64px;border-radius:12px;border:1px solid var(--line);background:var(--surface-2);display:none;align-items:center;justify-content:center;color:var(--ink-400);font-size:20px}
        .asset-card,.request-card,.issue-card,.supplier-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-md);padding:14px 16px;margin-bottom:12px;box-shadow:var(--shadow-sm);transition:box-shadow .15s ease}
        .asset-card:hover,.request-card:hover,.issue-card:hover,.supplier-card:hover{box-shadow:var(--shadow-md)}
        .grid-cards{display:grid;grid-template-columns:1fr 1fr;gap:16px}
        .supplier-card{padding:16px 20px;min-height:230px}
        .supplier-meta{color:var(--ink-500);font-size:11.5px;margin-bottom:18px}
        .supplier-avatar{width:26px;height:26px;border-radius:50%;display:grid;place-items:center;background:var(--info-bg);color:var(--info-ink);font-size:12px;margin-bottom:10px;font-weight:700}

        /* ---------- Text helpers ---------- */
        .muted-line{color:var(--ink-500);font-size:12.5px;margin:8px 0;display:flex;gap:10px;align-items:flex-start}
        .tag{display:inline-block;padding:2px 9px;font-size:10.5px;border-radius:7px;background:var(--surface-2);border:1px solid var(--line);color:var(--ink-500);margin-right:6px;font-weight:600}
        .tiny{font-size:11.5px;color:var(--ink-500)}.tiny-2{font-size:10.5px;color:var(--ink-400)}
        .code-badge{display:inline-block;padding:2px 7px;border-radius:7px;font-size:9.5px;background:var(--info-bg);color:var(--info-ink);font-weight:700;letter-spacing:.02em}
        .pill-opex{background:var(--warning-bg);color:var(--warning-ink);border-radius:12px;padding:2px 8px;font-size:10.5px;font-weight:700}

        /* ---------- Status pills ---------- */
        .status{display:inline-flex;align-items:center;gap:6px;border-radius:20px;padding:3px 10px;font-size:10.5px;font-weight:700;border:1px solid transparent;letter-spacing:.01em}
        .status.available{background:var(--success-bg);color:var(--success-ink);border-color:var(--success-line)}
        .status.in-use{background:var(--info-bg);color:var(--info-ink);border-color:var(--info-line)}
        .status.maintenance{background:var(--warning-bg);color:var(--warning-ink);border-color:var(--warning-line)}
        .status.pending{background:var(--warning-bg);color:var(--warning-ink);border-color:var(--warning-line)}
        .status.approved{background:var(--success-bg);color:var(--success-ink);border-color:var(--success-line)}
        .status.low{background:var(--danger-bg);color:var(--danger-ink);border-color:var(--danger-line)}

        /* ---------- Stock bar ---------- */
        .stock-bar{height:5px;border-radius:999px;background:var(--line);overflow:hidden;width:90px;margin-top:4px}
        .stock-fill{height:100%;background:linear-gradient(90deg,#2BC876,var(--success-ink))}
        .stock-fill.low{background:linear-gradient(90deg,#E85D6A,var(--danger-ink))}

        .request-actions{display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap}
        .empty-state{padding:36px 20px;text-align:center;color:var(--ink-500);font-size:13px}

        /* ---------- Forms ---------- */
        .form-shell{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:24px;box-shadow:var(--shadow-sm)}
        .form-control,.form-select,.form-check-input{border-color:var(--line);border-radius:var(--r-sm);font-size:13px;padding:9px 12px}
        .form-control:focus,.form-select:focus{box-shadow:0 0 0 3px rgba(227,176,78,.18);border-color:var(--gold-500)}
        .form-label{font-size:12px;font-weight:700;color:var(--ink-700);margin-bottom:5px}

        /* ---------- Sectioned forms (grouped cards with a header) ---------- */
        .form-section{border:1px solid var(--line);border-radius:var(--r-lg);background:var(--surface);margin-bottom:18px;box-shadow:var(--shadow-sm)}
        .form-section-head{display:flex;align-items:center;gap:12px;padding:15px 20px;background:var(--surface-2);border-bottom:1px solid var(--line);border-top-left-radius:var(--r-lg);border-top-right-radius:var(--r-lg)}
        .form-section-icon{width:34px;height:34px;min-width:34px;border-radius:10px;display:grid;place-items:center;background:var(--gold-050);color:var(--gold-600);font-size:15px}
        .form-section-title{font-size:13.5px;font-weight:700;color:var(--ink-900);margin:0;font-family:var(--font-display)}
        .form-section-sub{font-size:11.5px;color:var(--ink-500);margin-top:1px}
        .form-section-body{padding:22px 20px}
        .form-actionbar{position:sticky;bottom:0;background:var(--surface);border-top:1px solid var(--line);border-radius:0 0 var(--r-lg) var(--r-lg);padding:16px 20px;display:flex;gap:10px;align-items:center;box-shadow:0 -4px 16px -8px rgba(18,22,43,.12);margin-top:-1px}

        /* ---------- Settings ---------- */
        .settings-list{display:grid;gap:14px;max-width:640px}
        .settings-item{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-md);padding:18px 20px;box-shadow:var(--shadow-sm)}
        .settings-item h5{font-size:14px;font-weight:700;margin:0 0 6px;color:var(--ink-900)}

        /* ---------- Tabs ---------- */
        .page-tabs{display:flex;gap:22px;margin:12px 0 16px;font-size:12.5px;font-weight:600;flex-wrap:wrap;border-bottom:1px solid var(--line)}
        .page-tabs a,.page-tabs span{color:var(--ink-500);text-decoration:none;padding-bottom:10px;border-bottom:2px solid transparent;transition:color .15s ease,border-color .15s ease}
        .page-tabs .active,.page-tabs a.active{color:var(--navy-800);border-color:var(--gold-500);font-weight:700}
        .page-tabs a:hover{color:var(--navy-800)}

        /* ---------- QR / scanner ---------- */
        .qr-card{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:18px;box-shadow:var(--shadow-sm)}
        .qr-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px}
        .qr-tiles{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
        .qr-tile{background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-md);padding:14px;text-align:center}
        .scanner-shell{background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-lg);padding:16px}
        .scanner-box{background:linear-gradient(155deg,var(--navy-800),var(--navy-950));border-radius:var(--r-lg);padding:14px;min-height:340px;color:#fff}
        .scanner-result{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-md);padding:16px;box-shadow:var(--shadow-sm)}
        .report-stat{background:var(--surface);border:1px solid var(--line);border-radius:var(--r-lg);padding:16px;box-shadow:var(--shadow-sm)}

        /* ---------- Key/value info table ---------- */
        .kv-table{width:100%;border-collapse:separate;border-spacing:0;font-size:12.5px;border:1px solid var(--line);border-radius:var(--r-md);overflow:hidden;margin-bottom:18px;box-shadow:var(--shadow-sm);background:var(--surface)}
        .kv-table tr:not(:first-child) th,.kv-table tr:not(:first-child) td{border-top:1px solid var(--line-2)}
        .kv-table tr:nth-child(even){background:var(--surface-2)}
        .kv-table tr:nth-child(even) th{background:var(--surface-2)}
        .kv-table th{width:190px;text-align:left;padding:11px 16px;background:var(--surface);color:var(--ink-500);font-weight:700;font-size:10.5px;text-transform:uppercase;letter-spacing:.04em;vertical-align:top;white-space:nowrap;border-right:1px solid var(--line-2)}
        .kv-table th i{color:var(--gold-600);font-size:12px;margin-right:2px}
        .kv-table td{padding:11px 16px;color:var(--ink-900);line-height:1.5;font-weight:500}
        .kv-table td.kv-wide{white-space:normal}

        /* ---------- Review / approval item cards (requisitions & proposals) ---------- */
        .review-item-card{border:1px solid var(--line);border-radius:var(--r-md);padding:14px 16px;margin-bottom:12px;background:var(--surface-2)}
        .review-item-head{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:12px;flex-wrap:wrap}
        .review-item-name{font-weight:700;font-size:12.5px;color:var(--ink-900)}
        .chip-row{display:flex;gap:6px;flex-wrap:wrap}
        .chip-mini{display:inline-flex;align-items:center;gap:4px;font-size:10.5px;font-weight:700;padding:3px 9px;border-radius:20px;background:var(--surface);border:1px solid var(--line);color:var(--ink-700);white-space:nowrap}
        .chip-mini.chip-warn{background:var(--warning-bg);color:var(--warning-ink);border-color:var(--warning-line)}
        .field-hint{font-size:10.5px;color:var(--ink-500);margin-top:4px;line-height:1.4}

        /* ---------- Reject disclosure (keeps the danger action out of the way until asked for) ---------- */
        .reject-disclosure{margin-top:14px;border-top:1px dashed var(--line);padding-top:12px}
        .reject-disclosure summary{cursor:pointer;font-size:11.5px;font-weight:700;color:var(--ink-500);list-style:none;display:inline-flex;align-items:center;gap:6px;padding:4px 0;user-select:none}
        .reject-disclosure summary:hover{color:var(--danger-ink)}
        .reject-disclosure summary::-webkit-details-marker{display:none}
        .reject-disclosure[open] summary{color:var(--danger-ink);margin-bottom:8px}
        .reject-disclosure-body{background:var(--danger-bg);border:1px solid var(--danger-line);border-radius:var(--r-md);padding:14px}
        .kv-table tr{animation:fadeInUp .35s ease both}
        .kv-table tr:nth-child(1){animation-delay:.02s}.kv-table tr:nth-child(2){animation-delay:.05s}.kv-table tr:nth-child(3){animation-delay:.08s}
        .kv-table tr:nth-child(4){animation-delay:.11s}.kv-table tr:nth-child(5){animation-delay:.14s}.kv-table tr:nth-child(6){animation-delay:.17s}
        .kv-table tr:nth-child(7){animation-delay:.2s}.kv-table tr:nth-child(8){animation-delay:.23s}.kv-table tr:nth-child(9){animation-delay:.26s}

        /* ---------- Approval timeline ---------- */
        .approval-timeline{list-style:none;margin:0 0 6px;padding:0;position:relative}
        .approval-timeline li{position:relative;padding:2px 0 22px 38px;animation:fadeInUp .4s ease both}
        .approval-timeline li:nth-child(1){animation-delay:.03s}.approval-timeline li:nth-child(2){animation-delay:.09s}.approval-timeline li:nth-child(3){animation-delay:.15s}
        .approval-timeline li:nth-child(4){animation-delay:.21s}.approval-timeline li:nth-child(5){animation-delay:.27s}.approval-timeline li:nth-child(6){animation-delay:.33s}
        .approval-timeline li:last-child{padding-bottom:0}
        .approval-timeline li::before{content:'';position:absolute;left:11px;top:26px;bottom:-2px;width:2px;background:var(--line)}
        .approval-timeline li:last-child::before{display:none}
        .approval-timeline .step-dot{position:absolute;left:0;top:0;width:24px;height:24px;border-radius:50%;display:grid;place-items:center;font-size:12px;color:#fff;background:var(--ink-400);box-shadow:0 0 0 4px var(--surface)}
        .approval-timeline li.signed .step-dot{background:linear-gradient(155deg,#2BC876,var(--success-ink))}
        .approval-timeline li.pending .step-dot{background:linear-gradient(155deg,var(--gold-400),var(--gold-600));animation:pulseDot 2s ease-in-out infinite}
        .approval-timeline .step-row{display:flex;align-items:flex-start;justify-content:space-between;gap:10px;flex-wrap:wrap}
        .approval-timeline .step-role{font-weight:700;font-size:12px;color:var(--ink-900)}
        .approval-timeline .step-name{font-size:11.5px;color:var(--ink-500);margin-top:1px}
        .approval-timeline .step-meta{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;white-space:nowrap;letter-spacing:.01em}
        .approval-timeline .step-meta.signed{background:var(--success-bg);color:var(--success-ink);border:1px solid var(--success-line)}
        .approval-timeline .step-meta.pending{background:var(--warning-bg);color:var(--warning-ink);border:1px solid var(--warning-line)}
        /* ---------- Approval trail extra states (FMO screens) ---------- */
        .approval-timeline li.waiting .step-dot{background:linear-gradient(155deg,var(--gold-400),var(--gold-600));animation:pulseDot 2s ease-in-out infinite}
        .approval-timeline li.rejected .step-dot,.approval-timeline li.blocked .step-dot{background:linear-gradient(155deg,#E85D6A,var(--danger-ink))}
        .approval-timeline .step-meta.waiting{background:var(--warning-bg);color:var(--warning-ink);border:1px solid var(--warning-line)}
        .approval-timeline .step-meta.rejected,.approval-timeline .step-meta.blocked{background:var(--danger-bg);color:var(--danger-ink);border:1px solid var(--danger-line)}
        .approval-timeline .step-note{font-size:11px;color:var(--ink-500);margin-top:3px;font-style:italic}

        /* ---------- Filter chips (reservation status filter) ---------- */
        .chip-row{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:14px}
        .chip{display:inline-flex;align-items:center;gap:7px;padding:8px 15px;border-radius:999px;border:1px solid var(--line);background:var(--surface);color:var(--ink-500);font-size:11.5px;font-weight:700;text-decoration:none;transition:all .14s ease}
        .chip:hover{background:var(--surface-2);color:var(--ink-900)}
        .chip.active{background:linear-gradient(155deg,var(--navy-700),var(--navy-900));color:#fff;border-color:transparent;box-shadow:0 4px 12px -3px rgba(12,19,48,.35)}
        .chip .chip-count{background:rgba(0,0,0,.08);border-radius:999px;padding:1px 8px;font-size:10.5px}
        .chip.active .chip-count{background:rgba(255,255,255,.18)}

        /* ---------- Items-needed checkbox + quantity picker ---------- */
        .req-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:10px}
        .req-card{display:flex;align-items:center;gap:10px;border:1px solid var(--line);border-radius:var(--r-sm);padding:10px 12px;background:var(--surface);transition:border-color .14s ease,box-shadow .14s ease}
        .req-card.is-on{border-color:var(--gold-500);box-shadow:0 0 0 3px rgba(227,176,78,.14)}
        .req-card label{display:flex;align-items:center;gap:9px;font-size:12.5px;font-weight:600;color:var(--ink-900);margin:0;cursor:pointer;flex:1}
        .req-card .req-unit{font-size:10.5px;color:var(--ink-400);font-weight:500}
        .req-qty{width:78px;flex:0 0 78px;font-size:12px;padding:5px 8px;border:1px solid var(--line);border-radius:8px;text-align:center}
        .req-qty[hidden]{display:none}
        .req-other-box{margin-top:12px;border:1px dashed var(--line);border-radius:var(--r-sm);padding:12px 14px;background:var(--surface-2)}
        .req-summary-table{width:100%;border-collapse:collapse;font-size:12.5px}
        .req-summary-table th,.req-summary-table td{padding:9px 12px;border-bottom:1px solid var(--line-2);text-align:left}
        .req-summary-table th{font-size:10.5px;text-transform:uppercase;letter-spacing:.04em;color:var(--ink-500)}
        .req-summary-table td.qty{font-weight:800;font-family:var(--font-display)}

        @keyframes pulseDot{0%,100%{box-shadow:0 0 0 4px var(--surface),0 0 0 0 rgba(227,176,78,0)}50%{box-shadow:0 0 0 4px var(--surface),0 0 0 6px rgba(227,176,78,.28)}}
        @keyframes fadeInUp{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}

        /* ---------- Formula / result summary card (forecast, reports) ---------- */
        .formula-card{background:linear-gradient(155deg,var(--navy-800),var(--navy-950));color:#fff;border-radius:var(--r-lg);padding:18px 20px;margin:16px 0;font-family:var(--font-display);font-size:15px;text-align:center;letter-spacing:.01em;box-shadow:var(--shadow-md)}
        .result-list{list-style:none;margin:14px 0 0;padding:0;display:grid;gap:10px}
        .result-list li{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:11px 14px;background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-sm);font-size:12.5px}
        .result-list li strong{color:var(--ink-900);font-weight:700}
        .result-list li span.result-val{font-weight:700;color:var(--ink-900);font-family:var(--font-display)}
        .section-icon-head{display:flex;align-items:center;gap:12px;margin-bottom:4px}
        .section-icon-head .form-section-icon{margin-bottom:0}
        .note-callout{display:flex;gap:10px;align-items:flex-start;background:var(--info-bg);border:1px solid var(--info-line);color:var(--info-ink);border-radius:var(--r-md);padding:11px 14px;font-size:11.5px;line-height:1.5;margin-top:4px}
        .note-callout i{font-size:15px;margin-top:1px}

        /* ---------- Pagination ---------- */
        .app-pagination{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-top:16px;padding-top:14px;border-top:1px solid var(--line)}
        .app-pagination-info{font-size:11.5px;color:var(--ink-500)}
        .app-pagination-list{list-style:none;display:flex;align-items:center;gap:4px;margin:0;padding:0;flex-wrap:wrap}
        .app-page-item{display:inline-flex}
        .app-page-link{display:inline-flex;align-items:center;justify-content:center;min-width:30px;height:30px;padding:0 8px;border-radius:9px;border:1px solid var(--line);background:var(--surface);color:var(--ink-700);font-size:11.5px;font-weight:600;text-decoration:none;transition:background .15s ease,color .15s ease,border-color .15s ease}
        .app-page-link:hover{background:var(--canvas);color:var(--ink-900)}
        .app-page-item.active .app-page-link{background:linear-gradient(155deg,var(--navy-700),var(--navy-900));border-color:var(--navy-900);color:#fff}
        .app-page-item.disabled .app-page-link{color:var(--ink-400);cursor:default;background:var(--surface-2)}
        .app-page-item.disabled .app-page-link:hover{background:var(--surface-2);color:var(--ink-400)}
        .app-page-dots{border-color:transparent;background:transparent}
        .app-page-link i{font-size:12px!important}

        /* ---------- Bootstrap color overrides (so its hardcoded grays/lights follow our theme, incl. dark mode) ---------- */
        .text-muted{color:var(--ink-500)!important}
        .text-danger{color:var(--danger-ink)!important}
        .border{border-color:var(--line)!important}
        .alert{border-radius:var(--r-md);font-size:12.5px}
        .alert-success{background:var(--success-bg);color:var(--success-ink);border-color:var(--success-line)}
        .alert-danger{background:var(--danger-bg);color:var(--danger-ink);border-color:var(--danger-line)}
        .form-control,.form-select{background:var(--surface);color:var(--ink-900);border-color:var(--line)}
        .form-control::placeholder{color:var(--ink-400)}

        /* Read-only / disabled fields must look obviously "not for typing in" —
           otherwise users click in, try to type, and think the form is broken.
           A flat muted fill + left accent bar reads as "locked" without the
           hatching pattern that used to wash out the text underneath it. */
        .form-control:disabled,.form-select:disabled,
        .form-control[readonly]{
            background:var(--surface-2);
            color:var(--ink-700);
            font-weight:600;
            border-color:var(--line);
            border-left:3px solid var(--ink-400);
            cursor:not-allowed;
            box-shadow:none;
        }
        .form-control:disabled::placeholder,.form-control[readonly]::placeholder{color:var(--ink-400);font-weight:400}
        .form-control[readonly]:focus,.form-control:disabled:focus{box-shadow:none;border-color:var(--line);border-left-color:var(--ink-400)}

        /* ---------- Page hero / welcome banner (dashboard & module intros) ---------- */
        .page-hero{position:relative;overflow:hidden;background:linear-gradient(135deg,var(--navy-800) 0%,var(--navy-900) 55%,var(--navy-950) 100%);border-radius:var(--r-lg);padding:24px 26px;margin-bottom:18px;color:#fff;box-shadow:var(--shadow-md);display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap}
        .page-hero::before{content:'';position:absolute;right:-40px;top:-60px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle at center,rgba(227,176,78,.28),transparent 70%);pointer-events:none}
        .page-hero::after{content:'';position:absolute;right:120px;bottom:-90px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle at center,rgba(59,195,224,.16),transparent 70%);pointer-events:none}
        .page-hero-eyebrow{font-size:10.5px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--gold-400);margin-bottom:6px}
        .page-hero-title{font-family:var(--font-display);font-size:23px;font-weight:800;letter-spacing:-.01em;margin:0;line-height:1.15}
        .page-hero-sub{font-size:12.5px;color:#AEB8D6;margin-top:6px;max-width:520px;line-height:1.55;position:relative}
        .page-hero-side{position:relative;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
        .hero-chip{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);border-radius:var(--r-md);padding:10px 14px;backdrop-filter:blur(6px)}
        .hero-chip i{font-size:16px;color:var(--gold-400)}
        .hero-chip-val{font-family:var(--font-display);font-weight:800;font-size:16px;line-height:1;color:#fff}
        .hero-chip-lbl{font-size:9.5px;text-transform:uppercase;letter-spacing:.05em;color:#9FACD6;font-weight:600;margin-top:2px}

        /* ---------- Snapshot tiles (dashboard planning summary) ---------- */
        .snapshot-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}
        .snapshot-tile{position:relative;background:var(--surface-2);border:1px solid var(--line);border-radius:var(--r-md);padding:16px 16px 14px;display:flex;align-items:center;gap:14px;transition:box-shadow .15s ease,transform .15s ease}
        .snapshot-tile:hover{box-shadow:var(--shadow-md);transform:translateY(-2px)}
        .snapshot-ico{width:44px;height:44px;min-width:44px;border-radius:12px;display:grid;place-items:center;font-size:19px;color:#fff;box-shadow:var(--shadow-sm)}
        .snapshot-ico.si-navy{background:linear-gradient(155deg,var(--navy-700),var(--navy-900))}
        .snapshot-ico.si-gold{background:linear-gradient(155deg,var(--gold-400),var(--gold-600))}
        .snapshot-ico.si-cyan{background:linear-gradient(155deg,#3BC3E0,#1D8FAE)}
        .snapshot-num{font-family:var(--font-display);font-size:26px;font-weight:800;line-height:1;color:var(--ink-900);letter-spacing:-.02em}
        .snapshot-lbl{font-size:11.5px;color:var(--ink-500);margin-top:4px;font-weight:600;line-height:1.35}

        /* ---------- Section subheading (used inside panels) ---------- */
        .subhead{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;margin:0 0 14px}
        .subhead-title{display:inline-flex;align-items:center;gap:8px;font-family:var(--font-display);font-size:13.5px;font-weight:700;color:var(--ink-900);margin:0}
        .subhead-title i{color:var(--gold-600);font-size:15px}

        /* ---------- Requisition detail summary strip ---------- */
        .req-summary{display:flex;align-items:center;gap:14px;flex-wrap:wrap;padding:16px 18px;border:1px solid var(--line);border-radius:var(--r-lg);background:var(--surface-2);margin-bottom:16px}
        .req-summary-ico{width:46px;height:46px;min-width:46px;border-radius:13px;display:grid;place-items:center;font-size:20px;color:#fff;background:linear-gradient(155deg,var(--navy-700),var(--navy-900));box-shadow:var(--shadow-sm)}
        .req-summary-no{font-family:var(--font-display);font-size:18px;font-weight:800;color:var(--ink-900);letter-spacing:-.01em;line-height:1.1}
        .req-summary-meta{font-size:11.5px;color:var(--ink-500);margin-top:2px}
        .req-summary-spacer{flex:1}
        .action-panel-head{display:flex;align-items:center;gap:10px;padding-bottom:14px;margin-bottom:16px;border-bottom:1px solid var(--line)}
        .action-panel-ico{width:34px;height:34px;min-width:34px;border-radius:10px;display:grid;place-items:center;background:var(--gold-050);color:var(--gold-600);font-size:15px}

        /* ---------- Form intro helper (top of every form) ---------- */
        .form-hint-row{display:flex;align-items:center;gap:8px;font-size:11.5px;color:var(--ink-500);margin-bottom:16px}
        .form-hint-row .req-dot{color:var(--danger-ink);font-weight:800}
        .form-section:hover{box-shadow:var(--shadow-md)}
        .form-section-head .step-badge{margin-left:auto;font-family:var(--font-display);font-size:11px;font-weight:800;color:var(--ink-400);background:var(--surface);border:1px solid var(--line);border-radius:999px;width:26px;height:26px;display:grid;place-items:center}

        @media (max-width: 991px){
            .snapshot-grid{grid-template-columns:1fr}
            .page-hero{padding:20px}
            .page-hero-title{font-size:20px}
        }

        .mobile-menu{display:none}
        @media (max-width: 991px){
            .sidebar{position:fixed;left:-162px;transition:.25s ease;box-shadow:var(--shadow-lg)}
            body.sidebar-open .sidebar{left:0}
            .mobile-menu{display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border:none;border-radius:12px;background:rgba(255,255,255,.08);color:#fff}
            /* The topbar used to clip (overflow:hidden) whatever couldn't fit
               on one line, which is what made buttons/text disappear on
               narrower screens. Letting it wrap keeps everything visible. */
            .topbar{padding:12px 16px;overflow:visible;flex-wrap:wrap;row-gap:10px;min-height:0}
            .top-actions{flex-wrap:wrap;justify-content:flex-end;row-gap:8px}
            .content{padding:18px}
            .stat-grid,.panel-grid-2,.grid-cards,.report-grid,.qr-grid,.qr-tiles{grid-template-columns:1fr}
            .user-meta{display:none}
            .page-subtitle-note{display:none}
            .request-actions{justify-content:flex-start}
        }
        @media (max-width: 576px){
            .data-table{font-size:11.5px}
            .data-table thead{display:none}
            .data-table tbody tr{display:block;padding:10px 0;border-top:1px solid var(--line)}
            .data-table tbody td{display:flex;flex-wrap:wrap;justify-content:space-between;gap:8px 16px;padding:8px 10px;border-top:none}
            .data-table tbody td::before{content:attr(data-label);font-weight:700;color:var(--ink-500);text-transform:uppercase;font-size:10px;flex-basis:100%}
            /* Action buttons (view/QR/edit/delete icons) need their own row so
               they can wrap instead of being squeezed off-screen. */
            .data-table tbody td[data-label="Actions"]{gap:8px}
            .data-table tbody td[data-label="Actions"] > *{flex:0 0 auto}
            .top-actions{gap:8px}
            .page-actions{padding-right:10px;gap:6px;flex-wrap:wrap;row-gap:6px}
            .page-actions .btn-primaryx,.page-actions .btn-soft{padding:7px 10px;font-size:0}
            .page-actions .btn-primaryx i,.page-actions .btn-soft i{font-size:14px!important}
            .page-title{font-size:14px}
        }
        @media (max-width: 420px){
            .topbar{padding:10px 14px}
            .page-subtitle-hero{font-size:15px}
            .user-chip{padding-left:8px;gap:6px}
            .avatar{width:32px;height:32px;font-size:12px}
            .notif-link{width:34px;height:34px}
            .logout-btn{font-size:0;display:inline-flex;align-items:center}
            .logout-btn i{font-size:16px!important}
            .request-actions,.data-table tbody td[data-label="Actions"]{gap:6px}
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand-wrap">
            <div class="brand-box">
                <div class="brand-mark">NU</div>
                <div>
                    <div class="brand-title">NU Clark</div>
                    <div class="brand-sub">{{ auth()->check() && auth()->user()->isFmoSide() ? 'Facilities Office' : 'Asset Management' }}</div>
                </div>
            </div>
        </div>
        <nav class="nav-list">
            @php
                $navUser = auth()->user();
                $navFmoSuper = $navUser->isFmoSuperAdmin();
                $navFmoStaff = $navUser->isFmo();
                $navFmoSide = $navUser->isFmoSide();
                $navAll = $navUser->isSuperAdmin();
                $navAssetAdmin = $navUser->isAssetManagementAdmin();
                $navRequestor = $navUser->isRequestor();
                $navSupplyRequestor = $navUser->canRequestSupplies();
                $navHousekeeping = $navUser->isHousekeeping();
                $navDean = $navUser->isDeanApprover();
                $navExecutive = $navUser->isExecutiveApprover();
                $navProposalSigner = $navUser->isAdviserApprover() || $navUser->isSdaoApprover() || $navUser->isAcademicDirectorApprover() || $navDean || $navExecutive;
            @endphp

            {{-- ============================================================
                 FACILITIES (FMO) NAVIGATION
                 Shown only to the FMO Super Admin and FMO staff. Contains no
                 Asset Management / OPEX links at all, and no Activity
                 Proposals tab (that form belongs to requestors -- the FMO
                 sees the same information through Reservation Requests ->
                 View All Details).
                 ============================================================ --}}
            @if($navFmoSide)
            <a class="nav-linkx {{ request()->routeIs('fmo.dashboard') ? 'active' : '' }}" href="{{ route('fmo.dashboard') }}"><i class="bi bi-speedometer2"></i><span>Dashboard</span></a>
            <a class="nav-linkx {{ request()->routeIs('fmo.reservations.*') ? 'active' : '' }}" href="{{ route('fmo.reservations.index') }}"><i class="bi bi-calendar-check"></i><span>Reservations</span></a>
            <a class="nav-linkx {{ request()->routeIs('fmo.venues.*') ? 'active' : '' }}" href="{{ route('fmo.venues.index') }}"><i class="bi bi-building"></i><span>Venues</span></a>
            <a class="nav-linkx {{ request()->routeIs('fmo.items.*') ? 'active' : '' }}" href="{{ route('fmo.items.index') }}"><i class="bi bi-box-seam"></i><span>Facility Items</span></a>
            <a class="nav-linkx {{ request()->routeIs('fmo.services.*') ? 'active' : '' }}" href="{{ route('fmo.services.index') }}"><i class="bi bi-tools"></i><span>Services</span></a>
            @if($navFmoSuper)
            <a class="nav-linkx {{ request()->routeIs('fmo.users.*') ? 'active' : '' }}" href="{{ route('fmo.users.index') }}"><i class="bi bi-people"></i><span>FMO Users</span></a>
            @endif
            @endif

            {{-- ============================================================
                 ASSET MANAGEMENT NAVIGATION
                 Never rendered for FMO accounts. Facilities links have been
                 removed from the Asset Management Super Admin entirely.
                 ============================================================ --}}
            @if(!$navFmoSide)
            @if($navAll || $navAssetAdmin)
            <a class="nav-linkx {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-grid"></i><span>Dashboard</span></a>
            <a class="nav-linkx {{ request()->routeIs('items.*') && request('type', 'CAPEX') === 'CAPEX' ? 'active' : '' }}" href="{{ route('items.index', ['type' => 'CAPEX']) }}"><i class="bi bi-pc-display"></i><span>Capex</span></a>
            @endif
            @if($navAll || $navAssetAdmin || $navSupplyRequestor)
            <a class="nav-linkx {{ request()->routeIs('items.*') && request('type') === 'OPEX' ? 'active' : '' }}" href="{{ route('items.index', ['type' => 'OPEX']) }}"><i class="bi bi-layers"></i><span>Opex</span></a>
            @endif
            @if($navAll || $navAssetAdmin || $navSupplyRequestor || $navDean || $navExecutive)
            <a class="nav-linkx {{ request()->routeIs('requisitions.*') ? 'active' : '' }}" href="{{ route('requisitions.index') }}"><i class="bi bi-file-earmark-text"></i><span>Requisitions</span></a>
            @endif
            @if($navRequestor || $navProposalSigner)
            <a class="nav-linkx {{ request()->routeIs('activity-proposals.*') ? 'active' : '' }}" href="{{ route('activity-proposals.index') }}"><i class="bi bi-file-earmark-check"></i><span>Activity Proposals</span></a>
            @endif
            @if($navAll || $navAssetAdmin || $navHousekeeping)
            <a class="nav-linkx {{ request()->routeIs('asset-scans.*') ? 'active' : '' }}" href="{{ route('asset-scans.index') }}"><i class="bi bi-qr-code-scan"></i><span>Scans</span></a>
            @endif
            @if($navAll || $navAssetAdmin)
            <a class="nav-linkx {{ request()->routeIs('issuances.*') ? 'active' : '' }}" href="{{ route('issuances.index') }}"><i class="bi bi-arrow-repeat"></i><span>Issuance & Returns</span></a>
            <a class="nav-linkx {{ request()->routeIs('forecast.*') ? 'active' : '' }}" href="{{ route('forecast.index') }}"><i class="bi bi-graph-up-arrow"></i><span>Forecast</span></a>
            <a class="nav-linkx {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}"><i class="bi bi-truck"></i><span>Suppliers</span></a>
            <a class="nav-linkx {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-graph-up"></i><span>Reports</span></a>
            <a class="nav-linkx {{ request()->routeIs('access-vouchers.*') ? 'active' : '' }}" href="{{ route('access-vouchers.index') }}"><i class="bi bi-ticket-perforated"></i><span>Access Vouchers</span></a>
            <a class="nav-linkx {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people"></i><span>Users</span></a>
            @endif
            @if($navAll)
            <a class="nav-linkx {{ request()->routeIs('reference-data.*') ? 'active' : '' }}" href="{{ route('reference-data.index') }}"><i class="bi bi-sliders"></i><span>Reference Data</span></a>
            @endif
            @endif
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-menu" type="button" onclick="document.body.classList.toggle('sidebar-open')"><i class="bi bi-list"></i></button>
                <div>
                    <p class="page-title">NU Clark · {{ auth()->user()->isFmoSide() ? 'Facilities Management Office' : 'Asset Management' }}</p>
                    <div class="page-subtitle-hero">{{ $title ?? 'Dashboard' }}</div>
                    @isset($subtitle)
                    <div class="page-subtitle-note" title="{{ $subtitle }}">{{ $subtitle }}</div>
                    @endisset
                </div>
            </div>
            <div class="top-actions">
                @hasSection('page-actions')
                <div class="page-actions">@yield('page-actions')</div>
                @endif
                <a href="{{ route('notifications.index') }}" class="notif-link" title="Notifications"><i class="bi bi-bell top-icon"></i>@if(auth()->user()->unreadNotifications->count())<span class="notif-badge">{{ auth()->user()->unreadNotifications->count() }}</span>@endif</a>
                <div class="user-chip">
                    @php
                        $__name = auth()->user()->name ?? 'Admin';
                    @endphp
                    @php
                        $__initials = collect(explode(' ', trim($__name)))->map(fn($p) => mb_substr($p, 0, 1))->take(2)->implode('');
                    @endphp
                    <div class="avatar">{{ strtoupper($__initials) ?: 'A' }}</div>
                    <div class="user-meta">
                        <div class="user-name">{{ $__name }}</div>
                        <div class="user-role">{{ ucwords(str_replace('_', ' ', auth()->user()->role ?? 'admin')) }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success rounded-4 border-0 shadow-sm">{{ session('success') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
