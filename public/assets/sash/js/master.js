/* ===============================
   SELECT2 INITIALIZER (GLOBAL)
================================ */
window.initSelect2 = function (scope = document) {
    $(scope).find('.select2-searchable').select2({
        width: '100%',
        placeholder: 'Select an option',
        allowClear: false
    });
};


/* ===============================
   DATATABLE FILTER HANDLER
================================ */
window.initDataTableFilters = function (tableSelector, filters) {

    const table = $(tableSelector).DataTable();

    table.on('preXhr.dt', function (e, settings, data) {
        filters.forEach(function (filter) {
            data[filter] = $('#' + filter).val();
        });
    });

    $('#searchBtn').off('click').on('click', function () {
        table.ajax.reload();
    });

    $('#resetBtn').off('click').on('click', function () {
        $('#filters')[0].reset();
        table.ajax.reload();
    });
};


/* ===============================
   DELETE CONFIRMATION (SweetAlert2)
================================ */
window.bindDeleteAction = function (tableSelector, deleteUrl) {

    $(document).on('click', '.delete-btn', function () {

        const id = $(this).data('uuid');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        Swal.fire({
            title: "Are you sure?",
            text: "You can't recover this record after this action!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete it!",
            cancelButtonText: "Cancel"
        }).then(result => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: deleteUrl + '/' + id,
                type: "DELETE",
                data: { _token: csrfToken },
                success: function (response) {

                    if (!response.success) {
                        Swal.fire("Error!", response.message || "Delete failed.", "error");
                        return;
                    }

                    Swal.fire({
                        icon: "success",
                        title: "Deleted!",
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: true
                    }).then(() => {

                        // ✅ IF DataTable exists → reload
                        if ($.fn.DataTable && $(tableSelector).length) {
                            $(tableSelector).DataTable().ajax.reload(null, false);
                        }
                        // ✅ ELSE (view/edit page) → redirect
                        else {
                            window.location.href = document.referrer || '/';
                        }
                    });
                },
                error: function () {
                    Swal.fire("Error!", "Delete request failed.", "error");
                }
            });
        });
    });
};


/* ===============================
   STATUS TOGGLE (SweetAlert2)
================================ */
window.bindStatusToggle = function (tableSelector, statusUrl) {

    $(document).on('change', '.toggle-status', function () {

        const $checkbox = $(this);
        const id = $checkbox.data('uuid');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        const table = $(tableSelector).DataTable();

        const newStatus = $checkbox.is(':checked') ? 'Active' : 'Inactive';
        const oldStatus = newStatus === 'Active' ? 'Inactive' : 'Active';

        const $labelText = $checkbox
            .closest('.custom-switch')
            .find('.status-text');

        Swal.fire({
            title: newStatus === 'Active' ? 'Activate?' : 'Deactivate?',
            text: "Are you sure you want to continue?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel"
        }).then(result => {

            if (!result.isConfirmed) {
                revert();
                return;
            }

            $.ajax({
                url: statusUrl + '/' + id,
                type: "PUT",
                data: {
                    _token: csrfToken,
                    status: newStatus
                },
                success: function (response) {

                    if (response.success) {

                        Swal.fire({
                            icon: "success",
                            title: "Updated!",
                            text: "Status updated successfully.",
                            timer: 1500,
                            showConfirmButton: true
                        }).then(() => {
                            table.ajax.reload(null, false);
                            table.columns.adjust();
                        });

                    } else {
                        revert();
                        Swal.fire("Error!", response.message || "Update failed.", "error");
                    }
                },
                error: function () {
                    revert();
                    Swal.fire("Error!", "Status update failed.", "error");
                }
            });
        });

        function revert() {
            $checkbox.prop("checked", !$checkbox.prop("checked"));
            $labelText.text(oldStatus);
        }
    });
};


/* ===============================
   STATUS CHANGE – VIEW PAGE(Seperate Handler)
================================ */
window.bindViewStatusChange = function (statusUrl) {

    $(document).on('click', '.status-change-btn', function () {
        const $btn = $(this);
        const id = $btn.data('uuid');
        const csrf = $('meta[name="csrf-token"]').attr('content');

        const currentStatus = $btn.data('status');
        const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';

        Swal.fire({
            title: newStatus === 'Active' ? 'Activate User?' : 'Deactivate User?',
            text: 'Are you sure you want to continue?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'Cancel'
        }).then(result => {

            if (!result.isConfirmed) return;

            $.ajax({
                url: statusUrl + '/' + id,
                type: 'PUT',
                data: {
                    _token: csrf,
                    status: newStatus
                },
                success: function (res) {

                    if (!res.success) {
                        return Swal.fire('Error!', res.message || 'Update failed.', 'error');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        timer: 1200,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.reload();
                    });
                    // update badge UI
                    $btn
                        .data('status', newStatus)
                        .text(newStatus)
                        .toggleClass('bg-success bg-danger');
                },
                error: function () {
                    Swal.fire('Error!', 'Status update failed.', 'error');
                }
            });
        });
    });
};


/* ===============================
   STATE → CITY DEPENDENCY
================================ */
window.bindStateCity = function (stateSelector, citySelector, url, selectedCityId = null) {

    $(stateSelector).on('change', function () {

        const stateId = $(this).val();
        $(citySelector).html('<option value="">Select City</option>');

        if (!stateId) return;

        $("#global-loader").show();

        $.ajax({
            url: url,
            type: "POST",
            data: {
                state_id: stateId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (cities) {

                $.each(cities, function (_, city) {
                    $(citySelector).append(
                        '<option value="' + city.id + '">' + city.name + '</option>'
                    );
                });

                // ✅ EDIT PAGE SUPPORT: auto-select city
                if (selectedCityId) {
                    $(citySelector).val(selectedCityId).trigger('change');
                }

                $("#global-loader").hide();
            }
        });
    });
};
