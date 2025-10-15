$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const apiUrl = `/${tenant}/admin/members`;
  const memberForm = $('#memberForm');

  const memberId = $('#member_id').val();
  const isEdit = memberId !== '';

  // 初始化台灣城市和地區選擇功能
  initTaiwanCitiesSelect();

  const fv = FormValidation.formValidation(memberForm[0], {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: '請輸入會員姓名'
          }
        }
      },
      status: {
        validators: {
          notEmpty: {
            message: '請選擇會員狀態'
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
            case 'status':
              return '.col-12';
            default:
              return '.col-md-6';
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
        const formData = new FormData(memberForm[0]);
        const formObject = Object.fromEntries(formData.entries());

        showLoading('儲存中...');

        const url = isEdit ? `${apiUrl}/${memberId}` : apiUrl;
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
                text: '正在跳轉到會員管理頁面...',
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
          .delete(`${apiUrl}/${memberId}`)
          .then(function (response) {
            hideLoading();

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: '刪除成功',
                text: '正在跳轉到會員管理頁面...',
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

  // 台灣城市和地區選擇功能
  function initTaiwanCitiesSelect() {
    // 初始化城市 Select2
    $('#city').select2({
      placeholder: '請選擇城市',
      allowClear: true,
      minimumInputLength: 0,
      ajax: {
        url: '/api/taiwan/cities',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return {
            q: params.term || ''
          };
        },
        processResults: function (data) {
          return {
            results: data.results
          };
        },
        cache: true
      }
    });

    // 初始化地區 Select2
    $('#district').select2({
      placeholder: '請先選擇城市',
      allowClear: true,
      minimumInputLength: 0,
      disabled: true
    });

    // 城市選擇變更時更新地區選項
    $('#city').on('change', function () {
      const selectedCity = $(this).val();
      const districtSelect = $('#district');

      // 清空地區選項
      districtSelect.empty();
      $('#zipcode').val('');

      if (selectedCity) {
        // 啟用地區選擇並重新初始化 Select2
        districtSelect.prop('disabled', false);

        // 銷毀現有的 Select2 實例
        districtSelect.select2('destroy');

        // 重新初始化地區 Select2 並設定 AJAX
        districtSelect.select2({
          placeholder: '請選擇地區',
          allowClear: true,
          minimumInputLength: 0,
          ajax: {
            url: `/api/taiwan/districts/${encodeURIComponent(selectedCity)}`,
            dataType: 'json',
            delay: 250,
            data: function (params) {
              return {
                q: params.term || ''
              };
            },
            processResults: function (data) {
              return {
                results: data.results
              };
            },
            cache: true
          }
        });
      } else {
        // 禁用地區選擇
        districtSelect.prop('disabled', true);
        districtSelect.select2('destroy');
        districtSelect.select2({
          placeholder: '請先選擇城市',
          allowClear: true,
          minimumInputLength: 0,
          disabled: true
        });
      }
    });

    // 地區選擇變更時更新郵遞區號
    $('#district').on('change', function () {
      const selectedCity = $('#city').val();
      const selectedDistrict = $(this).val();

      if (selectedCity && selectedDistrict) {
        // 使用 Axios 載入郵遞區號
        axios
          .get(`/api/taiwan/zipcode/${encodeURIComponent(selectedCity)}/${encodeURIComponent(selectedDistrict)}`)
          .then(function (response) {
            $('#zipcode').val(response.data.zipcode || '');
          })
          .catch(function (error) {
            console.error('載入郵遞區號失敗:', error);
            $('#zipcode').val('');
          });
      } else {
        $('#zipcode').val('');
      }
    });

    // 如果是編輯模式，需要載入現有的地區選項
    if (isEdit && $('#city').val() && $('#district').val()) {
      const currentCity = $('#city').val();
      const currentDistrict = $('#district').val();

      // 啟用地區選擇並重新初始化 Select2
      $('#district').prop('disabled', false);
      $('#district').select2('destroy');

      // 重新初始化地區 Select2 並設定 AJAX
      $('#district').select2({
        placeholder: '請選擇地區',
        allowClear: true,
        minimumInputLength: 0,
        ajax: {
          url: `/api/taiwan/districts/${encodeURIComponent(currentCity)}`,
          dataType: 'json',
          delay: 250,
          data: function (params) {
            return {
              q: params.term || ''
            };
          },
          processResults: function (data) {
            return {
              results: data.results
            };
          },
          cache: true
        }
      });

      // 設定當前選中的地區
      const option = new Option(currentDistrict, currentDistrict, true, true);
      $('#district').append(option).trigger('change');
    }
  }
});
