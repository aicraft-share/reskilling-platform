<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                教材管理
            </h2>
            <a href="{{ route('admin.lecture-pages.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                新しい教材を追加
            </a>
        </div>
    </x-slot>

    <div class="space-y-8 pb-12">
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @foreach($courses as $course)
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="px-4 sm:px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                    <div class="flex items-center min-w-0">
                        <div class="bg-blue-600 text-white p-2 rounded-lg mr-3 sm:mr-4 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base sm:text-lg font-bold text-slate-800 truncate">{{ $course->title }}</h3>
                            <p class="text-[10px] sm:text-xs text-slate-500">ID: {{ $course->id }} | 教材数: {{ $course->lecturePages->count() }}</p>
                        </div>
                    </div>
                </div>

                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">セクション</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-16">順序</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">サムネイル</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">教材名</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">動画</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider w-24">公開状態</th>
                                <th class="px-6 py-3 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @php
                                $groupedLectures = $course->lecturePages->groupBy('section_name');
                            @endphp
                            @forelse($groupedLectures as $sectionName => $lectures)
                                @foreach($lectures as $index => $lecture)
                                    <tr class="hover:bg-slate-50 transition-colors duration-150">
                                        @if($index === 0)
                                            <td class="px-6 py-4 text-sm font-bold text-blue-600 align-top" rowspan="{{ $lectures->count() }}">
                                                {{ $sectionName }}
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 text-sm text-slate-400 font-mono">
                                            {{ $lecture->sort_order }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($lecture->thumbnail_path)
                                                <img src="{{ asset('storage/' . $lecture->thumbnail_path) }}" alt="Thumb" class="h-10 w-16 object-cover rounded border border-slate-100">
                                            @else
                                                <div class="h-10 w-16 bg-slate-50 rounded border border-slate-100 flex items-center justify-center text-[8px] text-slate-300">
                                                    No Img
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-slate-900">{{ $lecture->title }}</div>
                                            @if($lecture->description)
                                                <div class="text-xs text-slate-500 mt-0.5 line-clamp-1">{{ $lecture->description }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-slate-600">
                                            @if($lecture->youtube_url)
                                                <div class="flex items-center text-[10px] sm:text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded w-fit capitalize">
                                                    <svg class="w-3 h-3 mr-1 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path>
                                                    </svg>
                                                    Youtube
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">なし</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($lecture->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800">公開中</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-100 text-slate-800">非公開</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-3">
                                            <a href="{{ route('admin.lecture-pages.edit', $lecture) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition">編集</a>
                                            <form action="{{ route('admin.lecture-pages.destroy', $lecture) }}" method="POST" class="inline-block" onsubmit="return confirm('本当に削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-red-600 hover:text-red-800 transition">削除</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-400 text-sm italic">
                                        登録された教材はありません。
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card list -->
                <div class="md:hidden">
                    @php
                        $groupedLectures = $course->lecturePages->groupBy('section_name');
                    @endphp
                    @forelse($groupedLectures as $sectionName => $lectures)
                        <div class="bg-slate-50/50 px-4 py-2 border-b border-slate-100 flex items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-2">Section:</span>
                            <span class="text-xs font-bold text-blue-600">{{ $sectionName }}</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach($lectures as $lecture)
                                <div class="px-4 py-4 flex items-start gap-3">
                                    <div class="text-[10px] font-mono text-slate-400 mt-1 flex-shrink-0 w-6">#{{ $lecture->sort_order }}</div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ $lecture->title }}</h4>
                                            @if($lecture->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-medium bg-emerald-100 text-emerald-800">公開</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[8px] font-medium bg-slate-100 text-slate-800">非公開</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 line-clamp-1 mb-3">{{ $lecture->description }}</p>
                                        <div class="flex items-center justify-between">
                                            @if($lecture->youtube_url)
                                                <div class="flex items-center text-[10px] text-red-600 bg-red-50 px-1.5 py-0.5 rounded">
                                                    Video
                                                </div>
                                            @else
                                                <div class="text-[10px] text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
                                                    No Video
                                                </div>
                                            @endif
                                            <div class="flex gap-4">
                                                <a href="{{ route('admin.lecture-pages.edit', $lecture) }}" class="text-[10px] font-bold text-blue-600">編集</a>
                                                <form action="{{ route('admin.lecture-pages.destroy', $lecture) }}" method="POST" class="inline-block" onsubmit="return confirm('本当に削除しますか？');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[10px] font-bold text-red-600">削除</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="px-4 py-8 text-center text-slate-400 text-xs italic">
                            教材はありません。
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach

        @if($orphanLectures->count() > 0)
            <div class="bg-amber-50 overflow-hidden shadow-sm rounded-xl border border-amber-200">
                <div class="px-4 sm:px-6 py-4 bg-amber-100/50 border-b border-amber-200 flex items-center">
                    <svg class="w-5 h-5 text-amber-600 mr-2 sm:mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <h3 class="text-sm sm:text-lg font-bold text-amber-800">未分類の教材 ({{ $orphanLectures->count() }})</h3>
                </div>
                <div class="p-4 sm:p-6 text-xs sm:text-sm text-amber-700">
                    コースに紐付いていない教材があります。編集画面からコースを割り当ててください。
                </div>
            </div>
        @endif
    </div>
</x-app-layout>