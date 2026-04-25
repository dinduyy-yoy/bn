<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil - Event Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Registrasi Berhasil!</h2>
            
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            @endif

            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="font-semibold text-gray-800 mb-2">Langkah Selanjutnya:</h3>
                <ol class="text-left text-sm text-gray-600 space-y-2">
                    <li class="flex items-start">
                        <span class="font-medium mr-2">1.</span>
                        <span>Periksa email Anda untuk link verifikasi</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-medium mr-2">2.</span>
                        <span>Klik link verifikasi dalam 60 menit</span>
                    </li>
                    <li class="flex items-start">
                        <span class="font-medium mr-2">3.</span>
                        <span>Login setelah email diverifikasi</span>
                    </li>
                </ol>
            </div>

            <div class="space-y-3">
                <a href="{{ route('login') }}" 
                   class="block w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-medium text-center">
                    Kembali ke Login
                </a>
                
                <button 
                    onclick="window.location.reload()" 
                    class="block w-full bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition duration-200 font-medium">
                    Daftar Akun Lain
                </button>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-xs text-gray-500">
                    <strong>Tip:</strong> Periksa folder spam jika tidak menemukan email verifikasi.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
