<div>
    <!-- Hero Section -->
    <header id="home" class="relative bg-dark-900 pt-24 pb-12 lg:pt-32 lg:pb-24 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1593640408182-31c70c8268f5?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80"
                alt="Background" class="w-full h-full object-cover opacity-30">
            <div class="absolute inset-0 bg-gradient-to-r from-dark-900 via-dark-900/80 to-transparent"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center">
            <div class="w-full md:w-1/2 text-center md:text-left mb-10 md:mb-0">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                    釋放你的 <span class="text-brand-500">極致效能</span>
                </h1>
                <p class="text-gray-300 text-lg mb-8 max-w-lg mx-auto md:mx-0">
                    專為創作者、工程師與電競玩家打造。Velocity PC 提供最新的處理器與顯卡配置，讓你的創意與遊戲體驗不再受限。
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="#products"
                        class="px-8 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-lg transition transform hover:scale-105 flex items-center justify-center">
                        立即選購 <i data-lucide="arrow-right" class="ml-2 h-5 w-5"></i>
                    </a>
                    <a href="#features"
                        class="px-8 py-3 bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-sm font-semibold rounded-lg transition flex items-center justify-center">
                        了解更多
                    </a>
                </div>
            </div>
            <!-- Hero Image/3D Element placeholder -->
            <div class="w-full md:w-1/2 flex justify-center">
                <img src="https://images.unsplash.com/photo-1603302576837-37561b2e2302?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80"
                    alt="High End Laptop"
                    class="rounded-xl shadow-2xl border-4 border-gray-800 transform rotate-1 hover:rotate-0 transition duration-500 max-w-xs md:max-w-md lg:max-w-lg">
            </div>
        </div>
    </header>

    <!-- 分類與商品區 -->
    <section id="products" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">熱門精選商品</h2>
                <div class="w-20 h-1 bg-brand-600 mx-auto rounded-full"></div>
                <p class="mt-4 text-gray-500">嚴選最高品質硬體，滿足各種需求</p>
            </div>

            <!-- Category Filters -->
            <div class="flex flex-wrap justify-center gap-2 mb-10">
                <button onclick="filterProducts('all')"
                    class="filter-btn active px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-brand-600 text-white shadow-md hover:shadow-lg ring-2 ring-brand-600 ring-offset-2">全部商品</button>
                <button onclick="filterProducts('laptop')"
                    class="filter-btn px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100 border border-gray-200">筆記型電腦</button>
                <button onclick="filterProducts('desktop')"
                    class="filter-btn px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100 border border-gray-200">桌上型主機</button>
                <button onclick="filterProducts('accessory')"
                    class="filter-btn px-6 py-2 rounded-full text-sm font-medium transition-all duration-300 bg-white text-gray-600 hover:bg-gray-100 border border-gray-200">周邊配件</button>
            </div>

            <!-- Product Grid -->
            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <!-- Products will be injected here by JS -->
            </div>
        </div>
    </section>

    <!-- 服務特色 -->
    <section id="features" class="py-16 bg-gray-50 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-100 text-brand-600 mb-6">
                        <i data-lucide="truck" class="h-8 w-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">極速出貨</h3>
                    <p class="text-gray-500">全台本島 24 小時內快速到貨，保護周全，讓您無需等待。</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-100 text-brand-600 mb-6">
                        <i data-lucide="shield-check" class="h-8 w-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">原廠保固</h3>
                    <p class="text-gray-500">所有商品皆為原廠公司貨，享有完整售後保固與技術支援。</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-sm hover:shadow-md transition text-center">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-brand-100 text-brand-600 mb-6">
                        <i data-lucide="headphones" class="h-8 w-8"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">專業諮詢</h3>
                    <p class="text-gray-500">不懂規格？我們的專業團隊隨時為您提供最適合的配置建議。</p>
                </div>
            </div>
        </div>
    </section>

    <!-- 訂閱 / CTA -->
    <section class="py-16 bg-dark-900 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-brand-600 rounded-full opacity-20 blur-3xl">
        </div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-purple-600 rounded-full opacity-20 blur-3xl">
        </div>

        <div class="max-w-4xl mx-auto px-4 relative z-10 text-center">
            <h2 class="text-3xl font-bold mb-4">準備好升級您的裝備了嗎？</h2>
            <p class="text-gray-400 mb-8">加入我們的會員，首次購物即可獲得 95 折優惠代碼。</p>
            <form class="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto"
                onsubmit="event.preventDefault(); alert('感謝訂閱！優惠碼已寄出。');">
                <input type="email" placeholder="輸入您的電子郵件"
                    class="px-5 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-brand-500 w-full">
                <button type="submit"
                    class="px-6 py-3 bg-brand-600 hover:bg-brand-700 rounded-lg font-bold transition whitespace-nowrap">
                    訂閱電子報
                </button>
            </form>
        </div>
    </section>

</div>
