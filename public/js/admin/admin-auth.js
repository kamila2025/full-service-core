$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const adminLoginForm = $('#AdminLoginForm');
  const fv = FormValidation.formValidation(adminLoginForm[0], {
    fields: {
      email: {
        validators: {
          notEmpty: {
            message: '請輸入帳號'
          },
          emailAddress: {
            message: '請輸入有效的信箱'
          }
        }
      },
      password: {
        validators: {
          notEmpty: {
            message: '請輸入密碼'
          },
          stringLength: {
            min: 5,
            message: '密碼至少需要5個字元'
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
              return '.mb-3';
          }
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  });

  // 登入按鈕提交
  $('#loginBtn').on('click', function (e) {
    e.preventDefault();

    fv.validate().then(function (status) {
      if (status === 'Valid') {
        // 獲取表單數據
        const formData = new FormData(adminLoginForm[0]);
        const formObject = Object.fromEntries(formData.entries());

        Swal.fire({
          title: '登入中...',
          showConfirmButton: false,
          allowOutsideClick: false,
          willOpen: () => {
            Swal.showLoading();
          }
        });

        // 發送axios請求
        axios
          .post('/admin/login', formObject)
          .then(function (response) {
            // 關閉載入視窗
            Swal.close();

            if (response.data.success) {
              // 登入成功
              Swal.fire({
                icon: 'success',
                title: '登入成功！',
                text: '正在跳轉到管理後台...',
                timer: 1500,
                timerProgressBar: true,
                showConfirmButton: false,
                allowOutsideClick: false
              }).then(function () {
                window.location.href = response.data.data.redirect_url;
              });
            } else {
              // 登入失敗
              Swal.fire({
                icon: 'error',
                title: '登入失敗',
                text: response.data.message || '請檢查您的帳號密碼',
                confirmButtonText: '確定'
              });
            }
          })
          .catch(function (error) {
            // 關閉載入視窗
            Swal.close();

            Swal.fire({
              icon: 'error',
              title: '登入失敗',
              text: error.response.data.message,
              confirmButtonText: '確定'
            });
          });
      }
    });
  });

  // Enter鍵提交表單
  $('#email, #password').on('keypress', function (e) {
    if (e.which === 13) {
      $('#loginBtn').click();
    }
  });
});
