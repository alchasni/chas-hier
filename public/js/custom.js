function initializeTable(selector, ajaxUrl, columns) {
    return $(selector).DataTable({
        processing: true,
        autoWidth: false,
        ajax: { url: ajaxUrl },
        columns: columns
    });
}

function handleFormSubmit() {
    $('#modal-form').validator().on('submit', function (e) {
        if (!e.preventDefault()) {
            $.post($('#modal-form form').attr('action'), $('#modal-form form').serialize())
                .done((response) => {
                    $('#modal-form').modal('hide');
                    table.ajax.reload();

                    showToast('success', response?.message || 'Data Saved Successfully');
                })
                .fail((errors) => {
                    showToast('error', errors?.responseText || 'Failed to saved data');
                });
        }
    });
}

function openModal(modalSelector, title, formSelector, actionUrl, method = 'post') {
    $(modalSelector).modal('show');
    $(`${modalSelector} .modal-title`).text(title);

    $(formSelector)[0].reset();
    $(formSelector).attr('action', actionUrl);
    $(formSelector).find('[name=_method]').val(method);
    $(formSelector).find('[name=name]').focus();
}

function selectAllCheckboxes(masterCheckboxSelector, checkboxesSelector) {
    $(masterCheckboxSelector).on('click', function () {
        $(checkboxesSelector).prop('checked', this.checked);
    });
}

function showToast(icon, title) {
    let iconColorClass = '';
    switch (icon) {
        case 'info':
            iconColorClass = 'icon-info';
            break;
        case 'success':
            iconColorClass = 'icon-success';
            break;
        case 'error':
            iconColorClass = 'icon-error';
            break;
        case 'warning':
            iconColorClass = 'icon-warning';
            break;
        default:
            iconColorClass = 'icon-info';
    }

    const Toast = Swal.fire({
        toast: true,
        title: title,
        icon: icon,
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false,
        background: '#333',
        color: '#fff',
        position: 'top-end',
        customClass: {
            container: `icon-${icon}`,
            popup: 'custom-toast-popup'
        },
        didOpen: (toast) => {
            document.addEventListener('click', (event) => {
                if (!toast.contains(event.target)) {
                    Toast.close();
                }
            });
        }
    });
}

function showConfirmToast(title, url, successMsg, failMsg) {
    Swal.fire({
        title: title,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        background: '#333',
        color: '#fff',
        customClass: {
            confirmButton: 'swal2-confirm-button',
            cancelButton: 'swal2-cancel-button',
            title: 'swal2-title-custom',
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(url, {
                '_token': $('[name=csrf-token]').attr('content'),
                '_method': 'delete'
            })
                .done((response) => {
                    table.ajax.reload();
                    showToast('success', response?.message || successMsg);
                })
                .fail((errors) => {
                    showToast('error', errors?.responseText || failMsg);
                    showToast('error', );
                });
        }
    });
}
