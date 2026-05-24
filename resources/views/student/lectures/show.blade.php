<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('student.dashboard') }}" class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 shadow-sm transition-all duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h2 class="font-bold text-lg sm:text-xl text-slate-800 leading-tight truncate">
                {{ $lecturePage->title }}
            </h2>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-4 sm:py-8 px-4" x-data>
        <!-- Video Section -->
        @if($lecturePage->youtube_video_id)
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-slate-200 mb-8 transform transition-all">
                <div class="p-4 sm:p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h3 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            講義動画
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">動画を視聴して学習を進めてください。</p>
                    </div>
                    <div class="flex items-center gap-3 bg-blue-50 px-3 py-2 rounded-xl border border-blue-100">
                        <div class="text-right">
                            <div class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Progress</div>
                            <div class="text-lg sm:text-xl font-mono font-bold text-blue-700 leading-none">
                                <span id="progress-percent-display">{{ $videoProgress->progress_percent }}</span>%
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 16:9 Aspect Ratio Container -->
                <div class="relative w-full overflow-hidden bg-black" style="padding-top: 56.25%;">
                    <div id="player" class="absolute top-0 left-0 w-full h-full"></div>
                </div>
            </div>

            <script>
                // Load the IFrame Player API code asynchronously.
                var tag = document.createElement('script');
                tag.src = "https://www.youtube.com/iframe_api";
                var firstScriptTag = document.getElementsByTagName('script')[0];
                firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                var player;
                function onYouTubeIframeAPIReady() {
                    player = new YT.Player('player', {
                        videoId: '{{ $lecturePage->youtube_video_id }}',
                        playerVars: {
                            'playsinline': 1,
                            'rel': 0,
                            'modestbranding': 1
                        },
                        events: {
                            'onStateChange': onPlayerStateChange
                        }
                    });
                }

                var progressTimer = null;

                function onPlayerStateChange(event) {
                    if (event.data == YT.PlayerState.PLAYING) {
                        startProgressTracking();
                    } else {
                        stopProgressTracking();
                    }
                }

                function startProgressTracking() {
                    if (progressTimer) return;
                    // Check every 10 seconds
                    progressTimer = setInterval(function () {
                        if (player && typeof player.getCurrentTime === 'function' && typeof player.getDuration === 'function') {
                            var currentTime = player.getCurrentTime();
                            var duration = player.getDuration();
                            if (duration > 0) {
                                saveProgress(currentTime, duration);
                            }
                        }
                    }, 10000);
                }

                function stopProgressTracking() {
                    if (progressTimer) {
                        clearInterval(progressTimer);
                        progressTimer = null;
                    }
                }

                function saveProgress(currentTime, duration) {
                    fetch('{{ route("student.lectures.progress", $lecturePage) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            current_time: currentTime,
                            duration: duration
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('progress-percent-display').innerText = data.progress_percent;
                            }
                        })
                        .catch(error => console.error('Error saving progress:', error));
                }
            </script>
        @endif

        <!-- Submission Area -->
        <div class="space-y-8">
            <!-- New Submission Form -->
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-slate-200">
                <div class="p-5 sm:p-6 border-b border-slate-100 bg-gradient-to-br from-blue-600 to-blue-700">
                    <h3 class="text-base sm:text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        課題の提出
                    </h3>
                    <p class="text-xs sm:text-sm text-blue-100 mt-1">
                        完了したらファイルをアップロードして提出してください。
                    </p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('student.submissions.store', $lecturePage) }}"
                        enctype="multipart/form-data" class="space-y-6">
                        @csrf

                        <div>
                            <label class="block text-xs sm:text-sm font-bold text-slate-700 mb-3 uppercase tracking-wider">
                                提出ファイル選択 <span class="text-rose-500">*</span>
                            </label>
                            <label class="group relative flex flex-col items-center justify-center w-full p-8 sm:p-12 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50 hover:bg-slate-100 hover:border-blue-400 transition-all cursor-pointer">
                                <div class="flex flex-col items-center justify-center text-slate-400 group-hover:text-blue-500 transition">
                                    <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <p class="text-sm font-bold">クリックしてファイルを選択</p>
                                    <p class="text-[10px] sm:text-xs mt-1 text-slate-400">複数選択可能（画像、PDF、ドキュメント等）</p>
                                </div>
                                <input type="file" name="files[]" multiple required class="absolute inset-0 opacity-0 cursor-pointer" />
                            </label>
                            
                            <x-input-error :messages="$errors->get('files')" class="mt-2" />
                            <x-input-error :messages="$errors->get('files.*')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4">
                            <div class="flex items-start gap-2 max-w-xs">
                                <svg class="w-4 h-4 text-slate-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-[10px] text-slate-400 leading-relaxed">
                                    提出後の修正は、講師からのフィードバックがあるまで行えません。提出前に内容をよくご確認ください。
                                </p>
                            </div>
                            <x-primary-button class="w-full sm:w-auto h-12 px-8 bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200">
                                {{ __('課題を提出する') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- History -->
            <div class="pt-4">
                <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                    過去の提出履歴
                </h3>
                <div class="space-y-4">
                    @forelse($existingSubmissions as $submission)
                        <div class="bg-white overflow-hidden shadow-sm hover:shadow-md rounded-2xl border border-slate-200 transition-all">
                            <div class="p-5 sm:p-6">
                                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center flex-shrink-0 text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-xs font-mono font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Submitted at</p>
                                            <p class="text-sm sm:text-base font-bold text-slate-800">
                                                {{ $submission->created_at->isoFormat('YYYY/MM/DD HH:mm') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="self-start sm:self-center">
                                        @if($submission->status == 'submitted')
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                審査中
                                            </span>
                                        @elseif($submission->status == 'revision_required')
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                                                要修正・不合格
                                            </span>
                                        @elseif($submission->status == 'passed')
                                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                                合格！
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <!-- Items -->
                                    <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">添付ファイル ({{ $submission->items->count() }})</p>
                                        @if($submission->items->count() > 0)
                                            <ul class="space-y-2">
                                                @foreach($submission->items as $item)
                                                    <li class="flex items-center justify-between group">
                                                        <div class="flex items-center min-w-0 mr-2">
                                                            <svg class="w-3 h-3 text-blue-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                            </svg>
                                                            <button type="button" @click="$dispatch('open-preview', { url: '{{ route('student.submissions.preview', $item) }}', name: '{{ $item->original_name }}' })" 
                                                                class="text-xs text-slate-600 font-medium hover:text-blue-600 transition truncate text-left focus:outline-none">
                                                                {{ $item->original_name }}
                                                            </button>
                                                        </div>
                                                        <a href="{{ route('student.submissions.download', $item) }}" class="p-1 rounded-md hover:bg-blue-100 text-blue-500 transition-colors" title="Download">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                    
                                    <!-- Comment -->
                                    <div class="flex flex-col">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">講師コメント</p>
                                        <div class="flex-1 bg-slate-50/50 rounded-xl p-4 border border-slate-100 flex items-center justify-center">
                                            @if($submission->teacher_comment)
                                                <p class="text-sm text-slate-700 leading-relaxed italic">
                                                    "{{ $submission->teacher_comment }}"
                                                </p>
                                            @else
                                                <p class="text-[10px] text-slate-300 italic">フィードバック待ち</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border rounded-2xl p-12 text-center text-slate-400">
                            <p class="text-sm font-medium">まだ提出履歴はありません。</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>