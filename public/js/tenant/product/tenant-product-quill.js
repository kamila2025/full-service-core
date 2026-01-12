/**
 * Quill 編輯器初始化與 YouTube 影片嵌入功能
 */
(function () {
  'use strict';

  // 等待 Quill 和 jQuery 載入完成
  if (typeof Quill === 'undefined' || typeof $ === 'undefined') {
    console.error('Quill 或 jQuery 未載入');
    return;
  }

  // 初始化 Quill 編輯器
  function initQuillEditor(productDescription) {
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
        (function () {
          let isResizing = false;
          let startX, startY, startWidth, startHeight;

          const handleMouseMove = function (e) {
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

          const handleMouseUp = function (e) {
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

          resizeHandle.addEventListener(
            'mousedown',
            function (e) {
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
            },
            true
          );

          // 確保控制點可以接收點擊事件
          resizeHandle.addEventListener(
            'click',
            function (e) {
              e.stopPropagation();
              e.stopImmediatePropagation();
            },
            true
          );
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
        return match && match[2].length === 11 ? match[2] : null;
      }
    }

    YouTubeBlot.blotName = 'youtube';
    YouTubeBlot.tagName = 'div';
    Quill.register(YouTubeBlot);

    // 自定義工具欄處理器
    const YouTubeHandler = async function () {
      const { value: url } = await Swal.fire({
        title: '輸入 YouTube 影片網址',
        input: 'text',
        inputLabel: '請輸入 YouTube 影片網址',
        inputPlaceholder: '例如：https://www.youtube.com/watch?v=...',
        showCancelButton: true,
        confirmButtonText: '確定',
        cancelButtonText: '取消',
        inputValidator: value => {
          if (!value) {
            return '請輸入 YouTube 影片網址';
          }
        }
      });

      if (url) {
        const videoId = YouTubeBlot.extractVideoId(url);
        if (videoId) {
          const range = this.quill.getSelection(true);
          this.quill.insertEmbed(range.index, 'youtube', url, 'user');
          this.quill.setSelection(range.index + 1);
        } else {
          Swal.fire({
            icon: 'error',
            title: '錯誤',
            text: '無效的 YouTube 網址',
            confirmButtonText: '確定'
          });
        }
      }
    };

    // 初始化 Quill Editor
    const fullToolbar = [
      ['bold', 'italic', 'underline', 'strike'],
      ['blockquote', 'code-block'],
      [
        {
          header: 1
        },
        {
          header: 2
        }
      ],
      [
        {
          list: 'ordered'
        },
        {
          list: 'bullet'
        }
      ],
      [
        {
          script: 'sub'
        },
        {
          script: 'super'
        }
      ],
      [
        {
          indent: '-1'
        },
        {
          indent: '+1'
        }
      ],
      [
        {
          direction: 'rtl'
        }
      ],
      [
        {
          color: []
        },
        {
          background: []
        }
      ],
      [
        {
          align: []
        }
      ],
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
            youtube: YouTubeHandler
          }
        }
      },
      theme: 'snow'
    });

    // 綁定現有 YouTube 影片的調整大小功能
    function bindExistingYouTubeResizeHandlers() {
      const youtubeEmbeds = window.editor.root.querySelectorAll('.ql-youtube-embed');

      youtubeEmbeds.forEach(function (node) {
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
        (function (container, iframe, resizeHandle) {
          let isResizing = false;
          let startX, startY, startWidth, startHeight;

          const handleMouseMove = function (e) {
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

          const handleMouseUp = function (e) {
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

          resizeHandle.addEventListener(
            'mousedown',
            function (e) {
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
            },
            true
          );

          // 確保控制點可以接收點擊事件
          resizeHandle.addEventListener(
            'click',
            function (e) {
              e.stopPropagation();
              e.stopImmediatePropagation();
            },
            true
          );

          // 標記為已綁定
          resizeHandle.dataset.bound = 'true';
        })(container, iframe, resizeHandle);
      });
    }

    // 使用 MutationObserver 監聽 DOM 變化
    const observer = new MutationObserver(function (mutations) {
      let shouldBind = false;
      mutations.forEach(function (mutation) {
        if (mutation.addedNodes.length > 0) {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1) {
              if (node.classList && node.classList.contains('ql-youtube-embed')) {
                shouldBind = true;
              } else if (node.querySelector && node.querySelector('.ql-youtube-embed')) {
                shouldBind = true;
              }
            }
          });
        }
      });
      if (shouldBind) {
        setTimeout(function () {
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
    if (productDescription) {
      window.editor.root.innerHTML = productDescription;
      // 載入內容後，多次嘗試綁定（確保 DOM 完全更新）
      setTimeout(function () {
        bindExistingYouTubeResizeHandlers();
      }, 100);
      setTimeout(function () {
        bindExistingYouTubeResizeHandlers();
      }, 300);
      setTimeout(function () {
        bindExistingYouTubeResizeHandlers();
      }, 500);
    }

    // 當編輯器內容改變時，同步到隱藏的 input
    window.editor.on('text-change', function () {
      const content = window.editor.root.innerHTML;
      $('#description').val(content);
    });

    // 監聽編輯器內容變化（包括插入新內容）
    window.editor.on('editor-change', function (eventName, ...args) {
      if (eventName === 'text-change' || eventName === 'selection-change') {
        setTimeout(function () {
          bindExistingYouTubeResizeHandlers();
        }, 100);
      }
    });

    // 初始化時也同步一次
    $('#description').val(window.editor.root.innerHTML);

    // 頁面完全載入後再次綁定
    $(document).ready(function () {
      setTimeout(function () {
        bindExistingYouTubeResizeHandlers();
      }, 500);
    });
  }

  // 當 DOM 載入完成後初始化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      // 從全域變數獲取產品描述（由 Blade 模板設置）
      const productDescription = window.productDescription || null;
      initQuillEditor(productDescription);
    });
  } else {
    // DOM 已經載入完成
    const productDescription = window.productDescription || null;
    initQuillEditor(productDescription);
  }
})();
