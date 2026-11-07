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
<body class="bg-burnished-gold-hero min-h-screen flex items-center justify-center p-4 antialiased text-[#0B0A08] font-sans selection:bg-[#0B0A08] selection:text-[#FCE08C]">
    <div class="w-full max-w-md bg-[#0B0A08] rounded-3xl border-2 border-[#876211] p-8 sm:p-10 shadow-2xl backdrop-blur-md">
        <div class="text-center mb-8">
            <img src="{{ asset('assets/images/logo.jpeg') }}" class="w-16 h-16 rounded-full mx-auto mb-4 border-2 border-[#876211] shadow-lg" alt="ATOZ Logo">
            <h1 class="text-xl font-black tracking-tight uppercase text-[#FCE08C]">Staff Concierge</h1>
            <p class="text-xs text-[#E2BC50]/80 mt-1 font-semibold">Authorized Administrative Access Only</p>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs font-semibold">
            @foreach($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold text-[#E2BC50] uppercase tracking-wider mb-2">Work Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="{{ old('email') }}" 
                       required 
                       autofocus
                       placeholder="benzo@atozind.com"
                       class="w-full bg-[#1A1813] border-2 border-[#876211]/60 rounded-xl px-4 py-3 text-xs text-white placeholder-[#876211] focus:outline-none focus:border-[#FCE08C] focus:ring-1 focus:ring-[#FCE08C] transition-colors font-medium">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-[#E2BC50] uppercase tracking-wider mb-2">Master Key</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required 
                       placeholder="••••••••••••"
                       class="w-full bg-[#1A1813] border-2 border-[#876211]/60 rounded-xl px-4 py-3 text-xs text-white placeholder-[#876211] focus:outline-none focus:border-[#FCE08C] focus:ring-1 focus:ring-[#FCE08C] transition-colors font-medium">
            </div>

            <div class="flex items-center justify-between text-xs text-[#E2BC50]/80 pt-1 font-medium">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded bg-[#1A1813] border-[#876211] text-[#C99F2E] focus:ring-0">
                    <span>Keep signed in</span>
                </label>
                <span class="text-[11px] text-[#E2BC50]/60">Encrypted AES-256</span>
            </div>

            <button type="submit" 
                    class="w-full btn-gold uppercase tracking-wider text-xs py-3.5 rounded-full mt-4 transition-all shadow-xl active:scale-95 font-black text-[#0B0A08]">
                Authenticate &rarr;
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-[#876211]/40 text-center text-[11px] text-[#E2BC50]/70 font-semibold">
            <a href="{{ route('home') }}" class="hover:text-[#FCE08C] transition-colors">&larr; Return to Public Storefront</a>
        </div>
    </div>
</body>
</html>
