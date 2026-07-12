<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-100">

<div class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-md">

        <div class="bg-white rounded-3xl shadow-xl p-8">

            <div class="text-center mb-8">

                <div class="w-20 h-20 rounded-full bg-amber-100 mx-auto flex items-center justify-center">

                    <i  class=" w-18 h-18 text-amber-500">

                        <img src="{{ asset('images/ypp.png') }}" alt="Logo Aplikasi" >
                    </i>

                </div>

                <h1 class="text-3xl font-bold mt-5">
                    Sistem Absensi
                </h1>

                <p class="text-gray-500 mt-2">
                    Yayasan Prasasti Perdamaian
                </p>

            </div>

            @if ($errors->any())

                <div class="bg-red-100 border border-red-300 text-red-700 rounded-xl p-3 mb-5">

                    {{ $errors->first() }}

                </div>

            @endif

            <form action="{{ route('login.authenticate') }}" method="POST">

                @csrf

                <div class="mb-5">

                    <label class="block mb-2 font-semibold">
                        Username
                    </label>

                    <input
                        type="text"
                        name="username"
                        value="{{ old('username') }}"
                        class="w-full border rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-400 focus:outline-none"
                        placeholder="Masukkan username">

                </div>

                <div class="mb-6">

                    <label class="block mb-2 font-semibold">
                        Password
                    </label>

                    <div class="relative">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="w-full border rounded-xl px-4 py-3 pr-12 focus:ring-2 focus:ring-amber-400 focus:outline-none"
                            placeholder="Masukkan password">

                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2">

                            <i id="eyeIcon" data-lucide="eye" class="w-5 h-5 text-gray-500"></i>

                        </button>

                    </div>

                </div>

                <button
                    type="submit"
                    class="w-full bg-amber-500 hover:bg-amber-600 text-white font-bold py-3 rounded-xl transition">

                    Login

                </button>

            </form>

        </div>

        <p class="text-center mt-6 text-sm text-gray-500">

            © {{ date('Y') }} Yayasan Prasasti Perdamaian

        </p>

    </div>

</div>

<script>

    lucide.createIcons();

    const password = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');

    toggle.addEventListener('click',function(){

        if(password.type === 'password'){

            password.type='text';

            document.getElementById('eyeIcon').setAttribute('data-lucide','eye-off');

        }else{

            password.type='password';

            document.getElementById('eyeIcon').setAttribute('data-lucide','eye');

        }

        lucide.createIcons();

    });

</script>

</body>
</html>