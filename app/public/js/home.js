// Alert and escapeHtml functions are now in utils.js

// Function to load contacts
function loadContacts() {
    fetch('/api/contacts')
        .then(response => response.json())
        .then(data => {
            const contactsList = document.getElementById('contactsList');
            if (data.success && data.contacts.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Image</th><th>First Name</th><th>Last Name</th><th>Phone</th><th>Email</th><th>Actions</th></tr></thead><tbody>';
                data.contacts.forEach(contact => {
                    const imageHtml = contact.image_path
                        ? `<img src="${escapeHtml(contact.image_path)}" alt="Contact" class="contact-image">`
                        : '<span class="text-muted">No image</span>';
                    html += `<tr>
                        <td>${imageHtml}</td>
                        <td>${escapeHtml(contact.first_name || '')}</td>
                        <td>${escapeHtml(contact.last_name)}</td>
                        <td>${escapeHtml(contact.phone)}</td>
                        <td>${escapeHtml(contact.email)}</td>
                        <td>
                            <div class="contact-row" data-id="${contact.id}">
                                <a href="#" class="edit-link btn btn-sm btn-primary" data-id="${contact.id}">Edit</a>
                                <a href="#" class="delete-link btn btn-sm btn-danger ms-1" data-id="${contact.id}">Delete</a>
                            </div>
                        </td>

                    </tr>`;
                });
                html += '</tbody></table></div>';
                contactsList.innerHTML = html;
            } else {
                contactsList.innerHTML = '<p class="text-muted">No contacts yet. Add your first contact!</p>';
            }
        })
        .catch(error => {
            console.error('Error loading contacts:', error);
            document.getElementById('contactsList').innerHTML = '<p class="text-danger">Error loading contacts</p>';
        });
}

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');

    // Handle image preview
    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (5 MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                showAlert('Image size must be less than 5 MB', 'danger');
                e.target.value = '';
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                showAlert('Image must be JPEG or PNG format', 'danger');
                e.target.value = '';
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle remove image
    removeImageBtn.addEventListener('click', function() {
        imageInput.value = '';
        imagePreview.style.display = 'none';
        previewImg.src = '';
    });

    // Handle form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Create FormData for file upload
        const formData = new FormData();
        formData.append('first_name', document.getElementById('first_name').value.trim());
        formData.append('last_name', document.getElementById('last_name').value.trim());
        formData.append('phone', document.getElementById('phone').value.trim());
        formData.append('email', document.getElementById('email').value.trim());
        
        // Add image file if selected
        if (imageInput.files.length > 0) {
            formData.append('image', imageInput.files[0]);
        }

        // Show loading state
        const submitBtn = document.querySelector('#contactForm button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Adding...';

        fetch('/api/contacts', {
            method: 'POST',
            body: formData
            // Don't set Content-Type header, let browser set it with boundary for multipart/form-data
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                document.getElementById('contactForm').reset();
                imagePreview.style.display = 'none';
                previewImg.src = '';
                loadContacts();
            } else {
                const errorMessage = data.errors ? data.errors.join('<br>') : 'Error adding contact';
                showAlert(errorMessage, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error adding contact', 'danger');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });

    // Load contacts on page load
    loadContacts();
});


function openEditModal(id) {
    const m = document.getElementById('editModal');
    m.style.display = 'flex';
    m.setAttribute('aria-hidden', 'false');

    // Load contact data into the edit form
    if (typeof loadAndShowEditForm === 'function') {
        loadAndShowEditForm(id);
    } else {
        console.error('loadAndShowEditForm function not found');
    }
}

function closeEditModal() {
    const m = document.getElementById('editModal');
    m.style.display = 'none';
    m.setAttribute('aria-hidden', 'true');
}

/* delegated click handler for edit links */
document.addEventListener('click', function (e) {
    const el = e.target.closest && e.target.closest('.edit-link');
    if (!el) return;
    e.preventDefault();
    const id = el.dataset.id;
    if (!id) return;
    openEditModal(id);
});

/* delegated click handler for delete links */
document.addEventListener('click', function (e) {
    const el = e.target.closest && e.target.closest('.delete-link');
    if (!el) return;
    e.preventDefault();
    const id = el.dataset.id;
    if (!id) return;
    deleteContact(id);
});

// Function to delete contact
function deleteContact(id) {
    if (!confirm('Are you sure you want to delete this contact?')) {
        return;
    }

    fetch(`/api/contacts/${id}/delete`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            loadContacts();
        } else {
            const errorMessage = data.errors ? data.errors.join('<br>') : 'Error deleting contact';
            showAlert(errorMessage, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error deleting contact', 'danger');
    });
}