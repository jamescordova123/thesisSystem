<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Cashier Report')</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 2rem; color: #111; }
        h1 { font-size: 1.25rem; margin-bottom: 0.25rem; }
        .meta { color: #555; font-size: 0.875rem; margin-bottom: 1.5rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; margin-bottom: 1rem; }
        th, td { border: 1px solid #ccc; padding: 0.5rem 0.75rem; text-align: left; }
        th { background: #f5f5f5; }
        .no-print { margin-bottom: 1rem; }
        @media print { .no-print { display: none; } body { margin: 0.5rem; } }
    </style>
</head>
<body>
    <button type="button" class="no-print" onclick="window.print()">Print / Save as PDF</button>
    @yield('content')
</body>
</html>
