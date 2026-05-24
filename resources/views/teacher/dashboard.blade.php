<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            ホーム（担当企業一覧）
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Notification/Summary Section -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base sm:text-lg font-bold text-slate-900">要対応: 未レビューの提出があります</h3>
                    <p class="text-slate-500 text-xs sm:text-sm mt-1">生徒からの提出物を確認し、フィードバックを行ってください。</p>
                </div>
                <a href="{{ route('teacher.submissions.index', ['status' => 'submitted']) }}"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm">
                    レビュー画面へ
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>

        <!-- Scheduled MTG -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">今後のMTG予定</h3>
                <a href="{{ route('teacher.meetings.index') }}" class="text-xs sm:text-sm text-blue-600 hover:text-blue-800 font-medium">
                    すべて見る &rarr;
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">日時</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">企業 / トピック</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($upcomingMeetings as $meeting)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900 font-mono">
                                    {{ $meeting->scheduled_at->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-700">
                                    <div class="font-medium text-slate-900">{{ $meeting->company->name ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500 truncate w-32 sm:w-auto">{{ $meeting->title }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($meeting->zoom_start_url)
                                        <a href="{{ $meeting->zoom_start_url }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            開始
                                        </a>
                                    @else
                                        <span class="text-slate-400 text-[10px]">URL未発行</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-500 text-sm italic">
                                    予定されているMTGはありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assigned Companies -->
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-base sm:text-lg font-bold text-slate-900">担当企業・クラス</h3>
                <span class="text-xs text-slate-500">{{ $companies->count() }} 社</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">企業名</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">受講生徒数</th>
                            <th class="px-4 sm:px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse ($companies as $company)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900">
                                    {{ $company->name }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                    <span class="font-mono">{{ $company->students->count() }}</span> 名
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('teacher.companies.show', $company) }}"
                                        class="text-blue-600 hover:text-blue-900 font-bold">詳細</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-500 text-sm">
                                    担当している企業はありません。
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>