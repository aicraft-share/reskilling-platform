<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            提出物のレビュー
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" x-data>
        <!-- Submission Details -->
        <div class="space-y-6">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">提出内容</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-3 gap-4 border-b border-slate-100 pb-4">
                        <div class="text-sm text-slate-500">生徒名</div>
                        <div class="col-span-2 text-sm font-medium text-slate-900">{{ $submission->user->name }}</div>

                        <div class="text-sm text-slate-500">所属企業</div>
                        <div class="col-span-2 text-sm font-medium text-slate-900">
                            {{ $submission->user->company->name ?? 'なし' }}
                        </div>

                        <div class="text-sm text-slate-500">講義（課題）</div>
                        <div class="col-span-2 text-sm font-medium text-slate-900">{{ $submission->lecturePage->title }}
                        </div>

                        <div class="text-sm text-slate-500">提出日時</div>
                        <div class="col-span-2 text-sm text-slate-900">
                            {{ $submission->created_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-slate-700 mb-3">添付ファイル</h4>
                        @forelse($submission->items as $item)
                            <div class="mb-3 p-3 border border-slate-200 rounded-lg">
                                <p class="text-xs text-slate-500 mb-1">ファイル名: {{ $item->original_name }}</p>
                                @if($item->file_type == 'image')
                                    <img src="{{ asset('storage/' . $item->file_path) }}" alt="Submission Image"
                                        class="max-w-full h-auto rounded border border-slate-100 shadow-sm mt-2 mb-2">
                                @endif

                                <div class="mt-2 text-right space-x-2">
                                    <button type="button" @click="$dispatch('open-preview', { url: '{{ route('teacher.submissions.preview', $item) }}', name: '{{ $item->original_name }}' })"
                                        class="inline-flex items-center px-4 py-2 bg-slate-50 border border-slate-200 rounded-md font-semibold text-xs text-slate-700 hover:bg-slate-100 transition">
                                        <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        プレビュー
                                    </button>
                                    <a href="{{ route('teacher.submissions.download', $item) }}"
                                        class="inline-flex items-center px-4 py-2 bg-blue-50 border border-blue-200 rounded-md font-semibold text-xs text-blue-700 hover:bg-blue-100 transition">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                        </svg>
                                        ダウンロード
                                    </a>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">添付ファイルはありません。</p>
                        @endforelse
                    </div>
                </div>
            </div>
        <!-- Review Form -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 h-fit">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-900">レビュー・判定</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('teacher.submissions.review', $submission) }}">
                    @csrf

                    <!-- Verdict -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">判定コメント <span
                                class="text-rose-600">*</span></label>
                        <textarea name="teacher_comment" rows="4" required
                            class="block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                            placeholder="ここへ判定の理由やアドバイスを入力してください...">{{ old('teacher_comment', $submission->teacher_comment) }}</textarea>
                        <x-input-error :messages="$errors->get('teacher_comment')" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">確認結果 <span
                                class="text-rose-600">*</span></label>
                        <div class="space-y-3">
                            <label
                                class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition {{ $submission->status == 'passed' ? 'bg-blue-50 border-blue-200' : '' }}">
                                <input name="status" type="radio" value="passed"
                                    class="text-blue-600 focus:ring-blue-500 h-4 w-4" {{ $submission->status == 'passed' ? 'checked' : '' }} required>
                                <span class="ml-3 block text-sm font-semibold text-slate-900">確認完了（合格）</span>
                            </label>

                            <label
                                class="flex items-center p-3 border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition {{ $submission->status == 'revision_required' ? 'bg-amber-50 border-amber-200' : '' }}">
                                <input name="status" type="radio" value="revision_required"
                                    class="text-amber-600 focus:ring-amber-500 h-4 w-4" {{ $submission->status == 'revision_required' ? 'checked' : '' }}>
                                <span class="ml-3 block text-sm font-semibold text-slate-900">再提出（Revision
                                    Required）</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('teacher.submissions.index') }}"
                            class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                        <x-primary-button>
                            {{ __('ステータスを更新') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>