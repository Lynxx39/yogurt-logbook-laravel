<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'YogurtLog') — Logbook Proyek Yogurt</title>
  <meta name="description" content="Platform logbook digital pemantau proyek fermentasi yogurt siswa berbasis indikator ilmiah.">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/yogurt.css') }}">
  <link rel="stylesheet" href="{{ asset('css/yogurt-extra.css') }}">
  @stack('styles')
</head>
<body>
  @yield('content')
  <div id="toast-container"></div>
  <script src="{{ asset('js/yogurt.js') }}"></script>
  @stack('scripts')
</body>
</html>
