<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            ダッシュボード
        </h2>
    </x-slot>

    <div class="space-y-6 pb-12">

        {{-- Greeting --}}
        <div class="flex items-center gap-3">
            @if(auth()->user()->avatar_path)
                <img src="{{ asset('storage/' . auth()->user()->avatar_path) }}" class="w-12 h-12 rounded-full object-cover border-2 border-blue-100" alt="">
            @else
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <span class="text-blue-600 font-bold text-lg">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                </div>
            @endif
            <div>
                <p class="text-slate-500 text-sm">おかえりなさい</p>
                <h1 class="text-2xl font-bold text-slate-800">{{ auth()->user()->name }} さん</h1>
            </div>
        </div>

        {{-- ① 次回までにやること --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-blue-50 to-white">
                <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h2 class="text-base font-bold text-slate-800">次回までにやること</h2>
            </div>

            <div class="p-6">
                @if($nextAction)
                    <div class="space-y-5">
                        {{-- 必須視聴講義 --}}
                        <div>
                            <p class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                必須視聴講義
                            </p>
                            <ul class="space-y-2">
                                @foreach($nextAction->lecturePages as $lecture)
                                    <li>
                                        <a href="{{ route('student.lectures.show', $lecture) }}"
                                            class="flex items-start gap-3 p-3 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50 transition-all group">
                                            <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5 group-hover:bg-blue-200 transition-colors">
                                                <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                @if($lecture->course)
                                                    <p class="text-[10px] text-slate-400 font-medium mb-0.5">{{ $lecture->course->title }}</p>
                                                @endif
                                                <p class="text-sm font-semibold text-slate-800 group-hover:text-blue-700 transition-colors line-clamp-2">{{ $lecture->title }}</p>
                                            </div>
                                            <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-400 flex-shrink-0 mt-1 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- 課題・指示内容 --}}
                        <div>
                            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                課題・指示内容
                            </p>
                            <div class="p-4 bg-amber-50 border border-amber-100 rounded-xl">
                                <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $nextAction->instruction_text }}</p>
                            </div>
                        </div>

                        {{-- 担当講師 & ボタン --}}
                        <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                            <div class="flex items-center gap-2 text-sm text-slate-500">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>担当講師：<strong class="text-slate-700">{{ $nextAction->teacher->name }}</strong></span>
                                <span class="text-slate-300">·</span>
                                <span class="text-slate-400 text-xs">{{ $nextAction->updated_at->format('m/d 更新') }}</span>
                            </div>
                            <a href="{{ route('student.courses.index') }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-colors shadow-sm shadow-blue-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                講義を見る
                            </a>
                        </div>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-slate-500 text-sm">現在、次回までに指定されている内容はありません。</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- ② & ③ 次回MTG + 最新チャット (2 columns on PC) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- ② 次回MTG --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-indigo-50 to-white">
                    <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-800">次回MTG</h2>
                </div>

                <div class="p-6">
                    @if($nextMeeting)
                        <div class="space-y-4">
                            <div>
                                <p class="text-lg font-bold text-slate-800 mb-1">{{ $nextMeeting->title }}</p>
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-semibold text-indigo-700">{{ $nextMeeting->scheduled_at->format('Y年m月d日（D） H:i') }}</span>
                                </div>
                                @if($nextMeeting->creator)
                                    <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        担当：{{ $nextMeeting->creator->name }}
                                    </p>
                                @endif
                            </div>
                            <div class="flex gap-3 pt-2 border-t border-slate-100">
                                @if($nextMeeting->zoom_join_url)
                                    <a href="{{ $nextMeeting->zoom_join_url }}" target="_blank"
                                        class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm shadow-indigo-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                        </svg>
                                        Zoomに参加
                                    </a>
                                @endif
                                <a href="{{ route('student.meetings.show', $nextMeeting) }}"
                                    class="flex-1 flex items-center justify-center gap-2 px-4 py-2.5 bg-slate-100 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-200 transition-colors">
                                    詳細を見る
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">現在、予定されているMTGはありません。</p>
                            <a href="{{ route('student.meetings.index') }}" class="mt-3 text-xs text-blue-600 hover:underline">MTG一覧を見る</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ③ 最新チャット --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100 bg-gradient-to-r from-emerald-50 to-white">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-800">講師チャット</h2>
                    @if($chatThread)
                        <div class="ml-auto">
                            @php
                                $unread = $chatThread->messages->where('sender_id', '!=', auth()->id())->whereNull('read_at')->count();
                            @endphp
                            @if($unread > 0)
                                <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full">{{ $unread }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    @if($chatThread && $latestMessages->count() > 0)
                        <div class="space-y-3 mb-5">
                            @foreach($latestMessages->reverse() as $msg)
                                <div class="flex items-start gap-3 {{ $msg->sender_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                    <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center flex-shrink-0 text-xs font-bold text-slate-600">
                                        {{ mb_substr($msg->sender?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="flex flex-col {{ $msg->sender_id === auth()->id() ? 'items-end' : 'items-start' }} max-w-[80%]">
                                        <div class="px-3 py-2 rounded-xl text-sm leading-relaxed {{ $msg->sender_id === auth()->id() ? 'bg-blue-600 text-white rounded-tr-sm' : 'bg-slate-100 text-slate-800 rounded-tl-sm' }}">
                                            <p class="line-clamp-3">{{ $msg->message }}</p>
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-1 px-1">{{ $msg->created_at->format('m/d H:i') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <a href="{{ route('chats.index') }}"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm shadow-emerald-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            チャットを開く
                        </a>
                    @else
                        <div class="flex flex-col items-center justify-center py-8 text-center">
                            <div class="w-14 h-14 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 text-sm">まだメッセージはありません。</p>
                            <a href="{{ route('chats.index') }}" class="mt-3 text-xs text-emerald-600 hover:underline">チャットを開く</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>