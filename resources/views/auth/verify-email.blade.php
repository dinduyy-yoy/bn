<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - Event Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-800">Verifikasi Email Anda</h2>
            <p class="text-gray-600 mt-2">Sebelum melanjutkan, periksa email Anda untuk link verifikasi.</p>
        </div>

        @if (session('success'))
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                <p class="text-sm">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('message'))
            <div class="mb-4 bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded">
                <p class="text-sm">{{ session('message') }}</p>
            </div>
        @endif

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-600 text-center">
                Kami telah mengirim email verifikasi ke:
                <br>
                <strong class="text-gray-800">{{ auth()->user()->email }}</strong>
            </p>
        </div>

        <div class="space-y-4">
            <p class="text-sm text-gray-600 text-center">
                Tidak menerima email? Periksa folder spam atau klik tombol di bawah untuk mengirim ulang.
            </p>

            <form action="{{ route('verification.send') }}" method="POST" class="text-center">
                @csrf
                <button 
                    type="submit" 
                    class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-medium"
                >
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="text-gray-600 hover:text-gray-800 text-sm">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t border-gray-200">
            <p class="text-xs text-gray-500 text-center">
                Jika Anda tidak mendaftar dengan email ini, silakan hubungi tim support kami.
            </p>
        </div>
    </div>
</body>
</html>
