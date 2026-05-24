<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            講師を追加
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.teachers.store') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('氏名')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                        required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('メールアドレス')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Profile Section -->
                <div class="mt-6 border-t pt-6">
                    <h3 class="text-md font-semibold text-slate-700 mb-4">プロフィール情報</h3>

                    <!-- Experience -->
                    <div>
                        <x-input-label for="years_of_experience" :value="__('経験年数（年）')" />
                        <x-text-input id="years_of_experience" class="block mt-1 w-24" type="number"
                            name="years_of_experience" :value="old('years_of_experience')" min="0" />
                        <x-input-error :messages="$errors->get('years_of_experience')" class="mt-2" />
                    </div>

                    <!-- Specialty Fields -->
                    <div class="mt-4">
                        <x-input-label for="specialty_fields" :value="__('得意分野')" />
                        <x-text-input id="specialty_fields" class="block mt-1 w-full" type="text"
                            name="specialty_fields" :value="old('specialty_fields')"
                            placeholder="例: Web開発, AI実装, プロジェクト管理" />
                        <x-input-error :messages="$errors->get('specialty_fields')" class="mt-2" />
                    </div>

                    <!-- Skills -->
                    <div class="mt-4">
                        <x-input-label for="skills" :value="__('スキル')" />
                        <textarea id="skills" name="skills" rows="3"
                            class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm text-sm"
                            placeholder="例: PHP, Python, React, AWS">{{ old('skills') }}</textarea>
                        <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                    </div>
                </div>

                <!-- Automatic Credentials Notice -->
                <div class="mt-6 border-t pt-6">
                    <div class="p-4 bg-blue-50 rounded-lg text-sm text-blue-700">
                        <p class="font-bold mb-1">ログイン情報の自動生成</p>
                        <p>ログインIDとパスワードは登録時に自動的に生成されます。登録完了後の画面に表示されますので、必ず控えて講師の方へお伝えください。</p>
                    </div>
                </div>

                <!-- Assigned Companies (Optional Legacy Support) -->
                <!-- Note: Company assignment is now primarily done from Company side (1:1), but if m:n is needed, keep this. Hiding for now to avoid confusion with new 1:1 logic. -->

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('admin.teachers.index') }}"
                        class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                    <x-primary-button>
                        {{ __('登録する') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>