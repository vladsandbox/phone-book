<?php
/**
 * @var string $login User login name
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
</head>
<body class="home-page">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Phone Book</h3>
                    <div>
                        <span class="text-muted">User: <strong><?php echo htmlspecialchars($login); ?></strong></span>
                        <a href="/logout" class="btn btn-sm btn-danger ms-2">Logout</a>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">Add Contact</h5>
                    <form id="contactForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="text" class="form-control" id="phone" name="phone">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="image" class="form-label">Contact Image (Optional - JPEG or PNG, max 5 MB)</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/jpg,image/png">
                                <small class="form-text text-muted">Select an image file (JPEG or PNG, maximum 5 MB)</small>
                                <div id="imagePreview" class="mt-2" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 5px; margin-top: 10px;">
                                    <button type="button" class="btn btn-sm btn-danger ms-2" id="removeImage">Remove</button>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Contact</button>
                    </form>
                    <div id="alertContainer"></div>
                    
                    <div class="contacts-table">
                        <h5 class="mb-3">Contacts List</h5>
                        <div id="contactsList">
                            <p class="text-muted">Loading contacts...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #editModal {
        position: fixed;
        inset: 0;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        padding: 20px;
    }

    #editModal .panel {
        background: #fff;
        padding: 30px;
        max-width: 600px;
        width: 100%;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        max-height: 90vh;
        overflow-y: auto;
    }

    #editModal .close {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
        color: #666;
        padding: 0;
        width: 30px;
        height: 30px;
    }

    #editModal .close:hover {
        color: #000;
    }

    .edit-form-container {
        position: relative;
    }
</style>

<div id="editModal" aria-hidden="true">
    <div class="panel">
        <button class="close" onclick="closeEditModal()">&times;</button>
        <?php include __DIR__ . '/edit.php'; ?>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/utils.js"></script>
<script src="/js/home.js"></script>

</body>
</html>