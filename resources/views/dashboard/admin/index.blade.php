<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin')</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>

<body class="bg-gray-100">

<div class="min-h-screen flex">

    @include('includes.components.sidebar')

    <div class="flex-1 lg:ml-72">

        {{-- @include('includes.components.header') --}}

        <div class="p-6">
            @yield('content')

        </div>

    </div>

</div>

<script>
    lucide.createIcons();
</script>

</body>
</html>