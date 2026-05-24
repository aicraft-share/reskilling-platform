<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            Zoom連携設定
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                    <p class="font-bold">成功</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">エラー</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-slate-900">
                    <h3 class="text-lg font-medium text-slate-900 mb-4">接続設定状況</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <h4 class="font-semibold text-slate-700 mb-2">現在の設定値 (.env)</h4>
                            <dl class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Account ID:</dt>
                                    <dd class="font-mono">
                                        {{ config('services.zoom.account_id') ? '設定済み (' . substr(config('services.zoom.account_id'), 0, 4) . '***)' : '未設定' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Client ID:</dt>
                                    <dd class="font-mono">
                                        {{ config('services.zoom.client_id') ? '設定済み (' . substr(config('services.zoom.client_id'), 0, 4) . '***)' : '未設定' }}
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Client Secret:</dt>
                                    <dd class="font-mono">
                                        {{ config('services.zoom.client_secret') ? '設定済み (***)' : '未設定' }}</dd>
                                </div>
                            </dl>
                            <p class="text-xs text-slate-400 mt-2">※ 値を変更する場合はサーバーの環境変数を更新してください。</p>
                        </div>

                        <div
                            class="flex items-center justify-center bg-slate-50 p-4 rounded-lg border border-slate-200">
                            <form action="{{ route('admin.zoom-settings.test') }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150 ease-in-out shadow-sm flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    接続テストを実行
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-6">
                        <h3 class="text-lg font-medium text-slate-900 mb-2">設定方法</h3>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-slate-600">
                            <li><a href="https://marketplace.zoom.us/" target="_blank"
                                    class="text-blue-600 hover:underline">Zoom Marketplace</a> にログインします。</li>
                            <li><strong>Develop > Build App</strong> から <strong>Server-to-Server OAuth</strong>
                                アプリを作成します。</li>
                            <li>生成された <strong>Account ID, Client ID, Client Secret</strong> を <code>.env</code>
                                ファイルに設定します。</li>
                            <li>「接続テストを実行」ボタンを押して、API連携が正しく行われるか確認します。</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>