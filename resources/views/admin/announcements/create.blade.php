<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.announcements.index') }}"
                class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                新規お知らせ配信
            </h2>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto py-6">
        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-md shadow-sm mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm text-red-800 font-medium">入力内容にエラーがあります</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex items-center gap-3">
                <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                <h3 class="text-lg font-medium text-slate-800">配信設定</h3>
            </div>

            <form action="{{ route('admin.announcements.store') }}" method="POST" class="p-6">
                @csrf

                <div class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">タイトル <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title"
                            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="重要なお知らせ" value="{{ old('title') }}" required>
                    </div>

                    <!-- Target Scope -->
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-3">配信先ロール <span
                                class="text-red-500">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- All -->
                            <label
                                class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ old('target_scope_type', 'all') == 'all' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-slate-300' }}">
                                <input type="radio" name="target_scope_type" value="all" class="sr-only" {{ old('target_scope_type', 'all') == 'all' ? 'checked' : '' }}
                                    onchange="updateStyles('all')">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900">全体</span>
                                        <span class="mt-1 flex items-center text-xs text-slate-500">全てのユーザーへ配信</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-indigo-600 {{ old('target_scope_type', 'all') == 'all' ? '' : 'hidden' }}"
                                    id="icon-all" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>

                            <!-- Instructors -->
                            <label
                                class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ old('target_scope_type') == 'instructors' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-slate-300' }}">
                                <input type="radio" name="target_scope_type" value="instructors" class="sr-only" {{ old('target_scope_type') == 'instructors' ? 'checked' : '' }}
                                    onchange="updateStyles('instructors')">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900">講師</span>
                                        <span class="mt-1 flex items-center text-xs text-slate-500">運用講師のみ</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-indigo-600 {{ old('target_scope_type') == 'instructors' ? '' : 'hidden' }}"
                                    id="icon-instructors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>

                            <!-- Companies -->
                            <label
                                class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ old('target_scope_type') == 'companies' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-slate-300' }}">
                                <input type="radio" name="target_scope_type" value="companies" class="sr-only" {{ old('target_scope_type') == 'companies' ? 'checked' : '' }}
                                    onchange="updateStyles('companies')">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900">企業</span>
                                        <span class="mt-1 flex items-center text-xs text-slate-500">法人担当者のみ</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-indigo-600 {{ old('target_scope_type') == 'companies' ? '' : 'hidden' }}"
                                    id="icon-companies" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>

                            <!-- Students -->
                            <label
                                class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none {{ old('target_scope_type') == 'students' ? 'border-indigo-500 ring-1 ring-indigo-500' : 'border-slate-300' }}">
                                <input type="radio" name="target_scope_type" value="students" class="sr-only" {{ old('target_scope_type') == 'students' ? 'checked' : '' }}
                                    onchange="updateStyles('students')">
                                <span class="flex flex-1">
                                    <span class="flex flex-col">
                                        <span class="block text-sm font-medium text-slate-900">生徒</span>
                                        <span class="mt-1 flex items-center text-xs text-slate-500">受講生のみ</span>
                                    </span>
                                </span>
                                <svg class="h-5 w-5 text-indigo-600 {{ old('target_scope_type') == 'students' ? '' : 'hidden' }}"
                                    id="icon-students" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </label>
                        </div>
                    </div>

                    <!-- Body -->
                    <div>
                        <label for="body" class="block text-sm font-medium text-slate-700 mb-1">本文 <span
                                class="text-red-500">*</span></label>
                        <p class="text-xs text-slate-500 mb-2">配信するメッセージの内容を入力してください。</p>
                        <textarea id="body" name="body" rows="8"
                            class="block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>{{ old('body') }}</textarea>
                    </div>
                </div>

                <div class="mt-8 pt-5 border-t border-slate-200 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.announcements.index') }}"
                        class="inline-flex justify-center rounded-md border border-slate-300 bg-white py-2 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        キャンセル
                    </a>
                    <button type="submit"
                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                        対象ユーザーに配信する
                    </button>
                    <!-- Loading State (hidden initially) -->
                    <div id="loading" class="hidden inline-flex items-center text-sm text-slate-500">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        配信処理中...
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Simple client-side UX logic for custom radio buttons
            function updateStyles(selectedValue) {
                const types = ['all', 'instructors', 'companies', 'students'];

                types.forEach(type => {
                    const label = document.querySelector(`input[value="${type}"]`).closest('label');
                    const icon = document.getElementById(`icon-${type}`);

                    if (type === selectedValue) {
                        label.classList.add('border-indigo-500', 'ring-1', 'ring-indigo-500');
                        label.classList.remove('border-slate-300');
                        icon.classList.remove('hidden');
                    } else {
                        label.classList.remove('border-indigo-500', 'ring-1', 'ring-indigo-500');
                        label.classList.add('border-slate-300');
                        icon.classList.add('hidden');
                    }
                });
            }

            // Show spinner on submit to prevent double-clicks
            document.querySelector('form').addEventListener('submit', function (e) {
                if (this.checkValidity()) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const loader = document.getElementById('loading');

                    submitBtn.classList.add('hidden');
                    loader.classList.remove('hidden');
                }
            });
        </script>
    @endpush
</x-app-layout>