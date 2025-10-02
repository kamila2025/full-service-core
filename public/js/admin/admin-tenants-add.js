$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const apiUrl = '/admin/tenants';
  const admintenantForm = $('#adminTenantForm');

  const tenantId = $('#tenant_id').val();
  const isEdit = tenantId !== '';

  // 判斷是否為編輯
  const passwordValidators = isEdit
    ? {
        stringLength: {
          min: 5,
          message: '租戶密碼至少需要5個字元'
        }
      }
    : {
        notEmpty: {
          message: '請輸入租戶密碼'
        },
        stringLength: {
          min: 5,
          message: '租戶密碼至少需要5個字元'
        }
      };

  const fv = FormValidation.formValidation(admintenantForm[0], {
    fields: {
      id: {
        validators: {
          notEmpty: {
            message: '請輸入租戶ID'
          }
        }
      },
      name: {
        validators: {
          notEmpty: {
            message: '請輸入租戶名稱'
          }
        }
      },
      email: {
        validators: {
          notEmpty: {
            message: '請輸入租戶信箱'
          },
          emailAddress: {
            message: '請輸入有效的信箱'
          }
        }
      },
      password: {
        validators: passwordValidators
      },
      expire_date: {
        validators: {
          notEmpty: {
            message: '請選擇租戶到期時間'
          },
          date: {
            format: 'YYYY-MM-DD',
            message: '請選擇有效的日期'
          }
        }
      },
      status: {
        validators: {
          notEmpty: {
            message: '請選擇租戶狀態'
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
        const formData = new FormData(admintenantForm[0]);
        const formObject = Object.fromEntries(formData.entries());

        Swal.fire({
          title: '儲存中...',
          showConfirmButton: false,
          allowOutsideClick: false,
          willOpen: () => {
            Swal.showLoading();
          }
        });

        const url = isEdit ? `${apiUrl}/${tenantId}` : apiUrl;
        const method = isEdit ? 'PUT' : 'POST';

        if (isEdit && !formObject.password) {
          delete formObject.password;
        }

        axios({
          method: method,
          url: url,
          data: formObject
        })
          .then(function (response) {
            Swal.close();

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: '儲存成功！',
                text: '正在跳轉到租戶管理頁面...',
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
            Swal.close();

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
          .delete(`${apiUrl}/${tenantId}`)
          .then(function (response) {
            Swal.close();

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: '刪除成功',
                text: '正在跳轉到租戶管理頁面...',
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
            Swal.close();

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
