<x-ui.page-container title="Akun Saya">
    <x-ui.alert-message />

    {{-- Change Password Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-[var(--color-border)] flex items-center">
            <x-heroicon-o-key class="w-5 h-5 mr-2 text-blue-600" />
            <h3 class="text-lg font-semibold text-gray-900">Change Password</h3>
        </div>
        <form wire:submit.prevent="changePassword" class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" wire:model.defer="current_password" placeholder="Enter current password" class="mt-1 py-3 px-3 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                    @error('current_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" wire:model.defer="new_password" placeholder="Enter new password" class="mt-1 py-3 px-3 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                    @error('new_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" wire:model.defer="new_password_confirmation" placeholder="Confirm new password" class="mt-1 py-3 px-3 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                </div>
            </div>
            <div class="relative px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-6 rounded-b-lg">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm">
                    Change Password
                </button>
            </div>
        </form>
    </div>

    {{-- Change Email Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-[var(--color-border)] flex items-center">
            <x-heroicon-o-envelope class="w-5 h-5 mr-2 text-blue-600" />
            <h3 class="text-lg font-semibold text-gray-900">Change Email</h3>
        </div>
        <form wire:submit.prevent="changeEmail" class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Email</label>
                    <input type="email" wire:model.defer="new_email" placeholder="Enter new email" class="mt-1 py-3 px-3 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                    @error('new_email') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" wire:model.defer="email_password" placeholder="Enter current password" class="mt-1 py-3 px-3 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                    @error('email_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="relative px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-6 rounded-b-lg">
                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm">
                    Change Email
                </button>
            </div>
        </form>
    </div>

    {{-- Delete Account Section --}}
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-[var(--color-border)] flex items-center">
            <x-heroicon-o-trash class="w-5 h-5 mr-2 text-red-600" />
            <h3 class="text-lg font-semibold text-red-600">Delete Account</h3>
        </div>
        <div class="px-6 py-6">
            <p class="mb-4 text-red-500">Warning: This action is irreversible. All your data will be permanently deleted.</p>
            @if (!$delete_confirm)
                <button wire:click="confirmDeleteAccount" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm">Delete My Account</button>
            @else
                <form wire:submit.prevent="deleteAccount">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                            <input type="password" wire:model.defer="delete_password" placeholder="Enter your password" class="mt-1 py-3 px-3 focus:ring-red-500 focus:border-red-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" required />
                            @error('delete_password') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="relative px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse mt-6 rounded-b-lg">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-700 text-base font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 sm:ml-3 sm:w-auto sm:text-sm">Yes, Delete My Account</button>
                        <button type="button" wire:click="cancelDeleteAccount" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 transition-all duration-200 sm:mr-3 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-page-container>