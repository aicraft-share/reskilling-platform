<x-app-layout>
    @php
        $user = Auth::user();
        $announcementBasePath = $user->isStudent()
            ? '/student/announcements'
            : ($user->isTeacher() || $user->isInstructor()
                ? '/teacher/announcements'
                : '/company/announcements');
    @endphp
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            お知らせ
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-4 sm:py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-4 sm:p-6 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <h3 class="text-xs sm:text-sm font-bold text-slate-500 uppercase tracking-wider">受信済みメッセージ</h3>
                <span class="text-xs text-slate-400">{{ $announcements->total() }} 件</span>
            </div>

            @if($announcements->isEmpty())
                <div class="p-12 sm:p-20 text-center text-slate-400">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="h-8 w-8 text-slate-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium">現在お知らせはありません。</p>
                </div>
            @else
                <ul class="divide-y divide-slate-100">
                    @foreach($announcements as $recipient)
                        <li class="relative transition-all duration-200 hover:bg-slate-50 group {{ $recipient->is_read ? 'bg-white' : 'bg-blue-50/20' }}">
                            <a href="{{ url($announcementBasePath . '/' . $recipient->id) }}" class="block px-4 sm:px-6 py-4 sm:py-6">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <!-- Status Indicator -->
                                    <div class="mt-1.5 flex-shrink-0">
                                        @if(!$recipient->is_read)
                                            <span class="relative flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                                            </span>
                                        @else
                                            <div class="h-3 w-3 rounded-full border border-slate-200"></div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                            <p class="text-sm sm:text-base font-bold truncate {{ $recipient->is_read ? 'text-slate-600' : 'text-slate-900 group-hover:text-blue-600' }} transition">
                                                {{ $recipient->announcement->title }}
                                            </p>
                                            <p class="text-[10px] sm:text-xs font-mono {{ $recipient->is_read ? 'text-slate-400' : 'text-slate-500 font-bold' }} whitespace-nowrap">
                                                {{ $recipient->announcement->created_at->isoFormat('YYYY/MM/DD HH:mm') }}
                                            </p>
                                        </div>
                                        <p class="text-xs sm:text-sm {{ $recipient->is_read ? 'text-slate-400' : 'text-slate-600' }} line-clamp-1 sm:line-clamp-2">
                                            {{ $recipient->announcement->body }}
                                        </p>
                                        <div class="mt-3 flex items-center gap-2">
                                            <div class="flex items-center text-[10px] text-slate-400">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $recipient->announcement->creator->name ?? 'Admin' }}
                                            </div>
                                            @if(!$recipient->is_read)
                                                <span class="text-[10px] font-bold text-red-600 px-1 py-0.5 rounded bg-red-50">NEW</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="hidden sm:flex self-center ml-4">
                                        <svg class="h-5 w-5 text-slate-300 group-hover:text-blue-500 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>

                @if($announcements->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $announcements->links() }}
                    </div>
                @endif
            @endif
        </div>
        
        <div class="mt-6 px-4 py-3 bg-blue-50/50 border border-blue-100 rounded-xl flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-xs text-blue-700 leading-relaxed">
                重要なお知らせは、登録されているメールアドレスにも送信される場合があります。通知設定は各ユーザーのアカウント設定から変更可能です。
            </p>
        </div>
    </div>
</x-app-layout>
