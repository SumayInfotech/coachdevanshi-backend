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
   DELETE CONFIRMATION (SweetAlert v1)
================================ */
window.bindDeleteAction = function (tableSelector, deleteUrl) {

    $(document).on('click', '.delete-btn', function () {

        const id = $(this).data('uuid');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        swal({
            title: "Are you sure?",
            text: "You can't recover this record after this action!",
            type: "error",
            showCancelButton: true,
            confirmButtonText: "Yes, Delete it!",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function (isConfirm) {

            if (!isConfirm) return;

            $.ajax({
                url: deleteUrl + '/' + id,
                type: "DELETE",
                data: { _token: csrfToken },
                success: function (response) {

                    if (response.success) {

                        swal({
                            title: "Deleted!",
                            text: "Record deleted successfully.",
                            type: "success",
                            timer: 1500,
                            showConfirmButton: true
                        });

                        $(tableSelector).DataTable().ajax.reload(null, false);

                    } else {
                        swal("Error!", response.message || "Something went wrong.", "error");
                    }
                },
                error: function () {
                    swal("Error!", "Delete request failed.", "error");
                }
            });
        });
    });
};


/* ===============================
   STATUS TOGGLE (SweetAlert v1)
================================ */
window.bindStatusToggle = function (tableSelector, statusUrl) {

    $(document).on('change', '.toggle-status', function () {

        const $checkbox = $(this);
        const id = $checkbox.data('uuid');
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        const newStatus = $checkbox.is(':checked') ? 'Active' : 'Inactive';
        const oldStatus = newStatus === 'Active' ? 'Inactive' : 'Active';

        const $labelText = $checkbox
            .closest('.custom-switch')
            .find('.custom-switch-description');

        Swal.fire({
            title: newStatus === 'Active' ? 'Activate?' : 'Deactivate?',
            text: "Are you sure you want to continue?",
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            cancelButtonText: "Cancel",
            closeOnConfirm: false
        }, function (isConfirm) {

            if (!isConfirm) return revert();

            $.ajax({
                url: statusUrl + '/' + id,
                type: "PUT",
                data: {
                    _token: csrfToken,
                    status: newStatus
                },
                success: function (response) {

                    if (response.success) {

                        swal({
                            title: "Updated!",
                            text: "Status updated successfully.",
                            type: "success",
                            timer: 1500,
                            showConfirmButton: true
                        });

                        $(tableSelector).DataTable().ajax.reload(null, false);

                    } else {
                        revert();
                        swal("Error!", response.message || "Update failed.", "error");
                    }
                },
                error: function () {
                    revert();
                    swal("Error!", "Status update failed.", "error");
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
