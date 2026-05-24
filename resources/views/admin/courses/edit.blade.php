<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                コース編集
            </h2>
            <a href="{{ route('admin.courses.index') }}" class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-slate-700 transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                一覧に戻る
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
            <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
                @csrf
                @method('PATCH')

                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="title" value="コース名" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $course->title)" required placeholder="例：AI Craft 基礎コース" />
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" value="説明文" />
                            <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm" placeholder="コースの概要を入力してください。">{{ old('description', $course->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <x-input-label for="thumbnail" value="サムネイル画像" />
                            @if($course->thumbnail_path)
                                <div class="mt-2 mb-4 group relative inline-block">
                                    <img src="{{ asset('storage/' . $course->thumbnail_path) }}" alt="Current Thumbnail" class="h-32 w-48 object-cover rounded-lg border border-slate-200 shadow-sm transition group-hover:opacity-75">
                                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <span class="bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded truncate w-32">変更するには下で選択</span>
                                    </div>
                                </div>
                            @endif
                            <div class="mt-1 flex flex-col items-center p-6 border-2 border-dashed border-slate-200 rounded-xl hover:border-blue-400 transition-colors bg-slate-50">
                                <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a1 1 0 011.414 0L16 16m-2-2l1.586-1.586a1 1 0 011.414 0L21 20M17 9a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <input id="thumbnail" name="thumbnail" type="file" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            </div>
                            <x-input-error :messages="$errors->get('thumbnail')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="status" value="公開状態" />
                                <select id="status" name="status" class="mt-1 block w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm">
                                    <option value="draft" {{ old('status', $course->status) === 'draft' ? 'selected' : '' }}>下書き</option>
                                    <option value="published" {{ old('status', $course->status) === 'published' ? 'selected' : '' }}>公開中</option>
                                    <option value="archived" {{ old('status', $course->status) === 'archived' ? 'selected' : '' }}>アーカイブ</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="sort_order" value="並び順" />
                                <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $course->sort_order)" required />
                                <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end border-t border-slate-100 pt-8">
                    <x-secondary-button type="button" onclick="history.back()" class="mr-3">
                        キャンセル
                    </x-secondary-button>
                    <x-primary-button class="bg-blue-600 hover:bg-blue-700">
                        コースを更新する
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
