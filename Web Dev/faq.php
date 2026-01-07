<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - ProManage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #020617; }
        .hero-mesh {
            background-color: #020617;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 64, 175, 0.3) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.2) 0, transparent 50%);
            min-height: 100vh;
        }
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .nav-glass {
            background: rgba(2, 6, 23, 0.7);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="text-slate-200 antialiased">

    <div class="hero-mesh flex flex-col min-h-screen">
        
        <nav class="nav-glass sticky top-0 z-50 py-4 px-6 sm:px-12 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                </div>
                <span class="text-xl font-bold text-white">ProManage</span>
            </div>
            <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
                <a href="index.php" class="hover:text-blue-400 transition">Home</a>
                <a href="aboutUs.php" class="hover:text-blue-400 transition">About Us</a>
                <a href="faq.php" class="text-blue-400 border-b-2 border-blue-400 pb-1">FAQ</a>
            </div>
            <a href="login.php" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-full text-sm font-bold transition shadow-lg shadow-blue-500/20">
                Sign In
            </a>
        </nav>

        <main class="flex-grow py-20 px-6 sm:px-12">
            <div class="max-w-4xl mx-auto">
                
                <div class="text-center mb-16">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest mb-6">
                        Support Center
                    </div>
                    <h1 class="text-5xl font-extrabold text-white mb-6">Common Questions</h1>
                    <p class="text-slate-400 text-lg">Everything you need to know about the ProManage ecosystem.</p>
                </div>

                <div class="space-y-4">
                    
                    <div class="glass p-6 rounded-3xl group cursor-pointer hover:bg-white/5 transition-all">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white">How safe is my payment data?</h3>
                            <i data-lucide="plus" class="w-5 h-5 text-blue-400 transition-transform group-hover:rotate-45"></i>
                        </div>
                        <p class="mt-4 text-slate-400 text-sm leading-relaxed hidden group-hover:block animate-in fade-in slide-in-from-top-2">
                            We use industry-standard SSL encryption and partner with verified payment gateways to ensure every transaction is secure and traceable.
                        </p>
                    </div>

                    <div class="glass p-6 rounded-3xl group cursor-pointer hover:bg-white/5 transition-all">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white">Can I manage multiple properties?</h3>
                            <i data-lucide="plus" class="w-5 h-5 text-blue-400 transition-transform group-hover:rotate-45"></i>
                        </div>
                        <p class="mt-4 text-slate-400 text-sm leading-relaxed hidden group-hover:block animate-in fade-in slide-in-from-top-2">
                            Absolutely. The Owner Dashboard is built for scaling. You can add, edit, and track revenue for an unlimited number of units from one account.
                        </p>
                    </div>

                    <div class="glass p-6 rounded-3xl group cursor-pointer hover:bg-white/5 transition-all">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-white">How do tenants sign the agreement?</h3>
                            <i data-lucide="plus" class="w-5 h-5 text-blue-400 transition-transform group-hover:rotate-45"></i>
                        </div>
                        <p class="mt-4 text-slate-400 text-sm leading-relaxed hidden group-hover:block animate-in fade-in slide-in-from-top-2">
                            Once an owner creates a lease, the tenant receives a notification in their dashboard to review and digitally accept the terms.
                        </p>
                    </div>

                </div>

                <div class="mt-16 glass p-8 rounded-[2.5rem] border border-blue-500/20 text-center">
                    <h3 class="text-xl font-bold text-white mb-2">Still have questions?</h3>
                    <p class="text-slate-400 text-sm mb-6">Our support team is available 24/7 to help you.</p>
                    <a href="mailto:proManage@gmail.com" class="inline-flex items-center gap-2 text-blue-400 font-bold hover:text-blue-300">
                        Contact Support <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

            </div>
        </main>

        <footer class="py-8 text-center text-slate-500 text-xs border-t border-slate-900">
            &copy; 2024 ProManage. Built for better living.
        </footer>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>