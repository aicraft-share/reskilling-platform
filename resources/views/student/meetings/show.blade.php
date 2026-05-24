<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            MTG詳細: {{ $meeting->title }}
        </h2>
    </x-slot>

    @php
        $youtubeUrl = null;
        if ($meeting instanceof \App\Models\MeetingLog) {
            $youtubeUrl = $meeting->youtube_url;
        } elseif ($meeting instanceof \App\Models\Meeting && $meeting->meetingLog) {
            $youtubeUrl = $meeting->meetingLog->youtube_url;
        }
    @endphp

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">

                    <h3 class="text-lg font-medium text-slate-900 border-b pb-2 mb-4">基本情報</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-slate-500">開催日時</dt>
                            <dd class="mt-1 text-2xl font-bold text-slate-900">
                                {{ $meeting->scheduled_at->format('Y年m月d日 H:i') }}
                            </dd>
                            <dd class="text-sm text-slate-500">所要時間: {{ $meeting->duration_minutes }}分</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-slate-500">主催</dt>
                            <dd class="mt-1 text-slate-900">{{ $meeting->company->name }} /
                                {{ $meeting->creator->name }}
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-slate-900 border-b pb-2 mb-4">参加方法</h3>

                        <div class="bg-indigo-50 border border-indigo-100 rounded-md p-6 text-center">

                            @if(\Carbon\Carbon::parse($meeting->scheduled_at)->addMinutes($meeting->duration_minutes)->isPast())
                                <!-- Finished State -->
                                <p class="text-slate-500 font-bold {{ $youtubeUrl ? 'mb-4' : '' }}">このMTGは終了しました</p>

                                @if($youtubeUrl)
                                    <a href="{{ $youtubeUrl }}" target="_blank"
                                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-md text-white bg-red-600 hover:bg-red-700 shadow-sm transition ease-in-out duration-150">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path>
                                        </svg>
                                        録画アーカイブを視聴する
                                    </a>
                                @endif

                            @else
                                <!-- Active/Scheduled State -->
                                <div class="mb-6 space-y-2">
                                    <p class="text-sm text-slate-600">・開始5分前から入室できます</p>
                                    <p class="text-sm text-slate-600">・Zoomアプリが自動で起動します</p>
                                </div>

                                @if($meeting->zoom_join_url)
                                    <a href="{{ $meeting->zoom_join_url }}" target="_blank"
                                        class="inline-flex items-center justify-center px-8 py-4 border border-transparent text-lg font-bold rounded-md text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition ease-in-out duration-150">
                                        Zoomで参加する
                                    </a>
                                @else
                                    <p class="text-red-500">参加URLが設定されていません。講師に確認してください。</p>
                                @endif
                            @endif

                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <h3 class="text-lg font-medium text-slate-900 mb-4">今回の要約</h3>

                        <div class="bg-gray-50 border border-gray-200 rounded-md p-6">
                            @if($meeting->transcript_summary)
                                <div class="prose prose-sm max-w-none text-slate-700 whitespace-pre-wrap">
                                    {{ $meeting->transcript_summary }}</div>
                            @else
                                {{-- Use summary_status if available, or fallback to checking instance type/status if not
                                hidden (but we hid status) --}}
                                @php
                                    $status = $meeting->summary_status ?? 'not_generated';
                                @endphp

                                @if($status === 'ready')
                                    <div class="prose prose-sm max-w-none text-slate-700 whitespace-pre-wrap">
                                        {{ $meeting->transcript_summary }}</div>
                                @elseif($status === 'generating')
                                    <p class="text-slate-500">要約を準備中です</p>
                                @elseif($status === 'failed')
                                    <p class="text-red-500">要約の生成に失敗しています。講師に連絡してください</p>
                                @else
                                    <p class="text-slate-500">要約はまだ準備中です</p>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <a href="{{ route('student.meetings.index') }}"
                            class="text-slate-600 hover:text-slate-900 font-medium">← 一覧に戻る</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>