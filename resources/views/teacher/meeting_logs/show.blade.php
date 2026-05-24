<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                MTG管理詳細: {{ $meetingLog->title }}
            </h2>
            <a href="{{ route('teacher.meeting-logs.index') }}"
                class="text-slate-600 hover:text-slate-900 text-sm underline">
                一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Section 1: Basic Info & Editing Form -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-3xl">
                    <h3 class="text-lg font-medium text-slate-900 mb-6">基本情報・録画URL管理</h3>

                    <form action="{{ route('teacher.meeting-logs.update', $meetingLog) }}" method="POST"
                        class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Readonly Info -->
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-4 rounded-md border border-slate-200 mb-6">
                            <div>
                                <span class="block text-sm font-medium text-slate-500 mb-1">実施日時</span>
                                <span
                                    class="text-sm text-slate-900 font-semibold">{{ $meetingLog->started_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-slate-500 mb-1">企業</span>
                                <span
                                    class="text-sm text-slate-900 font-semibold">{{ $meetingLog->company->name ?? '不明' }}</span>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-slate-500 mb-1">参加生徒</span>
                                <span
                                    class="text-sm text-slate-900 font-semibold">{{ $meetingLog->students->pluck('name')->join(', ') }}</span>
                            </div>
                            <div>
                                <span class="block text-sm font-medium text-slate-500 mb-1">ZoomミーティングID / URL</span>
                                @if($meetingLog->zoom_meeting_id)
                                    <span class="text-sm text-slate-900 font-mono">{{ $meetingLog->zoom_meeting_id }}</span>
                                    @if($meetingLog->zoom_join_url)
                                        <div class="mt-1">
                                            <a href="{{ $meetingLog->zoom_join_url }}" target="_blank"
                                                class="text-xs text-indigo-600 hover:text-indigo-900 underline">参加URLを開く</a>
                                        </div>
                                    @endif
                                @else
                                    <span class="text-sm text-slate-400">未発行</span>
                                @endif
                            </div>
                        </div>

                        <!-- Editable Fields -->
                        <div>
                            <x-input-label for="title" value="タイトル" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                :value="old('title', $meetingLog->title)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div>
                            <x-input-label for="youtube_url" value="YouTube URL" />
                            <div class="flex items-center gap-2 mt-1">
                                <x-text-input id="youtube_url" name="youtube_url" type="url" class="block w-full"
                                    :value="old('youtube_url', $meetingLog->youtube_url)"
                                    placeholder="https://youtube.com/..." />
                                @if($meetingLog->youtube_url)
                                    <a href="{{ $meetingLog->youtube_url }}" target="_blank"
                                        class="shrink-0 text-sm text-indigo-600 hover:text-indigo-900 underline whitespace-nowrap">
                                        視聴
                                    </a>
                                @endif
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('youtube_url')" />
                            @empty($meetingLog->youtube_url)
                                <p class="mt-1 text-xs text-amber-600">※ YouTube URLは現在未登録です。</p>
                            @endempty
                        </div>

                        <div>
                            <x-input-label for="memo" value="メモ" />
                            <textarea id="memo" name="memo" rows="4"
                                class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('memo', $meetingLog->memo) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('memo')" />
                        </div>

                        <div class="flex items-center justify-end gap-4 border-t border-slate-200 pt-4 mt-6">
                            <x-primary-button>
                                基本情報・URLを更新する
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Section 2: Transcript Upload -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-4xl">
                    <h3 class="text-lg font-medium text-slate-900 mb-2">文字起こし（字幕ファイル）登録</h3>
                    <p class="text-sm text-slate-500 mb-6">YouTube Studioからダウンロードした字幕ファイル（.srt / .vtt）をアップロードしてください。</p>

                    <!-- Status Display -->
                    <div class="mb-6">
                        @if($meetingLog->transcript_status === 'not_uploaded')
                            <div class="p-4 bg-slate-50 text-slate-500 rounded-md border border-slate-200 text-sm">
                                登録されていません。字幕ファイルをアップロードしてください。
                            </div>
                        @elseif($meetingLog->transcript_status === 'failed')
                            <div class="p-4 bg-red-50 text-red-600 rounded-md border border-red-200 text-sm">
                                字幕ファイルの読み込みに失敗しました。再度アップロードしてください。
                            </div>
                        @elseif($meetingLog->transcript_status === 'ready')
                            <textarea readonly
                                class="block w-full h-80 rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm bg-slate-50">{{ $meetingLog->transcript_text }}</textarea>
                            <div class="mt-2 text-right text-xs text-slate-500">
                                登録日時: {{ optional($meetingLog->transcript_uploaded_at)->format('Y-m-d H:i:s') }}
                            </div>
                        @endif
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('teacher.meeting-logs.transcript.upload', $meetingLog) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                            <div class="flex-grow">
                                <x-input-label for="transcript_file" value="字幕ファイル (.srt / .vtt)" />
                                <input type="file" name="transcript_file" id="transcript_file" accept=".srt,.vtt" class="block w-full mt-1 text-sm text-slate-500
                                            file:mr-4 file:py-2 file:px-4
                                            file:rounded-full file:border-0
                                            file:text-sm file:font-semibold
                                            file:bg-indigo-50 file:text-indigo-700
                                            hover:file:bg-indigo-100
                                        " />
                                <x-input-error :messages="$errors->get('transcript_file')" class="mt-2" />
                            </div>
                            <div class="shrink-0 mb-1">
                                <!-- Slight margin bottom to align with input baseline visually -->
                                <x-secondary-button type="submit">
                                    {{ $meetingLog->transcript_status === 'ready' ? '再アップロード' : 'アップロード' }}
                                </x-secondary-button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Section 3: AI Summary Generation (Coming Soon) -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-4xl">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <h3 class="text-lg font-medium text-slate-900 mb-1">AI要約</h3>
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
            </div>

        </div>
    </div>
</x-app-layout>