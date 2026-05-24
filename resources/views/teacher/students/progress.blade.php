<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('teacher.companies.show', $student->company) }}" class="text-slate-500 hover:text-slate-700 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ $student->name }} 様の動画視聴進捗
            </h2>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">カリキュラム進捗</h3>
            <p class="text-sm text-slate-500 mt-1">
                各講義のYouTube動画の視聴状況を確認できます。
            </p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">講義名</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">動画有無</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">視聴進捗 (%)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">最大到達時間</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">最終視聴日時</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($lecturePages as $lecture)
                        @php
                            $progress = $progresses->get($lecture->id);
                        @endphp
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 text-sm font-medium text-slate-900">
                                {{ $lecture->title }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                @if($lecture->youtube_video_id)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        あり
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800">
                                        なし
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($lecture->youtube_video_id && $progress)
                                    <div class="flex items-center">
                                        <div class="w-full bg-slate-200 rounded-full h-2.5 max-w-[100px] mr-2">
                                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress->progress_percent }}%"></div>
                                        </div>
                                        <span class="text-sm font-bold text-slate-700">{{ $progress->progress_percent }}%</span>
                                    </div>
                                @elseif($lecture->youtube_video_id)
                                    <span class="text-sm text-slate-400">未視聴</span>
                                @else
                                    <span class="text-sm text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                @if($progress && $progress->max_position_seconds > 0)
                                    {{ floor($progress->max_position_seconds / 60) }}分
                                    {{ $progress->max_position_seconds % 60 }}秒
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                @if($progress && $progress->last_watched_at)
                                    {{ $progress->last_watched_at->format('Y/m/d H:i') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500 text-sm">
                                講義ページが登録されていません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
