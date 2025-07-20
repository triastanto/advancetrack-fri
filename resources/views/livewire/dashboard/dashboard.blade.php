@if ($role === 'lecturer')
    <div class="px-4 py-6 md:px-8 bg-gray-50 min-h-screen">
        <!-- Welcome Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                <x-heroicon-o-academic-cap class="w-6 h-6 text-blue-600" />
                Selamat Datang, {{ Auth::user()->name }}!
                </h1>
            <p class="text-gray-600 mt-1">Kelola dokumen akademik dan pantau status studi Anda</p>
        </div>

        <!-- Document Status Summary -->
        @if($documentStats)
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-clock class="w-6 h-6 text-yellow-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Menunggu</p>
                        <p class="text-xl font-semibold text-gray-900">{{ $documentStats['pending'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-check-circle class="w-6 h-6 text-green-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Terverifikasi</p>
                        <p class="text-xl font-semibold text-gray-900">{{ $documentStats['verified'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-x-circle class="w-6 h-6 text-red-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Ditolak</p>
                        <p class="text-xl font-semibold text-gray-900">{{ $documentStats['rejected'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <x-heroicon-o-document-text class="w-6 h-6 text-blue-500" />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total</p>
                        <p class="text-xl font-semibold text-gray-900">{{ $documentStats['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Main Content Grid - 2 Columns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Recent Notifications -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <x-heroicon-o-bell class="w-5 h-5 mr-2 text-blue-600" />
                            Notifikasi Terbaru
                        </h3>
                        <a href="{{ route('notifications') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Lihat Semua
                        </a>
                    </div>
                    
                    @if($recentNotifications && $recentNotifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($recentNotifications->take(4) as $notification)
                                <div class="flex items-start space-x-3 p-3 rounded-lg {{ $notification->read_at ? 'bg-gray-50' : 'bg-blue-50' }}">
                                    <div class="flex-shrink-0 mt-1">
                                        <x-heroicon-o-bell class="w-4 h-4 {{ $notification->read_at ? 'text-gray-400' : 'text-blue-500' }}" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium {{ $notification->read_at ? 'text-gray-600' : 'text-gray-900' }}">
                                            {{ $notification->data['title'] ?? 'Notifikasi' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(!$notification->read_at)
                                        <div class="flex-shrink-0">
                                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-heroicon-o-bell class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                            <p class="text-sm text-gray-500">Tidak ada notifikasi baru</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Study Calendar Status -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <x-heroicon-o-calendar class="w-5 h-5 mr-2 text-green-600" />
                            Status Studi
                        </h3>
                        <a href="{{ route('study-calendar.manage') }}" class="text-sm text-blue-600 hover:text-blue-800">
                            Kelola
                        </a>
                    </div>
                    
                    @if(!empty($studyInfo) && isset($studyInfo['program']))
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Program Studi:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $studyInfo['program'] ?? 'Tidak tersedia' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Status:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    @if(isset($studyInfo['status']) && $studyInfo['status'] == 5) bg-green-100 text-green-800
                                    @elseif(isset($studyInfo['status']) && $studyInfo['status'] == 2) bg-yellow-100 text-yellow-800
                                    @elseif(isset($studyInfo['status']) && $studyInfo['status'] == 4) bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if(isset($studyInfo['status']) && $studyInfo['status'] == 5) Aktif
                                    @elseif(isset($studyInfo['status']) && $studyInfo['status'] == 2) Menunggu Persetujuan
                                    @elseif(isset($studyInfo['status']) && $studyInfo['status'] == 4) Ditolak
                                    @else Draft @endif
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Semester:</span>
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $studyInfo['current_semester'] ?? '-' }}/{{ $studyInfo['total_semester'] ?? '-' }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Mulai:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $studyInfo['start_date'] ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Estimasi Selesai:</span>
                                <span class="text-sm font-medium text-gray-900">{{ $studyInfo['estimated_end'] ?? '-' }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-heroicon-o-calendar class="w-12 h-12 mx-auto text-gray-400 mb-3" />
                            <p class="text-sm text-gray-500 mb-4">Belum ada kalender studi</p>
                            <a href="{{ route('study-calendar.create') }}" 
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                Buat Kalender Studi
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Academic Document Reminders -->
        @if($this->getAcademicReminders()->isNotEmpty())
            <div class="mb-6">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 rounded-xl shadow-sm border border-amber-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-amber-600" />
                                </div>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-bold text-gray-900">Pengingat Dokumen Akademik</h3>
                                <p class="text-sm text-gray-600">Dokumen yang perlu perhatian Anda</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">
                                {{ $this->getAcademicReminders()->count() }} Pengingat
                            </span>
                        </div>
                    </div>
                    
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach($this->getAcademicReminders() as $reminder)
                            <div class="bg-white rounded-lg border border-amber-200 p-5 shadow-sm hover:shadow-md transition-shadow duration-200">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-2xl">{{ $reminder['icon'] }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="text-base font-semibold text-gray-900">
                                                {{ $reminder['title'] }}
                                            </h4>
                                            @if($reminder['priority'] === 'high')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Prioritas Tinggi
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mb-4 leading-relaxed">
                                            {{ $reminder['message'] }}
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <a href="{{ $reminder['action_url'] }}" 
                                               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 shadow-sm">
                                                <x-heroicon-o-arrow-right class="w-4 h-4 mr-2" />
                                                {{ $reminder['action_text'] }}
                                            </a>
                                            <div class="flex items-center text-xs text-gray-500">
                                                <x-heroicon-o-clock class="w-4 h-4 mr-1" />
                                                Segera
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Recent Documents -->
        @if($recentDocuments && $recentDocuments->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <x-heroicon-o-document-text class="w-5 h-5 mr-2 text-gray-600" />
                        Dokumen Terbaru
                    </h3>
                    <a href="{{ route('documents.study-requirements') }}" class="text-sm text-blue-600 hover:text-blue-800">
                        Lihat Semua
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dokumen
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jenis
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentDocuments as $document)
                                @php
                                    // Determine document type (class)
                                    $isAcademic = $document instanceof \App\Models\AcademicDocument;
                                    $isApproval = $document instanceof \App\Models\ApprovalDocument;
                                    // Status mapping
                                    if ($isAcademic) {
                                        if ($document->workflow_state == 3) {
                                            $statusLabel = 'Terverifikasi';
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } elseif ($document->workflow_state == 2) {
                                            $statusLabel = 'Menunggu';
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        } elseif ($document->workflow_state == 4) {
                                            $statusLabel = 'Ditolak';
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } else {
                                            $statusLabel = 'Draft';
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                        }
                                    } elseif ($isApproval) {
                                        if ($document->workflow_state == 4) {
                                            $statusLabel = 'Terverifikasi';
                                            $statusClass = 'bg-green-100 text-green-800';
                                        } elseif ($document->workflow_state == 2 || $document->workflow_state == 3) {
                                            $statusLabel = 'Menunggu';
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                        } elseif ($document->workflow_state == 5) {
                                            $statusLabel = 'Ditolak';
                                            $statusClass = 'bg-red-100 text-red-800';
                                        } else {
                                            $statusLabel = 'Draft';
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                        }
                                    } else {
                                        $statusLabel = 'Draft';
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $document->file_name ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $document->documentType->display_name ?? 'Unknown' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $document->created_at ? $document->created_at->format('d M Y') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
@else
    <!-- Non-Lecturer Dashboard (Analytics) -->
    <div class="px-4 py-6 md:px-8 bg-gray-50 min-h-screen">
    <livewire:monitoring.analytics />
    </div>
@endif