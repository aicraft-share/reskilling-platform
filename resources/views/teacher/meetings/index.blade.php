<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight whitespace-nowrap">
                MTG 一覧
            </h2>
            <a href="{{ route('teacher.meetings.create') }}"
                class="flex-shrink-0 inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white hover:bg-blue-500 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                MTGを作成（Zoom）
            </a>
        </div>
    </x-slot>

    <div>
        @if (session('success'))
            <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded" role="alert">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-slate-900">

                @if($meetings->isEmpty())
                    <div class="text-center py-10 text-slate-500">
                        担当企業のMTGはまだありません。
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">開催日時</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">タイトル</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">企業 / 参加者</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">状態</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">録画</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">文字起こし</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">AI要約</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">操作</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-200">
                                @foreach($meetings as $item)
                                    @php
                                        $isMeeting = $item instanceof \App\Models\Meeting;
                                        $log = $isMeeting ? $item->meetingLog : $item; // MeetingLog for log data
                                    @endphp
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-slate-900">
                                            {{ $item->scheduled_at?->format('Y/m/d H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-700">
                                            {{ $item->title }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-600">
                                            <div class="font-medium">{{ $item->company?->name ?? '(削除済み企業)' }}</div>
                                            @if($isMeeting)
                                                <div class="text-xs text-slate-400">{{ $item->participants->count() }}名</div>
                                            @else
                                                <div class="text-xs text-slate-400">{{ $item->students->pluck('name')->join(', ') }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($item->scheduled_at?->isFuture())
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">予定</span>
                                            @else
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-600">終了</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($log && $log->youtube_url)
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-700">登録済</span>
                                            @else
                                                <span class="text-xs text-slate-400">未登録</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($log && $log->transcript_status === 'ready')
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-teal-100 text-teal-700">完了</span>
                                            @else
                                                <span class="text-xs text-slate-400">未登録</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($log && $log->transcript_summary)
                                                <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-violet-100 text-violet-700">生成済</span>
                                            @else
                                                <span class="text-xs text-slate-400">未生成</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($isMeeting)
                                                <a href="{{ route('teacher.meetings.show', $item) }}"
                                                    class="text-blue-600 hover:text-blue-900 font-medium text-sm">詳細・管理</a>
                                            @else
                                                <a href="{{ route('teacher.meeting-logs.show', $item) }}"
                                                    class="text-blue-600 hover:text-blue-900 font-medium text-sm">詳細・管理</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $meetings->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
