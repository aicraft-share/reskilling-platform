<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                設定
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $company->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                ステータス: {{ $company->status === 'active' ? '有効' : $company->status }}
            </span>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{
        isSubmitting: false,
        toast: { show: false, message: '', type: 'success' },
        showToast(msg, type = 'success') {
            this.toast.message = msg;
            this.toast.type = type;
            this.toast.show = true;
            setTimeout(() => { this.toast.show = false; }, 3000);
        },
        async submitForm(e) {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
            
            const form = e.target;
            const formData = new FormData(form);
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                const data = await response.json();
                if (response.ok) {
                    this.showToast(data.message || '更新しました');
                    if (form.action.includes('password')) {
                        form.reset();
                    }
                } else if (response.status === 422 && data.errors) {
                    const firstError = Object.values(data.errors).flat()[0];
                    this.showToast(firstError || 'バリデーションエラーが発生しました', 'error');
                } else {
                    const errorMsg = data.message || 'エラーが発生しました';
                    this.showToast(errorMsg, 'error');
                }
            } catch (error) {
                this.showToast('通信エラーが発生しました', 'error');
            } finally {
                this.isSubmitting = false;
            }
        }
    }">



            <!-- 1. 企業情報 & プロフィール -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-slate-900">
                            {{ __('基本情報') }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ __("アカウントのメールアドレスと、共有の事業概要・メモを更新します。") }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('company.settings.update-profile') }}" enctype="multipart/form-data" class="mt-6 space-y-6" @submit.prevent="submitForm">
                        @csrf
                        @method('patch')

                        <!-- Company Name (Read Only) -->
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 mb-6">
                            <label class="block text-sm font-medium text-slate-500 mb-1">企業名</label>
                            <div class="text-slate-900 text-lg font-semibold">{{ $company->name }}</div>
                            <div class="mt-2 text-sm text-slate-500">
                                契約開始日: {{ $company->contract_start_date ? $company->contract_start_date->format('Y年m月d日') : '設定なし' }}
                            </div>
                            <div class="mt-3 text-sm text-slate-500">
                                <div class="mb-1">担当講師:</div>
                                @if($company->teachers->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($company->teachers as $teacher)
                                            <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 border border-blue-100">
                                                {{ $teacher->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @elseif($company->teacher)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-1 text-xs font-medium text-blue-700 border border-blue-100">
                                        {{ $company->teacher->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">未設定</span>
                                @endif
                            </div>
                            <p class="mt-2 text-xs text-slate-400">※ 企業名・契約情報等の変更については管理者までお問い合わせください。</p>
                        </div>

                        <!-- Avatar Upload -->
                        <div>
                            <x-input-label for="avatar" :value="__('プロフィール画像')" />
                            @if($user->avatar_path)
                                <div class="mt-2 mb-4">
                                    <img src="{{ asset('storage/' . $user->avatar_path) }}" alt="Current Avatar" class="h-20 w-20 rounded-full object-cover border border-slate-200">
                                </div>
                            @endif
                            <input id="avatar" name="avatar" type="file" accept="image/*" class="mt-1 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                            <p class="mt-1 text-xs text-slate-400">JPG, PNG, GIF形式のみ対応（最大2MB）</p>
                        </div>

                        <!-- Login ID (Read Only) -->
                        <div class="mb-6">
                            <x-input-label for="login_id_display" :value="__('ログインID')" />
                            <div class="mt-1 flex items-center">
                                <div class="bg-slate-100 border border-slate-200 rounded-lg py-2 px-4 text-blue-700 font-mono font-bold text-lg select-all">
                                    {{ $user->login_id }}
                                </div>
                                <span class="ml-3 text-xs text-slate-500">※ ログインに使用する固定IDです。変更はできません。</span>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-6">
                            <x-input-label for="email" :value="__('連絡用メールアドレス')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            <p class="mt-1 text-xs text-slate-500">お知らせの受信等に使用されます。</p>
                        </div>

                        <!-- Business Description -->
                        <div>
                            <x-input-label for="business_description" :value="__('事業概要・メモ')" />
                            <textarea id="business_description" name="business_description" rows="5" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('business_description', $company->business_description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('business_description')" />
                            <p class="mt-1 text-sm text-slate-500">このメモは担当講師などのシステム内ユーザーと共有されます。</p>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('保存') }}</x-primary-button>
                        </div>
                    </form>
                </section>
            </div>

            <!-- 2. パスワード再設定 -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-slate-900">
                            {{ __('パスワードの変更') }}
                        </h2>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ __('アカウントのセキュリティを維持するために、長くてランダムなパスワードを使用してください。') }}
                        </p>
                    </header>

                    <form method="post" action="{{ route('company.settings.update-password') }}" class="mt-6 space-y-6" @submit.prevent="submitForm">
                        @csrf
                        @method('patch')

                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">パスワードの条件</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <ul role="list" class="list-disc pl-5 space-y-1">
                                        <li>8文字以上の長さが必要です</li>
                                        <li>推測されにくい複雑な文字列を推奨します</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <x-input-label for="current_password" :value="__('現在のパスワード')" />
                        <x-text-input id="current_password" name="current_password" type="password"
                            class="mt-1 block w-full" autocomplete="current-password" />
                        <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('新しいパスワード')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('新しいパスワード（確認用）')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
                            class="mt-1 block w-full" autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>


                    <div class="flex items-center gap-4">
                        <x-primary-button x-bind:disabled="isSubmitting" x-bind:class="{ 'opacity-50 cursor-not-allowed': isSubmitting }">
                            <span x-show="!isSubmitting">{{ __('保存') }}</span>
                            <span x-show="isSubmitting" style="display: none;">{{ __('処理中...') }}</span>
                        </x-primary-button>
                    </div>
                    </form>
                </section>
            </div>

        <!-- Toast Notification (Improved) -->
        <div x-show="toast.show" 
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="fixed bottom-5 right-5 z-[9999] min-w-[300px]"
        >
            <div class="rounded-xl p-4 shadow-2xl border"
                 :class="toast.type === 'success' ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200'">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                         :class="toast.type === 'success' ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600'">
                        <template x-if="toast.type === 'success'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </template>
                        <template x-if="toast.type === 'error'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </template>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-bold" :class="toast.type === 'success' ? 'text-emerald-900' : 'text-rose-900'" x-text="toast.message"></p>
                    </div>
                    <button @click="toast.show = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
