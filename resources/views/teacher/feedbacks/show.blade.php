<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.feedbacks.index') }}" class="text-gray-500 hover:text-gray-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ __('フィードバック詳細') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data>
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">提出・評価情報</h3>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">企業名</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $feedback->user->company->name ?? '不明な企業' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">生徒名</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $feedback->user->name }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500">課題名</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $feedback->lecturePage->title ?? '不明な課題' }}</dd>
                        </div>

                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">提出日時</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $feedback->created_at->format('Y-m-d H:i:s') }}
                            </dd>
                        </div>

                        <div class="sm:col-span-1">
                            <dt class="text-sm font-medium text-gray-500">フィードバック日時</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $feedback->reviewed_at ? $feedback->reviewed_at->format('Y-m-d H:i:s') : '-' }}</dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 mb-2">評価結果</dt>
                            <dd>
                                @if($feedback->status === \App\Models\Submission::STATUS_PASSED)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="-ml-1 mr-1.5 h-4 w-4 text-green-600" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        合格
                                    </span>
                                @elseif($feedback->status === \App\Models\Submission::STATUS_REVISION_REQUIRED)
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <svg class="-ml-1 mr-1.5 h-4 w-4 text-red-600" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        要修正
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $feedback->status }}
                                    </span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="p-6 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">フィードバック本文</h3>
                    <div
                        class="bg-white p-6 rounded-md border border-gray-200 text-gray-800 whitespace-pre-wrap leading-relaxed">
                        {{ $feedback->teacher_comment ?? '（コメントなし）' }}</div>
                </div>

                @if($feedback->items && $feedback->items->count() > 0)
                    <div class="p-6 border-t border-gray-200">
                        <h3 class="text-sm font-bold text-gray-900 mb-3">提出物ファイル一覧</h3>
                        <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                            @foreach($feedback->items as $item)
                                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                    <div class="w-0 flex-1 flex items-center text-blue-600">
                                        <svg class="flex-shrink-0 h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <button type="button" @click="$dispatch('open-preview', { url: '{{ route('teacher.submissions.preview', $item) }}', name: '{{ $item->original_name }}' })" class="truncate flex-1 text-left hover:underline focus:outline-none">
                                            {{ $item->original_name }}
                                        </button>
                                    </div>
                                    <div class="ml-4 flex-shrink-0 flex space-x-4 border-l border-gray-200 pl-4">
                                        <button type="button" @click="$dispatch('open-preview', { url: '{{ route('teacher.submissions.preview', $item) }}', name: '{{ $item->original_name }}' })" class="font-medium text-slate-600 hover:text-slate-900 transition focus:outline-none">
                                            プレビュー
                                        </button>
                                        <a href="{{ route('teacher.submissions.download', $item) }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                                            ダウンロード
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <div class="flex justify-start mt-6">
                <a href="{{ route('teacher.feedbacks.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    一覧へ戻る
                </a>
            </div>

        </div>
    </div>
</x-app-layout>