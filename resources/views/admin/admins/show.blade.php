<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.admins.index') }}"
                    class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    管理者詳細
                </h2>
            </div>

            <span
                class="inline-flex items-center rounded-md bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700 ring-1 ring-inset ring-indigo-600/20">Admin</span>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center">
                    <div
                        class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl mr-4 shadow-inner">
                        {{ Str::substr($admin->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-slate-900">{{ $admin->name }}</h3>
                        <p class="text-sm text-slate-500">{{ $admin->email }}</p>
                    </div>
                </div>
                <div class="border-t border-slate-200">
                    <dl class="divide-y divide-slate-100">
                        <div
                            class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-slate-50 transition-colors">
                            <dt class="text-sm font-medium text-slate-500">アカウントID</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">{{ $admin->id }}</dd>
                        </div>
                        <div
                            class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-slate-50 transition-colors">
                            <dt class="text-sm font-medium text-slate-500">氏名</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">{{ $admin->name }}</dd>
                        </div>
                        <div
                            class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-slate-50 transition-colors">
                            <dt class="text-sm font-medium text-slate-500">メールアドレス</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">{{ $admin->email }}</dd>
                        </div>
                        <div
                            class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-slate-50 transition-colors">
                            <dt class="text-sm font-medium text-slate-500">権限ロール</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">管理者 (Admin)</dd>
                        </div>
                        <div
                            class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-slate-50 transition-colors">
                            <dt class="text-sm font-medium text-slate-500">登録日時</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">
                                {{ $admin->created_at->format('Y/m/d H:i:s') }}
                            </dd>
                        </div>
                        <div
                            class="px-6 py-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6 hover:bg-slate-50 transition-colors">
                            <dt class="text-sm font-medium text-slate-500">最終更新</dt>
                            <dd class="mt-1 text-sm text-slate-900 sm:col-span-2 sm:mt-0">
                                {{ $admin->updated_at->format('Y/m/d H:i:s') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>