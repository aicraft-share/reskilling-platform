@props(['credentials'])

@if($credentials)
<div class="mb-8 bg-white border-2 border-blue-200 rounded-xl overflow-hidden shadow-lg">
    <div class="bg-blue-600 px-6 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-2 text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04kM12 20.944a11.955 11.955 0 01-8.618-3.04A11.952 11.952 0 0012 21.056c4.959 0 9.26-3.041 11.056-7.391a11.954 11.954 0 01-8.618-3.041" />
            </svg>
            <h3 class="font-bold text-lg">アカウント情報が生成されました</h3>
        </div>
        <span class="px-3 py-1 bg-blue-500/30 border border-blue-400 text-white text-xs font-bold rounded-full uppercase tracking-wider">
            一度のみ表示
        </span>
    </div>
    <div class="p-6">
        <div class="flex flex-col md:flex-row gap-6 mb-6">
            <!-- Login ID Field -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-500 mb-1">ログインID</label>
                <div class="relative group">
                    <input type="text" readonly value="{{ $credentials['login_id'] }}" id="copy-login-id"
                        class="w-full bg-slate-50 border-slate-200 rounded-lg py-3 px-4 text-blue-700 font-mono font-bold text-lg focus:ring-0 focus:border-slate-200">
                    <button onclick="copyToClipboard('copy-login-id', 'btn-copy-id')" id="btn-copy-id"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-blue-600 bg-white shadow-sm border border-slate-100 rounded-md transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Password Field -->
            <div class="flex-1">
                <label class="block text-sm font-medium text-slate-500 mb-1">初期パスワード</label>
                <div class="relative group">
                    <input type="text" readonly value="{{ $credentials['password'] }}" id="copy-password"
                        class="w-full bg-slate-50 border-slate-200 rounded-lg py-3 px-4 text-slate-900 font-mono font-bold text-lg focus:ring-0 focus:border-slate-200">
                    <button onclick="copyToClipboard('copy-password', 'btn-copy-pw')" id="btn-copy-pw"
                        class="absolute right-2 top-1/2 -translate-y-1/2 p-2 text-slate-400 hover:text-blue-600 bg-white shadow-sm border border-slate-100 rounded-md transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="flex items-start space-x-3 p-4 bg-amber-50 rounded-lg border border-amber-100 italic">
            <svg class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-sm text-amber-800 leading-relaxed font-semibold">
                重要：このパスワードは一度しか表示されません。今すぐコピーして対象者に安全に伝えてください。画面を閉じると二度と確認できません。
            </p>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(inputId, btnId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(btnId);
        
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            const originalIcon = btn.innerHTML;
            btn.innerHTML = `
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            `;
            setTimeout(() => {
                btn.innerHTML = originalIcon;
            }, 2000);
        });
    }
</script>
@endif
