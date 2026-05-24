<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('チャット') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4 sm:mb-6">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest">メッセージ一覧</h3>
        </div>

        @if($threads->isEmpty())
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <p class="text-slate-500 font-medium">現在チャットスレッドはありません。</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4">
                @foreach($threads as $thread)
                    <a href="{{ route('chats.show', $thread) }}" class="block bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 shadow-sm hover:shadow-md hover:border-blue-300 transition group relative overflow-hidden">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4 flex-1">
                                <!-- Avatar Placeholder -->
                                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center border border-blue-200 flex-shrink-0">
                                    <span class="text-blue-600 font-bold text-lg">
                                        @if($user->isStudent())
                                            {{ mb_substr($thread->instructor->name ?? '?', 0, 1) }}
                                        @else
                                            {{ mb_substr($thread->student->name ?? '?', 0, 1) }}
                                        @endif
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <p class="text-sm sm:text-base font-bold text-slate-900 truncate group-hover:text-blue-600 transition">
                                            @if($user->isStudent())
                                                担当講師: {{ $thread->instructor->name ?? '未設定' }}
                                            @else
                                                生徒: {{ $thread->student->name ?? '不明' }}
                                            @endif
                                        </p>
                                        @if($user->isAdmin() && $thread->company)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-500">{{ $thread->company->name }}</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <p class="text-xs text-slate-400 font-medium flex items-center">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            最終更新: {{ $thread->last_message_at ? $thread->last_message_at->isoFormat('MM/DD HH:mm') : '未送信' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between w-full sm:w-auto mt-2 sm:mt-0">
                                <div class="sm:hidden">
                                    <!-- Mobile only detail hint -->
                                </div>
                                <div class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-blue-600 bg-blue-50 group-hover:bg-blue-600 group-hover:text-white transition">
                                    {{ $user->isStudent() || $user->isTeacher() || $user->isInstructor() ? 'チャットを開く' : '閲覧する' }}
                                    <svg class="w-4 h-4 ml-2 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>