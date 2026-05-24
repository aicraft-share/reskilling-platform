<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.announcements.index') }}"
                class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                配信詳細
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <!-- Main Content (Left) -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                    <div
                        class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">{{ $announcement->title }}</h3>
                            <div class="mt-2 text-sm text-slate-500 flex items-center gap-4">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $announcement->created_at->format('Y/m/d H:i') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    作成者: {{ $announcement->creator->name ?? '不明' }}
                                </span>
                            </div>
                        </div>
                        <div>
                            @if($announcement->target_scope_type === 'all')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">配信先:
                                    全体</span>
                            @elseif($announcement->target_scope_type === 'instructors')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">配信先:
                                    講師のみ</span>
                            @elseif($announcement->target_scope_type === 'companies')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-800">配信先:
                                    企業のみ</span>
                            @elseif($announcement->target_scope_type === 'students')
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">配信先:
                                    生徒のみ</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="prose max-w-none text-slate-700 whitespace-pre-wrap leading-relaxed">
                            {{ $announcement->body }}</div>
                    </div>
                </div>
            </div>

            <!-- Stats Sidebar (Right) -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Delivery Metrics Card -->
                <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <h3 class="text-md font-medium text-slate-800">配信ステータス</h3>
                    </div>
                    <div class="p-5">
                        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-1">
                            <div
                                class="px-4 py-5 bg-slate-50 shadow rounded-lg overflow-hidden sm:p-6 border border-slate-100">
                                <dt class="text-sm font-medium text-slate-500 truncate">総配信件数</dt>
                                <dd class="mt-1 text-3xl font-semibold text-slate-900">{{ number_format($total) }} <span
                                        class="text-sm font-normal text-slate-500">件</span></dd>
                            </div>

                            <div
                                class="px-4 py-5 bg-emerald-50 shadow rounded-lg overflow-hidden sm:p-6 border border-emerald-100">
                                <dt class="text-sm font-medium text-emerald-600 truncate flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    既読
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-emerald-700">{{ number_format($readCount) }}
                                    <span class="text-sm font-normal text-emerald-600">件</span></dd>
                            </div>

                            <div
                                class="px-4 py-5 bg-slate-50 shadow rounded-lg overflow-hidden sm:p-6 border border-slate-100">
                                <dt class="text-sm font-medium text-slate-500 truncate flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    未読
                                </dt>
                                <dd class="mt-1 text-3xl font-semibold text-slate-600">{{ number_format($unreadCount) }}
                                    <span class="text-sm font-normal text-slate-500">件</span></dd>
                            </div>
                        </dl>

                        @if($total > 0)
                            <div class="mt-6">
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-medium text-slate-700">既読率</span>
                                    <span
                                        class="font-bold text-emerald-600">{{ number_format(($readCount / $total) * 100, 1) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2.5">
                                    <div class="bg-emerald-500 h-2.5 rounded-full"
                                        style="width: {{ ($readCount / $total) * 100 }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>