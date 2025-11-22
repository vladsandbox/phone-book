<div class="edit-form-container">
    <h5 class="mb-3">Edit Contact</h5>
    <form id="editContactForm">
        <input type="hidden" id="edit_contact_id" value="">

        <div class="mb-3">
            <label for="edit_first_name" class="form-label">First Name *</label>
            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
        </div>

        <div class="mb-3">
            <label for="edit_last_name" class="form-label">Last Name *</label>
            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
        </div>

        <div class="mb-3">
            <label for="edit_phone" class="form-label">Phone Number *</label>
            <input type="text" class="form-control" id="edit_phone" name="phone" required>
        </div>

        <div class="mb-3">
            <label for="edit_email" class="form-label">Email *</label>
            <input type="email" class="form-control" id="edit_email" name="email" required>
        </div>

        <div class="mb-3">
            <label for="edit_image" class="form-label">Contact Image (Optional)</label>
            <div id="edit_current_image_container" class="mb-2" style="display: none;">
                <img id="edit_current_image" src="" alt="Current Image" class="contact-image" style="max-width: 150px; max-height: 150px; border-radius: 5px;">
            </div>
            <input type="file" class="form-control" id="edit_image" name="image" accept="image/jpeg,image/jpg,image/png">
            <small class="form-text text-muted">JPEG or PNG, max 5 MB</small>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancel</button>
        </div>
    </form>
</div>

<script>
let currentEditContactId = null;

async function loadAndShowEditForm(id) {
    currentEditContactId = id;
    document.getElementById('edit_contact_id').value = id;

    // Reset form and image
    const editForm = document.getElementById('editContactForm');
    const imageInput = document.getElementById('edit_image');
    if (imageInput) {
        imageInput.value = '';
    }

    const imageContainer = document.getElementById('edit_current_image_container');
    const imageElement = document.getElementById('edit_current_image');

    // Clear previous image
    imageElement.src = '';
    imageContainer.style.display = 'none';

    try {
        const res = await fetch(`/api/contacts/${id}/edit`);
        const json = await res.json();

        if (json.success) {
            const c = json.contact;
            document.getElementById('edit_first_name').value = c.first_name || '';
            document.getElementById('edit_last_name').value = c.last_name || '';
            document.getElementById('edit_phone').value = c.phone || '';
            document.getElementById('edit_email').value = c.email || '';

            // Load image if exists
            if (c.image_path && c.image_path.trim() !== '') {
                imageElement.src = c.image_path;
                imageContainer.style.display = 'block';
            }
        } else {
            showAlert(json.errors.join('<br>'), 'danger');
        }
    } catch (error) {
        console.error('Error loading contact:', error);
        showAlert('Error loading contact data', 'danger');
    }
}

async function submitEditForm(e) {
    e.preventDefault();

    if (!currentEditContactId) {
        showAlert('Contact ID not found', 'danger');
        return;
    }

    const form = document.getElementById('editContactForm');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.textContent = 'Saving...';

    try {
        const res = await fetch(`/api/contacts/${currentEditContactId}/update`, {
            method: 'POST',
            body: formData
        });

        const json = await res.json();

        if (json.success) {
            showAlert(json.message, 'success');
            closeEditModal();
            loadContacts(); // Reload contacts list
        } else {
            const errorMessage = json.errors ? json.errors.join('<br>') : 'Error updating contact';
            showAlert(errorMessage, 'danger');
        }
    } catch (error) {
        console.error('Error updating contact:', error);
        showAlert('Error updating contact', 'danger');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
}

// Attach submit handler to edit form
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editContactForm');
    if (editForm) {
        editForm.addEventListener('submit', submitEditForm);
    }
});
</script>
