$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const apiUrl = `/${tenant}/admin/products`;
  const productForm = $('#productForm');

  const productId = $('#product_id').val();
  const isEdit = productId !== '';

  // 圖片上傳相關變數
  let images = [];

  const previewTemplate = `<div class="dz-preview dz-file-preview">
    <div class="dz-details">
      <div class="dz-thumbnail">
        <img data-dz-thumbnail>
        <span class="dz-nopreview">No preview</span>
        <div class="dz-success-mark"></div>
        <div class="dz-error-mark"></div>
        <div class="dz-error-message"><span data-dz-errormessage></span></div>
        <div class="progress">
          <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
        </div>
      </div>
      <div class="dz-filename" data-dz-name></div>
      <div class="dz-size" data-dz-size></div>
    </div>
  </div>`;

  // 初始化 Dropzone
  const dropzoneImages = document.querySelector('#dropzone-images');
  if (dropzoneImages) {
    const myDropzoneMulti = new Dropzone(dropzoneImages, {
      previewTemplate: previewTemplate,
      parallelUploads: 1,
      maxFilesize: 10,
      addRemoveLinks: true,
      dictRemoveFile: '移除檔案',
      dictCancelUpload: '取消上傳',
      url: '#',
      init: function () {
        // 如果是編輯模式，載入現有圖片
        if (isEdit && window.existingImages) {
          console.log('Loading existing images:', window.existingImages);
          const dropzone = this;
          window.existingImages.forEach(function (imageData) {
            const mockFile = {
              id: imageData.id,
              name: imageData.filename,
              size: imageData.size
            };

            images.push({
              name: imageData.filename,
              size: 0,
              type: 'image/jpeg',
              data: imageData.url,
              isExisting: true,
              id: imageData.id
            });

            dropzone.displayExistingFile(mockFile, imageData.url);
          });
        }

        this.on('addedfile', function (file) {
          // 將檔案轉為 base64
          const reader = new FileReader();
          reader.onload = function (e) {
            const base64Data = e.target.result;
            images.push({
              name: file.name,
              size: file.size,
              type: file.type,
              data: base64Data
            });
          };
          reader.readAsDataURL(file);
        });

        this.on('removedfile', function (file) {
          const removedImage = images.find(f => f.name === file.name);

          // 如果是現有圖片，需要從資料庫刪除
          if (removedImage && removedImage.isExisting && removedImage.id) {
            axios
              .delete(`/${tenant}/admin/products/images/${removedImage.id}`)
              .then(function (response) {
                if (response.data.success) {
                  images = images.filter(f => f.name !== file.name);

                  Swal.fire({
                    icon: 'success',
                    title: '圖片刪除成功',
                    text: response.data.message,
                    confirmButtonText: '確定'
                  });
                } else {
                  images = images.filter(f => f.name !== file.name);

                  Swal.fire({
                    icon: 'error',
                    title: '圖片刪除失敗',
                    text: response.data.message,
                    confirmButtonText: '確定'
                  });
                }
              })
              .catch(function (error) {
                images = images.filter(f => f.name !== file.name);

                Swal.fire({
                  icon: 'error',
                  title: '圖片刪除失敗',
                  text: error.response.data.message,
                  confirmButtonText: '確定'
                });
              });
          } else {
            images = images.filter(f => f.name !== file.name);
          }
        });
      }
    });
  }

  const fv = FormValidation.formValidation(productForm[0], {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: '請輸入商品名稱'
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: function (field, ele) {
          switch (field) {
            default:
              return '.col-12';
          }
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  $('#saveButton').on('click', function (e) {
    e.preventDefault();

    fv.validate().then(function (status) {
      if (status === 'Valid') {
        const formData = new FormData(productForm[0]);
        const formObject = Object.fromEntries(formData.entries());

        // 分類處理
        let categories = [];
        $('#categories option:selected').each(function () {
          categories.push($(this).val());
        });
        formObject.categories = categories;

        // 圖片處理
        const newImages = images.filter(img => !img.isExisting);
        formObject.images = newImages;

        // 多規格處理
        let variants = [];

        // 檢查是否有多規格資料
        if (window.variantData && window.variantData.combinations.length > 0) {
          // 使用多規格資料
          variants = window.variantData.combinations.map(combination => ({
            sku: combination.sku,
            price: combination.price,
            compare_at_price: combination.compare_at_price,
            cost_price: combination.cost_price,
            barcode: combination.barcode,
            combination: combination.combination
          }));
        } else {
          // 如果沒有多規格資料，使用主要價格欄位作為單一規格
          variants.push({
            sku: `SKU-${Date.now()}`,
            price: $('#price').val(),
            compare_at_price: $('#compare_at_price').val(),
            cost_price: $('#cost_price').val(),
            barcode: null,
            combination: '預設規格'
          });
        }

        formObject.variants = variants;

        showLoading('儲存中...');

        const url = isEdit ? `${apiUrl}/${productId}` : apiUrl;
        const method = isEdit ? 'PUT' : 'POST';

        axios({
          method: method,
          url: url,
          data: formObject
        })
          .then(function (response) {
            hideLoading();

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: '儲存成功！',
                text: '正在跳轉到商品管理頁面...',
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false
              }).then(function () {
                window.location.href = response.data.data.redirect_url;
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: '儲存失敗',
                text: response.data.message,
                confirmButtonText: '確定'
              });
            }
          })
          .catch(function (error) {
            hideLoading();

            Swal.fire({
              icon: 'error',
              title: '儲存失敗',
              text: error.response.data.message,
              confirmButtonText: '確定'
            });
          });
      }
    });
  });

  $('#deleteBtn').on('click', function (e) {
    e.preventDefault();

    Swal.fire({
      title: '確定要刪除嗎',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: '確定刪除',
      cancelButtonText: '取消'
    }).then(result => {
      if (result.isConfirmed) {
        Swal.fire({
          title: '刪除中...',
          showConfirmButton: false,
          allowOutsideClick: false,
          willOpen: () => {
            Swal.showLoading();
          }
        });

        axios
          .delete(`${apiUrl}/${productId}`)
          .then(function (response) {
            hideLoading();

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: '刪除成功',
                text: '正在跳轉到商品管理頁面...',
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false
              }).then(function () {
                window.location.href = response.data.data.redirect_url;
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: '刪除失敗',
                text: response.data.message,
                confirmButtonText: '確定'
              });
            }
          })
          .catch(function (error) {
            hideLoading();

            Swal.fire({
              icon: 'error',
              title: '刪除失敗',
              text: error.response.data.message,
              confirmButtonText: '確定'
            });
          });
      }
    });
  });
});

// 多規格管理功能
let variantTypes = [];
let variantCombinations = [];
let tagifyInstances = [];

// 增加規格類型
function addVariantType() {
  const container = document.getElementById('variantTypesContainer');
  const variantTypeHtml = `
    <div class="variant-type-item mb-3 p-3 border rounded">
      <div class="row align-items-center">
        <div class="col-md-4">
          <label class="form-label">規格名稱</label>
          <input type="text" class="form-control variant-type-name" placeholder="例：顏色">
        </div>
        <div class="col-md-6">
          <label class="form-label">選項</label>
          <input type="text" class="form-control variant-options-tagify"
                 placeholder="輸入選項後按 Enter 新增">
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-danger" onclick="removeVariantType(this)">
            <i class="bx bx-trash"></i>
          </button>
        </div>
      </div>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', variantTypeHtml);

  // 初始化新的 Tagify 實例
  initializeTagify();
  updateVariantCombinations();
}

// 初始化 Tagify
function initializeTagify() {
  // 清除現有的 Tagify 實例
  tagifyInstances.forEach(instance => {
    if (instance && instance.destroy) {
      instance.destroy();
    }
  });
  tagifyInstances = [];

  // 為所有 Tagify 輸入框初始化
  document.querySelectorAll('.variant-options-tagify').forEach(input => {
    if (!input.tagify) {
      const tagify = new Tagify(input, {
        placeholder: '輸入選項後按 Enter 新增',
        delimiters: ',| ', // 支援逗號和空格分隔
        maxTags: 20,
        dropdown: {
          enabled: 0 // 關閉自動完成下拉選單
        },
        transformTag: function (tagData) {
          tagData.class = 'tagify__tag--primary';
        }
      });

      // 監聽變化事件
      tagify.on('change', function () {
        updateVariantCombinations();
      });

      tagifyInstances.push(tagify);
    }
  });
}

// 移除規格類型
function removeVariantType(button) {
  button.closest('.variant-type-item').remove();
  updateVariantCombinations();
}

// 更新規格組合表格
function updateVariantCombinations() {
  const types = [];
  const typeItems = document.querySelectorAll('.variant-type-item');

  typeItems.forEach(item => {
    const name = item.querySelector('.variant-type-name').value;
    const tagifyInput = item.querySelector('.variant-options-tagify');

    let options = [];
    if (tagifyInput && tagifyInput.tagify) {
      // 從 Tagify 取得選項
      options = tagifyInput.tagify.value.map(tag => tag.value).filter(val => val.trim());
    } else {
      // 備用方案：從原始輸入框取得值
      const inputValue = tagifyInput ? tagifyInput.value : '';
      options = inputValue
        .split(',')
        .map(opt => opt.trim())
        .filter(val => val);
    }

    if (name.trim() && options.length > 0) {
      types.push({ name, options });
    }
  });

  // 生成所有可能的組合
  const combinations = generateCombinations(types);

  // 更新表格
  const tbody = document.getElementById('variantCombinationsTable');
  tbody.innerHTML = '';

  combinations.forEach((combination, index) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${combination.name}</td>
      <td><input type="text" class="form-control form-control-sm variant-sku" placeholder="SKU"></td>
      <td><input type="number" class="form-control form-control-sm variant-price" value="0" min="0" required></td>
      <td><input type="number" class="form-control form-control-sm variant-compare-price" value="0" min="0"></td>
      <td><input type="number" class="form-control form-control-sm variant-cost-price" value="0" min="0"></td>
      <td><input type="text" class="form-control form-control-sm variant-barcode" placeholder="條碼"></td>
    `;
    tbody.appendChild(row);
  });

  variantTypes = types;
  variantCombinations = combinations;
}

// 生成所有可能的組合
function generateCombinations(types) {
  if (types.length === 0) return [];

  function cartesianProduct(arrays) {
    return arrays.reduce(
      (acc, curr) => {
        const result = [];
        acc.forEach(a => {
          curr.forEach(c => {
            result.push([...a, c]);
          });
        });
        return result;
      },
      [[]]
    );
  }

  const combinations = cartesianProduct(types.map(type => type.options));

  return combinations.map(combination => ({
    name: combination.join('.'),
    values: combination
  }));
}

// 儲存規格
function saveVariants() {
  const combinations = [];
  const rows = document.querySelectorAll('#variantCombinationsTable tr');

  rows.forEach((row, index) => {
    const sku = row.querySelector('.variant-sku').value;
    const price = row.querySelector('.variant-price').value;
    const comparePrice = row.querySelector('.variant-compare-price').value;
    const costPrice = row.querySelector('.variant-cost-price').value;
    const barcode = row.querySelector('.variant-barcode').value;

    if (price && price > 0) {
      combinations.push({
        sku: sku || `SKU-${Date.now()}-${index}`,
        price: parseFloat(price),
        compare_at_price: comparePrice ? parseFloat(comparePrice) : null,
        cost_price: costPrice ? parseFloat(costPrice) : null,
        barcode: barcode || null,
        combination: variantCombinations[index] ? variantCombinations[index].name : ''
      });
    }
  });

  // 更新變數供表單提交使用
  window.variantData = {
    types: variantTypes,
    combinations: combinations
  };

  // 更新顯示區域
  updateVariantDisplay(combinations);

  // 關閉 Modal
  const modal = bootstrap.Modal.getInstance(document.getElementById('variantModal'));
  modal.hide();

  Swal.fire({
    icon: 'success',
    title: '規格儲存成功',
    text: `已建立 ${combinations.length} 個規格組合`,
    confirmButtonText: '確定'
  });
}

// 更新規格顯示區域
function updateVariantDisplay(combinations) {
  const container = document.getElementById('variantContainer');

  if (combinations.length === 0) {
    container.innerHTML = '<p class="mb-0 text-muted">為您的商品新增規格，例：尺寸、顏色</p>';
    return;
  }

  let html = '<div class="row">';
  combinations.forEach((combination, index) => {
    html += `
      <div class="col-md-6 mb-3">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title">${combination.combination}</h6>
            <div class="row">
              <div class="col-6">
                <small class="text-muted">SKU:</small><br>
                <span>${combination.sku}</span>
              </div>
              <div class="col-6">
                <small class="text-muted">售價:</small><br>
                <span class="text-primary fw-bold">$${combination.price}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  });
  html += '</div>';

  container.innerHTML = html;
}

// 當 Modal 開啟時初始化 Tagify
document.addEventListener('DOMContentLoaded', function () {
  const variantModal = document.getElementById('variantModal');
  if (variantModal) {
    variantModal.addEventListener('shown.bs.modal', function () {
      // 延遲初始化，確保 DOM 已完全載入
      setTimeout(() => {
        initializeTagify();
        updateVariantCombinations();
      }, 100);
    });
  }
});
