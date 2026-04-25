<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Event Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Buat Akun Baru</h2>
            <p class="text-gray-600 mt-2">Daftar untuk mengakses sistem</p>
            <div class="mt-4 p-3 bg-blue-50 rounded-md">
                <p class="text-sm text-blue-700">
                    <strong>Penting:</strong> Email verifikasi akan dikirim setelah pendaftaran
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Nama Lengkap -->
            <div>
                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="nama_lengkap" 
                    name="nama_lengkap" 
                    value="{{ old('nama_lengkap') }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan nama lengkap"
                >
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="email@example.com"
                >
            </div>

            <!-- Nomor Handphone -->
            <div>
                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                    Nomor Handphone <span class="text-red-500">*</span>
                </label>
                <input 
                    type="tel" 
                    id="no_hp" 
                    name="no_hp" 
                    value="{{ old('no_hp') }}"
                    required
                    pattern="[0-9]{10,15}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="08123456789"
                >
            </div>

            <!-- Kategori Pendaftar -->
            <div>
                <label for="kategori_pendaftar" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori Pendaftar <span class="text-red-500">*</span>
                </label>
                <select 
                    id="kategori_pendaftar" 
                    name="kategori_pendaftar" 
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Mahasiswa" {{ old('kategori_pendaftar') == 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                    <option value="Umum" {{ old('kategori_pendaftar') == 'Umum' ? 'selected' : '' }}>Umum</option>
                    <option value="Dosen" {{ old('kategori_pendaftar') == 'Dosen' ? 'selected' : '' }}>Dosen</option>
                </select>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    minlength="8"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Minimal 8 karakter"
                >
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password <span class="text-red-500">*</span>
                </label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required
                    minlength="8"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Ulangi password"
                >
            </div>

            <!-- Submit Button -->
            <div>
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition duration-200 font-medium"
                >
                    Daftar
                </button>
            </div>

            <!-- Login Link -->
            <div class="text-center">
                <p class="text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Login di sini
                    </a>
                </p>
            </div>
        </form>
    </div>

    <script>
        // Validasi nomor handphone
        document.getElementById('no_hp').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Validasi password match
        document.getElementById('password_confirmation').addEventListener('input', function(e) {
            const password = document.getElementById('password').value;
            const confirmation = e.target.value;
            
            if (password !== confirmation && confirmation !== '') {
                e.target.setCustomValidity('Password tidak cocok');
            } else {
                e.target.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
