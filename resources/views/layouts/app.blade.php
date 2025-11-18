<!DOCTYPE html>
<html lang="{{ request()->get('lang', 'uk') }}">
<head>
  <meta charset="UTF-8">
  <title>@yield('title', 'Notes')</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    :root {
      color-scheme: light;
    }
    html.dark {
      color-scheme: dark;
    }
    body {
      max-width: 1000px;
      margin: 0 auto;
      padding: 16px;
      font-family: system-ui, sans-serif;
      background: #ffffff;
      color: #222222;
      transition: background 0.25s ease, color 0.25s ease;
    }
    html.dark body {
      background: #1e1e1e;
      color: #f5f5f5;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 8px;
      border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    th {
      text-align: left;
    }
    .btn {
      padding: 6px 10px;
      cursor: pointer;
      border-radius: 8px;
      border: none;
    }
    .btn-danger {
      background: #c0392b;
      color: #fff;
    }
    .btn-primary {
      background: #3498db;
      color: #fff;
    }
    .btn-secondary {
      background: #7f8c8d;
      color: #fff;
    }
    input[type="text"], textarea, select {
      width: 100%;
      padding: 6px 8px;
      box-sizing: border-box;
      border-radius: 8px;
      border: 1px solid #d0d5dd;
      background: inherit;
      color: inherit;
    }
    .error {
      color: crimson;
      font-size: 12px;
    }
    .pagination {
      margin-top: 12px;
      display: flex;
      gap: 8px;
      align-items: center;
      flex-wrap: wrap;
    }
    a { color: inherit; }
  </style>
  @yield('styles')
</head>
<body>
  @yield('content')

  <script>
    (function() {
      const LS_THEME_KEY = 'notes:theme';
      function applyTheme(theme) {
        if (theme === 'dark') {
          document.documentElement.classList.add('dark');
        } else {
          document.documentElement.classList.remove('dark');
        }
      }
      const saved = localStorage.getItem(LS_THEME_KEY) || 'light';
      applyTheme(saved);
      window.toggleTheme = function() {
        const current = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        const next = current === 'dark' ? 'light' : 'dark';
        localStorage.setItem(LS_THEME_KEY, next);
        applyTheme(next);
      };
    })();
  </script>
  @yield('scripts')
</body>
</html>
