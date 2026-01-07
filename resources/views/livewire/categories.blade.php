<div>
    <!-- Page Header (Smaller Hero) -->
    <header class="bg-dark-900 pt-24 pb-12 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1550745165-9bc0b252726f?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80"
                class="w-full h-full object-cover">
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">全系列商品</h1>
            <p class="text-gray-400 max-w-2xl">探索最頂級的硬體設備，從極致效能的電競筆電到專業工作站，應有盡有。</p>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Left Sidebar (Category Menu) -->
            <aside class="w-full lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden lg:sticky lg:top-24">
                    <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center lg:block">
                        <h2 class="font-bold text-lg text-gray-900 flex items-center">
                            商品分類
                        </h2>
                        <!-- Mobile Toggle for Sidebar Content -->
                        <button class="lg:hidden text-gray-500"
                            onclick="document.getElementById('sidebar-content').classList.toggle('hidden')">
                            <i data-lucide="chevron-down" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div id="sidebar-content" class="hidden lg:block">
                        <ul class="py-2">
                            <!-- 全部商品 -->
                            <li>
                                <a href="{{ route('tenant.frontend.categories', ['tenant' => tenant('id')]) }}"
                                    class="w-full text-left px-4 py-3 flex items-center hover:bg-gray-50 transition category-item {{ is_null($categoryID) ? 'active' : '' }}">
                                    <span class="font-medium">全部商品</span>
                                </a>
                            </li>

                            @foreach ($categories as $category)
                                @php
                                    $isActive = $this->isCategoryActive($category);
                                @endphp
                                <li class="border-t border-gray-100">
                                    <a href="{{ route('tenant.frontend.categories.show', ['tenant' => tenant('id'), 'category_id' => $category->id]) }}"
                                        class="w-full text-left px-4 py-3 flex justify-between items-center hover:bg-gray-50 transition category-item {{ $isActive ? 'active' : '' }}"
                                        id="cat-btn-{{ $category->id }}">
                                        <div class="flex items-center">
                                            <span class="font-medium">{{ $category->name }}</span>
                                        </div>
                                        @if ($category->children && $category->children->count() > 0)
                                            <i data-lucide="chevron-down"
                                                class="w-4 h-4 text-gray-400 transition-transform duration-300 chevron {{ $isActive ? 'rotate' : '' }}"
                                                id="chevron-{{ $category->id }}"></i>
                                        @endif
                                    </a>
                                    @if ($category->children && $category->children->count() > 0)
                                        <div class="submenu bg-gray-50 {{ $isActive ? 'open' : '' }}"
                                            id="submenu-{{ $category->id }}">
                                            <ul class="py-1 pl-11 pr-4 space-y-1">
                                                @foreach ($category->children as $subCategory)
                                                    <li>
                                                        <a href="{{ route('tenant.frontend.categories.show', ['tenant' => tenant('id'), 'category_id' => $subCategory->id]) }}"
                                                            class="w-full text-left py-2 text-sm text-gray-500 hover:text-brand-600 transition subcategory-item {{ $categoryID == $subCategory->id ? 'active text-brand-600' : '' }}"
                                                            id="sub-btn-{{ $subCategory->id }}">
                                                            {{ $subCategory->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Right Product Grid -->
            <main class="flex-1">
                <!-- Toolbar -->
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                    <div class="mb-4 sm:mb-0">
                        <nav class="text-sm text-gray-500 mb-1">
                            <a href="{{ route('tenant.frontend.home', ['tenant' => tenant('id')]) }}"
                                class="hover:text-brand-600 cursor-pointer">首頁</a>
                            <span class="mx-1">/</span>
                            <span class="text-gray-900 font-medium">{{ $breadcrumb }}</span>
                        </nav>
                        <p class="text-xs text-gray-400">顯示 {{ $products->total() }} 筆結果</p>
                    </div>
                </div>

                <!-- Products Grid -->
                @if ($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($products as $product)
                            @php
                                $firstImage = $product->images->first();
                                $imageUrl = $firstImage
                                    ? asset('storage/tenants/' . tenant('id') . '/' . $firstImage->url)
                                    : 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80';
                                $minPrice = $product->variants->min('price') ?? 0;
                                $firstCategory = $product->categories->first();
                            @endphp
                            <div
                                class="product-card bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full transition-all duration-300">
                                <div class="relative overflow-hidden group h-48 bg-gray-100">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                                    @if ($firstCategory)
                                        <div class="absolute top-2 right-2">
                                            <span
                                                class="bg-black/70 text-white text-xs px-2 py-1 rounded backdrop-blur-sm">{{ $firstCategory->name }}</span>
                                        </div>
                                    @endif
                                    <div
                                        class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <a href="{{ route('tenant.frontend.products.show', ['tenant' => tenant('id'), 'product_id' => $product->id]) }}"
                                            class="bg-white text-gray-900 font-bold py-2 px-6 rounded-full transform translate-y-4 group-hover:translate-y-0 transition duration-300 hover:bg-brand-50 shadow-lg">
                                            瀏覽商品
                                        </a>
                                    </div>
                                </div>
                                <div class="p-5 flex flex-col flex-grow">
                                    <h3
                                        class="font-bold text-lg text-gray-900 mb-1 group-hover:text-brand-600 transition">
                                        {{ $product->name }}</h3>
                                    <p class="text-sm text-gray-500 mb-4 flex-grow line-clamp-2">
                                        {{ $product->description ?? '' }}</p>
                                    <div
                                        class="flex items-center justify-between mt-auto pt-4 border-t border-gray-100">
                                        <div class="flex flex-col">
                                            <span class="text-xs text-gray-400">建議售價</span>
                                            <span
                                                class="text-xl font-bold text-brand-600">{{ number_format($minPrice) }}
                                                元</span>
                                        </div>
                                        <a href="{{ route('tenant.frontend.products.show', ['tenant' => tenant('id'), 'product_id' => $product->id]) }}"
                                            class="bg-gray-50 p-2 rounded-full hover:bg-brand-50 text-gray-400 hover:text-brand-600 transition">
                                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @else
                    <!-- No Results State -->
                    <div class="py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                            <i data-lucide="search-x" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">找不到相關商品</h3>
                        <p class="text-gray-500 mt-2">請嘗試選擇其他分類。</p>
                        <a href="{{ route('tenant.frontend.categories', ['tenant' => tenant('id')]) }}"
                            class="mt-4 px-4 py-2 bg-brand-600 text-white rounded-md text-sm hover:bg-brand-700 transition inline-block">顯示全部</a>
                    </div>
                @endif
            </main>
        </div>
    </div>

    <style>
        /* 側邊欄動畫 */
        .submenu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
            opacity: 0;
        }

        .submenu.open {
            max-height: 500px;
            opacity: 1;
        }

        .chevron {
            transition: transform 0.3s ease;
        }

        .chevron.rotate {
            transform: rotate(180deg);
        }

        /* 選中狀態樣式 */
        .category-item {
            text-decoration: none;
        }

        .category-item.active {
            color: #2563eb;
            font-weight: 700;
            background-color: #eff6ff;
        }

        .subcategory-item {
            display: block;
            text-decoration: none;
        }

        .subcategory-item.active {
            color: #2563eb;
            font-weight: 500;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</div>
