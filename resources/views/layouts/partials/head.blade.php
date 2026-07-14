<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config={theme:{extend:{
  fontFamily:{sans:['Plus Jakarta Sans','system-ui','sans-serif']},
  colors:{brand:{50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',500:'#10b981',600:'#059669',700:'#047857',800:'#065f46',900:'#064e3b'}},
  boxShadow:{soft:'0 8px 30px -6px rgba(5,150,105,.12)'}
}}}};
</script>
<title>@yield('title', 'Mantri Tani')</title>
@stack('head')
