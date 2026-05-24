<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            講義一覧（カリキュラム）
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6 pb-24 lg:pb-12">
        @forelse ($courses as $course)
            <div class="bg-white overflow-hidden shadow-sm hover:shadow-xl rounded-2xl border border-slate-200 flex flex-col transition-all duration-300 group cursor-pointer transform hover:-translate-y-1"
                onclick="window.location='{{ route('student.courses.show', $course) }}'">

                <!-- Thumbnail Container -->
                <div class="w-full aspect-video bg-slate-100 relative overflow-hidden flex-shrink-0">
                    @if($course->thumbnail_path)
                        <img src="{{ asset('storage/' . $course->thumbnail_path) }}" alt="{{ $course->title }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-slate-300 bg-slate-50">
                            <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <span class="text-[10px] font-medium tracking-wider uppercase">No Thumbnail</span>
                        </div>
                    @endif
                    <div class="absolute bottom-3 right-3 bg-slate-900/80 backdrop-blur-md px-2 py-1 rounded-lg text-[10px] font-bold text-white shadow-lg border border-white/10">
                        {{ $course->lecture_pages_count }} Lessons
                    </div>
                </div>

                <!-- Course Badge -->
                <div class="px-4 pt-3">
                    <span class="inline-block bg-blue-600 px-2 py-1 rounded-lg text-[10px] font-bold text-white">
                        Course {{ str_pad($course->sort_order, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>

                <!-- Content -->
                <div class="p-4 sm:p-5 flex-1 flex flex-col">
                    <div class="h-10 sm:h-12 mb-2">
                        <h3 class="font-bold text-sm sm:text-base text-slate-800 line-clamp-2 group-hover:text-blue-600 transition duration-200 leading-tight">
                            {{ $course->title }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 mb-6 line-clamp-2 flex-1 italic">
                        {{ $course->description ?? '説明はありません' }}
                    </p>
                    <div class="pt-4 flex items-center justify-center border-t border-slate-100">
                        <span class="inline-flex items-center text-xs font-bold text-blue-600 group-hover:underline decoration-2 underline-offset-4">
                            講義一覧を見る
                            <svg class="ml-1.5 w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7-7 7" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-slate-500 italic">公開されているコースはありません</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
