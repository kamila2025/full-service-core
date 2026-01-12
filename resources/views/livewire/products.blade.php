<div>
    <!-- 麵包屑導航 -->
    <div class="pt-24 pb-4 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="text-sm text-gray-500 flex items-center">
                <a href="{{ route('tenant.frontend.home', ['tenant' => tenant('id')]) }}"
                    class="hover:text-brand-600 transition">首頁</a>
                @foreach ($breadcrumb as $index => $crumb)
                    <i data-lucide="chevron-right" class="w-4 h-4 mx-2 text-gray-300"></i>
                    @if ($index < count($breadcrumb) - 1)
                        <a href="{{ route('tenant.frontend.categories.show', ['tenant' => tenant('id'), 'category_id' => $crumb->id]) }}"
                            class="hover:text-brand-600 transition">{{ $crumb->name }}</a>
                    @else
                        <span class="text-gray-900 font-medium">{{ $crumb }}</span>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>

    <!-- 主要商品區塊 -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 lg:gap-8">

                <!-- 左側：圖片畫廊 -->
                <div class="p-6 lg:p-10 bg-gray-50 flex flex-col justify-center">
                    <!-- 主圖 -->
                    <div
                        class="relative aspect-[4/3] rounded-xl overflow-hidden bg-white shadow-sm mb-4 group cursor-zoom-in">
                        @php
                            $mainImage = $product->images->first();
                            $mainImageUrl = $mainImage
                                ? asset('storage/tenants/' . tenant('id') . '/' . $mainImage->url)
                                : 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=1200&q=80';
                        @endphp
                        <img id="main-image" src="{{ $mainImageUrl }}" alt="{{ $product->name }}"
                            class="w-full h-full object-contain main-image hover:scale-110 transition duration-500">
                        {{-- <div
                            class="absolute top-4 left-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                            熱銷中
                        </div> --}}
                    </div>

                    <!-- 縮圖列表 -->
                    <div class="flex gap-4 overflow-x-auto pb-2 no-scrollbar justify-center">
                        @foreach ($product->images as $index => $image)
                            @php
                                $imageUrl = asset('storage/tenants/' . tenant('id') . '/' . $image->url);
                                $thumbnailUrl = $imageUrl;
                            @endphp
                            <button onclick="changeImage(this, '{{ $imageUrl }}')"
                                class="thumbnail {{ $index === 0 ? 'active border-brand-600' : '' }} w-20 h-20 rounded-lg border-2 border-transparent hover:border-brand-300 overflow-hidden bg-white transition flex-shrink-0">
                                <img src="{{ $thumbnailUrl }}" class="w-full h-full object-cover">
                            </button>
                        @endforeach
                        @if ($product->images->count() === 0)
                            <button onclick="changeImage(this, '{{ $mainImageUrl }}')"
                                class="thumbnail active border-brand-600 w-20 h-20 rounded-lg border-2 border-transparent hover:border-brand-300 overflow-hidden bg-white transition flex-shrink-0">
                                <img src="{{ $mainImageUrl }}" class="w-full h-full object-cover">
                            </button>
                        @endif
                    </div>
                </div>

                <!-- 右側：商品資訊 -->
                <div class="p-6 lg:p-10 flex flex-col">
                    <div class="mb-4">
                        @if ($product->categories->first())
                            <span
                                class="inline-block bg-brand-50 text-brand-600 text-xs font-bold px-3 py-1 rounded-full mb-3">
                                {{ $product->categories->first()->name }}
                            </span>
                        @endif
                        <h1 class="text-3xl font-bold text-gray-900 leading-tight mb-2">{{ $product->name }}</h1>

                    </div>

                    <div class="mb-6">
                        <div class="flex items-end gap-3">
                            <span class="text-4xl font-bold text-brand-600">NT$
                                {{ number_format($currentPrice) }}</span>
                            @if ($originalPrice)
                                <span class="text-lg text-gray-400 line-through mb-1">NT$
                                    {{ number_format($originalPrice) }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- 規格選擇 (變體) -->
                    {{-- @if ($product->variants->count() > 0)
                        <div class="mb-6 space-y-4">
                            @foreach ($product->variants as $variant)
                                <div>
                                    <span class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $variant->name ?? '規格' }}
                                    </span>
                                    <div class="flex gap-3 flex-wrap">
                                        @foreach ($product->variants as $v)
                                            <button wire:click="selectVariant({{ $v->id }})"
                                                class="px-4 py-2 border-2 rounded-lg transition {{ $selectedVariant == $v->id ? 'border-brand-600 text-brand-600 font-bold bg-brand-50' : 'border-gray-200 text-gray-600 hover:border-gray-300' }}">
                                                {{ $v->name ?? '選項 ' . $v->id }}
                                                @if ($v->price > $currentPrice)
                                                    <span class="text-xs">(+NT$
                                                        {{ number_format($v->price - $currentPrice) }})</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif --}}

                    <div class="mt-auto">
                        <hr class="border-gray-100 my-6">

                        <!-- 購買操作區 -->
                        <div class="flex flex-col sm:flex-row gap-4 mt-auto">
                            <!-- 數量選擇 -->
                            <div class="flex items-center border border-gray-300 rounded-lg w-full sm:w-auto">
                                <button wire:click="updateQuantity(-1)"
                                    class="quantity-btn p-3 text-gray-600 transition rounded-l-lg">
                                    <i data-lucide="minus" class="w-4 h-4"></i>
                                </button>
                                <input type="number" wire:model="quantity" min="1" max="10"
                                    class="w-12 text-center border-none focus:ring-0 text-gray-900 font-bold" readonly>
                                <button wire:click="updateQuantity(1)"
                                    class="quantity-btn p-3 text-gray-600 transition rounded-r-lg">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <!-- 加入購物車 -->
                            <button wire:click="addToCart"
                                class="flex-1 bg-brand-600 hover:bg-brand-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg hover:shadow-xl transition transform active:scale-95 flex justify-center items-center">
                                <i data-lucide="shopping-cart" class="w-5 h-5 mr-2"></i>
                                加入購物車
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 商品詳細 Tab -->
            <div class="border-t border-gray-100 bg-white">
                <div class="px-6 lg:px-10 py-5 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center">
                        <span class="w-1 h-6 bg-brand-600 mr-3 rounded-full"></span>
                        商品介紹
                    </h3>
                </div>
                <div class="p-6 lg:p-10 prose max-w-none text-gray-600 product-description">
                    {!! $product->description ?? '<p>商品詳細介紹內容...</p>' !!}
                </div>
            </div>
        </div>

        <!-- 相關商品推薦 -->
        @if ($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                    <i data-lucide="sparkles" class="w-5 h-5 mr-2 text-yellow-500"></i>
                    你可能也喜歡
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $relatedProduct)
                        @php
                            $relatedImage = $relatedProduct->images->first();
                            $relatedImageUrl = $relatedImage
                                ? asset('storage/tenants/' . tenant('id') . '/' . $relatedImage->url)
                                : 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80';
                            $relatedMinPrice = $relatedProduct->variants->min('price') ?? 0;
                        @endphp
                        <a href="{{ route('tenant.frontend.products.show', ['tenant' => tenant('id'), 'product_id' => $relatedProduct->id]) }}"
                            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:-translate-y-1 hover:shadow-md transition duration-300 block">
                            <div class="aspect-[4/3] bg-gray-200 relative group overflow-hidden">
                                <img src="{{ $relatedImageUrl }}" alt="{{ $relatedProduct->name }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-1">{{ $relatedProduct->name }}</h3>
                                <p class="text-brand-600 font-bold">NT$ {{ number_format($relatedMinPrice) }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </main>

    <style>
        /* 數量選擇器樣式 */
        .quantity-btn:hover {
            background-color: #eff6ff;
            color: #2563eb;
        }

        /* 圖片切換動畫 */
        .main-image {
            transition: opacity 0.3s ease-in-out;
        }

        .thumbnail.active {
            border-color: #2563eb;
            opacity: 1;
        }

        .tab-btn.active {
            border-bottom: 2px solid #2563eb;
            color: #2563eb;
            font-weight: 600;
        }

        /* 保留文字換行 */
        .product-description {
            white-space: pre-line;
            word-wrap: break-word;
        }
    </style>

    <script>
        // 初始化 Icons
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });

        // 圖片切換邏輯
        function changeImage(el, src) {
            // 更新主圖
            const mainImg = document.getElementById('main-image');
            if (mainImg) {
                mainImg.style.opacity = '0.5'; // 簡單的淡出效果
                setTimeout(() => {
                    mainImg.src = src;
                    mainImg.style.opacity = '1';
                }, 150);
            }

            // 更新縮圖狀態
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active', 'border-brand-600'));
            el.classList.add('active', 'border-brand-600');
        }

        // Livewire 事件監聽
        document.addEventListener('livewire:init', () => {
            Livewire.on('notify', (event) => {
                if (typeof window.showToast === 'function') {
                    window.showToast(event[0].message || '操作成功');
                }
            });
        });
    </script>
</div>
