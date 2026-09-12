<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.favicon')
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
            --canvas:#F4F7FC; --surface:#FFFFFF; --surface-2:#F7F9FD;
            --ink-900:#17233B; --ink-700:#34435F; --ink-500:#68758E; --ink-400:#8A96AB;
            --line:#DCE4F1; --line-2:#E9EEF6;
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
        .req-card.is-on{border-color:#1F7A45;background:#F2FBF6;box-shadow:0 0 0 3px rgba(31,122,69,.12)}
        .req-card label{display:flex;align-items:center;gap:9px;font-size:12.5px;font-weight:600;color:var(--ink-900);margin:0;cursor:pointer;flex:1}
        .req-card .req-unit{font-size:10.5px;color:var(--ink-400);font-weight:500}
        .req-qty{width:78px;flex:0 0 78px;font-size:12px;padding:5px 8px;border:1px solid var(--line);border-radius:8px;text-align:center}
        .req-qty[hidden]{display:none}
        .req-other-box{margin-top:12px;border:1px dashed var(--line);border-radius:var(--r-sm);padding:12px 14px;background:var(--surface-2)}
        .req-summary-table{width:100%;border-collapse:collapse;font-size:12.5px}
        .req-summary-table th,.req-summary-table td{padding:9px 12px;border-bottom:1px solid var(--line-2);text-align:left}
        .req-summary-table th{font-size:10.5px;text-transform:uppercase;letter-spacing:.04em;color:var(--ink-500)}
        .req-summary-table td.qty{font-weight:800;font-family:var(--font-display)}

        .pf-upload{border:1px dashed var(--line);border-radius:var(--r-sm);padding:12px 14px;background:var(--surface-2)}
        .pf-upload-label{display:flex;align-items:center;gap:8px;font-size:12.5px;font-weight:700;color:var(--ink-900);margin-bottom:8px}
        .pf-attachment{display:flex;gap:10px;align-items:flex-start;padding:10px 12px;margin-bottom:10px;border:1px solid var(--line);border-radius:10px;background:var(--surface-2)}
        .pf-attachment i{font-size:16px;color:var(--gold-600)}
        .pf-attachment a{font-size:12.5px;font-weight:700;color:var(--navy-700);text-decoration:none}
        .pf-attachment a:hover{text-decoration:underline}
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


        /* ======================================================================
           2026 Premium Form System — UI/UX only
           Shared across the application, with richer layouts for Activity
           Proposal and Charge Slip. No route, name, id, validation, or backend
           behavior is changed by these styles.
           ====================================================================== */
        .content .form-label{
            display:block;margin:0 0 8px;color:#2E3D58;font-size:11.5px;font-weight:800;
            letter-spacing:.015em;
        }
        .content .form-control,.content .form-select{
            min-height:46px;border:1px solid #D5DFEE;border-radius:12px;background:#fff;
            color:#17233B;font-size:13px;padding:11px 14px;box-shadow:0 1px 2px rgba(25,45,80,.025);
            transition:border-color .15s ease,box-shadow .15s ease,background .15s ease,transform .15s ease;
        }
        .content textarea.form-control{min-height:118px;line-height:1.65;resize:vertical}
        .content .form-control::placeholder{color:#9AA6B9}
        .content .form-control:hover,.content .form-select:hover{border-color:#B8C8DF}
        .content .form-control:focus,.content .form-select:focus{
            border-color:#376FD0;background:#fff;box-shadow:0 0 0 4px rgba(55,111,208,.10),0 8px 22px rgba(27,61,116,.06);outline:0;
        }
        .content .form-control[readonly],.content .form-control:disabled,.content .form-select:disabled{
            background:#F2F6FB;color:#57667F;border-color:#DCE5F2;border-left:1px solid #DCE5F2;font-weight:700;
            cursor:not-allowed;box-shadow:none;
        }
        .content .form-control[readonly]:focus,.content .form-control:disabled:focus{border-color:#DCE5F2;box-shadow:none}
        .content .field-hint{margin-top:7px;color:#79869B;font-size:11px;line-height:1.45}
        .content .field-hint strong{color:#526078}
        .content input[type="file"].form-control{padding:7px 9px;min-height:46px}
        .content input[type="file"]::file-selector-button{
            border:0;border-radius:8px;padding:8px 11px;margin-right:10px;background:#EDF3FF;color:#275AAE;font-weight:800;font-size:11.5px;
        }
        .content .form-check-input{border-color:#C9D5E6}
        .content .form-check-input:checked{background-color:#245EB9;border-color:#245EB9}
        .content .form-check-input:focus{box-shadow:0 0 0 3px rgba(36,94,185,.12);border-color:#3B73CE}

        .premium-form-page{max-width:1440px;margin:0 auto;padding-bottom:28px}
        .premium-form-intro{
            position:relative;overflow:hidden;display:flex;align-items:center;gap:18px;margin-bottom:22px;padding:25px 28px;
            border:1px solid rgba(57,104,183,.14);border-radius:22px;background:linear-gradient(135deg,#FFFFFF 0%,#F5F9FF 66%,#EEF4FF 100%);
            box-shadow:0 14px 34px rgba(31,66,123,.08);
        }
        .premium-form-intro::after{content:'';position:absolute;width:250px;height:250px;right:-90px;top:-105px;border-radius:50%;background:radial-gradient(circle,rgba(45,99,190,.13),transparent 70%);pointer-events:none}
        .premium-form-intro-icon{width:58px;height:58px;min-width:58px;border-radius:17px;display:grid;place-items:center;background:linear-gradient(145deg,#2E6AC9,#173F83);color:#fff;font-size:25px;box-shadow:0 12px 25px rgba(30,76,151,.22)}
        .premium-form-intro-copy{position:relative;z-index:1;min-width:0;flex:1}
        .premium-form-eyebrow{text-transform:uppercase;letter-spacing:.11em;color:#3970C7;font-size:10px;font-weight:900;margin-bottom:5px}
        .premium-form-intro h2{font-family:var(--font-display);font-size:23px;letter-spacing:-.025em;color:#17233B;margin:0;font-weight:800}
        .premium-form-intro p{margin:6px 0 0;max-width:760px;color:#68758E;font-size:12.5px;line-height:1.55}
        .premium-form-intro-badge{position:relative;z-index:1;display:flex;align-items:center;gap:8px;white-space:nowrap;border:1px solid #D5E2F5;background:rgba(255,255,255,.8);border-radius:999px;padding:10px 14px;color:#315FAD;font-size:11px;font-weight:800;box-shadow:0 5px 16px rgba(38,82,151,.06)}
        .premium-form-intro-badge i{font-size:14px!important}

        .premium-form{display:grid;gap:18px}
        .premium-section{overflow:visible;border:1px solid #DDE6F2;border-radius:20px;background:#fff;box-shadow:0 8px 26px rgba(29,55,99,.055);transition:box-shadow .18s ease,border-color .18s ease}
        .premium-section:hover{border-color:#CDD9E9;box-shadow:0 12px 34px rgba(29,55,99,.075)}
        .premium-section-head{display:flex;align-items:center;gap:14px;padding:18px 22px;border-bottom:1px solid #E9EEF6;background:linear-gradient(180deg,#FBFDFF,#F8FAFD);border-radius:20px 20px 0 0}
        .premium-step{width:39px;height:39px;min-width:39px;border-radius:12px;display:grid;place-items:center;background:#EAF2FF;color:#285FAF;font-family:var(--font-display);font-size:11px;font-weight:900;box-shadow:inset 0 0 0 1px #D9E6F9}
        .premium-section-head h3{font-family:var(--font-display);margin:0;color:#1B2942;font-size:14px;font-weight:800;letter-spacing:-.01em}
        .premium-section-head p{margin:3px 0 0;color:#7A879B;font-size:11.5px;line-height:1.45}
        .premium-section-body{padding:24px 22px}

        .premium-schedule-grid{display:grid;grid-template-columns:minmax(0,1fr) minmax(360px,.8fr);gap:22px;align-items:stretch}
        .premium-schedule-fields{padding:2px}
        .premium-date-summary{border:1px solid #D8E5F6;border-radius:17px;padding:18px;background:linear-gradient(145deg,#F7FAFF,#EEF4FE);min-height:100%;display:flex;flex-direction:column;justify-content:center}
        .premium-date-summary-top{display:flex;align-items:center;gap:8px;color:#4770AD;font-size:10.5px;text-transform:uppercase;letter-spacing:.07em;font-weight:900}
        .premium-date-summary>strong{font-family:var(--font-display);font-size:19px;color:#193C75;margin:6px 0 14px}
        .premium-date-pair{display:grid;grid-template-columns:1fr auto 1fr;gap:10px;align-items:end}
        .premium-date-pair>i{margin-bottom:14px;color:#6D88B1}
        .premium-date-pair span{display:block;margin-bottom:6px;color:#7586A0;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.05em}
        .premium-date-summary p{margin:12px 0 0;color:#74829A;font-size:10.5px;line-height:1.45}

        .premium-requirements-wrap{display:grid;gap:22px}
        .premium-requirements-wrap>.col-12{width:100%;padding:0}
        .premium-requirements-wrap .module-note{margin-top:0!important;color:#7B879A}
        .premium-requirements-wrap .req-grid{grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px}
        .premium-requirements-wrap .req-card{min-height:58px;border-color:#DCE5F1;border-radius:14px;background:#FAFCFF;padding:11px 13px}
        .premium-requirements-wrap .req-card:hover{border-color:#BFD0E7;background:#F6FAFF}
        .premium-requirements-wrap .req-card.is-on{border-color:#86AEE8;background:#F1F6FF;box-shadow:0 0 0 3px rgba(53,110,199,.08)}
        .premium-requirements-wrap .req-card.is-on label{color:#214F96}
        .premium-requirements-wrap .req-qty{border:1px solid #C9D9EE;border-radius:10px;background:#fff;color:#234D8C;padding:7px 8px;font-weight:800}
        .premium-requirements-wrap .req-other-box{border:1px dashed #C7D5E7;border-radius:14px;background:#F9FBFE;padding:14px}

        .premium-upload-zone{display:grid;grid-template-columns:auto minmax(150px,1fr) minmax(300px,1.2fr);align-items:center;gap:14px;padding:17px;border:1px dashed #AFC4E1;border-radius:17px;background:#F8FBFF}
        .premium-upload-icon{width:44px;height:44px;border-radius:13px;display:grid;place-items:center;background:#E8F1FF;color:#2C63B6;font-size:20px}
        .premium-upload-copy{display:flex;flex-direction:column;gap:3px}
        .premium-upload-copy strong{color:#203252;font-size:12.5px}
        .premium-upload-copy span{color:#8090A7;font-size:10.5px}
        .premium-textarea{background:linear-gradient(180deg,#fff,#FCFDFF)!important}

        .premium-routing-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
        .premium-routing-grid-three{grid-template-columns:repeat(3,minmax(0,1fr))}
        .premium-route-card{position:relative;display:flex;gap:12px;min-height:155px;padding:16px;border:1px solid #DCE5F1;border-radius:16px;background:linear-gradient(180deg,#FFFFFF,#FAFCFF)}
        .premium-route-card:hover{border-color:#BFD0E6;box-shadow:0 8px 20px rgba(34,69,122,.055)}
        .premium-route-card.is-automatic{border-color:#C7DCF7;background:linear-gradient(145deg,#F8FBFF,#EEF5FF)}
        .premium-route-number{width:30px;height:30px;min-width:30px;border-radius:9px;display:grid;place-items:center;background:#EAF1FC;color:#3767AC;font-family:var(--font-display);font-size:10px;font-weight:900}
        .premium-route-content{min-width:0;flex:1}
        .premium-route-kicker{display:block;color:#7E8BA1;text-transform:uppercase;letter-spacing:.07em;font-size:9px;font-weight:900;margin:1px 0 2px}
        .premium-route-content>strong{display:block;color:#203150;font-size:12.5px;margin-bottom:10px}
        .premium-route-content>p{margin:8px 0 0;color:#7E8B9F;font-size:10.5px;line-height:1.45}
        .premium-auto-assigned{display:inline-flex;align-items:center;gap:6px;border:1px solid #B9D9CA;background:#EDF8F2;color:#17704A;border-radius:999px;padding:7px 10px;font-size:10.5px;font-weight:800}
        .premium-auto-assigned i{font-size:11px!important}
        .premium-route-card .approver-tree-trigger{min-height:44px;background:#fff}

        .premium-document-strip{display:flex;align-items:end;justify-content:space-between;gap:20px;padding:17px 18px;border:1px solid #E0E7F1;border-radius:16px;background:linear-gradient(145deg,#FBFDFF,#F4F8FD)}
        .premium-document-kicker{display:block;text-transform:uppercase;letter-spacing:.11em;font-size:9.5px;color:#6282B3;font-weight:900;margin-bottom:3px}
        .premium-document-strip strong{font-family:var(--font-display);font-size:18px;color:#1C2D4B}
        .premium-document-ref{width:min(100%,300px)}

        .premium-budget-card{display:flex;align-items:center;gap:14px;border:1px solid #D7E5F5;border-radius:16px;background:linear-gradient(135deg,#F7FAFF,#EDF4FE);padding:15px 17px}
        .premium-budget-icon{width:43px;height:43px;min-width:43px;border-radius:13px;display:grid;place-items:center;background:#fff;color:#376CB9;border:1px solid #D8E5F5;box-shadow:0 4px 12px rgba(37,76,133,.06)}
        .premium-budget-copy{display:grid;gap:1px;min-width:0}
        .premium-budget-copy>span{color:#6E7F99;font-size:10px;text-transform:uppercase;letter-spacing:.07em;font-weight:900}
        .premium-budget-copy>strong{font-family:var(--font-display);color:#214A88;font-size:20px;line-height:1.1}
        .premium-budget-copy small{color:#7D8BA0;font-size:10.5px}
        .premium-budget-status{margin-left:auto;white-space:nowrap;border:1px solid #CBDBEF;background:#fff;color:#56739C;border-radius:999px;padding:8px 10px;font-size:10px;font-weight:800}

        .premium-table-wrap{overflow:auto;border:1px solid #DFE6F0;border-radius:16px;background:#fff}
        .premium-entry-table{min-width:1100px;margin:0}
        .premium-entry-table thead th{background:#F4F7FB;border-bottom:1px solid #DDE5F0;border-right:0!important;color:#627087;font-size:9.5px;padding:11px 10px}
        .premium-entry-table tbody td{padding:10px;border-right:0!important;border-top:1px solid #EDF1F6}
        .premium-entry-table tbody tr:first-child td{border-top:0}
        .premium-entry-table tbody tr:hover td{background:#FBFDFF}
        .premium-entry-table .form-control,.premium-entry-table .form-select{min-height:41px;border-radius:10px;padding:9px 10px;font-size:11.5px}
        .premium-remove-col{width:48px}
        .premium-remove-row{width:35px;height:35px;border:1px solid #F1CCD2;border-radius:10px;background:#FFF7F8;color:#C23C4D;display:grid;place-items:center;transition:.15s ease}
        .premium-remove-row:hover{background:#FDECEF;border-color:#E7AAB3;transform:translateY(-1px)}
        .premium-table-footer{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-top:14px;flex-wrap:wrap}
        .premium-add-row{background:#EEF3FA;color:#315D9D!important;box-shadow:none;border:1px solid #D6E1F0}
        .premium-add-row:hover{background:#E4EDF9}
        .premium-total-card{min-width:230px;border:1px solid #D5E3F5;border-radius:15px;background:#F5F9FF;padding:12px 15px;text-align:right}
        .premium-total-card span{display:block;color:#75849B;text-transform:uppercase;letter-spacing:.06em;font-size:9.5px;font-weight:900}
        .premium-total-card strong{display:block;font-family:var(--font-display);font-size:20px;color:#214E90;margin-top:2px}
        .premium-warning{display:flex;align-items:flex-start;gap:10px;border:1px solid #F0C9CE;border-radius:14px;background:#FFF6F7;color:#A73545;padding:12px 14px}
        .premium-warning>i{margin-top:1px}
        .premium-warning div{display:grid;gap:2px}.premium-warning strong{font-size:11.5px}.premium-warning span{font-size:10.5px;color:#B65A67;line-height:1.45}

        .premium-form-actionbar{position:sticky;bottom:14px;z-index:30;display:flex;align-items:center;justify-content:space-between;gap:20px;padding:14px 16px;border:1px solid rgba(194,208,228,.95);border-radius:17px;background:rgba(255,255,255,.94);box-shadow:0 15px 38px rgba(28,54,97,.14);backdrop-filter:blur(10px)}
        .premium-action-note{display:flex;align-items:center;gap:8px;color:#758299;font-size:10.5px;line-height:1.4}
        .premium-action-note i{color:#4773B7}
        .premium-action-buttons{display:flex;align-items:center;gap:9px;white-space:nowrap}
        .premium-form-actionbar .btn-primaryx{border-radius:11px;padding:11px 17px;background:linear-gradient(145deg,#2D67C4,#173F82);box-shadow:0 8px 18px rgba(30,75,148,.22)}
        .premium-form-actionbar .btn-soft{border-radius:11px;padding:11px 16px;background:#EEF2F7;color:#546279;box-shadow:none}
        .premium-form-actionbar .btn-soft:hover{background:#E4EAF2;color:#34435F}

        /* Department → approver trees visually match the premium input system. */
        .premium-form .approver-tree-trigger{border-color:#D5DFEE!important;background:#fff!important;border-radius:12px!important;color:#34435F!important;font-size:12px!important}
        .premium-form .approver-tree-select.open .approver-tree-trigger{border-color:#376FD0!important;box-shadow:0 0 0 4px rgba(55,111,208,.10)!important}
        .premium-form .approver-tree-panel{border-color:#D6E0EE!important;border-radius:14px!important;background:#fff!important;box-shadow:0 18px 42px rgba(29,56,101,.16)!important;padding:7px!important}
        .premium-form .approver-tree-group-label{color:#30405D!important;border-radius:10px!important}
        .premium-form .approver-tree-group-label:hover{background:#F3F7FC!important}
        .premium-form .approver-tree-group.expanded>.approver-tree-group-label{background:#EAF2FF!important;color:#285CA9!important}
        .premium-form .approver-tree-leaf{color:#526078!important;border-radius:9px!important}
        .premium-form .approver-tree-leaf:hover{background:#F5F8FC!important}
        .premium-form .approver-tree-leaf.selected{background:#EAF2FF!important;color:#24569D!important;font-weight:800!important}

        @media (max-width:1100px){
            .premium-schedule-grid{grid-template-columns:1fr}
            .premium-routing-grid-three{grid-template-columns:1fr 1fr}
        }
        @media (max-width:767px){
            .premium-form-page{padding-bottom:14px}
            .premium-form-intro{align-items:flex-start;padding:20px;border-radius:18px}.premium-form-intro-icon{width:48px;height:48px;min-width:48px;border-radius:14px;font-size:20px}.premium-form-intro h2{font-size:19px}.premium-form-intro-badge{display:none}
            .premium-section{border-radius:17px}.premium-section-head{padding:15px 16px;border-radius:17px 17px 0 0}.premium-section-body{padding:18px 16px}.premium-step{width:34px;height:34px;min-width:34px}
            .premium-routing-grid,.premium-routing-grid-three{grid-template-columns:1fr}
            .premium-upload-zone{grid-template-columns:auto 1fr}.premium-upload-zone input{grid-column:1/-1}
            .premium-document-strip{align-items:stretch;flex-direction:column}.premium-document-ref{width:100%}
            .premium-budget-status{display:none}
            .premium-date-pair{grid-template-columns:1fr}.premium-date-pair>i{display:none}
            .premium-form-actionbar{position:static;align-items:stretch;flex-direction:column}.premium-action-buttons{width:100%}.premium-action-buttons>*{flex:1;justify-content:center}
        }


        /* ======================================================================
           2026 Premium UI Refinement — stronger fillable fields + redesigned
           Super Admin user directories. UI-only; existing routes/hooks remain.
           ====================================================================== */
        .content .form-control:not([readonly]):not(:disabled),
        .content .form-select:not(:disabled){
            border:1.5px solid #AFC0D8;
            background:linear-gradient(180deg,#FFFFFF 0%,#FCFDFF 100%);
            box-shadow:0 1px 2px rgba(19,48,91,.035), inset 0 0 0 1px rgba(255,255,255,.7);
        }
        .content .form-control:not([readonly]):not(:disabled):hover,
        .content .form-select:not(:disabled):hover{
            border-color:#7F9FCB;
            box-shadow:0 2px 7px rgba(33,76,138,.07);
        }
        .content .form-control:not([readonly]):not(:disabled):focus,
        .content .form-select:not(:disabled):focus{
            border-color:#1F5DBB;
            box-shadow:0 0 0 4px rgba(31,93,187,.12),0 8px 20px rgba(25,65,124,.08);
        }
        .premium-form .form-control:not([readonly]):not(:disabled),
        .premium-form .form-select:not(:disabled){
            border:1.5px solid #9FB4D0;
            background:#FFFFFF;
            box-shadow:0 1px 3px rgba(21,52,98,.05), inset 0 0 0 1px #F6F9FD;
        }
        .premium-form .form-control:not([readonly]):not(:disabled):hover,
        .premium-form .form-select:not(:disabled):hover{border-color:#6E94C9;background:#FBFDFF}
        .premium-form .form-control:not([readonly]):not(:disabled):focus,
        .premium-form .form-select:not(:disabled):focus{border-color:#1C5AB7;box-shadow:0 0 0 4px rgba(28,90,183,.13),0 9px 24px rgba(24,62,118,.09)}
        .premium-form .premium-section-body{background:linear-gradient(180deg,#FFFFFF 0%,#FCFDFF 100%)}
        .premium-form .premium-route-card .approver-tree-trigger{border:1.5px solid #9FB4D0!important}
        .premium-form .premium-upload-zone{border:1.5px dashed #8EAAD0;background:linear-gradient(145deg,#FBFDFF,#F4F8FE)}
        .premium-form .premium-table-wrap{border:1.5px solid #BCCAE0}
        .premium-form .premium-entry-table .form-control,
        .premium-form .premium-entry-table .form-select{border-width:1.5px}

        /* User administration workspace */
        .user-admin-page{max-width:1500px;margin:0 auto;display:grid;gap:18px;padding-bottom:24px}
        .user-admin-hero{position:relative;overflow:hidden;display:flex;align-items:center;gap:18px;padding:24px 26px;border:1px solid #C9D8EC;border-radius:24px;background:linear-gradient(135deg,#FFFFFF 0%,#F4F8FF 62%,#EAF2FF 100%);box-shadow:0 14px 34px rgba(25,63,121,.08)}
        .user-admin-hero::after{content:'';position:absolute;width:300px;height:300px;right:-90px;top:-160px;border-radius:50%;background:radial-gradient(circle,rgba(38,99,190,.18),transparent 68%);pointer-events:none}
        .user-admin-hero-icon{position:relative;z-index:1;width:62px;height:62px;min-width:62px;border-radius:18px;display:grid;place-items:center;background:linear-gradient(145deg,#2F6AC7,#153C7B);color:#fff;font-size:25px;box-shadow:0 12px 25px rgba(24,71,146,.24)}
        .user-admin-hero-copy{position:relative;z-index:1;flex:1;min-width:0}
        .user-admin-eyebrow{display:block;margin-bottom:4px;color:#3C70BE;font-size:9.5px;font-weight:900;letter-spacing:.105em;text-transform:uppercase}
        .user-admin-hero h2{font-family:var(--font-display);font-size:22px;letter-spacing:-.025em;margin:0;color:#172A49}
        .user-admin-hero p{margin:6px 0 0;color:#687A95;font-size:12px;line-height:1.55;max-width:760px}
        .user-admin-stats{position:relative;z-index:1;display:grid;grid-template-columns:repeat(3,minmax(88px,1fr));gap:8px}
        .user-mini-stat{min-width:88px;padding:11px 13px;border:1px solid rgba(184,203,229,.9);border-radius:14px;background:rgba(255,255,255,.82);box-shadow:0 4px 14px rgba(32,75,139,.055)}
        .user-mini-stat span{display:block;color:#7E8CA1;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.06em}
        .user-mini-stat strong{display:block;margin-top:2px;font-family:var(--font-display);font-size:20px;color:#214E91}

        .user-command-bar,.user-create-shell,.user-directory-shell{border:1px solid #CED9E9;border-radius:21px;background:#fff;box-shadow:0 7px 22px rgba(30,61,109,.055)}
        .user-command-bar{display:flex;align-items:center;gap:18px;padding:14px 16px}
        .user-command-search{display:flex;align-items:center;gap:9px;flex:1;min-width:0;padding:7px 8px 7px 13px;border:1.5px solid #B8C7DC;border-radius:14px;background:#FAFCFF;transition:.15s ease}
        .user-command-search:focus-within{border-color:#5D84BD;box-shadow:0 0 0 4px rgba(42,100,187,.09);background:#fff}
        .user-command-search>i{color:#6F8098}
        .user-command-search .search-input{border:0!important;outline:0!important;box-shadow:none!important;background:transparent!important;min-height:36px;flex:1;color:#243653;font-size:12.5px;padding:0 4px}
        .user-command-search .btn-primaryx{white-space:nowrap;border-radius:10px;padding:9px 13px}
        .user-clear-search{width:31px;height:31px;border-radius:9px;display:grid;place-items:center;color:#7E8BA0;text-decoration:none;background:#EEF3F9}
        .user-clear-search:hover{color:#264F8F;background:#E5EEF9}
        .user-command-note{display:flex;align-items:center;gap:8px;max-width:390px;color:#74839A;font-size:10.5px;line-height:1.45}
        .user-command-note i{color:#3B70BE;font-size:14px}.user-command-note strong{color:#485A76}

        .user-directory-shell{padding:0;overflow:hidden}
        .user-directory-head{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:18px 20px;border-bottom:1px solid #E1E7F0;background:linear-gradient(180deg,#FFFFFF,#F9FBFE)}
        .user-directory-head h3,.user-create-head h3{font-family:var(--font-display);font-size:17px;color:#1D304F;margin:0}
        .user-directory-count{white-space:nowrap;padding:7px 11px;border:1px solid #D5E0EE;border-radius:999px;background:#F6F9FD;color:#5D6F88;font-size:10px;font-weight:800}
        .user-card-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px;padding:16px;background:#F6F8FC}
        .user-account-card{position:relative;overflow:hidden;border:1px solid #C8D5E5;border-radius:18px;background:#fff;box-shadow:0 5px 18px rgba(31,59,102,.05);transition:transform .15s ease,border-color .15s ease,box-shadow .15s ease}
        .user-account-card:hover{transform:translateY(-1px);border-color:#A9BEDB;box-shadow:0 10px 26px rgba(28,60,109,.08)}
        .user-card-topline{height:4px;background:#C4CEDC}.user-card-topline.is-active{background:linear-gradient(90deg,#228B62,#65C99E)}.user-card-topline.is-inactive{background:linear-gradient(90deg,#C88B2D,#E4BD70)}
        .user-card-main{padding:17px}
        .user-card-identity{display:flex;align-items:center;gap:11px;min-width:0}
        .user-avatar-large{width:45px;height:45px;min-width:45px;border-radius:14px;display:grid;place-items:center;background:linear-gradient(145deg,#E7F0FD,#D5E5FB);border:1px solid #C7D9F1;color:#245A9F;font-family:var(--font-display);font-size:17px;font-weight:900}
        .user-card-nameblock{min-width:0;flex:1}.user-card-nameblock h4{font-family:var(--font-display);font-size:14px;color:#1F304C;margin:0 0 3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.user-email{display:flex;align-items:center;gap:5px;color:#7D8BA0;font-size:10.5px;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.user-email i{font-size:10px}
        .user-state-pill{display:inline-flex;align-items:center;gap:5px;padding:6px 8px;border-radius:999px;font-size:9.5px;font-weight:800;white-space:nowrap}.user-state-pill.active{background:#E9F7F0;color:#18734D;border:1px solid #BFE5D2}.user-state-pill.inactive{background:#FFF5E7;color:#9C6816;border:1px solid #EED7AB}
        .user-card-facts{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px;margin-top:15px}.user-card-facts-three{grid-template-columns:repeat(3,minmax(0,1fr))}
        .user-fact{min-width:0;padding:10px 11px;border:1px solid #E0E6EF;border-radius:12px;background:#F9FBFD}.user-fact span{display:block;margin-bottom:4px;color:#8B97A9;font-size:8.5px;font-weight:900;letter-spacing:.055em;text-transform:uppercase}.user-fact strong{display:flex;align-items:center;gap:6px;min-width:0;color:#34445F;font-size:10.5px;font-weight:750;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.user-fact strong i{color:#4A76B5;font-size:11px}
        .user-card-health{display:flex;gap:7px;flex-wrap:wrap;margin-top:12px;padding-top:12px;border-top:1px dashed #DDE4ED}.user-health-item{display:inline-flex;align-items:center;gap:5px;padding:5px 7px;border-radius:8px;font-size:9.5px;font-weight:700}.user-health-item.ok{background:#EDF8F3;color:#237250}.user-health-item.warn{background:#FFF5E9;color:#9B681A}.user-health-item.neutral{background:#EFF4FA;color:#5E6F88}
        .user-card-actions{display:flex;align-items:center;gap:7px;margin-top:13px}.user-manage-btn{display:flex;align-items:center;justify-content:center;gap:7px;min-height:38px;flex:1;border:1px solid #C5D4E8;border-radius:11px;background:linear-gradient(180deg,#FFFFFF,#F4F8FD);color:#315F9F;font-size:10.5px;font-weight:850;transition:.15s ease}.user-manage-btn:hover{border-color:#8EACD2;background:#EEF5FF;color:#1F5199}.user-manage-chevron{margin-left:auto;font-size:10px;transition:transform .15s ease}.user-manage-btn[aria-expanded="true"] .user-manage-chevron{transform:rotate(180deg)}
        .user-danger-icon,.user-secondary-icon{width:38px;height:38px;border-radius:11px;display:grid;place-items:center;transition:.15s ease}.user-danger-icon{border:1px solid #EDC9CE;background:#FFF7F8;color:#C04452}.user-danger-icon:hover{background:#FCEAEC;border-color:#DEAAB1}.user-secondary-icon{border:1px solid #CBD7E6;background:#F7F9FC;color:#61718A}.user-secondary-icon:hover{background:#EBF1F8;color:#365B8F}
        .user-management-collapse{border-top:1px solid #DCE4EF}.user-management-panel{padding:18px;background:linear-gradient(180deg,#F8FAFD,#F3F7FC)}
        .user-panel-heading{display:flex;align-items:center;gap:10px;margin-bottom:14px}.user-panel-heading-icon{width:36px;height:36px;min-width:36px;border-radius:11px;display:grid;place-items:center;background:#E6EFFC;color:#2E64AE;border:1px solid #CCDCF2}.user-panel-heading-icon.security{background:#F4F0FC;color:#7352A5;border-color:#DED2F1}.user-panel-heading-icon.signature{background:#EEF7F3;color:#317657;border-color:#CDE7DA}.user-panel-heading strong{display:block;color:#263A59;font-size:11.5px}.user-panel-heading span{display:block;margin-top:2px;color:#7C8A9F;font-size:9.8px;line-height:1.35}
        .user-settings-form,.user-security-panel,.user-signature-panel{padding:15px;border:1px solid #D5E0EC;border-radius:15px;background:#fff}.user-panel-save-row{display:flex;justify-content:flex-end;padding-top:2px}.user-panel-save-row .btn-primaryx{border-radius:10px}.user-readonly-tag{margin-left:5px;padding:2px 5px;border-radius:5px;background:#EDF1F6;color:#7A889B;font-size:7.5px;letter-spacing:.04em;text-transform:uppercase}
        .user-security-panel,.user-signature-panel{margin-top:12px}.user-reset-btn{min-height:46px;width:100%;justify-content:center}
        .user-audit-panel{margin-top:13px;padding:11px;border:1px solid #E2E7EE;border-radius:11px;background:#FAFBFD}.user-audit-title{margin-bottom:5px;color:#65758D;font-size:9.5px;font-weight:850;text-transform:uppercase;letter-spacing:.05em}.user-audit-row{padding:6px 0;border-bottom:1px dashed #E0E5EC;color:#45566F;font-size:9.5px}.user-audit-row:last-child{border-bottom:0}.user-audit-row span{color:#8B97A8}.user-audit-empty{color:#8C98A9;font-size:9.5px}
        .user-pagination-wrap{padding:0 18px 16px;background:#F6F8FC}.user-empty-card{grid-column:1/-1;min-height:190px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;border:1px dashed #C6D3E4;border-radius:15px;background:#fff;color:#8290A3}.user-empty-card i{font-size:27px;color:#6986AF}.user-empty-card strong{color:#435570;font-size:12px}.user-empty-card span{font-size:10.5px}

        /* FMO create/filter workspace */
        .user-pending-banner{display:flex;align-items:center;gap:11px;padding:12px 15px;border:1px solid #ECD5AA;border-radius:15px;background:linear-gradient(135deg,#FFF9EF,#FFF4E2);color:#845A19}.user-pending-icon{width:36px;height:36px;border-radius:11px;display:grid;place-items:center;background:#fff;border:1px solid #ECD8B5}.user-pending-banner strong{display:block;font-size:11px}.user-pending-banner span{display:block;margin-top:2px;color:#A27A3B;font-size:9.8px}
        .user-create-shell{overflow:hidden}.user-create-head{display:flex;align-items:center;gap:12px;padding:17px 19px;border-bottom:1px solid #DFE7F0;background:linear-gradient(135deg,#F9FBFE,#F2F7FD)}.user-create-icon{width:42px;height:42px;border-radius:13px;display:grid;place-items:center;background:linear-gradient(145deg,#2D67BC,#19477F);color:#fff;font-size:17px}.user-create-head p{margin:3px 0 0;color:#7D8BA0;font-size:10px}.user-create-form{padding:18px}.user-create-signature{margin-top:15px;padding:14px;border:1px solid #D4E0EC;border-radius:15px;background:#F8FBFE}.user-inline-info{display:flex;align-items:flex-start;gap:10px;margin-bottom:12px;color:#42678F}.user-inline-info>i{margin-top:1px;font-size:15px}.user-inline-info strong{display:block;font-size:10.5px}.user-inline-info span{display:block;margin-top:2px;color:#79899E;font-size:9.8px;line-height:1.45}.user-create-actions{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-top:15px;padding-top:14px;border-top:1px solid #E5EAF1}.user-create-actions>span{display:flex;align-items:center;gap:6px;color:#7B899D;font-size:9.8px}.user-create-actions>span i{color:#3371B8}.user-create-actions .btn-primaryx{border-radius:11px}
        .user-filter-deck{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 16px;border-bottom:1px solid #E2E8F0;background:#FBFCFE}.user-role-filters{display:flex;gap:6px;flex-wrap:wrap}.user-filter-chip{display:inline-flex;align-items:center;gap:5px;padding:7px 10px;border:1px solid #D3DDEA;border-radius:999px;background:#fff;color:#66778F;text-decoration:none;font-size:9.8px;font-weight:800}.user-filter-chip:hover{border-color:#A9BFDA;color:#365E92}.user-filter-chip.active{border-color:#315F9E;background:#315F9E;color:#fff;box-shadow:0 4px 10px rgba(35,81,146,.16)}.user-command-search-compact{max-width:410px}.user-scope-note{display:flex;align-items:flex-start;gap:10px;padding:13px 15px;border:1px solid #D3E0ED;border-radius:15px;background:#F7FAFD;color:#49627E}.user-scope-note>i{margin-top:1px;color:#3671B4}.user-scope-note strong{display:block;font-size:10.5px}.user-scope-note span{display:block;margin-top:2px;color:#7B899C;font-size:9.8px;line-height:1.45}

        @media (max-width:1180px){.user-card-grid{grid-template-columns:1fr}.user-admin-hero{align-items:flex-start}.user-admin-stats{grid-template-columns:repeat(3,1fr)}.user-card-facts-three{grid-template-columns:repeat(2,minmax(0,1fr))}.user-filter-deck{align-items:stretch;flex-direction:column}.user-command-search-compact{max-width:none}}
        @media (max-width:820px){.user-admin-hero{flex-wrap:wrap}.user-admin-stats{width:100%}.user-command-bar{align-items:stretch;flex-direction:column}.user-command-note{max-width:none}.user-directory-head{align-items:flex-start}.user-create-actions{align-items:stretch;flex-direction:column}.user-create-actions .btn-primaryx{justify-content:center}.user-card-facts,.user-card-facts-three{grid-template-columns:1fr 1fr}}
        @media (max-width:576px){.user-admin-hero{padding:19px;border-radius:18px}.user-admin-hero-icon{width:48px;height:48px;min-width:48px;border-radius:14px;font-size:20px}.user-admin-hero h2{font-size:18px}.user-admin-stats{gap:6px}.user-mini-stat{min-width:0;padding:9px}.user-mini-stat strong{font-size:16px}.user-command-search{flex-wrap:wrap}.user-command-search .btn-primaryx{width:100%;justify-content:center}.user-card-grid{padding:10px}.user-card-identity{align-items:flex-start;flex-wrap:wrap}.user-card-nameblock{min-width:calc(100% - 60px)}.user-state-pill{margin-left:56px;margin-top:-8px}.user-card-facts,.user-card-facts-three{grid-template-columns:1fr}.user-card-actions{flex-wrap:wrap}.user-manage-btn{flex-basis:100%}.user-directory-head-wrap{flex-direction:column}.user-panel-save-row .btn-primaryx{width:100%;justify-content:center}}


        /* -----------------------------------------------------------------
           2026 Premium UI Refinement V3 — stronger readability + full
           inventory create-page redesign for Charge Slip / CAPEX / OPEX.
           Focus: higher contrast fields, clearer section separation,
           grouped readonly details, and more readable labels.
        ----------------------------------------------------------------- */
        .premium-form .form-label,
        .inventory-studio-page .form-label{display:inline-flex;align-items:center;gap:6px;margin-bottom:8px;padding:4px 10px;border-radius:999px;background:#EEF4FD;color:#1F4881;font-size:10.5px;font-weight:900;letter-spacing:.045em;text-transform:uppercase}
        .premium-form .form-control,
        .premium-form .form-select,
        .inventory-studio-page .form-control,
        .inventory-studio-page .form-select{min-height:52px;border:2px solid #8EA8CB;background:#FFFFFF;color:#18253D;font-size:14px;font-weight:650;line-height:1.45;border-radius:15px;box-shadow:inset 0 1px 0 rgba(255,255,255,.72),0 1px 2px rgba(18,30,49,.02)}
        .premium-form textarea.form-control,
        .inventory-studio-page textarea.form-control{min-height:124px;padding-top:14px}
        .premium-form .form-control::placeholder,
        .premium-form .form-select,
        .inventory-studio-page .form-control::placeholder,
        .inventory-studio-page .form-select{color:#7B8CA5;font-weight:550}
        .premium-form .form-control:not([readonly]):not(:disabled):hover,
        .premium-form .form-select:not(:disabled):hover,
        .inventory-studio-page .form-control:not([readonly]):not(:disabled):hover,
        .inventory-studio-page .form-select:not(:disabled):hover{border-color:#557FBC;background:#FCFEFF;box-shadow:0 0 0 1px rgba(85,127,188,.08)}
        .premium-form .form-control:not([readonly]):not(:disabled):focus,
        .premium-form .form-select:not(:disabled):focus,
        .inventory-studio-page .form-control:not([readonly]):not(:disabled):focus,
        .inventory-studio-page .form-select:not(:disabled):focus{border-color:#1855B0;background:#fff;box-shadow:0 0 0 4px rgba(24,85,176,.13),0 12px 24px rgba(18,51,102,.08)}
        .premium-form .form-control[readonly],
        .premium-form .form-control:disabled,
        .premium-form .form-select:disabled,
        .inventory-studio-page .form-control[readonly],
        .inventory-studio-page .form-control:disabled,
        .inventory-studio-page .form-select:disabled{background:linear-gradient(180deg,#F1F5FA,#EAF0F7);border-color:#BACADD;color:#455A79;font-weight:700}
        .premium-form .tiny,.premium-form .inventory-help,.inventory-studio-page .tiny,.inventory-studio-page .inventory-help{margin-top:7px;color:#667892;font-size:11.5px;line-height:1.5}
        .premium-section,.inventory-panel{border:1px solid #CCD9E8;border-radius:24px;background:#fff;box-shadow:0 14px 38px rgba(19,40,79,.05)}
        .premium-section-head{padding:21px 24px;border-bottom:1px solid #DDE6F0;background:linear-gradient(180deg,#FDFEFF,#F4F8FD)}
        .premium-section-head h3{font-size:15px;color:#172944}
        .premium-section-head p{font-size:12px;color:#667A95;max-width:760px}
        .premium-section-body{padding:24px;background:linear-gradient(180deg,#FFFFFF 0%,#FAFCFF 100%)}
        .premium-budget-card{border-width:2px;background:linear-gradient(135deg,#F7FAFF,#EDF4FE)}
        .premium-table-wrap{border:2px solid #C2D0E0;border-radius:18px;background:#fff}
        .premium-entry-table thead th{background:#F4F8FD;color:#23436F;border-bottom:1px solid #D7E0EA;font-size:11px;font-weight:900;letter-spacing:.045em;text-transform:uppercase}
        .premium-entry-table tbody td{border-bottom:1px solid #E9EEF5;vertical-align:middle}
        .premium-route-card{border:2px solid #D6E0EC;background:linear-gradient(180deg,#FFFFFF,#F8FBFF)}
        .premium-warning{border:2px solid #E7C0C4;background:#FFF6F7}
        .premium-document-strip{border:2px solid #D6E0EC;background:linear-gradient(135deg,#F8FBFF,#F2F6FC);border-radius:18px;padding:18px}
        .premium-form-actionbar{border:2px solid #D6E0EC;background:rgba(255,255,255,.98)}

        .inventory-studio-page{display:grid;gap:22px;padding-bottom:18px}
        .inventory-studio-hero{display:grid;grid-template-columns:auto 1fr auto;gap:18px;align-items:center;padding:26px 28px;border:1px solid #C9D8EB;border-radius:28px;background:linear-gradient(130deg,#FFFFFF 0%,#F4F8FF 47%,#EAF2FF 100%);box-shadow:0 22px 50px rgba(23,56,109,.10)}
        .inventory-studio-hero-mark{width:72px;height:72px;border-radius:22px;display:grid;place-items:center;background:linear-gradient(145deg,#255CB2,#123A7B);color:#fff;font-size:30px;box-shadow:0 18px 34px rgba(23,59,122,.24)}
        .inventory-studio-eyebrow{display:inline-block;margin-bottom:8px;color:#1F4E95;font-size:11px;font-weight:900;letter-spacing:.08em;text-transform:uppercase}
        .inventory-studio-hero-copy h2{margin:0;font-family:var(--font-display);font-size:31px;font-weight:850;letter-spacing:-.03em;color:#11284B}
        .inventory-studio-hero-copy p{margin:8px 0 0;max-width:760px;color:#61748E;font-size:13px;line-height:1.65}
        .inventory-studio-hero-side{display:flex;flex-direction:column;align-items:flex-end;gap:12px}
        .inventory-studio-backbtn{border-radius:14px;padding:12px 15px;background:#fff;border:1px solid #C8D6E7;color:#355C93;font-weight:800}
        .inventory-studio-pillbar{display:flex;gap:8px;flex-wrap:wrap;justify-content:flex-end}
        .inventory-studio-pillbar span{display:inline-flex;align-items:center;gap:7px;padding:8px 11px;border-radius:999px;background:#fff;border:1px solid #D0DCEC;color:#456382;font-size:10.5px;font-weight:850;box-shadow:0 6px 16px rgba(28,60,108,.05)}
        .inventory-studio-form{display:grid;gap:20px}
        .inventory-studio-shell{display:grid;gap:20px}
        .inventory-panel-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:22px 24px;border-bottom:1px solid #DEE6F1;background:linear-gradient(180deg,#FEFFFF,#F4F8FD);border-radius:24px 24px 0 0}
        .inventory-panel-head-compact{align-items:center}
        .inventory-panel-kicker{display:inline-flex;margin-bottom:8px;padding:4px 10px;border-radius:999px;background:#EAF1FC;color:#1F549E;font-size:10px;font-weight:900;letter-spacing:.055em;text-transform:uppercase}
        .inventory-panel-head h3{margin:0;font-family:var(--font-display);font-size:20px;font-weight:800;color:#152A49;letter-spacing:-.02em}
        .inventory-panel-head p{margin:6px 0 0;color:#66788F;font-size:12.5px;line-height:1.55;max-width:740px}
        .inventory-step-chip,.inventory-step-bullet{display:grid;place-items:center;min-width:52px;height:52px;padding:0 14px;border-radius:16px;background:linear-gradient(145deg,#184A99,#0D306B);color:#fff;font-size:12px;font-weight:900;letter-spacing:.06em;box-shadow:0 12px 26px rgba(15,49,107,.22)}
        .inventory-step-bullet{min-width:42px;height:42px;border-radius:999px;background:#fff;color:#24539C;border:2px solid #CAD7E7;box-shadow:none}
        .inventory-panel-body{padding:24px 24px 26px;background:linear-gradient(180deg,#FFFFFF 0%,#FAFCFF 100%);border-radius:0 0 24px 24px}
        .inventory-studio-grid-main{display:grid;grid-template-columns:minmax(0,1.5fr) minmax(320px,.78fr);gap:20px;align-items:start}
        .inventory-entry-card,.inventory-side-card,.inventory-field-card{border:1px solid #D4DFEC;border-radius:22px;background:#fff;box-shadow:0 12px 28px rgba(24,47,88,.05)}
        .inventory-entry-card{padding:20px}
        .inventory-entry-card-head,.inventory-side-card-head,.inventory-subhead{display:flex;align-items:flex-start;gap:12px}
        .inventory-entry-card-icon,.inventory-side-card-icon,.inventory-subhead i{width:40px;height:40px;min-width:40px;border-radius:13px;display:grid;place-items:center;background:#EAF2FF;color:#2458A4;border:1px solid #CFDCF0;font-size:17px}
        .inventory-entry-card-head strong,.inventory-side-card-head strong,.inventory-subhead strong{display:block;color:#213754;font-size:13px;font-weight:850}
        .inventory-entry-card-head span,.inventory-side-card-head span,.inventory-subhead span{display:block;margin-top:3px;color:#74849A;font-size:10.8px;line-height:1.5}
        .inventory-side-card{padding:20px;background:linear-gradient(180deg,#FAFCFF,#F5F9FF)}
        .inventory-meta-stack{display:grid;gap:12px;margin-top:16px}
        .inventory-meta-box{padding:16px;border:1px solid #D2DEEC;border-radius:18px;background:#fff;box-shadow:inset 0 1px 0 rgba(255,255,255,.8)}
        .inventory-meta-box span{display:block;color:#71829B;font-size:10px;font-weight:900;letter-spacing:.055em;text-transform:uppercase}
        .inventory-meta-box strong{display:block;margin-top:8px;color:#153156;font-size:18px;font-weight:850;line-height:1.3}
        .inventory-meta-box small{display:block;margin-top:6px;color:#6E7F96;font-size:11px;line-height:1.5}
        .inventory-side-note,.inventory-quiet-banner,.inventory-inline-hint{display:flex;align-items:flex-start;gap:10px;padding:14px 15px;border-radius:16px;border:1px solid #D6E2EF;background:linear-gradient(135deg,#F8FBFF,#EFF5FD)}
        .inventory-side-note i,.inventory-quiet-banner i,.inventory-inline-hint i{margin-top:2px;color:#2C64B1;font-size:16px}
        .inventory-side-note strong,.inventory-quiet-banner strong,.inventory-inline-hint strong{display:block;color:#25406A;font-size:11px}
        .inventory-side-note span,.inventory-quiet-banner span,.inventory-inline-hint span{display:block;margin-top:3px;color:#6F7F95;font-size:10.8px;line-height:1.55}
        .inventory-field{padding:0}
        .inventory-help{padding-left:2px}
        .inventory-field-card{padding:20px}
        .inventory-field-card-tall{height:100%}
        .inventory-hint-panel{padding-top:4px}
        .inventory-metric-card{display:flex;flex-direction:column;justify-content:center;min-height:100%;padding:16px;border:1px solid #D2DEEC;border-radius:18px;background:linear-gradient(145deg,#F7FAFF,#EEF4FE)}
        .inventory-metric-card span{color:#62758F;font-size:10.5px;font-weight:900;letter-spacing:.05em;text-transform:uppercase}
        .inventory-metric-card strong{margin-top:8px;color:#16335A;font-size:17px;font-weight:850;line-height:1.35}
        .inventory-metric-card small{margin-top:8px;color:#6C7C91;font-size:11px;line-height:1.55}
        .inventory-upload-box{padding:16px;border:2px dashed #A8BDD8;border-radius:20px;background:linear-gradient(135deg,#FBFDFF,#F4F8FE)}
        .inventory-current-image{padding:16px;border:1px solid #D8E1EC;border-radius:20px;background:#fff}
        .inventory-current-image-label{display:block;margin-bottom:10px;color:#6A7C95;font-size:10px;font-weight:900;letter-spacing:.05em;text-transform:uppercase}
        .inventory-current-image img{width:180px;height:180px;object-fit:cover;border-radius:18px;border:1px solid #D6E0EC;background:#F4F8FD;box-shadow:0 12px 22px rgba(18,42,79,.08)}
        .inventory-status-row{display:flex;align-items:center;gap:10px;margin:0;padding:14px 16px;border:1px solid #D3DEEB;border-radius:16px;background:#F9FBFE}
        .inventory-status-row .form-check-input{margin-top:0;width:1.15em;height:1.15em}
        .inventory-status-row .form-check-label{color:#264062;font-size:12.5px;font-weight:700}
        .inventory-studio-actionbar{position:sticky;bottom:14px;z-index:30;display:flex;align-items:center;justify-content:space-between;gap:16px;padding:16px 18px;border:1px solid #CEDAEA;border-radius:20px;background:rgba(255,255,255,.97);box-shadow:0 18px 42px rgba(17,45,92,.14);backdrop-filter:blur(12px)}
        .inventory-studio-actionnote{display:flex;align-items:flex-start;gap:12px;min-width:0}
        .inventory-studio-actionnote i{font-size:18px;color:#2C68B7;margin-top:1px}
        .inventory-studio-actionnote strong{display:block;color:#23406A;font-size:11.5px}
        .inventory-studio-actionnote span{display:block;margin-top:3px;color:#718099;font-size:10.8px;line-height:1.45}
        .inventory-studio-actionbuttons{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
        .inventory-studio-actionbuttons .btn-primaryx,.inventory-studio-actionbuttons .btn-soft{border-radius:14px;padding:12px 17px;font-weight:800}

        @media (max-width:1100px){
          .inventory-studio-hero{grid-template-columns:auto 1fr;align-items:flex-start}
          .inventory-studio-hero-side{grid-column:1/-1;align-items:flex-start}
          .inventory-studio-pillbar{justify-content:flex-start}
          .inventory-studio-grid-main{grid-template-columns:1fr}
        }
        @media (max-width:768px){
          .inventory-studio-hero{padding:22px 20px;border-radius:22px}
          .inventory-studio-hero-mark{width:58px;height:58px;border-radius:18px;font-size:24px}
          .inventory-studio-hero-copy h2{font-size:24px}
          .inventory-panel-head,.inventory-panel-body{padding-left:18px;padding-right:18px}
          .inventory-studio-actionbar,.premium-form-actionbar{position:static;flex-direction:column;align-items:stretch}
          .inventory-studio-actionbuttons,.premium-action-buttons{width:100%}
          .inventory-studio-actionbuttons>*,.premium-action-buttons>*{flex:1;justify-content:center}
        }
        @media (max-width:576px){
          .inventory-studio-hero{grid-template-columns:1fr}
          .inventory-studio-hero-mark{width:54px;height:54px}
          .inventory-panel-head h3{font-size:17px}
          .inventory-entry-card,.inventory-side-card,.inventory-field-card{padding:16px}
          .inventory-current-image img{width:100%;height:auto;max-width:220px}
        }


        /* ======================================================================
           2026 Responsive + Contrast Pass V4 — UI only
           System-wide mobile behavior and stronger form hierarchy. This block
           intentionally overrides earlier responsive/table rules without
           touching routes, controllers, validation, data, or business logic.
           ====================================================================== */
        :root{--canvas:#EEF3F9;--line:#CBD7E5;--line-2:#E2E9F2}
        html,body{max-width:100%;overflow-x:hidden}
        .main{min-width:0;max-width:100%}
        .content{min-width:0}
        .content>*{max-width:100%}
        img,svg,video,canvas{max-width:100%;height:auto}

        /* Stronger surface hierarchy: canvas -> section -> field. */
        .content .surface,
        .content .data-panel,
        .content .form-shell,
        .content .form-section,
        .content .premium-section,
        .content .inventory-panel,
        .content .settings-item,
        .content .qr-card,
        .content .scanner-shell,
        .content .report-stat{border-color:#C4D2E3;box-shadow:0 10px 28px rgba(25,50,91,.055)}
        .content .form-shell,
        .content .form-section-body,
        .content .premium-section-body,
        .content .inventory-panel-body,
        .content .user-create-form,
        .content .user-management-panel{background:#F4F7FB}
        .content .form-section-head,
        .content .premium-section-head,
        .content .inventory-panel-head{background:linear-gradient(180deg,#FFFFFF,#EAF1F8);border-bottom-color:#C9D6E5}

        /* Labels are deliberately darker and no longer blend into the white UI. */
        .content form .form-label,
        .content .form-label{
            display:flex;align-items:center;gap:6px;width:max-content;max-width:100%;
            margin:0 0 8px;padding:0 0 0 9px;border-left:3px solid #315F9F;border-radius:0;
            background:transparent;color:#16345E;font-size:12px;font-weight:850;
            letter-spacing:.012em;text-transform:none;line-height:1.35;
        }
        .content form .form-control,
        .content form .form-select,
        .content .tree-select-trigger.form-select,
        .content .approver-tree-trigger{
            min-height:50px;border:2px solid #7895BA!important;border-radius:13px!important;
            background:#FFFFFF!important;color:#14243C!important;font-size:13.5px;font-weight:650;
            box-shadow:0 2px 0 rgba(39,74,123,.04),inset 0 1px 0 rgba(255,255,255,.75)!important;
            transition:border-color .15s ease,box-shadow .15s ease,background .15s ease!important;
        }
        .content form textarea.form-control{min-height:126px;line-height:1.6}
        .content form .form-control::placeholder{color:#71829B;opacity:1;font-weight:500}
        .content form .form-control:not([readonly]):not(:disabled):hover,
        .content form .form-select:not(:disabled):hover,
        .content .tree-select-trigger.form-select:hover,
        .content .approver-tree-trigger:hover{border-color:#426FA9!important;background:#FBFDFF!important}
        .content form .form-control:not([readonly]):not(:disabled):focus,
        .content form .form-select:not(:disabled):focus,
        .content .tree-select.open .tree-select-trigger,
        .content .approver-tree-select.open .approver-tree-trigger{
            border-color:#0E4EAA!important;background:#FFFFFF!important;
            box-shadow:0 0 0 4px rgba(22,85,172,.15),0 9px 20px rgba(23,58,110,.08)!important;outline:0!important;
        }
        .content form .form-control[readonly],
        .content form .form-control:disabled,
        .content form .form-select:disabled{
            background:#E5EBF3!important;border:2px solid #B8C6D7!important;color:#465B78!important;
            font-weight:750!important;box-shadow:none!important;cursor:not-allowed;
        }
        .content input[type="file"].form-control{padding:7px 9px;background:#fff!important}
        .content input[type="file"]::file-selector-button{min-height:32px;background:#E5EEFB;color:#174E9B;border:1px solid #B8CBE4;border-radius:9px}
        .content .inventory-help,.content .field-hint,.content .tiny,.content .module-note{color:#596D89}
        .content .inventory-entry-card,.content .inventory-side-card,.content .inventory-field-card,
        .content .premium-route-card,.content .premium-document-strip,.content .premium-budget-card{
            border-color:#BFCFE1;background:#FFFFFF;
        }
        .content .inventory-meta-box{border-color:#BFCFE1;background:#EEF3F8}
        .content .inventory-meta-box strong{color:#17365F}

        /* Tables stay semantically intact on mobile. We scroll them instead of
           hiding headers/turning rows into cards, because some system tables
           do not carry data-label attributes for every column. */
        .table-responsive,.mobile-table-scroll{width:100%;max-width:100%;overflow-x:auto!important;overflow-y:visible;-webkit-overflow-scrolling:touch;overscroll-behavior-inline:contain}
        .mobile-table-scroll{border-radius:14px}
        .data-table,.kv-table,.table{max-width:none}

        /* Mobile sidebar backdrop. */
        .sidebar-backdrop{display:none;position:fixed;inset:0;z-index:18;background:rgba(7,18,42,.48);backdrop-filter:blur(2px)}

        @media (max-width: 991.98px){
            body.sidebar-open{overflow:hidden}
            .sidebar{
                left:0!important;transform:translateX(-104%);width:min(82vw,300px);max-width:300px;
                transition:transform .24s cubic-bezier(.2,.8,.2,1);z-index:40;border-right:1px solid rgba(255,255,255,.1)
            }
            body.sidebar-open .sidebar{left:0!important;transform:translateX(0)}
            body.sidebar-open .sidebar-backdrop{display:block}
            .brand-wrap{padding:18px 16px}
            .nav-list{padding:10px 12px 18px}
            .nav-linkx{min-height:46px;padding:12px 13px;font-size:13px}
            .nav-linkx i{font-size:17px}

            .main{width:100%;min-width:0}
            .topbar{padding:10px 12px 11px;gap:8px;align-items:center;overflow:visible}
            .topbar>.d-flex:first-child{flex:1 1 100%;min-width:0;gap:10px!important}
            .mobile-menu{flex:0 0 42px;width:42px;height:42px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.09)}
            .page-title{font-size:9px!important;line-height:1.2;letter-spacing:.07em}
            .page-subtitle-hero{font-size:16px;line-height:1.25;margin-top:3px;white-space:normal}
            .top-actions{width:100%;min-width:0;gap:8px;padding-top:8px;border-top:1px solid rgba(255,255,255,.09);justify-content:flex-end}
            .page-actions{min-width:0;flex:1 1 auto;display:flex;flex-wrap:nowrap;gap:7px;padding:0;border-right:0;overflow-x:auto;scrollbar-width:none;-webkit-overflow-scrolling:touch}
            .page-actions::-webkit-scrollbar{display:none}
            .page-actions .btn-primaryx,.page-actions .btn-soft{flex:0 0 auto;min-height:38px;padding:8px 11px;font-size:10.5px!important;white-space:nowrap}
            .page-actions .btn-primaryx i,.page-actions .btn-soft i{font-size:13px!important}
            .notif-link{flex:0 0 38px;margin-left:auto}
            .user-chip{flex:0 0 auto;padding-left:8px;border-left:0}
            .logout-btn{display:inline-flex;align-items:center;gap:0;flex:0 0 auto;font-size:0}
            .logout-btn i{font-size:16px!important}

            .content{padding:14px 12px 22px}
            .module-head{align-items:flex-start;margin-bottom:14px}
            .module-head>div:first-child{min-width:0}
            .module-title{font-size:17px;line-height:1.3}
            .module-note{font-size:11.5px;line-height:1.5}
            .page-hero,.premium-form-intro,.inventory-studio-hero,.user-admin-hero{border-radius:18px}
            .page-hero{padding:18px}
            .page-hero-side{width:100%}
            .hero-chip{flex:1 1 140px}
            .form-shell{padding:16px}
            .form-section-head,.premium-section-head,.inventory-panel-head{padding:16px}
            .form-section-body,.premium-section-body,.inventory-panel-body{padding:16px}
            .premium-schedule-grid{grid-template-columns:1fr}
            .premium-routing-grid,.premium-routing-grid-three{grid-template-columns:1fr}
            .premium-upload-zone{grid-template-columns:auto 1fr}
            .premium-upload-zone>input,.premium-upload-zone>.form-control,.premium-upload-zone>.form-select{grid-column:1/-1}
            .premium-document-strip{align-items:stretch;flex-direction:column}
            .premium-document-ref{width:100%}
            .inventory-studio-grid-main{grid-template-columns:1fr}
            .inventory-studio-hero{grid-template-columns:auto 1fr;padding:19px}
            .inventory-studio-hero-side{grid-column:1/-1;width:100%;align-items:flex-start}
            .inventory-studio-pillbar{justify-content:flex-start}
            .inventory-studio-actionbar,.premium-form-actionbar,.form-actionbar{position:static;bottom:auto}
            .inventory-studio-actionbar,.premium-form-actionbar{flex-direction:column;align-items:stretch}
            .inventory-studio-actionbuttons,.premium-action-buttons{width:100%}
            .inventory-studio-actionbuttons>*,.premium-action-buttons>*{flex:1 1 auto;justify-content:center}

            .stat-grid,.panel-grid-2,.grid-cards,.report-grid,.qr-grid,.qr-tiles,.snapshot-grid{grid-template-columns:1fr!important}
            .user-admin-stats{grid-template-columns:repeat(2,minmax(0,1fr))!important;width:100%}
            .user-card-grid{grid-template-columns:1fr!important}
            .user-card-facts,.user-card-facts-three{grid-template-columns:repeat(2,minmax(0,1fr))!important}
            .user-filter-deck,.user-command-bar,.user-directory-head-wrap,.user-create-actions{align-items:stretch;flex-direction:column}
            .user-command-search,.user-command-search-compact{max-width:none;width:100%}
            .user-command-search input,.user-command-search-compact input{min-width:0;width:100%}

            .modal-dialog{width:auto;max-width:calc(100vw - 24px);margin:12px auto}
            .modal-content{max-height:calc(100dvh - 24px);overflow:hidden}
            .modal-body{overflow-y:auto;-webkit-overflow-scrolling:touch}
            .dropdown-menu{max-width:calc(100vw - 24px)}
            .tree-select-panel,.approver-tree-panel{max-width:calc(100vw - 36px)}
        }

        @media (max-width: 767.98px){
            /* Prevent iOS from zooming into form controls and improve tap targets. */
            .content form .form-control,.content form .form-select,.content .tree-select-trigger.form-select,.content .approver-tree-trigger{font-size:16px;min-height:50px}
            .content form .form-label,.content .form-label{font-size:12px;margin-bottom:7px}
            .content .btn-primaryx,.content .btn-soft,.content .btn-approve,.content .btn-reject,.content .small-btn{min-height:42px}
            .content .row{--bs-gutter-x:1rem;--bs-gutter-y:1rem}
            .content [style*="grid-template-columns"]{grid-template-columns:1fr!important}
            .kv-table{min-width:620px}
            .data-table{min-width:700px}
            .premium-entry-table{min-width:920px!important}
            .table-responsive>.data-table,.mobile-table-scroll>.data-table{width:100%}

            /* Override the older row-to-card table transform. */
            .data-table{display:table!important}
            .data-table thead{display:table-header-group!important}
            .data-table tbody{display:table-row-group!important}
            .data-table tbody tr{display:table-row!important;padding:0!important;border-top:0!important}
            .data-table tbody td{display:table-cell!important;padding:10px 12px!important;border-top:0!important;border-bottom:1px solid var(--line-2)!important;vertical-align:middle!important}
            .data-table tbody td::before{display:none!important;content:none!important}
            .data-table tbody td[data-label="Actions"]{white-space:nowrap}

            .premium-table-footer{align-items:stretch;flex-direction:column}
            .premium-total-card{width:100%}
            .req-summary{align-items:flex-start}
            .req-summary-spacer{display:none}
            .request-actions{width:100%;justify-content:flex-start}
            .request-actions>*{max-width:100%}
            .page-tabs{gap:6px;overflow-x:auto;flex-wrap:nowrap;padding-bottom:2px;scrollbar-width:none;-webkit-overflow-scrolling:touch}
            .page-tabs::-webkit-scrollbar{display:none}
            .page-tabs a,.page-tabs span{flex:0 0 auto;padding:9px 10px}
        }

        @media (max-width: 575.98px){
            .content{padding:10px 9px 18px}
            .topbar{padding:9px}
            .topbar>.d-flex:first-child{gap:8px!important}
            .page-subtitle-hero{font-size:15px}
            .avatar{width:32px;height:32px;border-radius:9px;font-size:11px}
            .notif-link{width:34px;height:34px;flex-basis:34px}
            .mobile-menu{width:38px;height:38px;flex-basis:38px;border-radius:10px}
            .page-actions .btn-primaryx,.page-actions .btn-soft{min-height:34px;padding:7px 9px;font-size:10px!important}

            /* On phones, all Bootstrap form columns become one readable column. */
            .content form .row>[class*="col-"]:not(.col-auto){width:100%;max-width:100%;flex:0 0 100%}
            .content .form-shell{padding:13px;border-radius:16px}
            .content .form-section,.content .premium-section,.content .inventory-panel{border-radius:17px}
            .content .form-section-head,.content .premium-section-head,.content .inventory-panel-head{border-radius:17px 17px 0 0;padding:14px}
            .content .form-section-body,.content .premium-section-body,.content .inventory-panel-body{padding:14px;border-radius:0 0 17px 17px}
            .content form .form-control,.content form .form-select,.content .tree-select-trigger.form-select,.content .approver-tree-trigger{border-radius:12px!important}
            .premium-form-intro{align-items:flex-start;padding:16px;gap:12px}
            .premium-form-intro-icon{width:46px;height:46px;min-width:46px;border-radius:13px;font-size:20px}
            .premium-form-intro h2{font-size:18px}
            .premium-form-intro p{font-size:11.5px}
            .premium-form-intro-badge{display:none}
            .premium-section-head{align-items:flex-start}
            .premium-step{width:34px;height:34px;min-width:34px}
            .premium-routing-grid,.premium-routing-grid-three{gap:10px}
            .premium-route-card{min-height:0;padding:14px}
            .premium-date-pair{grid-template-columns:1fr;align-items:stretch}
            .premium-date-pair>i{display:none}
            .premium-upload-zone{grid-template-columns:1fr;text-align:left}
            .premium-upload-icon{width:40px;height:40px}

            .inventory-studio-hero{grid-template-columns:1fr;padding:16px;border-radius:17px}
            .inventory-studio-hero-mark{width:50px;height:50px;border-radius:14px;font-size:21px}
            .inventory-studio-hero-copy h2{font-size:20px;line-height:1.25}
            .inventory-studio-hero-copy p{font-size:11.5px}
            .inventory-studio-pillbar{width:100%;gap:6px}
            .inventory-studio-pillbar span{font-size:9.5px;padding:7px 9px}
            .inventory-entry-card,.inventory-side-card,.inventory-field-card{padding:14px;border-radius:16px}
            .inventory-panel-head h3{font-size:17px}
            .inventory-step-chip,.inventory-step-bullet{min-width:38px;height:38px;border-radius:11px;padding:0 10px}
            .inventory-studio-actionbuttons{flex-direction:column}
            .inventory-studio-actionbuttons>*{width:100%}

            .user-admin-stats{grid-template-columns:1fr!important}
            .user-card-facts,.user-card-facts-three{grid-template-columns:1fr!important}
            .user-card-grid{padding:9px!important}
            .user-management-panel,.user-create-form{padding:12px!important}
            .user-state-pill{margin-left:0!important;margin-top:0!important}
            .user-card-actions{flex-direction:column;align-items:stretch}
            .user-card-actions>*{width:100%}

            .nu-dialog-foot{flex-direction:column-reverse}
            .nu-btn{width:100%}
        }


        /* ======================================================================
           2026 Mobile Hardening V5 — true narrow-screen reflow
           Fixes remaining horizontal clipping on forms/details without touching
           backend behavior, routes, IDs, form names, validation or workflows.
           ====================================================================== */
        @media (max-width: 767.98px){
            html,body,.app-shell,.main,.content{width:100%;max-width:100%;min-width:0}
            .content{overflow-x:clip}
            .content :where(.surface,.data-panel,.form-shell,.form-section,.premium-form-page,.premium-form,.premium-section,.premium-section-head,.premium-section-body,.premium-document-strip,.premium-document-ref,.premium-budget-card,.premium-route-card,.inventory-studio-page,.inventory-studio-form,.inventory-panel,.inventory-panel-head,.inventory-panel-body,.inventory-entry-card,.inventory-side-card,.inventory-field-card,.panel-grid-2,.report-grid,.user-admin-page,.user-create-shell,.user-directory-shell){min-width:0;max-width:100%}
            .content :where(.surface,.form-shell,.form-section,.premium-section,.inventory-panel,.panel-grid-2>*,.report-grid>*){width:100%}
            .content form,.content form *{min-width:0}
            .content form :where(.form-control,.form-select,textarea,input,select),
            .content :where(.tree-select-trigger.form-select,.approver-tree-trigger){width:100%!important;max-width:100%!important;box-sizing:border-box}
            .content :where(h1,h2,h3,h4,h5,h6,p,span,strong,small,a,div,td,th){overflow-wrap:anywhere}
            .module-head>*{min-width:0;max-width:100%}
            .module-title,.module-note{white-space:normal!important;overflow-wrap:anywhere;word-break:normal}

            /* Premium forms: remove Bootstrap negative-gutter overflow on phones/tablets. */
            .premium-form .row,
            .inventory-studio-form .row,
            .form-shell form .row{
                margin-left:0!important;margin-right:0!important;
                --bs-gutter-x:0rem;
            }
            .premium-form .row>[class*="col-"],
            .inventory-studio-form .row>[class*="col-"],
            .form-shell form .row>[class*="col-"]{
                min-width:0;max-width:100%;
            }
            .premium-section-head>div:not(.premium-step){min-width:0;flex:1}
            .premium-section-head h3,.premium-section-head p{white-space:normal;overflow-wrap:anywhere}
            .premium-document-strip{width:100%;min-width:0;max-width:100%;box-sizing:border-box}
            .premium-document-strip>*{min-width:0;max-width:100%}
            .premium-document-ref{width:100%!important;max-width:100%!important}
            .premium-budget-card{width:100%;min-width:0;flex-wrap:wrap}
            .premium-budget-copy{min-width:0;flex:1 1 180px}
            .premium-budget-status{margin-left:0}
            .premium-upload-zone,.premium-date-summary,.premium-route-card{width:100%;min-width:0;max-width:100%}
            .premium-route-content{min-width:0;max-width:100%}

            /* Key/value detail tables are not data grids. Stack each label/value
               pair vertically so Activity Proposal, reservation, venue and
               requisition details are fully readable without horizontal scroll. */
            .kv-table{
                display:block!important;width:100%!important;min-width:0!important;max-width:100%!important;
                border:0!important;border-radius:0!important;overflow:visible!important;
                background:transparent!important;box-shadow:none!important;border-collapse:separate!important;
            }
            .kv-table tbody{display:grid!important;width:100%!important;min-width:0;gap:9px}
            .kv-table tr{
                display:block!important;width:100%!important;min-width:0;max-width:100%;
                border:1px solid #C7D5E5!important;border-radius:13px!important;overflow:hidden!important;
                background:#fff!important;box-shadow:0 3px 10px rgba(22,49,91,.035);
            }
            .kv-table tr:nth-child(even),.kv-table tr:nth-child(even) th{background:#fff!important}
            .kv-table th,.kv-table td{
                display:block!important;width:100%!important;max-width:100%!important;min-width:0!important;
                white-space:normal!important;word-break:normal!important;overflow-wrap:anywhere!important;
                border:0!important;border-right:0!important;border-top:0!important;box-sizing:border-box;
            }
            .kv-table th{
                padding:9px 12px 7px!important;background:#EDF3F9!important;color:#4E6380!important;
                font-size:9.5px!important;line-height:1.35!important;letter-spacing:.055em!important;
            }
            .kv-table td{
                padding:10px 12px 12px!important;background:#fff!important;color:#182943!important;
                font-size:12px!important;line-height:1.55!important;
            }
            .kv-table td.kv-wide{white-space:normal!important}
            .kv-table .tag{max-width:100%;white-space:normal;overflow-wrap:anywhere;margin-bottom:5px}
            .pf-attachment{min-width:0;max-width:100%;flex-wrap:wrap}
            .pf-attachment>div{min-width:0;max-width:100%}
            .pf-attachment a{display:inline-block;max-width:100%;overflow-wrap:anywhere;word-break:break-word}

            /* Approval timeline must also wrap instead of pushing the page wide. */
            .approval-timeline{min-width:0;max-width:100%}
            .approval-timeline li{min-width:0;max-width:100%;padding-left:34px}
            .approval-timeline .step-row{display:grid!important;grid-template-columns:minmax(0,1fr)!important;gap:7px!important;min-width:0}
            .approval-timeline .step-row>div{min-width:0;max-width:100%}
            .approval-timeline .step-role,.approval-timeline .step-name{white-space:normal;overflow-wrap:anywhere}
            .approval-timeline .step-meta{width:max-content;max-width:100%;white-space:normal!important;line-height:1.35;overflow-wrap:anywhere}
            .approval-timeline img{max-width:100%!important}
            .note-callout{min-width:0;max-width:100%}

            /* Generic data tables remain scrollable, but never enlarge the page itself. */
            .table-responsive,.mobile-table-scroll{display:block;width:100%;max-width:100%;min-width:0;overflow-x:auto!important;overflow-y:visible;-webkit-overflow-scrolling:touch}
            .mobile-table-scroll>.data-table,.table-responsive>.data-table{max-width:none}
        }

        @media (max-width: 575.98px){
            /* One real column: no negative gutters and no hidden width left by Bootstrap. */
            .premium-form .row,
            .inventory-studio-form .row,
            .form-shell form .row{
                display:grid!important;grid-template-columns:minmax(0,1fr)!important;
                gap:15px!important;margin:0!important;
            }
            .premium-form .row>[class*="col-"],
            .inventory-studio-form .row>[class*="col-"],
            .form-shell form .row>[class*="col-"]{
                width:100%!important;max-width:100%!important;flex:0 0 100%!important;
                padding-left:0!important;padding-right:0!important;margin-top:0!important;
            }
            .premium-form-page{width:100%;max-width:100%;padding-bottom:12px}
            .premium-section{width:100%;max-width:100%;overflow:visible}
            .premium-section-head{width:100%;padding:13px 12px;gap:10px}
            .premium-section-head p{font-size:10.5px;line-height:1.45}
            .premium-section-body{width:100%;padding:12px}
            .premium-document-strip{padding:13px;border-radius:14px}
            .premium-document-strip strong{font-size:16px}
            .premium-form .form-label{width:100%;max-width:100%;font-size:11px}
            .premium-form :where(.form-control,.form-select,.tree-select-trigger.form-select,.approver-tree-trigger){font-size:16px!important;min-height:50px;width:100%!important;max-width:100%!important}
            .premium-form textarea.form-control{min-height:118px}

            /* Requested-items table becomes field cards on small phones. JS selectors,
               input names, row add/remove logic and calculations remain untouched. */
            .premium-table-wrap{overflow:visible!important;border:0!important;background:transparent!important}
            .premium-entry-table{display:block!important;width:100%!important;min-width:0!important;max-width:100%!important;background:transparent!important}
            .premium-entry-table thead{display:none!important}
            .premium-entry-table tbody{display:grid!important;gap:12px;width:100%!important}
            .premium-entry-table tbody tr{
                display:block!important;width:100%!important;min-width:0!important;max-width:100%!important;
                padding:12px!important;border:1px solid #C6D5E6!important;border-radius:15px!important;
                background:#fff!important;box-shadow:0 5px 14px rgba(20,49,92,.045);
            }
            .premium-entry-table tbody td{
                display:grid!important;grid-template-columns:100px minmax(0,1fr)!important;align-items:center;
                gap:9px!important;width:100%!important;min-width:0!important;max-width:100%!important;
                padding:7px 0!important;border:0!important;background:transparent!important;
            }
            .premium-entry-table tbody td::before{display:block!important;color:#536986;font-size:9px;font-weight:900;letter-spacing:.05em;text-transform:uppercase}
            .premium-entry-table tbody td:nth-child(1)::before{content:'Item'}
            .premium-entry-table tbody td:nth-child(2)::before{content:'Unit'}
            .premium-entry-table tbody td:nth-child(3)::before{content:'Available'}
            .premium-entry-table tbody td:nth-child(4)::before{content:'Quantity'}
            .premium-entry-table tbody td:nth-child(5)::before{content:'Unit Price'}
            .premium-entry-table tbody td:nth-child(6)::before{content:'Amount'}
            .premium-entry-table tbody td:nth-child(7)::before{content:'Remarks'}
            .premium-entry-table tbody td:nth-child(8){display:flex!important;justify-content:flex-end;padding-top:9px!important}
            .premium-entry-table tbody td:nth-child(8)::before{display:none!important;content:none!important}
            .premium-entry-table :where(.form-control,.form-select){width:100%!important;max-width:100%!important;min-width:0!important}
            .premium-remove-row{width:40px;height:40px}

            /* Detail pages get a slightly tighter mobile card treatment. */
            .panel-grid-2{display:grid!important;grid-template-columns:minmax(0,1fr)!important;gap:12px!important;width:100%}
            .panel-grid-2>.surface{padding:12px!important;border-radius:16px!important}
            .kv-table{margin-bottom:14px}
            .approval-timeline .step-role{font-size:11.5px}
            .approval-timeline .step-name{font-size:10.8px;line-height:1.45}
            .approval-timeline .step-meta{font-size:9.5px}
        }


        /* ======================================================================
           V6 inventory cleanup — removes the oversized side-card layout and
           user-facing developer note. Keeps V5 responsive hardening intact.
           ====================================================================== */
        .inventory-panel-identity .inventory-panel-body{padding-top:20px}
        .inventory-system-strip{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;margin-bottom:16px}
        .inventory-system-item{display:flex;align-items:flex-start;gap:12px;min-width:0;padding:14px 16px;border:1px solid #C7D5E5;border-radius:16px;background:linear-gradient(180deg,#EEF3F8,#E8EFF7)}
        .inventory-system-icon{width:38px;height:38px;min-width:38px;border-radius:11px;display:grid;place-items:center;background:#fff;border:1px solid #C6D5E8;color:#245BA7;font-size:16px}
        .inventory-system-item>div:last-child{min-width:0}
        .inventory-system-item span{display:block;color:#5B6F8A;font-size:9.5px;font-weight:900;letter-spacing:.06em;text-transform:uppercase}
        .inventory-system-item strong{display:block;margin-top:4px;color:#18365E;font-size:14px;font-weight:850;line-height:1.35;overflow-wrap:anywhere}
        .inventory-system-item small{display:block;margin-top:4px;color:#6A7C93;font-size:10.5px;line-height:1.45}
        .inventory-entry-workspace{padding:18px 18px 20px;border:1px solid #CAD8E8;border-radius:18px;background:#fff;box-shadow:0 8px 22px rgba(22,49,89,.035)}
        .inventory-entry-card-head-spaced{margin-bottom:18px;padding-bottom:14px;border-bottom:1px solid #E0E7F0}
        .inventory-step-grid{display:grid;gap:16px;align-items:start}
        .inventory-step-grid-capex{grid-template-columns:minmax(280px,.72fr) minmax(0,1.28fr)}
        .inventory-step-grid-opex{grid-template-columns:repeat(2,minmax(0,1fr))}
        .inventory-media-grid{display:grid;grid-template-columns:minmax(300px,.72fr) minmax(0,1.28fr);gap:16px;align-items:start}
        .inventory-reference-note{display:flex;align-items:center;gap:9px;padding:11px 13px;border:1px solid #D2DDE9;border-radius:13px;background:#F7FAFD;color:#60718A;font-size:10.5px;line-height:1.45}
        .inventory-reference-note i{color:#2C64B1;font-size:14px}
        .inventory-studio-actionbar-clean{justify-content:flex-end;gap:10px}
        .inventory-studio-actionbar-clean>.btn-soft,.inventory-studio-actionbar-clean>.btn-primaryx{border-radius:13px;padding:11px 16px;font-weight:800}
        .inventory-panel-body-identity .row{align-items:start}
        .inventory-panel-body-identity .inventory-help{min-height:0}

        @media (max-width:1100px){
            .inventory-step-grid-capex,.inventory-step-grid-opex,.inventory-media-grid{grid-template-columns:1fr}
        }
        @media (max-width:767.98px){
            .inventory-system-strip{grid-template-columns:1fr}
            .inventory-system-item{padding:13px}
            .inventory-entry-workspace{padding:14px}
            .inventory-studio-actionbar-clean{display:grid;grid-template-columns:1fr 1fr;position:static!important}
            .inventory-studio-actionbar-clean>*{width:100%;justify-content:center}
        }
        @media (max-width:575.98px){
            .inventory-panel-body-identity{padding:12px!important}
            .inventory-system-strip{gap:9px;margin-bottom:11px}
            .inventory-system-item{border-radius:13px;padding:11px 12px}
            .inventory-system-icon{width:34px;height:34px;min-width:34px;border-radius:10px}
            .inventory-system-item strong{font-size:12.5px}
            .inventory-system-item small{font-size:9.8px}
            .inventory-entry-workspace{padding:12px;border-radius:14px}
            .inventory-entry-card-head-spaced{margin-bottom:14px;padding-bottom:11px}
            .inventory-studio-actionbar-clean{grid-template-columns:1fr;padding:10px;border-radius:15px}
        }

    </style>
    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar" id="appSidebar" aria-label="Main navigation">
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
    <button type="button" class="sidebar-backdrop" id="sidebarBackdrop" aria-label="Close navigation"></button>

    <main class="main">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="mobile-menu" id="mobileMenuButton" type="button" aria-label="Open navigation" aria-controls="appSidebar" aria-expanded="false"><i class="bi bi-list"></i></button>
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
                        <div class="user-role">@php($__role = auth()->user()->role ?? 'admin'){{ $__role === 'fmo' ? 'FMO Staff' : ($__role === 'fmo_super_admin' ? 'FMO Super Admin' : ucwords(str_replace('_', ' ', $__role))) }}</div>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button></form>
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </main>
</div>
<script>
(function () {
    function initResponsiveAdminShell() {
        const body = document.body;
        const menuButton = document.getElementById('mobileMenuButton');
        const backdrop = document.getElementById('sidebarBackdrop');
        const sidebar = document.getElementById('appSidebar');

        function setSidebar(open) {
            body.classList.toggle('sidebar-open', open);
            if (menuButton) {
                menuButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                menuButton.setAttribute('aria-label', open ? 'Close navigation' : 'Open navigation');
            }
        }

        if (menuButton) menuButton.addEventListener('click', function () { setSidebar(!body.classList.contains('sidebar-open')); });
        if (backdrop) backdrop.addEventListener('click', function () { setSidebar(false); });
        if (sidebar) {
            sidebar.querySelectorAll('a.nav-linkx').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.matchMedia('(max-width: 991.98px)').matches) setSidebar(false);
                });
            });
        }
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && body.classList.contains('sidebar-open')) setSidebar(false);
        });
        window.addEventListener('resize', function () {
            if (!window.matchMedia('(max-width: 991.98px)').matches) setSidebar(false);
        });

        /* Make legacy data tables safely swipeable without changing their
           IDs, rows, inputs, forms, or JS selectors. */
        document.querySelectorAll('.data-table').forEach(function (table) {
            const parent = table.parentElement;
            if (!parent || parent.classList.contains('table-responsive') || parent.classList.contains('mobile-table-scroll')) return;
            const wrapper = document.createElement('div');
            wrapper.className = 'mobile-table-scroll';
            parent.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initResponsiveAdminShell);
    } else {
        initResponsiveAdminShell();
    }
})();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
@include('auth.partials.password-toggle')
@include('partials.alerts')
</body>
</html>
