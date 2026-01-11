"use strict";
$(document).ready(function() {
    let datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));
});

$('#reset-btn').on('click', function(){
    $('.check--item-wrapper .check-item .form-check-input[name="modules[]"]').prop('checked', false)
    $('#select-all').prop('checked', false)
})

$('#select-all').on('change', function(){
    if(this.checked === true) {
        $('.check--item-wrapper .check-item .form-check-input[name="modules[]"]').prop('checked', true)
    } else {
        $('.check--item-wrapper .check-item .form-check-input[name="modules[]"]').prop('checked', false)
    }
})

$('.check--item-wrapper .check-item .form-check-input').on('change', function(){
    if(this.checked === true) {
        $(this).prop('checked', true)
    } else {
        $(this).prop('checked', false)
    }
})

// Handle delete confirmation with SweetAlert
$(document).on('click', '.form-alert', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    var $btn = $(this);
    var formId = $btn.data('id');
    var message = $btn.data('message') || 'Are you sure you want to delete this role?';
    
    console.log('Delete button clicked, formId:', formId);
    
    if (!formId) {
        console.error('Form ID not found');
        if (typeof toastr !== 'undefined') {
            toastr.error('Form ID not found');
        }
        return false;
    }
    
    // Extract role ID from form ID (format: role-{id})
    var roleId = formId.replace('role-', '');
    if (!roleId) {
        console.error('Role ID not found in form ID:', formId);
        if (typeof toastr !== 'undefined') {
            toastr.error('Invalid form ID');
        }
        return false;
    }
    
    // Check if form exists
    var $form = $('#' + formId);
    var formAction = $form.length > 0 ? $form.attr('action') : null;
    
    console.log('Form found:', $form.length > 0, 'Action:', formAction);
    
    if (!$form.length || !formAction) {
        console.error('Form not found or no action URL');
        if (typeof toastr !== 'undefined') {
            toastr.error('Form not found. Please refresh the page.');
        } else {
            alert('Form not found. Please refresh the page.');
        }
        return false;
    }
    
    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            console.log('User confirmed deletion, sending AJAX request...');
            
            if (typeof PageLoader !== 'undefined') {
                PageLoader.show();
            }
            
            // Get CSRF token
            var csrfToken = $('meta[name="csrf-token"]').attr('content') || $form.find('input[name="_token"]').val();
            
            console.log('Sending AJAX DELETE request to:', formAction);
            console.log('CSRF Token:', csrfToken ? 'Found' : 'Not found');
            
            // Send AJAX request
            $.ajax({
                url: formAction,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                data: {
                    _token: csrfToken,
                    _method: 'DELETE'
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Delete successful, response:', response);
                    
                    if (typeof PageLoader !== 'undefined') {
                        PageLoader.hide();
                    }
                    
                    // Show success message
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message || 'Role deleted successfully');
                    }
                    
                    // Remove the row from table immediately
                    var $row = $btn.closest('tr');
                    if ($row.length) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                            // Reload page after a short delay to refresh the list
                            setTimeout(function() {
                                window.location.reload();
                            }, 500);
                        });
                    } else {
                        // Reload page if row not found
                        window.location.reload();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Delete error:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    
                    if (typeof PageLoader !== 'undefined') {
                        PageLoader.hide();
                    }
                    
                    var errorMessage = 'Failed to delete role. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        try {
                            var errorData = JSON.parse(xhr.responseText);
                            if (errorData.message) {
                                errorMessage = errorData.message;
                            }
                        } catch (e) {
                            // Use default error message
                        }
                    }
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.error(errorMessage);
                    } else {
                        alert(errorMessage);
                    }
                }
            });
        }
    });
});
