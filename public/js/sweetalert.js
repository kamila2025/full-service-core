// ====== 工具函數 ======
function showLoading(message) {
  Swal.fire({
    title: message,
    showConfirmButton: false,
    allowOutsideClick: false,
    willOpen: () => {
      Swal.showLoading();
    }
  });
}

function hideLoading() {
  Swal.close();
}

function showSuccess(message) {
  Swal.fire({
    icon: 'success',
    title: '成功',
    text: message,
    confirmButtonText: '確定'
  });
}

function showError(message) {
  Swal.fire({
    icon: 'error',
    title: '錯誤',
    text: message,
    confirmButtonText: '確定'
  });
}

function confirmDialog(message) {
  return Swal.fire({
    title: '確認',
    text: message,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: '確定',
    cancelButtonText: '取消'
  });
}
