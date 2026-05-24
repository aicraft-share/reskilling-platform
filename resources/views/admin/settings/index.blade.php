<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            設定
        </h2>
    </x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
            <!-- Profile Information -->
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-900">管理者基本情報</h3>
                    <p class="text-sm text-slate-500 mt-1">アカウントの基本情報とパスワードを更新できます。</p>
                </div>
                <div class="p-6">
                    @if (session('status') === 'profile-updated')
                        <div class="mb-4 bg-emerald-50 text-emerald-700 p-4 rounded-lg flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            プロフィールが更新されました。
                        </div>
                    @endif

                    <form method="post" action="{{ route('admin.settings.update-profile') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        <!-- Login ID (Read Only) -->
                        <div class="mb-6">
                            <x-input-label for="login_id_display" :value="__('ログインID')" />
                            <div class="mt-1 flex items-center">
                                <div class="bg-slate-100 border border-slate-200 rounded-lg py-2 px-4 text-blue-700 font-mono font-bold text-lg select-all">
                                    {{ auth()->user()->login_id }}
                                </div>
                                <span class="ml-3 text-xs text-slate-500">※ ログインに使用する固定IDです。変更はできません。</span>
                            </div>
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('名前')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                :value="old('name', $user->name)" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('連絡用メールアドレス')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                :value="old('email', $user->email)" required autocomplete="username" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            <p class="mt-1 text-xs text-slate-500">通知などの連絡用です。ログインには使用しません。</p>
                        </div>

                        <div class="border-t border-slate-100 pt-6 mt-6">
                            <h4 class="text-sm font-semibold text-slate-700 mb-4">パスワード変更（変更する場合のみ入力）</h4>

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
                                                <li>パスワードを変更する場合、現在のパスワードの入力が必要です</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <x-input-label for="current_password" :value="__('現在のパスワード')" />
                                    <x-text-input id="current_password" name="current_password" type="password"
                                        class="mt-1 block w-full" autocomplete="current-password" />
                                    <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="password" :value="__('新しいパスワード')" />
                                    <x-text-input id="password" name="password" type="password"
                                        class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('パスワード確認')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation"
                                        type="password" class="mt-1 block w-full" autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end" x-data="{ submitting: false }">
                            <x-primary-button @click="submitting = true" x-bind:disabled="submitting" x-bind:class="{ 'opacity-50 cursor-not-allowed': submitting }">
                                <span x-show="!submitting">{{ __('保存する') }}</span>
                                <span x-show="submitting" style="display: none;">{{ __('保存中...') }}</span>
                            </x-primary-button>
                        </div>
                    </form>
                </div>
        </div>
    </div>
</x-app-layout>