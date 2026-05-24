<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                MTG詳細: {{ $meeting->title }}
            </h2>
            <a href="{{ route('teacher.meetings.index') }}"
                class="text-slate-600 hover:text-slate-900 text-sm underline">← 一覧に戻る</a>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if (session('success'))
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                {{ session('error') }}
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- Section 1: Basic Info & Participants --}}
        {{-- ============================================================ --}}
        <div class="p-6 bg-white shadow sm:rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-base font-semibold text-slate-900 border-b pb-2 mb-4">基本情報</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500">開催日時</dt>
                            <dd class="font-semibold text-slate-900">
                                {{ $meeting->scheduled_at->format('Y年m月d日 H:i') }}
                                ({{ $meeting->duration_minutes }}分)
                            </dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">対象企業</dt>
                            <dd class="text-slate-900">{{ $meeting->company->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500">作成日</dt>
                            <dd class="text-slate-600">{{ $meeting->created_at->format('Y/m/d H:i') }}</dd>
                        </div>
                    </dl>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-slate-900 border-b pb-2 mb-4">
                        参加予定生徒 ({{ $meeting->participants->count() }}名)
                    </h3>
                    @if($meeting->participants->isEmpty())
                        <p class="text-sm text-slate-500">参加者は登録されていません。</p>
                    @else
                        <ul class="divide-y divide-slate-100 bg-slate-50 rounded-md border border-slate-200">
                            @foreach($meeting->participants as $participant)
                                <li class="p-3 flex items-center">
                                    <span class="text-sm font-medium text-slate-700">{{ $participant->student->name }}</span>
                                    <span class="ml-auto text-xs text-slate-400">{{ $participant->student->email }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Section 2: Zoom Connection Info --}}
        {{-- ============================================================ --}}
        <div class="p-6 bg-white shadow sm:rounded-lg">
            <h3 class="text-base font-semibold text-slate-900 border-b pb-2 mb-4">Zoom接続情報</h3>
            <div class="space-y-4">
                <div class="bg-blue-50 border border-blue-100 rounded-md p-4">
                    <p class="text-sm font-bold text-blue-800 mb-2">【講師用】ホストURL (開始用)</p>
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ $meeting->zoom_start_url }}"
                            class="w-full text-sm border-gray-300 rounded bg-white text-slate-600" readonly
                            onclick="this.select()">
                        <a href="{{ $meeting->zoom_start_url }}" target="_blank"
                            class="shrink-0 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded">
                            開始
                        </a>
                    </div>
                    <p class="text-xs text-blue-600 mt-2">※ このURLは講師専用です。生徒には共有しないでください。</p>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-md p-4">
                    <p class="text-sm font-bold text-slate-700 mb-2">【生徒用】参加URL</p>
                    <input type="text" value="{{ $meeting->zoom_join_url }}"
                        class="w-full text-sm border-gray-300 rounded bg-white text-slate-600" readonly
                        onclick="this.select()">
                    <p class="text-xs text-slate-500 mt-2">※ このURLは生徒のマイページに自動で表示されます。</p>
                </div>
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Section 3: Recording URL & Memo --}}
        {{-- ============================================================ --}}
        <div class="p-6 bg-white shadow sm:rounded-lg">
            <h3 class="text-base font-semibold text-slate-900 border-b pb-2 mb-4">録画URL・メモ 管理</h3>

            <form action="{{ route('teacher.meetings.update', $meeting) }}" method="POST" class="space-y-4 max-w-2xl">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="youtube_url" value="YouTube URL（録画）" />
                    <div class="flex items-center gap-2 mt-1">
                        <x-text-input id="youtube_url" name="youtube_url" type="url" class="block w-full"
                            :value="old('youtube_url', $meetingLog?->youtube_url)"
                            placeholder="https://youtube.com/..." />
                        @if($meetingLog?->youtube_url)
                            <a href="{{ $meetingLog->youtube_url }}" target="_blank"
                                class="shrink-0 text-sm text-indigo-600 hover:text-indigo-900 underline whitespace-nowrap">視聴</a>
                        @endif
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('youtube_url')" />
                    @if(!$meetingLog?->youtube_url)
                        <p class="mt-1 text-xs text-amber-600">※ YouTube URLは現在未登録です。</p>
                    @endif
                </div>

                <div>
                    <x-input-label for="memo" value="メモ（任意）" />
                    <textarea id="memo" name="memo" rows="3"
                        class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">{{ old('memo', $meetingLog?->memo) }}</textarea>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>録画URL・メモを保存</x-primary-button>
                </div>
            </form>
        </div>

        {{-- ============================================================ --}}
        {{-- Section 4: Transcript Upload --}}
        {{-- ============================================================ --}}
        <div class="p-6 bg-white shadow sm:rounded-lg">
            <h3 class="text-base font-semibold text-slate-900 border-b pb-2 mb-4">文字起こし（字幕ファイル）登録</h3>
            <p class="text-sm text-slate-500 mb-4">YouTube Studioからダウンロードした字幕ファイル（.srt / .vtt）をアップロードしてください。</p>

            @if($meetingLog)
                @if($meetingLog->transcript_status === 'not_uploaded')
                    <div class="p-4 bg-slate-50 text-slate-500 rounded-md border border-slate-200 text-sm mb-4">
                        登録されていません。字幕ファイルをアップロードしてください。
                    </div>
                @elseif($meetingLog->transcript_status === 'failed')
                    <div class="p-4 bg-red-50 text-red-600 rounded-md border border-red-200 text-sm mb-4">
                        字幕ファイルの読み込みに失敗しました。再度アップロードしてください。
                    </div>
                @elseif($meetingLog->transcript_status === 'ready')
                    <textarea readonly
                        class="block w-full h-64 rounded-md border-slate-300 shadow-sm sm:text-sm bg-slate-50 mb-2">{{ $meetingLog->transcript_text }}</textarea>
                    <div class="text-right text-xs text-slate-500 mb-4">
                        登録日時: {{ optional($meetingLog->transcript_uploaded_at)->format('Y-m-d H:i:s') }}
                    </div>
                @endif
            @else
                <div class="p-4 bg-slate-50 text-slate-500 rounded-md border border-slate-200 text-sm mb-4">
                    録画URLを保存後、文字起こしのアップロードが可能になります。
                </div>
            @endif

            <form action="{{ route('teacher.meetings.transcript.upload', $meeting) }}" method="POST"
                enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                    <div class="flex-grow">
                        <x-input-label for="transcript_file" value="字幕ファイル (.srt / .vtt)" />
                        <input type="file" name="transcript_file" id="transcript_file" accept=".srt,.vtt" class="block w-full mt-1 text-sm text-slate-500
                                   file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                                   file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700
                                   hover:file:bg-indigo-100" />
                        <x-input-error :messages="$errors->get('transcript_file')" class="mt-2" />
                    </div>
                    <div class="shrink-0">
                        <x-secondary-button type="submit">
                            {{ $meetingLog?->transcript_status === 'ready' ? '再アップロード' : 'アップロード' }}
                        </x-secondary-button>
                    </div>
                </div>
            </form>
        </div>

        {{-- ============================================================ --}}
        {{-- Section 5: AI Summary (Coming Soon) --}}
        {{-- ============================================================ --}}
        <div class="p-6 bg-white shadow sm:rounded-lg">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">AI要約</h3>
                    <p class="text-sm text-slate-500">文字起こしデータを元にAIで要約を生成・編集できます。</p>
                </div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 border border-amber-200">
                    🔧 準備中
                </span>
            </div>
            <div class="p-4 bg-amber-50 text-amber-700 rounded-md border border-amber-200 text-sm">
                この機能は現在準備中です。近日公開予定ですので、しばらくお待ちください。
            </div>
        </div>

        {{-- ============================================================ --}}
        {{-- Footer: Delete --}}
        {{-- ============================================================ --}}
        <div class="pt-4 pb-8 flex justify-end">
            <form action="{{ route('teacher.meetings.destroy', $meeting) }}" method="POST"
                onsubmit="return confirm('本当に削除しますか？\nZoom上のミーティングも削除されます。');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="text-red-600 hover:text-red-900 font-bold text-sm bg-red-50 hover:bg-red-100 px-4 py-2 rounded border border-red-200 transition">
                    このMTGを削除する
                </button>
            </form>
        </div>

    </div>
</x-app-layout>