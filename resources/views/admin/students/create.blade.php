<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            生徒を追加
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
        <div class="p-8">
            <form method="POST" action="{{ route('admin.students.store') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('生徒名')" />
                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')"
                        required autofocus placeholder="佐藤 健太" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-6">
                    <x-input-label for="email" :value="__('メールアドレス')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                        required placeholder="kenta.sato@example.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Automatic Credentials Notice -->
                <div class="mt-6 border-t pt-6">
                    <div class="p-4 bg-blue-50 rounded-lg text-sm text-blue-700">
                        <p class="font-bold mb-1">ログイン情報の自動生成</p>
                        <p>ログインIDとパスワードは登録時に自動的に生成されます。登録完了後の画面に表示されますので、必ず控えて生徒の方へお伝えください。</p>
                    </div>
                </div>

                <!-- Company -->
                <div class="mt-6">
                    <x-input-label for="company_id" :value="__('所属企業')" />
                    <select id="company_id" name="company_id"
                        class="block mt-1 w-full border-slate-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm"
                        required>
                        <option value="">選択してください</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('company_id')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-8">
                    <a href="{{ route('admin.students.index') }}"
                        class="text-sm text-slate-600 hover:text-slate-900 underline mr-4">キャンセル</a>
                    <x-primary-button>
                        {{ __('登録する') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>