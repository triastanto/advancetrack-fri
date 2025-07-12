<div class="border-b border-gray-200">
    <nav class="flex space-x-8 px-6" aria-label="Tabs">
        <button 
            wire:click="$set('activeTab', 'timeline-approval')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'timeline-approval' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
        >
            <div class="flex items-center">
                <x-heroicon-o-clock class="w-5 h-5 mr-2" />
                Timeline & Persetujuan
            </div>
        </button>
        <button 
            wire:click="$set('activeTab', 'study-info')"
            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ $activeTab === 'study-info' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
        >
            <div class="flex items-center">
                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2" />
                Informasi Studi
            </div>
        </button>
    </nav>
</div> 