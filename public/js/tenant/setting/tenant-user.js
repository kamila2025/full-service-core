$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const dataTables = $('.user-datatable');
  const apiUrl = `/${tenant}/admin/users`;

  if (dataTables.length) {
    var table = dataTables.DataTable({
      processing: true,
      serverSide: true,
      paging: true,
      info: true,
      fixedColumns: false,
      searching: false,
      ajax: {
        url: apiUrl,
        type: 'GET',
        error: function (xhr, error, thrown) {
          Swal.fire({
            icon: 'error',
            title: '資料載入錯誤',
            text: '無法載入資料，請重新整理頁面',
            confirmButtonText: '確定'
          });
        }
      },
      columns: [{ data: 'user_name' }, { data: 'user_email' }, { data: 'user_created_at' }, { data: null }],
      columnDefs: [
        {
          targets: -1,
          orderable: false,
          render: function (data, type, full, meta) {
            const isAdmin = full.user_isAdmin;

            if (!isAdmin) {
              return `
              <div class="d-flex align-items-center">
                <div class="dropdown">
                  <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    操作
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a href="${apiUrl}/${full.id}/edit" class="dropdown-item d-flex align-items-center">
                        <span>編輯</span>
                      </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <a href="javascript:void(0)" class="dropdown-item d-flex align-items-center text-danger delete-record" data-id="${full.id}">
                        <span>刪除</span>
                      </a>
                    </li>
                  </ul>
                  </div>
                </div>
              `;
            } else {
              return ``;
            }
          }
        }
      ],
      dom:
        '<"row mx-1"' +
        '<"col-sm-12 col-md-3" l>' +
        '<"col-sm-12 col-md-9"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1"<"me-3"f>rB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      displayLength: 10, // 每頁顯示幾筆
      lengthMenu: [10, 25, 50, 75, 100], // 顯示選單中的選項
      language: {
        processing: '處理中...',
        loadingRecords: '載入中...',
        lengthMenu: '顯示 _MENU_ 項結果',
        zeroRecords: '沒有符合的結果',
        info: '顯示第( _START_ ~ _END_ )項結果【共 _TOTAL_ 筆】',
        infoEmpty: '顯示第 0 至 0 項結果，共 0 項',
        infoFiltered: '(從 _MAX_ 項結果中過濾)',
        infoPostFix: '',
        search: '搜尋:',
        searchPlaceholder: '關鍵字..',
        paginate: {
          first: '第一頁',
          previous: '上一頁',
          next: '下一頁',
          last: '最後一頁'
        },
        aria: {
          sortAscending: ': 升冪排列',
          sortDescending: ': 降冪排列'
        }
      },
      buttons: []
    });
  }

  // 刪除員工
  $(document).on('click', '.delete-record', function () {
    const id = $(this).data('id');

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
          .delete(`${apiUrl}/${id}`)
          .then(function (response) {
            Swal.close();

            if (response.data.success) {
              table.ajax.reload();

              Swal.fire({
                icon: 'success',
                title: '刪除成功',
                text: response.data.message,
                customClass: {
                  confirmButton: 'btn btn-success'
                }
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
