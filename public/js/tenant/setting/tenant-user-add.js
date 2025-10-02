$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const apiUrl = `/${tenant}/admin/users`;
  const userForm = $('#userForm');

  const userId = $('#user_id').val();
  const isEdit = userId !== '';

  // 判斷是否為編輯
  const passwordValidators = isEdit
    ? {
        stringLength: {
          min: 5,
          message: '員工密碼至少需要5個字元'
        }
      }
    : {
        notEmpty: {
          message: '請輸入員工密碼'
        },
        stringLength: {
          min: 5,
          message: '員工密碼至少需要5個字元'
        }
      };

  const fv = FormValidation.formValidation(userForm[0], {
    fields: {
      name: {
        validators: {
          notEmpty: {
            message: '請輸入員工姓名'
          }
        }
      },
      email: {
        validators: {
          notEmpty: {
            message: '請輸入員工信箱'
          },
          emailAddress: {
            message: '請輸入有效的信箱'
          }
        }
      },
      password: {
        validators: passwordValidators
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
        const formData = new FormData(userForm[0]);
        const formObject = Object.fromEntries(formData.entries());

        // 收集選中的權限
        const selectedPermissions = [];
        $('.permission-checkbox:checked').each(function () {
          selectedPermissions.push($(this).val());
        });
        formObject.permissions = selectedPermissions;

        Swal.fire({
          title: '儲存中...',
          showConfirmButton: false,
          allowOutsideClick: false,
          willOpen: () => {
            Swal.showLoading();
          }
        });

        const url = isEdit ? `${apiUrl}/${userId}` : apiUrl;
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
                text: '正在跳轉到員工管理頁面...',
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
          .delete(`${apiUrl}/${userId}`)
          .then(function (response) {
            Swal.close();

            if (response.data.success) {
              Swal.fire({
                icon: 'success',
                title: '刪除成功',
                text: '正在跳轉到員工管理頁面...',
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

  // 角色變更時自動勾選權限
  function loadRolePermissions(selectedRole) {
    if (selectedRole) {
      // 獲取角色的權限
      axios
        .get(`/${tenant}/admin/users/role/${selectedRole}/permissions`)
        .then(function (response) {
          if (response.data.success) {
            const rolePermissions = response.data.data.permissions || [];

            // 清除所有權限勾選
            $('.permission-checkbox').prop('checked', false);

            // 勾選角色擁有的權限
            rolePermissions.forEach(function (permission) {
              $(`input[value="${permission}"]`).prop('checked', true);
            });
          }
        })
        .catch(function (error) {
          console.error('獲取角色權限失敗:', error);
        });
    } else {
      // 如果沒有選擇角色，清除所有權限勾選
      $('.permission-checkbox').prop('checked', false);
    }
  }

  $('#role').on('change', function () {
    loadRolePermissions($(this).val());
  });

  // 頁面載入時，如果有預選的角色，載入其權限
  const initialRole = $('#role').val();
  if (initialRole) {
    loadRolePermissions(initialRole);
  }
});
