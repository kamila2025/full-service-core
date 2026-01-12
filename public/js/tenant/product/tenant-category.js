$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const apiUrl = `/${tenant}/admin/categories`;
  const $treeContainer = $('#categoryTree');
  const $saveBtn = $('#saveSortBtn');
  const MAX_DEPTH = 4;
  let hasUnsavedChanges = false;

  // 載入分類並渲染
  loadCategories().then(renderTree).then(initSortables);

  // 儲存排序
  $saveBtn.on('click', async function saveSort() {
    if (!hasUnsavedChanges) return;

    const payload = serializeTree();
    try {
      showLoading('儲存排序中...');
      await axios.post(`${apiUrl}/sort`, { categories: payload });
      hideLoading();
      showSuccess('排序更新成功');
      $saveBtn.prop('disabled', true).removeClass('btn-warning').addClass('btn-outline-secondary');
      hasUnsavedChanges = false;
      await reloadTree(); // 重新載入一次，確保排序正確
    } catch (e) {
      hideLoading();
      showError(e?.response?.data?.message || '排序更新失敗，請聯絡管理者');
    }
  });

  // 讀取資料
  async function loadCategories() {
    try {
      const response = await axios.get(`${apiUrl}/tree`);
      const payload = response.data.data ?? response.data ?? [];
      return Array.isArray(payload) ? payload : payload.categories ?? [];
    } catch (e) {
      showError('載入分類失敗');
      return [];
    }
  }

  // 由扁平 => 樹
  function buildTree(flat) {
    const map = {};
    flat.forEach(row => {
      map[row.id] = { ...row, children: [] };
    });
    const roots = [];
    flat.forEach(row => {
      if (row.parent_id && map[row.parent_id]) {
        map[row.parent_id].children.push(map[row.id]);
      } else {
        roots.push(map[row.id]);
      }
    });
    // 依 sort 排序
    const sortRecursive = nodes => {
      nodes.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0));
      nodes.forEach(n => sortRecursive(n.children));
    };
    sortRecursive(roots);
    return roots;
  }

  // 渲染樹狀 UL/LI
  function renderTree(list) {
    const roots = buildTree(list);
    $treeContainer.empty().append(renderList(roots));
  }

  function renderList(nodes, level = 0) {
    const $ul = $('<ul class="cat-list"></ul>');
    nodes.forEach(n => {
      const hasChildren = n.children && n.children.length > 0;
      const levelClass = level === 0 ? 'level-1' : level === 1 ? 'level-2' : 'level-3';

      const $li = $(`
        <li class="cat-item ${levelClass}" data-id="${n.id}" data-level="${level}">
          <div class="cat-card d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
              <span class="drag-handle" title="拖拉排序/移動"><i class="bx bx-grid-vertical fs-5"></i></span>
              ${
                hasChildren
                  ? '<i class="bx bx-chevron-down expand-toggle me-2" style="cursor: pointer;" title="展開/收合"></i>'
                  : '<span class="me-4"></span>'
              }
              <strong>${n.name}</strong>
              <span>指定商品：${n.product_count}</span>
              <span class="badge ${n.status_badge}">${n.status_name}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
              ${
                level === 1
                  ? ''
                  : `
                <button class='btn btn-sm btn-outline-primary add-child' data-id='${n.id}' title='新增子分類'>
                  <i class='bx bx-plus'></i>
                </button>
              `
              }
              <button class="btn btn-sm btn-outline-primary edit" data-id="${n.id}" title="編輯">
                <i class="bx bx-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger delete" data-id="${n.id}" title="刪除">
                <i class="bx bx-trash"></i>
              </button>
            </div>
          </div>
        </li>
      `);

      // 子清單
      if (hasChildren) {
        $li.append(renderList(n.children, level + 1));
      } else {
        $li.append('<ul class="cat-list"></ul>');
      }

      // 新增子分類：打開 modal，將 parent_id 帶入
      $li.on('click', '.add-child', function () {
        $('#categoryId').val('');
        $('#parentId').val($(this).data('id'));
        $('#name').val('');
        $('#status').val('active').trigger('change');
        $('#categoryModal').find('.card-title').text('新增子分類');
        $('#categoryModal').modal('show');
      });

      // 編輯
      $li.on('click', '.edit', async function () {
        const id = $(this).data('id');
        const res = await axios.get(`${apiUrl}/${id}`);
        const row = res.data.data?.category ?? res.data.category ?? res.data;
        $('#categoryId').val(row.id);
        $('#parentId').val(row.parent_id ?? '');
        $('#name').val(row.name ?? '');
        $('#status')
          .val(row.status ?? 'active')
          .trigger('change');
        $('#categoryModal').find('.card-title').text('編輯分類');
        $('#categoryModal').modal('show');
      });

      // 刪除
      $li.on('click', '.delete', async function () {
        const id = $(this).data('id');
        const ok = await confirmDialog('確定要刪除嗎？');
        if (!ok.isConfirmed) return;

        showLoading('刪除中...');

        try {
          await axios.delete(`${apiUrl}/${id}`).then(function (response) {
            hideLoading();
            if (response.data.success) {
              showSuccess(response.data.message);
            } else {
              showError(response.data.message);
            }
          });
          await reloadTree();
        } catch (e) {
          hideLoading();
          showError(e?.response?.data?.message || '刪除失敗');
        }
      });

      $ul.append($li);
    });
    return $ul;
  }

  // 初始化 Sortable：對所有 cat-list 啟用，允許跨清單移動
  function initSortables() {
    // 先摧毀舊的（避免重覆綁定）
    $treeContainer.find('.cat-list').each(function () {
      if (this._sortable) {
        this._sortable.destroy();
        this._sortable = null;
      }
    });

    $treeContainer.find('.cat-list').each(function () {
      const sortable = new Sortable(this, {
        group: { name: 'categories', pull: true, put: true },
        handle: '.drag-handle',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65,
        ghostClass: 'bg-light',
        onMove: function (evt) {
          // 深度限制：目標父層深度 + 被拖曳元素自身深度 <= MAX_DEPTH
          const dragged = evt.dragged; // <li>
          const to = evt.to; // 目標 <ul>
          const newDepth = getUlDepth(to) + 1;
          return newDepth <= MAX_DEPTH;
        },
        onEnd: function () {
          markAsChanged();
        }
      });
      // 存起來以便重設
      this._sortable = sortable;
    });

    // 綁定展開/收合事件
    bindExpandCollapseEvents();
  }

  // 綁定展開/收合事件
  function bindExpandCollapseEvents() {
    $('.expand-toggle')
      .off('click')
      .on('click', function (e) {
        e.stopPropagation();
        const $item = $(this).closest('.cat-item');
        const $list = $item.find('> .cat-list').first();

        if ($list.is(':visible')) {
          $list.slideUp(300);
          $(this).removeClass('bx-chevron-down').addClass('bx-chevron-right');
          $item.addClass('collapsed');
        } else {
          $list.slideDown(300);
          $(this).removeClass('bx-chevron-right').addClass('bx-chevron-down');
          $item.removeClass('collapsed');
        }
      });
  }

  // 標記為已變更
  function markAsChanged() {
    hasUnsavedChanges = true;
    $saveBtn.prop('disabled', false).removeClass('btn-outline-secondary').addClass('btn-warning');
  }

  // 重新讀取並更新 Sortable
  async function reloadTree() {
    const list = await loadCategories();
    renderTree(list);
    initSortables();
  }

  // 扁平化整棵樹：[{id, parent_id, sort}]
  function serializeTree() {
    const arr = [];
    const walk = (ul, parentId) => {
      $(ul)
        .children('.cat-item')
        .each(function (index) {
          const id = parseInt($(this).data('id'), 10);
          arr.push({ id, parent_id: parentId || null, sort: index }); // sort 從 0 開始或 1 視你的後端
          const childUl = $(this).children('.cat-list')[0];
          if (childUl) walk(childUl, id);
        });
    };
    const rootUl = $treeContainer.children('.cat-list')[0];
    if (rootUl) walk(rootUl, null);
    return arr;
  }

  // 深度輔助：<ul> 的層級（root = 1）
  function getUlDepth(ul) {
    let depth = 0,
      el = ul;
    while (el && el !== document && !$(el).is('#categoryTree')) {
      if (el.classList && el.classList.contains('cat-list')) depth++;
      el = el.parentElement;
    }
    return depth; // root 的 <ul> 為 1
  }

  // 新增主分類
  $('#addMainCategoryBtn').on('click', function () {
    $('#categoryId').val('');
    $('#parentId').val('');
    $('#name').val('');
    $('#status').val('active').trigger('change');
    $('#categoryModal').find('.card-title').text('新增主分類');
    $('#categoryModal').modal('show');
  });

  // ====== 表單（新增/編輯）送出 ======
  $('#saveBtn').on('click', async function (e) {
    e.preventDefault();
    const form = document.getElementById('categoryForm');
    const fd = new FormData(form);
    const id = fd.get('categoryId');
    const data = Object.fromEntries(fd.entries());

    try {
      showLoading('儲存中...');
      if (id) {
        await axios.put(`${apiUrl}/${id}`, data).then(function (response) {
          hideLoading();
          if (response.data.success) {
            showSuccess(response.data.message);
          } else {
            showError(response.data.message);
          }
        });
      } else {
        await axios.post(`${apiUrl}`, data).then(function (response) {
          hideLoading();
          if (response.data.success) {
            showSuccess(response.data.message);
          } else {
            showError(response.data.message);
          }
        });
      }

      $('#categoryModal').modal('hide');
      await reloadTree();
    } catch (err) {
      hideLoading();
      showError(err?.response?.data?.message || '儲存失敗');
    }
  });
});
