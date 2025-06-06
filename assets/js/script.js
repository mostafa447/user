$(document).ready(function() {
    // Add User Form Submission
    $('#add-user-form').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#name').val(),
            email: $('#email').val(),
            phone: $('#phone').val()
        };
        
        $.ajax({
            url: 'api/usersApi.php',
            type: 'POST',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if(response.status === 'success') {
                    // Add the new user to the table
                    const user = response.data;
                    const newRow = `
                        <tr id="user-row-${user.id}">
                            <td>${user.id}</td>
                            <td>${escapeHtml(user.name)}</td>
                            <td>${escapeHtml(user.email)}</td>
                            <td>${escapeHtml(user.phone || '')}</td>
                            <td>${user.created_at}</td>
                            <td class="action-buttons">
                                <button class="btn btn-sm btn-info edit-user" data-id="${user.id}" 
                                        data-name="${escapeHtml(user.name)}" 
                                        data-email="${escapeHtml(user.email)}" 
                                        data-phone="${escapeHtml(user.phone || '')}" 
                                        data-bs-toggle="modal" data-bs-target="#editUserModal">
                                    Edit
                                </button>
                                <button class="btn btn-sm btn-danger delete-user" data-id="${user.id}">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    `;
                    $('#users-table-body').prepend(newRow);
                    
                    // Reset form and close modal
                    $('#add-user-form')[0].reset();
                    $('#addUserModal').modal('hide');
                    
                    // Show success message
                    showMessage('success', response.message);
                } else {
                    // Show error message in the form
                    $('#add-form-message').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function() {
                $('#add-form-message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
    
    // Edit User - Populate Form
    $(document).on('click', '.edit-user', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const email = $(this).data('email');
        const phone = $(this).data('phone');
        
        $('#edit-id').val(id);
        $('#edit-name').val(name);
        $('#edit-email').val(email);
        $('#edit-phone').val(phone);
    });
    
    // Edit User Form Submission
    $('#edit-user-form').submit(function(e) {
        e.preventDefault();
        
        const formData = {
            id: $('#edit-id').val(),
            name: $('#edit-name').val(),
            email: $('#edit-email').val(),
            phone: $('#edit-phone').val()
        };
        
        $.ajax({
            url: 'api/usersApi.php',
            type: 'PUT',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            success: function(response) {
                if(response.status === 'success') {
                    // Update the user in the table
                    const user = response.data;
                    const row = $(`#user-row-${user.id}`);
                    row.html(`
                        <td>${user.id}</td>
                        <td>${escapeHtml(user.name)}</td>
                        <td>${escapeHtml(user.email)}</td>
                        <td>${escapeHtml(user.phone || '')}</td>
                        <td>${user.created_at}</td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-info edit-user" data-id="${user.id}" 
                                    data-name="${escapeHtml(user.name)}" 
                                    data-email="${escapeHtml(user.email)}" 
                                    data-phone="${escapeHtml(user.phone || '')}" 
                                    data-bs-toggle="modal" data-bs-target="#editUserModal">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-danger delete-user" data-id="${user.id}">
                                Delete
                            </button>
                        </td>
                    `);
                    
                    // Close modal
                    $('#editUserModal').modal('hide');
                    
                    // Show success message
                    showMessage('success', response.message);
                } else {
                    // Show error message in the form
                    $('#edit-form-message').html(`<div class="alert alert-danger">${response.message}</div>`);
                }
            },
            error: function() {
                $('#edit-form-message').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
            }
        });
    });
    
    // Delete User
    $(document).on('click', '.delete-user', function() {
        if(confirm('Are you sure you want to delete this user?')) {
            const id = $(this).data('id');
            
            $.ajax({
                url: 'api/usersApi.php',
                type: 'DELETE',
                data: JSON.stringify({id: id}),
                contentType: 'application/json',
                success: function(response) {
                    if(response.status === 'success') {
                        // Remove the user from the table
                        $(`#user-row-${id}`).remove();
                        
                        // Show success message
                        showMessage('success', response.message);
                    } else {
                        // Show error message
                        showMessage('danger', response.message);
                    }
                },
                error: function() {
                    showMessage('danger', 'An error occurred. Please try again.');
                }
            });
        }
    });
    
    // Helper function to show messages
    function showMessage(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const messageHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;
        
        $('#message-container').html(messageHtml);
        
        // Auto-hide the message after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
    
    // Helper function to escape HTML
    function escapeHtml(str) {
        if (!str) return '';
        return str
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});