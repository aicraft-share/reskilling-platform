<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Craft Reskilling') }}</title>
    <link rel="icon" href="{{ asset('favicon.png') }}?v={{ time() }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media (min-width: 1024px) {
            #app-sidebar {
                position: sticky !important;
                top: 0;
                height: 100vh;
                align-self: flex-start;
            }
        }
    </style>
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen bg-slate-50 relative">
        <!-- Sidebar Overlay (Mobile) -->
        <div x-show="sidebarOpen" 
             x-cloak
             x-transition:enter="transition ease-in-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in-out duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[60] lg:hidden"></div>

        <!-- Sidebar -->
        <aside id="app-sidebar" :class="sidebarOpen ? 'translate-x-0' : ''"
               class="fixed lg:static inset-y-0 left-0 z-[70] w-[260px] bg-white border-r border-slate-200 flex flex-col transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 overflow-y-auto">
            
            <!-- Close Button (Mobile) -->
            <div class="lg:hidden absolute top-4 right-4">
                <button @click="sidebarOpen = false" class="p-2 rounded-md text-slate-400 hover:text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Logo -->
            @php
                $isAdminPortal = request()->is('admin*') || request()->is('teacher*');
                $logoutRoute = $isAdminPortal ? route('admin.logout') : route('logout');
                $displayUser = ($isAdminPortal && Auth::guard('admin')->check()) ? Auth::guard('admin')->user() : (Auth::guard('web')->user() ?? Auth::guard('admin')->user());
                $dashboardRoute = $isAdminPortal ? ($displayUser->isAdmin() ? route('admin.dashboard') : route('teacher.dashboard')) : ($displayUser->isCompany() ? route('company.dashboard') : route('student.dashboard'));
            @endphp
            <div class="h-24 flex items-center px-6 border-b border-slate-200 flex-shrink-0">
                <a href="{{ $dashboardRoute }}" class="block">
                    <img src="{{ asset('logo.png') }}?v={{ time() }}" alt="AI Craft Reskilling"
                        class="w-auto h-auto max-h-20 max-w-full">
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto custom-scrollbar">
                @php
                    $user = $displayUser;
                @endphp

                @if($user->isAdmin())
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')"
                        icon="home">
                        ダッシュボード
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">管理</p>
                    </div>
                    <x-nav-link :href="route('admin.companies.index')" :active="request()->routeIs('admin.companies.*')"
                        icon="building">
                        企業管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.teachers.index')" :active="request()->routeIs('admin.teachers.*')"
                        icon="users">
                        講師管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.students.index')" :active="request()->routeIs('admin.students.*')"
                        icon="academic-cap">
                        生徒管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.assignments.index')" :active="request()->routeIs('admin.assignments.*')"
                        icon="clipboard-list">
                        課題管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.courses.index')"
                        :active="request()->routeIs('admin.courses.*')" icon="collection">
                        コース管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.lecture-pages.index')"
                        :active="request()->routeIs('admin.lecture-pages.*')" icon="book-open">
                        教材管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.admins.index')" :active="request()->routeIs('admin.admins.*')"
                        icon="shield-check">
                        権限管理
                    </x-nav-link>
                    <x-nav-link :href="route('admin.operation-logs.index')"
                        :active="request()->routeIs('admin.operation-logs.*')" icon="document-text">
                        操作ログ
                    </x-nav-link>
                    <x-nav-link :href="route('admin.announcements.index')"
                        :active="request()->routeIs('admin.announcements.*')" icon="bell">
                        お知らせ配信
                    </x-nav-link>
                    <x-nav-link :href="route('admin.meetings.index')" :active="request()->routeIs('admin.meetings.*')"
                        icon="video-camera">
                        MTG (Scheduled)
                    </x-nav-link>
                    <x-nav-link :href="route('admin.meeting-logs.index')"
                        :active="request()->routeIs('admin.meeting-logs.*')" icon="document-text">
                        MTGログ (実績)
                    </x-nav-link>

                    <x-nav-link :href="route('admin.mtgs.export')" :active="request()->routeIs('admin.mtgs.export')"
                        icon="chart-bar">
                        レポート (CSV)
                    </x-nav-link>

                    <x-nav-link :href="route('chats.index')" :active="request()->routeIs('chats.*')" icon="chat">
                        チャット管理
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">システム</p>
                    </div>
                    <x-nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*') || request()->routeIs('admin.lecture-pages.*')" icon="cog">
                        設定
                    </x-nav-link>

                @elseif($user->isTeacher())
                    <x-nav-link :href="route('teacher.dashboard')" :active="request()->routeIs('teacher.dashboard')"
                        icon="home">
                        ホーム
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.submissions.index', ['status' => 'submitted'])"
                        :active="request()->routeIs('teacher.submissions.*')" icon="check-circle">
                        提出レビュー
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.feedbacks.index')" :active="request()->routeIs('teacher.feedbacks.*')"
                        icon="chat-alt-2">
                        フィードバック
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">管理</p>
                    </div>
                    <x-nav-link :href="route('teacher.assignments.students')"
                        :active="request()->routeIs('teacher.assignments.*') || request()->routeIs('teacher.students.*') || request()->routeIs('teacher.companies.*')" icon="academic-cap">
                        担当
                    </x-nav-link>
                    <x-nav-link :href="url('/teacher/announcements')"
                        :active="request()->is('teacher/announcements*')" icon="bell">
                        お知らせ
                    </x-nav-link>
                    <x-nav-link :href="route('teacher.meetings.index')" :active="request()->routeIs('teacher.meetings.*')"
                        icon="video-camera">
                        MTG
                    </x-nav-link>
                    <x-nav-link :href="route('chats.index')" :active="request()->routeIs('chats.*')" icon="chat">
                        チャット
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">アカウント</p>
                    </div>
                    <x-nav-link :href="route('teacher.settings.edit')" :active="request()->routeIs('teacher.settings.*')"
                        icon="cog">
                        設定
                    </x-nav-link>

                @elseif($user->isStudent())
                    <x-nav-link :href="route('student.dashboard')" :active="request()->routeIs('student.dashboard')"
                        icon="home">
                        ダッシュボード
                    </x-nav-link>
                    <x-nav-link :href="route('chats.index')" :active="request()->routeIs('chats.*')" icon="chat">
                        講師チャット
                    </x-nav-link>
                    <x-nav-link :href="route('student.meetings.index')" :active="request()->routeIs('student.meetings.*')"
                        icon="video-camera">
                        MTG
                    </x-nav-link>
                    <x-nav-link :href="route('student.courses.index')" :active="request()->routeIs('student.courses.*')"
                        icon="book-open">
                        講義一覧
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">お知らせ</p>
                    </div>
                    <x-nav-link :href="url('/student/announcements')"
                        :active="request()->is('student/announcements*')" icon="bell">
                        お知らせ
                    </x-nav-link>
                    <x-nav-link :href="route('student.feedbacks.index')" :active="request()->routeIs('student.feedbacks.*')"
                        icon="chat-alt-2">
                        フィードバック
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">アカウント</p>
                    </div>
                    <x-nav-link :href="route('student.settings.edit')" :active="request()->routeIs('student.settings.*')"
                        icon="cog">
                        設定
                    </x-nav-link>

                @elseif($user->isCompany())
                    <x-nav-link :href="url('/company/announcements')"
                        :active="request()->is('company/announcements*') || request()->routeIs('company.dashboard')" icon="bell">
                        お知らせ
                    </x-nav-link>

                    <x-nav-link :href="route('company.students.index')" :active="request()->routeIs('company.students.*')" icon="academic-cap">
                        生徒管理
                    </x-nav-link>

                    <x-nav-link :href="route('chats.index')" :active="request()->routeIs('chats.*')" icon="chat">
                        生徒チャット管理
                    </x-nav-link>

                    <div class="pt-4 pb-2">
                        <p class="px-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">アカウント</p>
                    </div>
                    <x-nav-link :href="route('company.settings.show')" :active="request()->routeIs('company.settings.*')"
                        icon="cog">
                        設定
                    </x-nav-link>
                @endif
            </nav>

            <!-- User Profile (Sidebar Bottom) -->
            <div class="p-4 border-t border-slate-200 bg-slate-50/50">
                <div class="flex items-center">
                    @if($displayUser->avatar_path)
                        <img class="h-9 w-9 rounded-full object-cover border border-slate-200" src="{{ asset('storage/' . $displayUser->avatar_path) }}" alt="{{ $displayUser->name }}">
                    @else
                        <div class="h-9 w-9 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-base">
                            {{ mb_substr($displayUser->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="ml-3 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ $displayUser->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ $displayUser->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ $logoutRoute }}" class="mt-3">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-3 py-2 text-xs font-medium text-slate-600 hover:text-red-600 hover:bg-red-50 rounded-lg border border-slate-200 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        ログアウト
                    </button>
                </form>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Mobile Header (Non-fixed flow) -->
            <div class="lg:hidden flex-shrink-0 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 z-40">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="p-2 rounded-md text-slate-600 hover:bg-slate-100 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="ml-4 h-8">
                        <a href="{{ $dashboardRoute }}" class="block h-full">
                            <img src="{{ asset('logo.png') }}?v={{ time() }}" alt="Logo" class="h-full w-auto">
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    @if(Auth::user()->avatar_path)
                        <img class="h-8 w-8 rounded-full object-cover border border-slate-200" src="{{ asset('storage/' . Auth::user()->avatar_path) }}" alt="{{ Auth::user()->name }}">
                    @else
                        <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm">
                            {{ mb_substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col min-w-0 bg-slate-50">
            <!-- Minimal Header -->
            @if (isset($header))
                <header class="bg-white border-b border-slate-200 flex-shrink-0 sticky top-0 z-30">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <div class="flex-1 p-4 pb-28 sm:p-6 lg:p-8">
                <div class="max-w-7xl mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

        <!-- Global Preview Modal -->
        <div x-data="{ showPreview: false, previewUrl: '', previewName: '' }"
             @open-preview.window="showPreview = true; previewUrl = $event.detail.url; previewName = $event.detail.name">
            <div x-show="showPreview" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-slate-900 bg-opacity-75 transition-opacity backdrop-blur-sm"
                         x-show="showPreview" x-transition.opacity @click="showPreview = false" aria-hidden="true"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle w-full max-w-5xl h-[85vh] flex flex-col"
                         x-show="showPreview" x-transition.scale.95>
                        <div class="px-4 py-3 sm:px-6 border-b border-slate-200 flex justify-between items-center bg-white">
                            <h3 class="text-base sm:text-lg font-bold text-slate-800 truncate pr-4" id="modal-title" x-text="previewName"></h3>
                            <button @click="showPreview = false" type="button" class="text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded p-1 transition">
                                <span class="sr-only">閉じる</span>
                                <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1 bg-slate-100 p-2 sm:p-4">
                            <iframe :src="previewUrl" class="w-full h-full border-0 bg-white shadow-sm rounded-lg" title="File Preview"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; height: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
    </style>

    @stack('modals')
    @stack('scripts')
</body>
</html>
