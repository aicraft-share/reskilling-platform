<x-guest-layout>
    <!-- Header/Logo Area based on Type -->
    <div class="mb-8 text-center">
        @if(isset($isAdminLogin) && $isAdminLogin)
            <div
                class="inline-flex items-center justify-center px-4 py-1.5 mb-4 rounded-full bg-emerald-100 text-emerald-700 text-sm font-bold tracking-wider">
                管理者・講師専用
            </div>
            <a href="/">
                <img src="{{ asset('logo.png') }}?v={{ time() }}" alt="AI Craft Reskilling" class="h-16 mx-auto">
            </a>
            <p class="mt-4 text-sm text-slate-500">運営管理および講義管理はこちらからログインしてください。</p>
        @else
            <div
                class="inline-flex items-center justify-center px-4 py-1.5 mb-4 rounded-full bg-blue-100 text-blue-700 text-sm font-bold tracking-wider">
                受講生・企業専用
            </div>
            <a href="/">
                <img src="{{ asset('logo.png') }}?v={{ time() }}" alt="AI Craft Reskilling" class="h-16 mx-auto">
            </a>
            <p class="mt-4 text-sm text-slate-500">受講生および企業担当者の方はこちらからログインしてください。</p>
        @endif
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <div class="mb-4 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ (isset($isAdminLogin) && $isAdminLogin) ? route('admin.login') : route('login') }}">
        @csrf

        <!-- Login ID -->
        <div>
            <x-input-label for="login_id" :value="__('ID')" />
            <x-text-input id="login_id" class="block mt-1 w-full" type="text" name="login_id" :value="old('login_id')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('login_id')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center text-slate-600">
                <input id="remember_me" type="checkbox"
                    class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                <span class="ms-2 text-sm">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex flex-col space-y-4 mt-8">
            <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white {{ (isset($isAdminLogin) && $isAdminLogin) ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                ログイン
            </button>

            @if (Route::has('password.request'))
                <div class="text-center">
                    <a class="text-sm text-slate-500 hover:text-slate-800 transition-colors"
                        href="{{ route('password.request') }}">
                        パスワードをお忘れですか？
                    </a>
                </div>
            @endif
        </div>

        {{-- Removed login switch links as per request --}}
    </form>
</x-guest-layout>