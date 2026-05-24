<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.dashboard') }}" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ $course->title }}
            </h2>
        </div>
    </x-slot>

    <div class="mb-8">
        <p class="text-slate-600">{{ $course->description }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 pb-24 lg:pb-12">
        @forelse ($lecturePages as $page)
            <div class="bg-white overflow-hidden shadow-sm hover:shadow-xl rounded-2xl border border-slate-200 flex flex-col transition-all duration-300 group cursor-pointer transform hover:-translate-y-1"
                onclick="window.location='{{ route('student.lectures.show', $page) }}'">

                <!-- Lesson Index Badge -->
                <div class="flex-shrink-0 px-4 pt-4">
                    <span class="inline-block bg-slate-900 px-2 py-1 rounded-lg text-[10px] font-bold text-white">
                        Lesson {{ str_pad($page->sort_order, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <!-- Content -->
                @php
                    $hasVideo = !empty($page->youtube_url) || !empty($page->youtube_video_id);
                    $progressPercent = 0;
                    if ($hasVideo) {
                        $progressRecord = $page->lectureVideoProgresses->first();
                        if ($progressRecord) {
                            $progressPercent = $progressRecord->progress_percent;
                        }
                    }
                @endphp
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="h-10 sm:h-12 mb-2">
                        <h3 class="font-bold text-sm sm:text-base text-slate-800 line-clamp-2 group-hover:text-blue-600 transition duration-200 leading-tight">
                            {{ $page->title }}
                        </h3>
                    </div>
                    
                    <p class="text-xs text-slate-500 mb-4 line-clamp-2 flex-1">
                        {{ $page->description }}
                    </p>

                    @php
                        $submission = $page->submissions->first();
                        $statusBadgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                        $statusText = '未提出';

                        if ($submission) {
                            if ($submission->status === \App\Models\Submission::STATUS_PASSED) {
                                $statusBadgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
                                $statusText = '合格済み';
                            } elseif ($submission->status === \App\Models\Submission::STATUS_REVISION_REQUIRED) {
                                $statusBadgeClass = 'bg-red-50 text-red-700 border-red-100';
                                $statusText = '要修正';
                            } else {
                                $statusBadgeClass = 'bg-amber-50 text-amber-700 border-amber-100';
                                $statusText = '審査中';
                            }
                        }
                    @endphp

                    <div class="space-y-3 pb-4 border-b border-slate-100">
                        <!-- Assignment Status -->
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">課題ステータス</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border {{ $statusBadgeClass }}">
                                {{ $statusText }}
                            </span>
                        </div>

                        <!-- Video Progress -->
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">学習進捗</span>
                            @if($hasVideo)
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-16 sm:w-20 h-1.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200/50">
                                        <div class="h-full bg-gradient-to-r from-blue-400 to-blue-600 rounded-full" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-mono font-bold text-slate-600">{{ $progressPercent }}%</span>
                                </div>
                            @else
                                <span class="text-[10px] font-medium text-slate-300 italic">動画なし</span>
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-center">
                        <span class="inline-flex items-center text-xs font-bold text-blue-600 group-hover:underline decoration-2 underline-offset-4">
                            レクチャーを視聴する
                            <svg class="ml-1.5 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-sm p-12 text-center border border-slate-200">
                    <p class="text-slate-600 font-bold">このコースにはまだ講義がありません</p>
                </div>
            </div>
        @endforelse
    </div>
</x-app-layout>
