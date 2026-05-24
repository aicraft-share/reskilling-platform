<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('teacher.assignments.students') }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                {{ $student->name }} さんへの「次回までにやること」設定
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-slate-200">
                <div class="p-8">
                    <form method="POST" action="{{ route('teacher.students.next-action.store', $student) }}" class="space-y-8">
                        @csrf

                        <!-- Instruction Text -->
                        <div>
                            <x-input-label for="instruction_text" :value="__('課題・指示内容')" class="text-base font-bold text-slate-700 mb-2" />
                            <p class="text-sm text-slate-500 mb-3">次回MTGまでに取り組んでほしい具体的な指示を入力してください。</p>
                            <textarea id="instruction_text" name="instruction_text" rows="5" 
                                class="block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm transition-all"
                                placeholder="例：次回までにLPのファーストビューを作成して、スクリーンショットを提出してください。"
                                required>{{ old('instruction_text', $currentAction?->instruction_text) }}</textarea>
                            <x-input-error :messages="$errors->get('instruction_text')" class="mt-2" />
                        </div>

                        <!-- Lecture Selection -->
                        <div>
                            <x-input-label :value="__('必須視聴講義')" class="text-base font-bold text-slate-700 mb-2" />
                            <p class="text-sm text-slate-500 mb-4">次回までに必ず見てきてほしい講義を選択してください（複数選択可）。</p>
                            
                            <div class="space-y-6 max-h-[500px] overflow-y-auto pr-4 custom-scrollbar">
                                @foreach($courses as $course)
                                    <div class="border border-slate-100 rounded-xl p-4 bg-slate-50/50">
                                        <h3 class="font-bold text-slate-800 mb-3 flex items-center gap-2">
                                            <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                                            {{ $course->title }}
                                        </h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-4">
                                            @foreach($course->lecturePages as $lecture)
                                                <label class="flex items-start gap-3 p-3 bg-white border border-slate-200 rounded-lg hover:border-blue-400 hover:bg-blue-50 transition-all cursor-pointer group">
                                                    <input type="checkbox" name="lecture_page_ids[]" value="{{ $lecture->id }}" 
                                                        class="mt-1 rounded border-slate-300 text-blue-600 focus:ring-blue-500 transition-colors"
                                                        {{ (is_array(old('lecture_page_ids')) && in_array($lecture->id, old('lecture_page_ids'))) || ($currentAction && $currentAction->lecturePages->contains($lecture->id)) ? 'checked' : '' }}>
                                                    <span class="text-sm text-slate-700 group-hover:text-blue-800 transition-colors">
                                                        {{ $lecture->title }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('lecture_page_ids')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4 pt-6 border-t border-slate-100">
                            <a href="{{ route('teacher.assignments.students') }}" class="text-sm text-slate-500 hover:text-slate-700 transition-colors">
                                キャンセル
                            </a>
                            <x-primary-button class="px-8 py-3 rounded-full text-base bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                                設定を保存する
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</x-app-layout>
