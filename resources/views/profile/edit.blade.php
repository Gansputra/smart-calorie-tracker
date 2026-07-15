@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title')
    <i class="fa-solid fa-user mr-1.5"></i> Profil Saya
@endsection
@section('page-subtitle', 'Kelola informasi akun dan target nutrisi Anda')

@section('content')
<div class="max-w-3xl space-y-6">

    {{-- Profile Info Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-user text-primary-600"></i>
            Informasi Profil
        </h3>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Avatar --}}
            <div class="flex items-center gap-5">
                <div class="relative">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                          class="w-20 h-20 rounded-2xl object-cover border-2 border-slate-100 shadow-sm"
                          id="avatarPreview">
                    <label class="absolute -bottom-1 -right-1 w-7 h-7 bg-primary-600 rounded-lg flex items-center justify-center cursor-pointer hover:bg-primary-700 shadow-lg">
                        <i class="fa-solid fa-plus text-white text-xs"></i>
                        <input type="file" name="avatar" accept="image/*" class="hidden" id="avatarInput">
                    </label>
                </div>
                <div>
                    <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                    <p class="text-sm text-slate-500">{{ $user->email }}</p>
                    <p class="text-xs text-slate-400 mt-1">JPG, PNG, WebP (maks 2MB)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                           class="form-input @error('name') border-red-400 @enderror" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                           class="form-input @error('email') border-red-400 @enderror" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Physical Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label for="gender" class="form-label">Jenis Kelamin</label>
                    <select name="gender" id="gender" class="form-input">
                        <option value="">Pilih</option>
                        <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Laki-Laki</option>
                        <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label for="age" class="form-label">Usia (tahun)</label>
                    <input type="number" name="age" id="age" value="{{ old('age', $user->age) }}"
                           class="form-input" min="10" max="120" placeholder="25">
                </div>
                <div>
                    <label for="height" class="form-label">Tinggi (cm)</label>
                    <input type="number" name="height" id="height" value="{{ old('height', $user->height) }}"
                           class="form-input" step="0.5" min="50" max="300" placeholder="170">
                </div>
                <div>
                    <label for="weight_profile" class="form-label">Berat (kg)</label>
                    <input type="number" name="weight" id="weight_profile" value="{{ old('weight', $user->weight) }}"
                           class="form-input" step="0.1" min="20" max="500" placeholder="65">
                </div>
            </div>

            {{-- Targets --}}
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-sm font-semibold text-slate-600 mb-3"><i class="fa-solid fa-bullseye mr-1 text-slate-500"></i> Target Harian</p>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="calorie_target" class="form-label text-xs">Target Kalori (kkal)</label>
                        <input type="number" name="calorie_target" id="calorie_target"
                               value="{{ old('calorie_target', $user->calorie_target) }}"
                               class="form-input @error('calorie_target') border-red-400 @enderror"
                               min="500" max="10000">
                    </div>
                    <div>
                        <label for="protein_target" class="form-label text-xs">Target Protein (g)</label>
                        <input type="number" name="protein_target" id="protein_target"
                               value="{{ old('protein_target', $user->protein_target) }}"
                               class="form-input @error('protein_target') border-red-400 @enderror"
                               min="10" max="500">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i>
                Simpan Profil
            </button>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-5 flex items-center gap-2">
            <i class="fa-solid fa-lock text-orange-500"></i>
            Ganti Password
        </h3>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" id="current_password"
                       class="form-input @error('current_password') border-red-400 @enderror">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" name="password" id="new_password"
                           class="form-input @error('password') border-red-400 @enderror"
                           placeholder="Minimal 8 karakter">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="form-input" placeholder="Ulangi password baru">
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-floppy-disk mr-1.5"></i>
                Perbarui Password
            </button>
        </form>
    </div>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('avatarPreview').src = e.target.result; };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
