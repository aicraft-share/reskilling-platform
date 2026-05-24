<x-app-layout>
    @php
        $announcementBasePath = Auth::user()->isStudent()
            ? '/student/announcements'
            : (Auth::user()->isTeacher() || Auth::user()->isInstructor()
                ? '/teacher/announcements'
                : '/company/announcements');
    @endphp
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ url($announcementBasePath) }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                お知らせ詳細
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6">
        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-xl font-bold text-slate-900">{{ $recipientRecord->announcement->title }}</h3>
                <div class="mt-3 flex items-center gap-4 text-sm text-slate-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        配信日時: {{ $recipientRecord->announcement->created_at->format('Y年m月d日 H:i') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        送信元: 運営スタッフ
                    </span>
                </div>
            </div>

            <div class="p-8">
                <div class="prose max-w-none text-slate-800 leading-relaxed whitespace-pre-wrap">
                    {{ $recipientRecord->announcement->body }}</div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 text-right">
                <a href="{{ url($announcementBasePath) }}"
                    class="inline-flex items-center justify-center px-4 py-2 border border-slate-300 shadow-sm text-sm font-medium rounded-md text-slate-700 bg-white hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    一覧へ戻る
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
