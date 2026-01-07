<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>極速電腦 Velocity PC | 頂級電腦專賣</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6', // 主要藍色
                            600: '#2563eb',
                            900: '#1e3a8a', // 深藍
                        },
                        dark: {
                            800: '#1f2937',
                            900: '#111827',
                        }
                    },
                    fontFamily: {
                        sans: ['Noto Sans TC', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* 自定義樣式補充 */
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* 隱藏滾動條但保持功能 */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans antialiased flex flex-col min-h-screen">
    <!-- 導航列 -->
    @include('components.layouts.navbar')

    <main class="flex-grow">
        {{ $slot }}
    </main>

    <!-- Footer -->
    @include('components.layouts.footer')

    <!-- 購物車 Modal (隱藏) -->
    <div id="cart-overlay" class="fixed inset-0 bg-black/50 z-50 hidden transition-opacity" onclick="toggleCart()">
    </div>
    <div id="cart-drawer"
        class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white z-[60] transform translate-x-full transition-transform duration-300 shadow-2xl flex flex-col">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-900 flex items-center">
                <i data-lucide="shopping-bag" class="h-5 w-5 mr-2 text-brand-600"></i>
                購物車
            </h2>
            <button onclick="toggleCart()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="h-6 w-6"></i>
            </button>
        </div>

        <!-- Cart Items Area -->
        <div id="cart-items" class="flex-1 overflow-y-auto p-4 space-y-4">
            <!-- Empty State -->
            <div class="h-full flex flex-col items-center justify-center text-gray-400">
                <i data-lucide="shopping-cart" class="h-12 w-12 mb-3 opacity-20"></i>
                <p>您的購物車是空的</p>
                <button onclick="toggleCart()" class="mt-4 text-brand-600 font-medium hover:underline">去逛逛</button>
            </div>
        </div>

        <!-- Footer / Checkout -->
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            <div class="flex justify-between mb-4 text-lg font-bold text-gray-900">
                <span>總計</span>
                <span id="cart-total">NT$ 0</span>
            </div>
            <button
                class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-lg transition shadow-lg flex justify-center items-center"
                onclick="alert('進入結帳流程...')">
                前往結帳
            </button>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast"
        class="fixed bottom-5 right-5 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-20 opacity-0 transition-all duration-300 z-[70] flex items-center">
        <i data-lucide="check-circle" class="h-5 w-5 text-green-400 mr-2"></i>
        <span id="toast-message">已加入購物車</span>
    </div>

    <!-- JavaScript 邏輯 -->
    <script>
        // 初始化 Icons
        lucide.createIcons();

        // 購物車狀態（全局）
        window.cart = [];

        // 格式化價格（全局）
        window.formatPrice = (price) => {
            return new Intl.NumberFormat('zh-TW', {
                style: 'currency',
                currency: 'TWD',
                minimumFractionDigits: 0
            }).format(price);
        };

        // 更新購物車 UI（全局）
        window.updateCartUI = function() {
            // 更新數量徽章
            const countEl = document.getElementById('cart-count');
            if (countEl) {
                countEl.innerText = window.cart.length;
                countEl.classList.remove('opacity-0');
                if (window.cart.length === 0) countEl.classList.add('opacity-0');
            }

            // 計算總金額
            const total = window.cart.reduce((sum, item) => sum + item.price, 0);
            const totalEl = document.getElementById('cart-total');
            if (totalEl) {
                totalEl.innerText = window.formatPrice(total);
            }

            // 更新列表
            const list = document.getElementById('cart-items');
            if (list) {
                if (window.cart.length === 0) {
                    list.innerHTML = `
                        <div class="h-full flex flex-col items-center justify-center text-gray-400">
                            <i data-lucide="shopping-cart" class="h-12 w-12 mb-3 opacity-20"></i>
                            <p>您的購物車是空的</p>
                            <button onclick="toggleCart()" class="mt-4 text-brand-600 font-medium hover:underline">去逛逛</button>
                        </div>
                    `;
                } else {
                    list.innerHTML = window.cart.map((item, index) => `
                        <div class="flex gap-4 items-center bg-gray-50 p-3 rounded-lg">
                            <img src="${item.image}" class="w-16 h-16 object-cover rounded-md border border-gray-200">
                            <div class="flex-grow">
                                <h4 class="font-bold text-sm text-gray-900">${item.name}</h4>
                                <p class="text-brand-600 font-medium text-sm">${window.formatPrice(item.price)}</p>
                            </div>
                            <button onclick="removeFromCart(${index})" class="text-gray-400 hover:text-red-500 p-1">
                                <i data-lucide="trash-2" class="h-4 w-4"></i>
                            </button>
                        </div>
                    `).join('');
                }
                lucide.createIcons();
            }
        };

        // 移除購物車項目（全局）
        window.removeFromCart = function(index) {
            window.cart.splice(index, 1);
            window.updateCartUI();
        };

        // 開關購物車（全局）
        window.toggleCart = function() {
            const overlay = document.getElementById('cart-overlay');
            const drawer = document.getElementById('cart-drawer');

            if (drawer.classList.contains('translate-x-full')) {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    drawer.classList.remove('translate-x-full');
                    overlay.classList.add('opacity-100');
                }, 10);
            } else {
                drawer.classList.add('translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        };

        // 手機選單（全局）
        window.toggleMobileMenu = function() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        };

        // Toast 通知（全局）
        window.showToast = function(message) {
            const toast = document.getElementById('toast');
            const msg = document.getElementById('toast-message');
            if (toast && msg) {
                msg.innerText = message;
                toast.classList.remove('translate-y-20', 'opacity-0');
                setTimeout(() => {
                    toast.classList.add('translate-y-20', 'opacity-0');
                }, 3000);
            }
        };

        // 商品數據（僅在首頁使用）
        window.products = [{
                id: 1,
                name: "Velocity Pro X15",
                category: "laptop",
                price: 45900,
                image: "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "i7-13700H / RTX 4060 / 16GB RAM / 1TB SSD"
            },
            {
                id: 2,
                name: "Stealth Air 13",
                category: "laptop",
                price: 32900,
                image: "https://images.unsplash.com/photo-1517336714731-489689fd1ca8?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "Ultra 7 / Iris Xe / 1.1kg 極輕薄"
            },
            {
                id: 3,
                name: "Titan Tower GT",
                category: "desktop",
                price: 68900,
                image: "https://images.unsplash.com/photo-1587202372775-e229f172b9d7?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "i9-14900K / RTX 4080 / 水冷散熱"
            },
            {
                id: 4,
                name: "Office Mate S1",
                category: "desktop",
                price: 18900,
                image: "https://images.unsplash.com/photo-1593640408182-31c70c8268f5?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "i5-12400 / 8GB RAM / 商業文書首選"
            },
            {
                id: 5,
                name: "MechKey RGB Pro",
                category: "accessory",
                price: 3490,
                image: "https://images.unsplash.com/photo-1595225476474-87563907a212?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "Cherry MX 茶軸 / PBT 鍵帽 / 鋁合金底座"
            },
            {
                id: 6,
                name: "G-Sense Mouse",
                category: "accessory",
                price: 1890,
                image: "https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "25K DPI 感應器 / 輕量化設計 / 無線充電"
            },
            {
                id: 7,
                name: "Creator Display 4K",
                category: "accessory",
                price: 15900,
                image: "https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "27吋 IPS / 99% Adobe RGB / Type-C 供電"
            },
            {
                id: 8,
                name: "Velocity Go 14",
                category: "laptop",
                price: 28900,
                image: "https://images.unsplash.com/photo-1531297420494-8564e5f02ae0?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80",
                desc: "Ryzen 7 / 長效續航 / 文青首選"
            }
        ];

        // 輔助：分類名稱轉換
        window.getCategoryName = function(cat) {
            const map = {
                'laptop': '筆電',
                'desktop': '桌機',
                'accessory': '周邊'
            };
            return map[cat] || '商品';
        };

        // 渲染商品
        window.renderProducts = function(filter = 'all') {
            const grid = document.getElementById('product-grid');
            if (!grid) return;

            grid.innerHTML = '';

            const filteredProducts = filter === 'all' ?
                window.products :
                window.products.filter(p => p.category === filter);

            filteredProducts.forEach(product => {
                const card = document.createElement('div');
                card.className =
                    'product-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full transition-all duration-300 fade-in';
                card.innerHTML = `
                    <div class="relative overflow-hidden group h-48 bg-gray-200">
                        <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                            <button onclick="addToCart(${product.id})" class="bg-white text-gray-900 font-bold py-2 px-6 rounded-full transform translate-y-4 group-hover:translate-y-0 transition duration-300 hover:bg-brand-50">
                                加入購物車
                            </button>
                        </div>
                    </div>
                    <div class="p-5 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-semibold text-brand-600 uppercase tracking-wider bg-brand-50 px-2 py-1 rounded">${window.getCategoryName(product.category)}</span>
                        </div>
                        <h3 class="font-bold text-lg text-gray-900 mb-1">${product.name}</h3>
                        <p class="text-sm text-gray-500 mb-4 flex-grow line-clamp-2">${product.desc}</p>
                        <div class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                            <span class="text-xl font-bold text-gray-900">${window.formatPrice(product.price)}</span>
                            <button onclick="addToCart(${product.id})" class="text-brand-600 hover:text-brand-700 p-2 rounded-full hover:bg-brand-50 md:hidden">
                                <i data-lucide="plus-circle" class="h-6 w-6"></i>
                            </button>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
            lucide.createIcons(); // 重新渲染新插入的 icon
        };

        // 篩選功能
        window.filterProducts = function(category) {
            window.renderProducts(category);

            // 更新按鈕樣式
            document.querySelectorAll('.filter-btn').forEach(btn => {
                // 移除 active 樣式
                btn.classList.remove('bg-brand-600', 'text-white', 'shadow-md', 'ring-2', 'ring-brand-600',
                    'ring-offset-2');
                btn.classList.add('bg-white', 'text-gray-600', 'border', 'border-gray-200');
            });

            // 找出當前點擊的按鈕
            const activeBtn = event.target;
            activeBtn.classList.remove('bg-white', 'text-gray-600', 'border', 'border-gray-200');
            activeBtn.classList.add('bg-brand-600', 'text-white', 'shadow-md', 'ring-2', 'ring-brand-600',
                'ring-offset-2');
        };

        // 加入購物車
        window.addToCart = function(id) {
            const product = window.products.find(p => p.id === id);
            if (product) {
                window.cart.push(product);
                window.updateCartUI();
                window.showToast(`已將 ${product.name} 加入購物車`);
            }
        };

        // 初始渲染（僅在首頁）
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('product-grid')) {
                window.renderProducts();
            }
        });
    </script>
</body>

</html>
