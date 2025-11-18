<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Contacts')</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:#f6f7fb;margin:0}
    .container{max-width:960px;margin:24px auto;padding:24px;background:#fff;border-radius:16px;box-shadow:0 6px 24px rgba(0,0,0,0.08)}
    h1{margin-top:0}
    input,select,textarea{padding:8px 10px;border:1px solid #d0d5dd;border-radius:8px;font-size:14px;box-sizing:border-box;width:100%}
    button{padding:8px 12px;border:1px solid #d0d5dd;border-radius:8px;background:#f9fafb;cursor:pointer;font-size:14px}
    button.primary{background:#1f6feb;color:#fff;border-color:#1f6feb}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{padding:10px;border-bottom:1px solid #eef0f3;text-align:left;font-size:14px;vertical-align:top}
    .row{display:flex;gap:8px;align-items:center;flex-wrap:wrap}
    .space{flex:1}
    .toolbar{display:flex;gap:8px;align-items:center;margin:12px 0;flex-wrap:wrap}
    .pagination{display:flex;gap:8px;align-items:center;justify-content:flex-end;margin-top:12px}
    .badge{padding:2px 8px;border-radius:999px;background:#eef6ff;color:#19478a;font-size:12px}
    .modal{position:fixed;inset:0;background:rgba(0,0,0,0.5);display:none;place-items:center;z-index:1000}
    .modal.open{display:grid}
    .card{background:#fff;border-radius:16px;padding:20px;box-shadow:0 10px 32px rgba(0,0,0,0.15);max-width:420px;width:100%}
  </style>
</head>
<body>
  <div class="container">
    @yield('content')
  </div>

  <div id="modal" class="modal">
    <div class="card">
      <h3 id="modal-title">Confirm</h3>
      <div id="modal-body" style="margin:12px 0"></div>
      <div style="display:flex;justify-content:flex-end;gap:8px">
        <button type="button" onclick="Modal.close()">Cancel</button>
        <button type="button" class="primary" id="modal-confirm">Confirm</button>
      </div>
    </div>
  </div>

  <script>
    const Modal = (function () {
      const modal = document.getElementById('modal');
      const body = document.getElementById('modal-body');
      const confirmBtn = document.getElementById('modal-confirm');
      let currentForm = null;

      function open(message, form) {
        body.innerHTML = message;
        currentForm = form;
        modal.classList.add('open');
      }

      function close() {
        modal.classList.remove('open');
        currentForm = null;
      }

      confirmBtn.addEventListener('click', function () {
        if (currentForm) {
          const form = currentForm;
          close();
          form.submit();
        }
      });

      return {
        confirm(message, form) {
          open(message, form);
          return false;
        },
        close
      };
    })();
  </script>
</body>
</html>
