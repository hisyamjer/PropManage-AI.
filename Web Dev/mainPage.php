<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProManage - Modern Rental Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #020617; /* Slate 950 */
        }

        /* Animated Gradient Background */
        .hero-mesh {
            background-color: #020617;
            background-image: 
                radial-gradient(at 0% 0%, rgba(30, 64, 175, 0.3) 0, transparent 50%), 
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.2) 0, transparent 50%);
            min-height: 100vh;
        }
        
        /* Glassmorphism Effect */
        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-glass {
            background: rgba(2, 6, 23, 0.7);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Subtle floating animation for the video container */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        .float-element {
            animation: float 6s ease-in-out infinite;
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
                <span class="text-xl font-bold tracking-tight text-white">ProManage</span>
            </div>

            <div class="hidden md:flex items-center space-x-8 text-sm font-medium">
                <a href="mainPage.php" class="hover:text-blue-400 transition-colors">Home</a>
                <a href="aboutUs.php" class="hover:text-blue-400 transition-colors">About Us</a>
                <a href="faq.php" class="hover:text-blue-400 transition-colors">FAQ</a>
            </div>

            <div class="flex items-center gap-4">
                <a href="login.php" class="text-sm font-semibold hover:text-white transition-colors">Sign In</a>
                <a href="register.php" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-full text-sm font-bold transition-all shadow-lg shadow-blue-500/20">
                    Get Started
                </a>
            </div>
        </nav>

        <main class="flex-grow flex items-center">
            <div class="max-w-7xl mx-auto px-6 sm:px-12 grid lg:grid-cols-2 gap-16 py-12">
                
                <div class="flex flex-col justify-center space-y-8">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-bold uppercase tracking-widest w-fit">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                        Next-Gen Management
                    </div>

                    <h1 class="text-5xl sm:text-7xl font-extrabold tracking-tight text-white leading-[1.1]">
                        Manage Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Empire</span> Effortlessly.
                    </h1>

                    <p class="text-lg text-slate-400 max-w-lg leading-relaxed">
                        The all-in-one platform for modern landlords. Automate rent collection, sync with tenants, and track maintenance in one unified dashboard.
                    </p>

                    <div class="flex flex-wrap gap-4">
                        <a href="register.php" class="px-8 py-4 bg-white text-slate-950 font-bold rounded-2xl hover:bg-blue-50 transition-all flex items-center gap-2 group">
                            Register Now
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <div class="flex flex-col justify-center">
                            <div class="flex -space-x-2">
                                <img src="https://i.pravatar.cc/100?u=1" class="w-8 h-8 rounded-full border-2 border-slate-950">
                                <img src="https://i.pravatar.cc/100?u=2" class="w-8 h-8 rounded-full border-2 border-slate-950">
                                <img src="https://i.pravatar.cc/100?u=3" class="w-8 h-8 rounded-full border-2 border-slate-950">
                            </div>
                            <p class="text-[10px] text-slate-500 mt-1 font-bold uppercase tracking-tighter">Trusted by 500+ Landlords</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-6 pt-4 border-t border-slate-800/50">
                        <div class="flex items-center gap-3 text-slate-400 hover:text-blue-400 transition-colors cursor-pointer group">
                            <div class="p-2 glass rounded-lg group-hover:bg-blue-500/10">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm">proManage@gmail.com</span>
                        </div>
                        <div class="flex items-center gap-3 text-slate-400 hover:text-blue-400 transition-colors cursor-pointer group">
                            <div class="p-2 glass rounded-lg group-hover:bg-blue-500/10">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                            </div>
                            <span class="text-sm">+06 345 8763</span>
                        </div>
                    </div>
                </div>
                
                <div class="relative flex items-center justify-center">
                    <div class="absolute w-72 h-72 bg-blue-600/20 blur-[100px] rounded-full"></div>
                    
                    <div class="float-element glass p-2 rounded-[2.5rem] w-full max-w-lg relative z-10">
                        <div class="aspect-video bg-slate-900 rounded-[2rem] overflow-hidden flex items-center justify-center border border-white/5">
                            <video 
                                src="Media/vid.mp4" 
                                controls 
                                autoplay 
                                loop 
                                muted 
                                class="w-full h-full object-cover"
                            ></video>
                        </div>
                        
                        <div class="absolute -bottom-6 -left-6 glass p-4 rounded-2xl shadow-2xl hidden sm:flex items-center gap-4 border border-white/10">
                            <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-5 h-5 text-green-500"></i>
                            </div>
                            <div>
                                <p class="text-[10px] text-slate-400 uppercase font-bold">Revenue Growth</p>
                                <p class="text-lg font-bold text-white">+24.8%</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <footer class="py-8 px-6 sm:px-12 border-t border-slate-900">
            <div class="max-w-7xl mx-auto flex flex-col sm:flex-row justify-between items-center gap-4 text-slate-500 text-xs font-medium">
                <p>&copy; 2024 ProManage. Excellence in Real Estate Tech.</p>
                <div class="flex gap-6">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>

    <script>
        // Initialize Lucide Icons
        lucide.createIcons();
    </script>
</body>
</html>