<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                教材を追加
            </h2>
            <a href="{{ route('admin.lecture-pages.index') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">一覧に戻る</a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200 shadow-sm transition-all hover:shadow-md">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.lecture-pages.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Course Selection -->
                    <div>
                        <x-input-label for="course_id" :value="__('所属コース')" />
                        <select id="course_id" name="course_id" class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm" required>
                            <option value="">コースを選択してください</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
                    </div>

                    <!-- Section Name -->
                    <div>
                        <x-input-label for="section_name" :value="__('セクション名 (章・項目)')" />
                        <x-text-input id="section_name" class="block mt-1 w-full" type="text" name="section_name"
                            :value="old('section_name', '基本')" placeholder="例：はじめに、実践、応用など" />
                        <x-input-error :messages="$errors->get('section_name')" class="mt-2" />
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <input id="is_active" name="is_active" type="checkbox" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <label for="is_active" class="ml-2 block text-sm font-medium text-slate-700">
                        教材を公開する (有効)
                    </label>
                </div>


                    <div class="mb-4">
                        <x-input-label for="title" :value="__('タイトル')" />
                        <x-text-input id="title" class="block mt-1 w-full" type="text" name="title"
                            :value="old('title')" required autofocus />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    <!-- Sort Order -->
                    <div class="mt-6">
                        <x-input-label for="sort_order" :value="__('表示順（数値）')" />
                        <x-text-input id="sort_order" class="block mt-1 w-full" type="number" name="sort_order"
                            :value="old('sort_order', $nextSortOrder)" required min="0" />
                        <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                    </div>

                    <!-- Thumbnail -->
                    <div class="mt-6">
                        <x-input-label for="thumbnail" :value="__('サムネイル画像')" />
                        <input id="thumbnail" name="thumbnail" type="file" class="block mt-1 w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*">
                        <p class="mt-1 text-xs text-slate-500">※ 推奨サイズ: 1280x720px (16:9)</p>
                        <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                    </div>

                    <!-- YouTube URL -->
                    <div class="mt-6">
                        <x-input-label for="youtube_url" :value="__('YouTube動画URL (任意)')" />
                        <x-text-input id="youtube_url" class="block mt-1 w-full" type="url" name="youtube_url"
                            :value="old('youtube_url')" placeholder="https://www.youtube.com/watch?v=..." />
                        <p class="mt-1 text-xs text-slate-500">※ 動画を埋め込む場合はYouTubeの通常URLを入力してください。</p>
                        <x-input-error :messages="$errors->get('youtube_url')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <x-input-label for="description" :value="__('講義内容')" />
                        <textarea id="description" name="description" rows="10"
                            class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                            placeholder="ここに講義テキストを入力してください...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-8">
                        <a href="{{ route('admin.lecture-pages.index') }}"
                            class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                        <x-primary-button>
                            {{ __('保存') }}
                        </x-primary-button>
                    </div>
            </form>
        </div>
    </div>

    <!-- Title Helper Script -->
</x-app-layout>