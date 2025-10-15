$(function () {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  const dataTables = $('.member-datatable');
  const apiUrl = `/${tenant}/admin/members`;

  // 搜尋欄位
  const searchName = $('#searchName');
  const searchPhone = $('#searchPhone');
  const searchEmail = $('#searchEmail');
  const searchGender = $('#searchGender');
  const searchZipcode = $('#searchZipcode');
  const searchCity = $('#searchCity');
  const searchDistrict = $('#searchDistrict');
  const searchStatus = $('#searchStatus');
  const searchBirthdayStartDate = $('#searchBirthdayStartDate');
  const searchBirthdayEndDate = $('#searchBirthdayEndDate');
  const searchCreatedStartDate = $('#searchCreatedStartDate');
  const searchCreatedEndDate = $('#searchCreatedEndDate');
  const searchBtn = $('#searchBtn');
  const resetBtn = $('#resetBtn');

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
        data: function (d) {
          d.name = searchName.val();
          d.phone = searchPhone.val();
          d.email = searchEmail.val();
          d.gender = searchGender.val();
          d.zipcode = searchZipcode.val();
          d.city = searchCity.val();
          d.district = searchDistrict.val();
          d.status = searchStatus.val();
          d.birthday_start = searchBirthdayStartDate.val();
          d.birthday_end = searchBirthdayEndDate.val();
          d.created_start = searchCreatedStartDate.val();
          d.created_end = searchCreatedEndDate.val();
        },
        error: function (xhr, error, thrown) {
          Swal.fire({
            icon: 'error',
            title: '資料載入錯誤',
            text: '無法載入資料，請重新整理頁面',
            confirmButtonText: '確定'
          });
        }
      },
      columns: [
        { data: 'member_name' },
        { data: 'member_email' },
        { data: 'member_phone' },
        { data: 'member_gender' },
        { data: 'member_birthday' },
        { data: 'member_address' },
        { data: 'member_status' },
        { data: 'member_created_at' },
        { data: null }
      ],
      columnDefs: [
        {
          targets: 0,
          orderable: false,
          searchable: false,
          render: function (data, type, full, meta) {
            return `<a href="${apiUrl}/${full.member_id}/edit">${full.member_name}</a>`;
          }
        },
        {
          targets: 3,
          render: function (data, type, full, meta) {
            return `<span class="badge ${full['member_gender_badge']}">${full['member_gender']}</span>`;
          }
        },
        {
          targets: 6,
          render: function (data, type, full, meta) {
            return `<span class="badge ${full['member_status_badge']}">${full['member_status']}</span>`;
          }
        },
        {
          targets: -1,
          orderable: false,
          render: function (data, type, full, meta) {
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

  // 搜尋事件
  searchBtn.on('click', function () {
    table.draw();
  });

  // 重置事件
  resetBtn.on('click', function () {
    searchName.val('');
    searchPhone.val('');
    searchEmail.val('');
    searchGender.val('').trigger('change');
    searchZipcode.val('');
    searchCity.val('');
    searchDistrict.val('');
    searchStatus.val('').trigger('change');
    searchBirthdayStartDate.val('');
    searchBirthdayEndDate.val('');
    searchCreatedStartDate.val('');
    searchCreatedEndDate.val('');
    table.draw();
  });

  // 刪除商品
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
