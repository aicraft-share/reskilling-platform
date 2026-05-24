<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('フィードバック一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- 検索・フィルタセクション -->
            <div class="bg-white p-6 shadow sm:rounded-lg">
                <form method="GET" action="{{ route('teacher.feedbacks.index') }}" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <x-input-label for="keyword" :value="__('キーワード検索')" />
                        <x-text-input id="keyword" name="keyword" type="text" class="mt-1 block w-full"
                            placeholder="生徒名、企業名、課題名..."
                            value="{{ request('keyword') }}" />
                    </div>

                    <div class="w-full md:w-48">
                        <x-input-label for="student_id" :value="__('生徒絞り込み')" />
                        <select id="student_id" name="student_id" class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="">すべての生徒</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full md:w-48">
                        <x-input-label for="status" :value="__('評価結果')" />
                        <select id="status" name="status" class="mt-1 block w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm">
                            <option value="all" {{ request('status') === 'all' || !request()->has('status') ? 'selected' : '' }}>すべて</option>
                            <option value="{{ \App\Models\Submission::STATUS_PASSED }}" {{ request('status') === \App\Models\Submission::STATUS_PASSED ? 'selected' : '' }}>合格 (Passed)</option>
                            <option value="{{ \App\Models\Submission::STATUS_REVISION_REQUIRED }}" {{ request('status') === \App\Models\Submission::STATUS_REVISION_REQUIRED ? 'selected' : '' }}>要修正 (Revision Required)</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <x-primary-button type="submit" class="h-[42px]">
                            {{ __('検索') }}
                        </x-primary-button>
                        
                        @if(request()->has('keyword') || request()->has('status') || request()->has('student_id'))
                            <a href="{{ route('teacher.feedbacks.index') }}" class="ml-4 text-sm text-gray-600 hover:text-gray-900 underline mb-3">
                                クリア
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 一覧テーブル -->
            <div class="bg-white overflow-hidden shadow sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    フィードバック日時
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    企業・生徒名
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    課題名
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    評価結果
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    コメント冒頭
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    操作
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($submissions as $feedback)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $feedback->reviewed_at ? $feedback->reviewed_at->format('Y-m-d H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="font-medium">{{ $feedback->user->name ?? '不明な生徒' }}</div>
                                        <div class="text-xs text-gray-500">{{ $feedback->user->company->name ?? '不明な企業' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $feedback->lecturePage->title ?? '不明な課題' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($feedback->status === \App\Models\Submission::STATUS_PASSED)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                合格
                                            </span>
                                        @elseif($feedback->status === \App\Models\Submission::STATUS_REVISION_REQUIRED)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                要修正
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                {{ $feedback->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $feedback->teacher_comment }}">
                                        {{ Str::limit($feedback->teacher_comment, 50, '...') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('teacher.feedbacks.show', $feedback) }}" class="text-blue-600 hover:text-blue-900 font-semibold">詳細</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">
                                        まだフィードバック履歴がありません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($submissions->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $submissions->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
