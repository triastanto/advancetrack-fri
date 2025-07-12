@extends('layouts.app')

@section('content')
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[var(--color-bg)]">
    <div class="w-full sm:max-w-4xl mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <div class="mb-6 text-center">
            <img src="/logo.png" alt="AdvanceTrack FRI Logo" class="h-12 mx-auto mb-4" />
            <h2 class="text-2xl font-bold text-[var(--color-text-main)]">Buat Akun Baru</h2>
            <p class="mt-2 text-sm text-[var(--color-text-muted)]">Bergabung dengan AdvanceTrack FRI</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf
            
            <!-- User Account Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-[var(--color-border)]">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-user class="w-5 h-5 mr-2 text-blue-600" />
                        Informasi Akun
                    </h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-[var(--color-text-main)]">Nama Lengkap</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-user class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-[var(--color-text-main)]">Email</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-envelope-open class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-[var(--color-text-main)]">Password</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-lock-closed class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-[var(--color-text-main)]">Konfirmasi Password</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-lock-closed class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-[var(--color-border)]">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-briefcase class="w-5 h-5 mr-2 text-blue-600" />
                        Informasi Karyawan
                    </h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="nidn" class="block text-sm font-medium text-[var(--color-text-main)]">NIDN</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-identification class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="nidn" name="nidn" type="text" value="{{ old('nidn') }}" required
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder-[var(--color-text-muted)] focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('nidn')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="position" class="block text-sm font-medium text-[var(--color-text-main)]">Jabatan</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-briefcase class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="position" name="position" type="text" value="{{ old('position') }}" required
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder="Contoh: Dosen Teknik Industri" focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('position')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="functional_position" class="block text-sm font-medium text-[var(--color-text-main)]">Jabatan Fungsional</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-academic-cap class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <select id="functional_position" name="functional_position"
                                        class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="">Pilih jabatan fungsional</option>
                                    <option value="Asisten Ahli" {{ old('functional_position') == 'Asisten Ahli' ? 'selected' : '' }}>Asisten Ahli</option>
                                    <option value="Lektor" {{ old('functional_position') == 'Lektor' ? 'selected' : '' }}>Lektor</option>
                                    <option value="Lektor Kepala" {{ old('functional_position') == 'Lektor Kepala' ? 'selected' : '' }}>Lektor Kepala</option>
                                    <option value="Profesor" {{ old('functional_position') == 'Profesor' ? 'selected' : '' }}>Profesor</option>
                                    <option value="Tenaga Pendidik" {{ old('functional_position') == 'Tenaga Pendidik' ? 'selected' : '' }}>Tenaga Pendidik</option>
                                    <option value="Tenaga Kependidikan" {{ old('functional_position') == 'Tenaga Kependidikan' ? 'selected' : '' }}>Tenaga Kependidikan</option>
                                </select>
                            </div>
                            @error('functional_position')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-[var(--color-border)]">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-user class="w-5 h-5 mr-2 text-blue-600" />
                        Informasi Pribadi
                    </h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="birth_place" class="block text-sm font-medium text-[var(--color-text-main)]">Tempat Lahir</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-map-pin class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="birth_place" name="birth_place" type="text" value="{{ old('birth_place') }}"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder="Contoh: Jakarta" focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('birth_place')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="birth_date" class="block text-sm font-medium text-[var(--color-text-main)]">Tanggal Lahir</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-calendar class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="birth_date" name="birth_date" type="date" value="{{ old('birth_date') }}"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('birth_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gender" class="block text-sm font-medium text-[var(--color-text-main)]">Jenis Kelamin</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-user class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <select id="gender" name="gender" required
                                        class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            @error('gender')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-[var(--color-text-main)]">Nomor Telepon</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-phone class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="contact_phone" name="contact_phone" type="tel" value="{{ old('contact_phone') }}"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder="Contoh: 08123456789" focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-[var(--color-text-main)]">Email Kontak</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-envelope class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email') }}"
                                       class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder="email@example.com" focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                            </div>
                            @error('contact_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="origin_address" class="block text-sm font-medium text-[var(--color-text-main)]">Alamat Asal</label>
                        <div class="mt-1 relative">
                            <x-heroicon-o-home class="absolute left-3 top-3 text-[var(--color-text-muted)] w-5 h-5" />
                            <textarea id="origin_address" name="origin_address" rows="3"
                                      class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm placeholder="Masukkan alamat lengkap" focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">{{ old('origin_address') }}</textarea>
                        </div>
                        @error('origin_address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
                <div class="px-6 py-4 border-b border-[var(--color-border)]">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-blue-600" />
                        Informasi Akademik
                    </h3>
                </div>
                <div class="px-6 py-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="research_group_id" class="block text-sm font-medium text-[var(--color-text-main)]">Kelompok Keilmuan</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-academic-cap class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <select id="research_group_id" name="research_group_id"
                                        class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm">
                                    <option value="">Pilih kelompok keilmuan</option>
                                    @foreach($researchGroups as $group)
                                        <option value="{{ $group->id }}" {{ old('research_group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="research_lab_id" class="block text-sm font-medium text-[var(--color-text-main)]">Laboratorium Riset</label>
                            <div class="mt-1 relative">
                                <x-heroicon-o-building-office class="absolute left-3 top-1/2 -translate-y-1/2 text-[var(--color-text-muted)] w-5 h-5" />
                                <select id="research_lab_id" name="research_lab_id"
                                        class="appearance-none block w-full pl-10 pr-3 py-3 border border-[var(--color-border)] rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm" disabled>
                                    <option value="">Pilih laboratorium riset</option>
                                </select>
                            </div>
                            @error('research_lab_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm">
                    <x-heroicon-o-user-plus class="w-5 h-5 mr-2" />
                    Daftar
                </button>
            </div>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-[var(--color-text-muted)]">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-medium text-[var(--color-primary)] hover:text-[var(--color-primary-dark)] flex items-center gap-1 justify-center">
                    <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const researchGroupSelect = document.getElementById('research_group_id');
    const researchLabSelect = document.getElementById('research_lab_id');
    
    // Store the research groups data
    const researchGroupsData = @json($researchGroups);
    
    researchGroupSelect.addEventListener('change', function() {
        const selectedGroupId = this.value;
        researchLabSelect.innerHTML = '<option value="">Pilih laboratorium riset</option>';
        researchLabSelect.disabled = true;
        
        if (selectedGroupId) {
            const selectedGroup = researchGroupsData.find(group => group.id == selectedGroupId);
            if (selectedGroup && selectedGroup.research_labs) {
                selectedGroup.research_labs.forEach(lab => {
                    const option = document.createElement('option');
                    option.value = lab.id;
                    option.textContent = lab.name;
                    researchLabSelect.appendChild(option);
                });
                researchLabSelect.disabled = false;
            }
        }
    });
    
    // Set initial state if there's an old value
    const oldResearchGroupId = @json(old('research_group_id'));
    const oldResearchLabId = @json(old('research_lab_id'));
    
    if (oldResearchGroupId) {
        researchGroupSelect.value = oldResearchGroupId;
        researchGroupSelect.dispatchEvent(new Event('change'));
        
        // Set the lab selection after a short delay to ensure the options are populated
        setTimeout(() => {
            if (oldResearchLabId) {
                researchLabSelect.value = oldResearchLabId;
            }
        }, 100);
    }
});
</script>
@endsection
