{{--
    System feedback: toasts, dialogs and confirmations.

    Replaces three things that used to look unfinished:
      1. the flat green "…updated successfully." bar,
      2. the bulleted red validation list,
      3. the browser's native confirm() box on every delete.

    Design choice worth knowing: success messages appear as a TOAST that slides
    in and dismisses itself, while errors open a MODAL DIALOG that must be
    acknowledged. A save confirmation shouldn't interrupt the user's next click,
    but a failure should never be missed. To make successes modal too, change
    NUAlert.flash's `toast` call to `dialog`.
--}}

<style>
    /* ---------- Toast ---------- */
    .nu-toasts{position:fixed;top:18px;right:18px;z-index:2000;display:flex;flex-direction:column;gap:10px;max-width:min(380px,calc(100vw - 36px))}
    .nu-toast{display:flex;gap:12px;align-items:flex-start;padding:14px 16px;border-radius:14px;background:#fff;border:1px solid #E1E5F0;box-shadow:0 12px 32px -10px rgba(12,19,48,.28);transform:translateX(120%);opacity:0;transition:transform .32s cubic-bezier(.22,1,.36,1),opacity .25s ease}
    .nu-toast.in{transform:translateX(0);opacity:1}
    .nu-toast.out{transform:translateX(120%);opacity:0}
    .nu-toast-icon{width:30px;height:30px;border-radius:50%;flex:0 0 30px;display:flex;align-items:center;justify-content:center;font-size:15px;color:#fff}
    .nu-toast.success .nu-toast-icon{background:linear-gradient(150deg,#34C77B,#1F7A45)}
    .nu-toast.error .nu-toast-icon{background:linear-gradient(150deg,#E85D6A,#B02A37)}
    .nu-toast.info .nu-toast-icon{background:linear-gradient(150deg,#5B8DEF,#2B4EA8)}
    .nu-toast-body{flex:1;min-width:0}
    .nu-toast-title{font-size:12.5px;font-weight:800;color:#0C1330;margin-bottom:2px}
    .nu-toast-msg{font-size:12px;color:#5A5F73;line-height:1.5;word-wrap:break-word}
    .nu-toast-close{background:none;border:0;color:#AAB1C6;font-size:16px;line-height:1;cursor:pointer;padding:0 2px}
    .nu-toast-close:hover{color:#5A5F73}
    .nu-toast-bar{position:absolute;left:0;bottom:0;height:3px;border-radius:0 0 14px 14px;background:rgba(31,122,69,.35);animation:nuToastBar linear forwards}
    .nu-toast{position:relative;overflow:hidden}
    .nu-toast.error .nu-toast-bar{background:rgba(176,42,55,.35)}
    @keyframes nuToastBar{from{width:100%}to{width:0}}

    /* ---------- Dialog ---------- */
    .nu-backdrop{position:fixed;inset:0;background:rgba(8,13,34,.55);backdrop-filter:blur(3px);z-index:2100;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;transition:opacity .2s ease}
    .nu-backdrop.in{opacity:1}
    .nu-dialog{width:100%;max-width:440px;background:#fff;border-radius:18px;box-shadow:0 30px 70px -20px rgba(8,13,34,.5);overflow:hidden;transform:translateY(14px) scale(.97);transition:transform .26s cubic-bezier(.22,1,.36,1)}
    .nu-backdrop.in .nu-dialog{transform:translateY(0) scale(1)}
    .nu-dialog-head{padding:24px 24px 0;text-align:center}
    .nu-dialog-icon{width:54px;height:54px;border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:25px;color:#fff}
    .nu-dialog.success .nu-dialog-icon{background:linear-gradient(150deg,#34C77B,#1F7A45)}
    .nu-dialog.error .nu-dialog-icon{background:linear-gradient(150deg,#E85D6A,#B02A37)}
    .nu-dialog.warn .nu-dialog-icon{background:linear-gradient(150deg,#F0C876,#C9932E)}
    .nu-dialog.info .nu-dialog-icon{background:linear-gradient(150deg,#5B8DEF,#2B4EA8)}
    .nu-dialog-title{font-size:17px;font-weight:800;color:#0C1330;margin-bottom:6px}
    .nu-dialog-body{padding:0 24px 4px;text-align:center;font-size:13px;color:#5A5F73;line-height:1.6}
    .nu-dialog-list{text-align:left;margin:12px 0 0;padding:12px 14px;background:#FDF2F2;border:1px solid #F5D2D2;border-radius:10px;list-style:none}
    .nu-dialog-list li{font-size:12.5px;color:#9B2C2C;padding:3px 0 3px 18px;position:relative;line-height:1.5}
    .nu-dialog-list li::before{content:"•";position:absolute;left:5px;font-weight:800}
    .nu-dialog-foot{display:flex;gap:10px;padding:20px 24px 24px}
    .nu-btn{flex:1;padding:11px 18px;border-radius:11px;border:1px solid transparent;font-size:13px;font-weight:700;cursor:pointer;transition:filter .15s ease,background .15s ease}
    .nu-btn-primary{background:linear-gradient(155deg,#1B2550,#0C1330);color:#fff}
    .nu-btn-primary:hover{filter:brightness(1.15)}
    .nu-btn-danger{background:linear-gradient(155deg,#D14453,#B02A37);color:#fff}
    .nu-btn-danger:hover{filter:brightness(1.1)}
    .nu-btn-ghost{background:#fff;border-color:#D5D9E5;color:#454C66}
    .nu-btn-ghost:hover{background:#F4F6FB}
    @media (max-width:520px){.nu-toasts{left:14px;right:14px;max-width:none}.nu-toast{transform:translateY(-120%)}.nu-toast.in{transform:translateY(0)}.nu-toast.out{transform:translateY(-120%)}}
    @media (prefers-reduced-motion:reduce){.nu-toast,.nu-dialog,.nu-backdrop{transition:none}.nu-toast-bar{animation:none}}
</style>

<div class="nu-toasts" id="nuToasts" aria-live="polite" aria-atomic="true"></div>

<script>
window.NUAlert = (function () {
    var ICONS = {
        success: '<i class="bi bi-check-lg"></i>',
        error: '<i class="bi bi-exclamation-triangle"></i>',
        warn: '<i class="bi bi-exclamation-triangle"></i>',
        info: '<i class="bi bi-info-lg"></i>'
    };

    function toast(opts) {
        var host = document.getElementById('nuToasts');
        if (!host) { return; }
        var kind = opts.kind || 'success';
        var duration = opts.duration || 4500;

        var el = document.createElement('div');
        el.className = 'nu-toast ' + kind;
        el.setAttribute('role', kind === 'error' ? 'alert' : 'status');
        el.innerHTML =
            '<div class="nu-toast-icon">' + (ICONS[kind] || ICONS.info) + '</div>' +
            '<div class="nu-toast-body">' +
                '<div class="nu-toast-title"></div>' +
                '<div class="nu-toast-msg"></div>' +
            '</div>' +
            '<button type="button" class="nu-toast-close" aria-label="Dismiss">&times;</button>' +
            '<div class="nu-toast-bar" style="animation-duration:' + duration + 'ms"></div>';

        el.querySelector('.nu-toast-title').textContent = opts.title || (kind === 'error' ? 'Something went wrong' : 'Done');
        el.querySelector('.nu-toast-msg').textContent = opts.message || '';
        host.appendChild(el);
        requestAnimationFrame(function () { el.classList.add('in'); });

        var timer = setTimeout(close, duration);
        function close() {
            clearTimeout(timer);
            el.classList.add('out');
            setTimeout(function () { el.remove(); }, 340);
        }
        el.querySelector('.nu-toast-close').addEventListener('click', close);
        return close;
    }

    function dialog(opts) {
        return new Promise(function (resolve) {
            var kind = opts.kind || 'info';
            var backdrop = document.createElement('div');
            backdrop.className = 'nu-backdrop';

            var listHtml = '';
            if (opts.items && opts.items.length) {
                listHtml = '<ul class="nu-dialog-list">' + opts.items.map(function (i) {
                    var li = document.createElement('li');
                    li.textContent = i;
                    return li.outerHTML;
                }).join('') + '</ul>';
            }

            backdrop.innerHTML =
                '<div class="nu-dialog ' + kind + '" role="dialog" aria-modal="true">' +
                    '<div class="nu-dialog-head">' +
                        '<div class="nu-dialog-icon">' + (ICONS[kind] || ICONS.info) + '</div>' +
                        '<div class="nu-dialog-title"></div>' +
                    '</div>' +
                    '<div class="nu-dialog-body"><span class="nu-dialog-text"></span>' + listHtml + '</div>' +
                    '<div class="nu-dialog-foot"></div>' +
                '</div>';

            backdrop.querySelector('.nu-dialog-title').textContent = opts.title || 'Notice';
            backdrop.querySelector('.nu-dialog-text').textContent = opts.message || '';

            var foot = backdrop.querySelector('.nu-dialog-foot');
            function done(value) {
                backdrop.classList.remove('in');
                setTimeout(function () { backdrop.remove(); }, 220);
                document.removeEventListener('keydown', onKey);
                resolve(value);
            }

            if (opts.confirm) {
                var cancel = document.createElement('button');
                cancel.type = 'button';
                cancel.className = 'nu-btn nu-btn-ghost';
                cancel.textContent = opts.cancelText || 'Cancel';
                cancel.addEventListener('click', function () { done(false); });
                foot.appendChild(cancel);
            }

            var ok = document.createElement('button');
            ok.type = 'button';
            ok.className = 'nu-btn ' + (opts.danger ? 'nu-btn-danger' : 'nu-btn-primary');
            ok.textContent = opts.okText || (opts.confirm ? 'Confirm' : 'OK');
            ok.addEventListener('click', function () { done(true); });
            foot.appendChild(ok);

            function onKey(e) {
                if (e.key === 'Escape') { done(false); }
                if (e.key === 'Enter') { done(true); }
            }
            document.addEventListener('keydown', onKey);
            backdrop.addEventListener('click', function (e) { if (e.target === backdrop) { done(false); } });

            document.body.appendChild(backdrop);
            requestAnimationFrame(function () { backdrop.classList.add('in'); });
            setTimeout(function () { ok.focus(); }, 60);
        });
    }

    function confirmAction(opts) {
        return dialog(Object.assign({ kind: 'warn', confirm: true, danger: true }, opts));
    }

    return { toast: toast, dialog: dialog, confirm: confirmAction };
})();

/* Any element carrying data-confirm gets the styled dialog instead of the
   browser's native confirm(). Works for both forms and links. */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (form.dataset.confirmed === '1') { return; }
            e.preventDefault();
            NUAlert.confirm({
                title: form.dataset.confirmTitle || 'Please confirm',
                message: form.dataset.confirm,
                okText: form.dataset.confirmOk || 'Yes, continue'
            }).then(function (ok) {
                if (ok) { form.dataset.confirmed = '1'; form.submit(); }
            });
        });
    });

    document.querySelectorAll('a[data-confirm]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            NUAlert.confirm({
                title: link.dataset.confirmTitle || 'Please confirm',
                message: link.dataset.confirm,
                okText: link.dataset.confirmOk || 'Yes, continue'
            }).then(function (ok) { if (ok) { window.location.href = link.href; } });
        });
    });
});
</script>

{{-- Server-side flash messages, handed to the component above. --}}
@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        NUAlert.toast({ kind: 'success', title: 'Success', message: @json(session('success')) });
    });
</script>
@endif

@if(session('info'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        NUAlert.toast({ kind: 'info', title: 'Heads up', message: @json(session('info')) });
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        NUAlert.dialog({ kind: 'error', title: 'Something went wrong', message: @json(session('error')) });
    });
</script>
@endif

@if(isset($errors) && $errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var items = @json($errors->all());
        NUAlert.dialog({
            kind: 'error',
            title: items.length > 1 ? 'Please fix these ' + items.length + ' items' : 'Please check this',
            message: items.length > 1 ? 'Your changes were not saved.' : items[0],
            items: items.length > 1 ? items : [],
            okText: 'Got it'
        });
    });
</script>
@endif
