@extends('layouts/layoutMaster')

@section('title', isset($product) ? '編輯商品' : '新增商品')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dropzone/dropzone.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/quill/editor.css') }}" />
    <style>
        /* YouTube 影片嵌入樣式 */
        .ql-youtube-embed {
            margin: 10px 0;
        }

        .youtube-container {
            position: relative;
            display: inline-block;
            width: 560px;
            max-width: 100%;
            margin: 0 auto;
        }

        .youtube-container iframe {
            width: 100%;
            height: 315px;
            border: none;
            border-radius: 4px;
        }

        .resize-handle {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 30px;
            height: 30px;
            background: #2563eb;
            color: white;
            cursor: nwse-resize;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            border-radius: 4px 0 4px 0;
            z-index: 1000;
            opacity: 0.9;
            transition: all 0.2s;
            pointer-events: auto;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            user-select: none;
            -webkit-user-select: none;
        }

        .resize-handle:hover {
            opacity: 1;
            background: #1d4ed8;
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        }

        .youtube-container:hover .resize-handle {
            opacity: 1;
        }

        .youtube-container {
            pointer-events: auto;
        }

        .ql-youtube-embed {
            pointer-events: auto;
        }

        /* 工具欄 YouTube 按鈕樣式 */
        .ql-toolbar .ql-youtube::before {
            content: "YouTube";
            font-size: 12px;
            font-weight: bold;
        }

        .ql-toolbar .ql-youtube {
            width: auto;
            padding: 0 8px;
        }
    </style>
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/dropzone/dropzone.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/quill/katex.js') }}"></script>

@endsection

@section('page-script')
    <script>
        // 初始化 select2
        $('.select2').select2();

        // 自定義 YouTube 影片 Blot
        const BlockEmbed = Quill.import('blots/block/embed');

        class YouTubeBlot extends BlockEmbed {
            static create(value) {
                const node = super.create();
                const videoId = this.extractVideoId(value);
                if (!videoId) {
                    return node;
                }

                node.setAttribute('contenteditable', 'false');
                node.setAttribute('class', 'ql-youtube-embed');

                // 創建可調整大小的容器
                const container = document.createElement('div');
                container.className = 'youtube-container';
                container.style.position = 'relative';
                container.style.width = '560px';
                container.style.maxWidth = '100%';
                container.style.margin = '0 auto';

                // 創建調整大小的控制點
                const resizeHandle = document.createElement('div');
                resizeHandle.className = 'resize-handle';
                resizeHandle.innerHTML = '↘';
                resizeHandle.setAttribute('contenteditable', 'false');
                resizeHandle.style.pointerEvents = 'auto';
                resizeHandle.style.userSelect = 'none';
                resizeHandle.style.webkitUserSelect = 'none';

                // 創建 iframe
                const iframe = document.createElement('iframe');
                iframe.setAttribute('src', `https://www.youtube.com/embed/${videoId}`);
                iframe.setAttribute('frameborder', '0');
                iframe.setAttribute('allowfullscreen', 'true');
                iframe.style.width = '100%';
                iframe.style.height = '315px';
                iframe.setAttribute('data-video-id', videoId);

                container.appendChild(iframe);
                container.appendChild(resizeHandle);
                node.appendChild(container);

                // 調整大小功能 - 使用閉包確保變數作用域正確
                (function() {
                    let isResizing = false;
                    let startX, startY, startWidth, startHeight;

                    const handleMouseMove = function(e) {
                        if (!isResizing) return;
                        e.preventDefault();
                        const width = startWidth + (e.clientX - startX);
                        const height = startHeight + (e.clientY - startY);

                        // 限制最小和最大尺寸
                        const minWidth = 200;
                        const maxWidth = 1200;
                        const minHeight = 150;
                        const maxHeight = 675;

                        const newWidth = Math.max(minWidth, Math.min(maxWidth, width));
                        const newHeight = Math.max(minHeight, Math.min(maxHeight, height));

                        container.style.width = newWidth + 'px';
                        iframe.style.height = newHeight + 'px';
                    };

                    const handleMouseUp = function(e) {
                        if (!isResizing) return;
                        e.preventDefault();
                        e.stopPropagation();
                        isResizing = false;
                        document.removeEventListener('mousemove', handleMouseMove, true);
                        document.removeEventListener('mouseup', handleMouseUp, true);
                        document.body.style.cursor = '';
                        document.body.style.userSelect = '';
                        document.body.style.webkitUserSelect = '';

                        // 恢復控制點樣式
                        resizeHandle.style.background = '#2563eb';
                        resizeHandle.style.transform = '';
                    };

                    resizeHandle.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        isResizing = true;
                        startX = e.clientX;
                        startY = e.clientY;
                        startWidth = parseInt(window.getComputedStyle(container).width, 10);
                        startHeight = parseInt(window.getComputedStyle(iframe).height, 10);

                        document.body.style.cursor = 'nwse-resize';
                        document.body.style.userSelect = 'none';
                        document.body.style.webkitUserSelect = 'none';

                        // 添加視覺反饋
                        resizeHandle.style.background = '#1e40af';
                        resizeHandle.style.transform = 'scale(1.2)';

                        document.addEventListener('mousemove', handleMouseMove, true);
                        document.addEventListener('mouseup', handleMouseUp, true);
                    }, true);

                    // 確保控制點可以接收點擊事件
                    resizeHandle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                    }, true);
                })();

                return node;
            }

            static value(node) {
                const iframe = node.querySelector('iframe');
                if (iframe) {
                    return iframe.getAttribute('data-video-id');
                }
                return '';
            }

            static extractVideoId(url) {
                const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
                const match = url.match(regExp);
                return (match && match[2].length === 11) ? match[2] : null;
            }
        }

        YouTubeBlot.blotName = 'youtube';
        YouTubeBlot.tagName = 'div';
        Quill.register(YouTubeBlot);

        // 自定義工具欄處理器
        const YouTubeHandler = function() {
            const url = prompt('請輸入 YouTube 影片網址：');
            if (url) {
                const videoId = YouTubeBlot.extractVideoId(url);
                if (videoId) {
                    const range = this.quill.getSelection(true);
                    this.quill.insertEmbed(range.index, 'youtube', url, 'user');
                    this.quill.setSelection(range.index + 1);
                } else {
                    alert('無效的 YouTube 網址');
                }
            }
        };

        // 初始化 Quill Editor
        const fullToolbar = [
            ['bold', 'italic', 'underline', 'strike'],
            ['blockquote', 'code-block'],
            [{
                'header': 1
            }, {
                'header': 2
            }],
            [{
                'list': 'ordered'
            }, {
                'list': 'bullet'
            }],
            [{
                'script': 'sub'
            }, {
                'script': 'super'
            }],
            [{
                'indent': '-1'
            }, {
                'indent': '+1'
            }],
            [{
                'direction': 'rtl'
            }],
            [{
                'color': []
            }, {
                'background': []
            }],
            [{
                'align': []
            }],
            ['clean'],
            ['link', 'image', 'video', 'youtube']
        ];

        // 初始化 Quill Editor
        window.editor = new Quill('#editor', {
            bounds: '#editor',
            modules: {
                formula: true,
                toolbar: {
                    container: fullToolbar,
                    handlers: {
                        'youtube': YouTubeHandler
                    }
                }
            },
            theme: 'snow'
        });

        // 綁定現有 YouTube 影片的調整大小功能
        function bindExistingYouTubeResizeHandlers() {
            const youtubeEmbeds = window.editor.root.querySelectorAll('.ql-youtube-embed');

            youtubeEmbeds.forEach(function(node) {
                // 確保節點有正確的屬性
                node.setAttribute('contenteditable', 'false');
                node.style.pointerEvents = 'auto';

                let container = node.querySelector('.youtube-container');
                let resizeHandle = node.querySelector('.resize-handle');
                let iframe = null;

                // 如果缺少容器，嘗試從現有結構創建
                if (!container) {
                    // 檢查是否有直接的 iframe
                    iframe = node.querySelector('iframe');
                    if (iframe) {
                        container = document.createElement('div');
                        container.className = 'youtube-container';
                        container.style.position = 'relative';
                        container.style.width = iframe.style.width || '560px';
                        container.style.maxWidth = '100%';
                        container.style.margin = '0 auto';

                        // 保存 iframe 的樣式
                        const iframeWidth = iframe.style.width;
                        const iframeHeight = iframe.style.height;

                        // 移動 iframe 到容器中
                        iframe.parentNode.insertBefore(container, iframe);
                        iframe.style.width = '100%';
                        iframe.style.height = iframeHeight || '315px';
                        container.appendChild(iframe);
                    } else {
                        return; // 沒有 iframe，跳過
                    }
                } else {
                    iframe = container.querySelector('iframe');
                }

                // 如果還是沒有 iframe，跳過
                if (!iframe) {
                    return;
                }

                // 確保容器樣式正確
                if (!container.style.position) {
                    container.style.position = 'relative';
                }
                if (!container.style.width) {
                    container.style.width = '560px';
                }
                container.style.maxWidth = '100%';
                container.style.margin = '0 auto';
                container.style.pointerEvents = 'auto';

                // 如果已經綁定過事件，先移除舊的事件監聽器
                if (resizeHandle && resizeHandle.dataset.bound === 'true') {
                    // 創建新的 resizeHandle 來替換舊的
                    const oldHandle = resizeHandle;
                    resizeHandle = document.createElement('div');
                    resizeHandle.className = 'resize-handle';
                    resizeHandle.innerHTML = '↘';
                    oldHandle.parentNode.replaceChild(resizeHandle, oldHandle);
                }

                // 如果缺少調整控制點，創建它
                if (!resizeHandle) {
                    resizeHandle = document.createElement('div');
                    resizeHandle.className = 'resize-handle';
                    resizeHandle.innerHTML = '↘';
                    container.appendChild(resizeHandle);
                }

                // 設置 resizeHandle 的樣式和屬性
                resizeHandle.setAttribute('contenteditable', 'false');
                resizeHandle.style.position = 'absolute';
                resizeHandle.style.bottom = '0';
                resizeHandle.style.right = '0';
                resizeHandle.style.width = '30px';
                resizeHandle.style.height = '30px';
                resizeHandle.style.background = '#2563eb';
                resizeHandle.style.color = 'white';
                resizeHandle.style.cursor = 'nwse-resize';
                resizeHandle.style.display = 'flex';
                resizeHandle.style.alignItems = 'center';
                resizeHandle.style.justifyContent = 'center';
                resizeHandle.style.fontSize = '16px';
                resizeHandle.style.borderRadius = '4px 0 4px 0';
                resizeHandle.style.zIndex = '10000';
                resizeHandle.style.opacity = '0.9';
                resizeHandle.style.pointerEvents = 'auto';
                resizeHandle.style.userSelect = 'none';
                resizeHandle.style.webkitUserSelect = 'none';
                resizeHandle.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.2)';

                // 調整大小功能
                (function(container, iframe, resizeHandle) {
                    let isResizing = false;
                    let startX, startY, startWidth, startHeight;

                    const handleMouseMove = function(e) {
                        if (!isResizing) return;
                        e.preventDefault();
                        e.stopPropagation();
                        const width = startWidth + (e.clientX - startX);
                        const height = startHeight + (e.clientY - startY);

                        // 限制最小和最大尺寸
                        const minWidth = 200;
                        const maxWidth = 1200;
                        const minHeight = 150;
                        const maxHeight = 675;

                        const newWidth = Math.max(minWidth, Math.min(maxWidth, width));
                        const newHeight = Math.max(minHeight, Math.min(maxHeight, height));

                        container.style.width = newWidth + 'px';
                        iframe.style.height = newHeight + 'px';
                    };

                    const handleMouseUp = function(e) {
                        if (!isResizing) return;
                        e.preventDefault();
                        e.stopPropagation();
                        isResizing = false;
                        document.removeEventListener('mousemove', handleMouseMove, true);
                        document.removeEventListener('mouseup', handleMouseUp, true);
                        document.body.style.cursor = '';
                        document.body.style.userSelect = '';
                        document.body.style.webkitUserSelect = '';

                        // 恢復控制點樣式
                        if (resizeHandle) {
                            resizeHandle.style.background = '#2563eb';
                            resizeHandle.style.transform = '';
                        }
                    };

                    // 移除舊的事件監聽器（如果有的話）
                    const newResizeHandle = resizeHandle.cloneNode(true);
                    resizeHandle.parentNode.replaceChild(newResizeHandle, resizeHandle);
                    resizeHandle = newResizeHandle;

                    resizeHandle.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();

                        isResizing = true;
                        startX = e.clientX;
                        startY = e.clientY;
                        startWidth = parseInt(window.getComputedStyle(container).width, 10);
                        startHeight = parseInt(window.getComputedStyle(iframe).height, 10);

                        document.body.style.cursor = 'nwse-resize';
                        document.body.style.userSelect = 'none';
                        document.body.style.webkitUserSelect = 'none';

                        // 添加視覺反饋
                        resizeHandle.style.background = '#1e40af';
                        resizeHandle.style.transform = 'scale(1.2)';

                        document.addEventListener('mousemove', handleMouseMove, true);
                        document.addEventListener('mouseup', handleMouseUp, true);
                    }, true);

                    // 確保控制點可以接收點擊事件
                    resizeHandle.addEventListener('click', function(e) {
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                    }, true);

                    // 標記為已綁定
                    resizeHandle.dataset.bound = 'true';
                })(container, iframe, resizeHandle);
            });
        }

        // 使用 MutationObserver 監聽 DOM 變化
        const observer = new MutationObserver(function(mutations) {
            let shouldBind = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            if (node.classList && node.classList.contains('ql-youtube-embed')) {
                                shouldBind = true;
                            } else if (node.querySelector && node.querySelector(
                                    '.ql-youtube-embed')) {
                                shouldBind = true;
                            }
                        }
                    });
                }
            });
            if (shouldBind) {
                setTimeout(function() {
                    bindExistingYouTubeResizeHandlers();
                }, 200);
            }
        });

        // 開始觀察編輯器根節點
        observer.observe(window.editor.root, {
            childList: true,
            subtree: true
        });

        // 如果是編輯模式，載入現有的描述內容
        @if (isset($product) && $product->description)
            window.editor.root.innerHTML = {!! json_encode($product->description) !!};
            // 載入內容後，多次嘗試綁定（確保 DOM 完全更新）
            setTimeout(function() {
                bindExistingYouTubeResizeHandlers();
            }, 100);
            setTimeout(function() {
                bindExistingYouTubeResizeHandlers();
            }, 300);
            setTimeout(function() {
                bindExistingYouTubeResizeHandlers();
            }, 500);
        @endif

        // 當編輯器內容改變時，同步到隱藏的 input
        window.editor.on('text-change', function() {
            const content = window.editor.root.innerHTML;
            $('#description').val(content);
        });

        // 監聽編輯器內容變化（包括插入新內容）
        window.editor.on('editor-change', function(eventName, ...args) {
            if (eventName === 'text-change' || eventName === 'selection-change') {
                setTimeout(function() {
                    bindExistingYouTubeResizeHandlers();
                }, 100);
            }
        });

        // 初始化時也同步一次
        $('#description').val(window.editor.root.innerHTML);

        // 頁面完全載入後再次綁定
        $(document).ready(function() {
            setTimeout(function() {
                bindExistingYouTubeResizeHandlers();
            }, 500);
        });
    </script>
    <script src="{{ asset('js/tenant/product/tenant-product-add.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="app-ecommerce">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex flex-column justify-content-center">
                @if (isset($product))
                    <h3 class="mb-2 mt-3">{{ $product->name }}</h3>
                    <p class="text-muted">最後更新於 : {{ $product->updated_at->format('Y-m-d H:i') }}</p>
                @else
                    <h3 class="mb-1">新增商品</h3>
                @endif
            </div>
            <div class="d-flex align-content-center flex-wrap gap-3">
                <a href="{{ route('tenant.products.index', ['tenant' => tenant('id')]) }}"
                    class="btn btn-label-secondary">返回</a>
                @if (isset($product))
                    <a href="javascript:void(0)" type="button" id="deleteBtn" class="btn btn-label-danger">刪除</a>
                @endif
                <button type="button" id="saveButton" class="btn btn-primary">儲存</button>
            </div>
        </div>
        <form id="productForm">
            <input type='hidden' id="product_id" name='product_id' value="{{ $product->id ?? '' }}">

            <div class="row">
                <!-- 左側欄位 -->
                <div class="col-12 col-lg-8">
                    <!-- 商品資訊 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">商品資訊</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 mb-2">
                                    <label for="name" class="form-label">商品名稱</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ isset($product) ? $product->name : '' }}">
                                </div>

                                <div class="col-12 mb-2">
                                    <label for="description" class="form-label">商品描述</label>
                                    <div id="editor"></div>
                                    <input type="hidden" id="description" name="description"
                                        value="{{ isset($product) ? $product->description : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 商品資訊 -->

                    <!-- 商品圖片 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">商品圖片</h5>
                        </div>
                        <div class="card-body">
                            <div class="dropzone needsclick" id="dropzone-images">
                                <div class="dz-message needsclick">
                                    上傳檔案或是拖曳至此
                                    <span class="note needsclick">大小限制：10MB, 1500 × 1500 pixel 您可以上傳 JPEG, PNG or
                                        GIF 類型檔案</span>
                                </div>
                                <div class="fallback">
                                    <input name="file" type="file" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 商品圖片 -->

                    <!-- 商品規格 -->
                    {{-- <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">
                            <h5 class="card-title m-0">商品規格</h5>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#variantModal">
                                <i class="bx bx-plus me-1"></i>新增商品規格
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="variantContainer">
                                <p class="mb-0 text-muted">為您的商品新增規格，例：尺寸、顏色</p>
                            </div>
                        </div>
                    </div> --}}
                    <!-- 商品規格 -->

                    <!-- 商品價格 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">商品價格</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @if (isset($product) && $product->variants->count() == 1)
                                    @foreach ($product->variants as $variant)
                                        <div class="col-6 mb-2">
                                            <label for="price" class="form-label">售價</label>
                                            <input type="number" id="price" name="price" class="form-control"
                                                value="{{ isset($product) ? $variant->price : '50' }}" min="0">
                                        </div>

                                        <div class="col-6 mb-2">
                                            <label for="compare_at_price" class="form-label">原價</label>
                                            <input type="number" id="compare_at_price" name="compare_at_price"
                                                class="form-control"
                                                value="{{ isset($product) ? $variant->compare_at_price : '100' }}"
                                                min="0">
                                        </div>

                                        <div class="col-6 mb-2">
                                            <label for="cost_price" class="form-label">成本價</label>
                                            <input type="number" id="cost_price" name="cost_price" class="form-control"
                                                value="{{ isset($product) ? $variant->cost_price : '0' }}" min="0">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-6 mb-2">
                                        <label for="price" class="form-label">售價</label>
                                        <input type="number" id="price" name="price" class="form-control"
                                            value="50" min="0">
                                    </div>

                                    <div class="col-6 mb-2">
                                        <label for="compare_at_price" class="form-label">原價</label>
                                        <input type="number" id="compare_at_price" name="compare_at_price"
                                            class="form-control" value="100" min="0">
                                    </div>

                                    <div class="col-6 mb-2">
                                        <label for="cost_price" class="form-label">成本價</label>
                                        <input type="number" id="cost_price" name="cost_price" class="form-control"
                                            value="0" min="0">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- 商品價格 -->

                    <!-- 商品庫存 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">商品庫存</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12 mb-2">
                                    <label for="inventory_management" class="form-label">是否追蹤庫存數量</label>
                                    <select id="inventory_management" name="inventory_management"
                                        class="select2 form-select">
                                        <option value="store"
                                            {{ isset($product) ? ($product->inventory_management->value == 'store' ? 'selected' : '') : '' }}>
                                            追蹤庫存數量</option>
                                        <option value="none"
                                            {{ isset($product) ? ($product->inventory_management->value == 'none' ? 'selected' : '') : 'selected' }}>
                                            不追蹤庫存數量</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 商品庫存 -->
                </div>
                <!-- 左側欄位 -->

                <!-- 右側欄位 -->
                <div class="col-12 col-lg-4">
                    <!-- 發佈狀態 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">發佈狀態</h5>
                        </div>
                        <div class="card-body">
                            <div class="col-12 mb-2">
                                <select id="status" name="status" class="select2 form-select">
                                    <option value="active"
                                        {{ isset($product) ? ($product->status->value == 'active' ? 'selected' : '') : 'selected' }}>
                                        已發佈</option>
                                    <option value="inactive"
                                        {{ isset($product) ? ($product->status->value == 'inactive' ? 'selected' : '') : '' }}>
                                        未發佈</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 發佈狀態 -->

                    <!-- 商品分類 -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="col-12 mb-2">
                                <label for="categories" class="form-label">商品分類</label>
                                <select id="categories" class="select2 form-select" multiple>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category['id'] }}"
                                            {{ isset($product) ? ($product->categories->contains($category['id']) ? 'selected' : '') : '' }}>
                                            {{ $category['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 商品分類 -->
                </div>
                <!-- 右側欄位 -->
            </div>
        </form>
    </div>

    @if (isset($product) && $product->images->count() > 0)
        <script>
            window.existingImages = {!! json_encode(
                $product->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'filename' => $image->filename,
                        'size' => $image->size,
                        'url' => asset('storage/tenants/' . tenant('id') . '/' . $image->url),
                    ];
                }),
            ) !!};
        </script>
    @endif

    <!-- 多規格 Modal -->
    <div class="modal fade" id="variantModal" tabindex="-1" aria-labelledby="variantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="variantModalLabel">商品規格設定</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- 規格類型設定區域 -->
                    <div id="variantTypesContainer">
                        <div class="variant-type-item mb-3 p-3 border rounded">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <label class="form-label">規格名稱</label>
                                    <input type="text" class="form-control variant-type-name" placeholder="例：尺寸"
                                        value="尺寸">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">選項</label>
                                    <input type="text" class="form-control variant-options-tagify"
                                        placeholder="輸入選項後按 Enter 新增" value="L,M">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-outline-danger" onclick="removeVariantType(this)">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary" onclick="addVariantType()">
                        <i class="bx bx-plus"></i> 增加規格
                    </button>

                    <hr class="my-4">

                    <!-- 規格組合表格 -->
                    <div id="variantCombinationsContainer">
                        <h6>規格組合</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>規格</th>
                                        <th>編號 (SKU)</th>
                                        <th>售價 (TWD) *</th>
                                        <th>原價 (TWD)</th>
                                        <th>成本價 (TWD)</th>
                                        <th>條碼</th>
                                    </tr>
                                </thead>
                                <tbody id="variantCombinationsTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-primary" onclick="saveVariants()">儲存規格</button>
                </div>
            </div>
        </div>
    </div>
@endsection
