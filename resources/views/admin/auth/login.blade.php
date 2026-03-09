<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    @vite(['resources/css/admin.css'])
</head>

<body class="min-h-screen bg-slate-50 flex flex-col">

    <main class="flex flex-1 items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="flex justify-center mb-4">
                <img src="{{ asset('logo.svg') }}" alt="ORION" class="h-16 w-auto">
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-8">
                <h2 class="text-center text-xl font-semibold text-gray-800 mb-6">Admin Login</h2>

                @if($errors->any())
                    <div
                        class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-r-lg animate-pulse">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-slate-500 outline-none transition-all duration-200 placeholder:text-gray-400"
                            placeholder="Enter your username">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-slate-500 outline-none transition-all duration-200"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 text-admin-primary focus:ring-admin-primary">
                            <span class="text-sm text-gray-600">Remember me</span>
                        </label>
                    </div>

                    <button type="submit"
                        class="w-full bg-admin-blue hover:bg-admin-hover text-white font-semibold py-3.5 rounded-xl cursor-pointer transition-all duration-300 active:scale-[0.98]">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </main>

    <footer class="text-center text-gray-400 text-xs mb-8">
        Copyright &copy; {{ date('Y') }}
        <span class="font-medium">ORION</span>. All Rights Reserved.
        Design & Developed by:
        <span class="font-medium">Information Technology (IT), ORION</span>.
    </footer>

</body>

</html>