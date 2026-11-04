<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Authentication — ATOZ GADGETZ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#111110] min-h-screen flex items-center justify-center p-4 antialiased text-white font-sans selection:bg-white selection:text-black">
    <div class="w-full max-w-md bg-[#1C1B19] rounded-3xl border border-neutral-800 p-8 sm:p-10 shadow-2xl">
        <div class="text-center mb-8">
            <img src="{{ asset('assets/images/logo.jpeg') }}" class="w-16 h-16 rounded-full mx-auto mb-4 border border-neutral-700 shadow-md" alt="ATOZ Logo">
            <h1 class="text-xl font-extrabold tracking-tight uppercase">Staff Concierge</h1>
            <p class="text-xs text-neutral-400 mt-1">Authorized Administrative Access Only</p>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs">
            @foreach($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-semibold text-neutral-300 uppercase tracking-wider mb-2">Work Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email', 'admin@atozgadgetz.com') }}" 
                       required 
                       autofocus
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs text-white placeholder-neutral-500 focus:outline-none focus:border-white transition-colors">
            </div>

            <div>
                <label for="password" class="block text-xs font-semibold text-neutral-300 uppercase tracking-wider mb-2">Master Key</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       value="Admin@12345"
                       required 
                       class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs text-white placeholder-neutral-500 focus:outline-none focus:border-white transition-colors">
            </div>

            <div class="flex items-center justify-between text-xs text-neutral-400 pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-neutral-900 border-neutral-800 text-white focus:ring-0">
                    <span>Keep signed in</span>
                </label>
                <span class="text-[11px] text-neutral-500">Encrypted AES-256</span>
            </div>

            <button type="submit" 
                    class="w-full bg-white hover:bg-neutral-200 text-black font-bold uppercase tracking-wider text-xs py-3.5 rounded-full mt-4 transition-all shadow-lg active:scale-95">
                Authenticate &rarr;
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-neutral-800/80 text-center text-[11px] text-neutral-500">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">&larr; Return to Public Storefront</a>
        </div>
    </div>
</body>
</html>
