<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            権限管理 (管理者一覧)
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <x-credentials-card :credentials="session('generated_credentials')" />

            <!-- Success Message -->
            @if(session('success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-emerald-700 font-medium">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add New Admin Form -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50">
                            <h3 class="text-lg font-medium text-slate-800">新規管理者追加</h3>
                            <p class="text-sm text-slate-500 mt-1">システム管理者を新規で招待・登録します。</p>
                        </div>
                        <div class="p-5">
                            <form method="POST" action="{{ route('admin.admins.store') }}" class="space-y-4">
                                @csrf

                                <div>
                                    <label for="name" class="block text-sm font-medium text-slate-700">氏名</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-slate-700">メールアドレス</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <div class="p-4 bg-indigo-50 rounded-lg text-xs text-indigo-700">
                                        <p class="font-bold mb-1">ログイン情報の自動生成</p>
                                        <p>ログインIDとパスワードは登録時に自動的に生成されます。登録完了後のメッセージに表示されますので、必ず控えて管理者の方へお伝えください。</p>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <button type="submit"
                                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        登録する
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Admins List -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                            <h3 class="text-lg font-medium text-slate-800">管理者一覧</h3>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800">
                                全 {{ $admins->total() }} 名
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-white">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            ログインID</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            氏名 / Email</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            登録日</th>
                                        <th scope="col"
                                            class="px-6 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                            操作</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-200">
                                    @foreach($admins as $admin)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-700 font-mono">
                                                {{ $admin->login_id }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                                        {{ Str::substr($admin->name, 0, 1) }}
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-slate-900">{{ $admin->name }}
                                                            @if($admin->id === auth()->id())
                                                                <span
                                                                    class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">あなた</span>
                                                            @endif
                                                        </div>
                                                        <div class="text-xs text-slate-500">{{ $admin->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                                {{ $admin->created_at->format('Y/m/d H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.admins.show', $admin) }}"
                                                    class="text-indigo-600 hover:text-indigo-900 font-medium px-3 py-2 border border-slate-200 rounded-md hover:bg-slate-50">
                                                    詳細
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                            {{ $admins->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>