<!DOCTYPE html>
<html lang="id">
<head>@include('layouts.partials.head')</head>
<body class="app-shell">
<div class="app-frame flex flex-col min-h-screen">
@include('components.mobile.profile-hero', ['user' => $user ?? null])
<main class="flex-1 px-5 py-4 pb-[72px] animate-fade-up">
@yield('content')
</main>
@include('components.mobile.bottom-nav', ['active' => 'profil'])
</div>
</body>
</html>
