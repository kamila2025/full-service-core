<nav class="fixed w-full z-50 glass-nav transition-all duration-300" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center cursor-pointer" onclick="window.scrollTo(0,0)">
                <i data-lucide="cpu" class="h-8 w-8 text-brand-600 mr-2"></i>
                <span class="font-bold text-xl tracking-wide text-gray-900">Velocity<span
                        class="text-brand-600">PC</span></span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-8 items-center">
                <a href="#home" class="text-gray-600 hover:text-brand-600 font-medium transition">首頁</a>
                <a href="#products" class="text-gray-600 hover:text-brand-600 font-medium transition">熱銷商品</a>
                <a href="#features" class="text-gray-600 hover:text-brand-600 font-medium transition">服務特色</a>
                <a href="#contact" class="text-gray-600 hover:text-brand-600 font-medium transition">聯絡我們</a>
            </div>

            <!-- Icons -->
            <div class="flex items-center space-x-4">
                <button class="relative p-2 text-gray-600 hover:text-brand-600 transition" onclick="toggleCart()">
                    <i data-lucide="shopping-cart" class="h-6 w-6"></i>
                    <span id="cart-count"
                        class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-brand-600 rounded-full opacity-0 transition-opacity">0</span>
                </button>
                <!-- Mobile menu button -->
                <button class="md:hidden p-2 text-gray-600" onclick="toggleMobileMenu()">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Panel -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100">
        <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
            <a href="#home"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-600 hover:bg-gray-50">首頁</a>
            <a href="#products"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-600 hover:bg-gray-50">熱銷商品</a>
            <a href="#features"
                class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-brand-600 hover:bg-gray-50">服務特色</a>
        </div>
    </div>
</nav>
