<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1976d2;
            --secondary-color: #e91e63;
        }
        
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin: 0.2rem 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }
        
        .main-content {
            background: #f8f9fa;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.25);
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(25, 118, 210, 0.05);
        }
        
        .upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(25, 118, 210, 0.1);
        }
        
        .note-editor {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h4 class="mb-4">
                        <i class="fas fa-tachometer-alt me-2"></i>
                        <?php echo APP_NAME; ?>
                    </h4>
                    
                    <nav class="nav flex-column">
                        <a class="nav-link" href="/dashboard">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                        <a class="nav-link active" href="/dashboard/posts">
                            <i class="fas fa-file-alt me-2"></i>Posts
                        </a>
                        <a class="nav-link" href="/dashboard/categories">
                            <i class="fas fa-folder me-2"></i>Categories
                        </a>
                        <a class="nav-link" href="/dashboard/users">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <hr class="my-3">
                        <a class="nav-link" href="/logout">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-plus me-2"></i>Create New Post</h2>
                    <a href="/dashboard/posts" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Posts
                    </a>
                </div>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="/dashboard/posts/store" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Main Content Card -->
                            <div class="card mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="mb-0">
                                        <i class="fas fa-edit me-2"></i>Post Content
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Post Title *</label>
                                        <input type="text" class="form-control form-control-lg" id="title" name="title" required 
                                               placeholder="Enter your post title here...">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Content *</label>
                                        <textarea class="form-control" id="content" name="content" rows="15" required 
                                                  placeholder="Write your post content here..."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label">Excerpt</label>
                                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" 
                                                  placeholder="Brief description of your post (optional)"></textarea>
                                        <div class="form-text">A short summary of your post. If left empty, it will be auto-generated from content.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <!-- Settings Card -->
                            <div class="card mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="mb-0">
                                        <i class="fas fa-cog me-2"></i>Post Settings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Category</label>
                                        <select class="form-select" id="category_id" name="category_id">
                                            <option value="">Select Category</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>">
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft">Draft</option>
                                            <option value="published">Published</option>
                                            <option value="archived">Archived</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Create Post
                                        </button>
                                        <a href="/dashboard/posts" class="btn btn-outline-secondary">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Featured Image Card -->
                            <div class="card mb-4">
                                <div class="card-header bg-white border-0">
                                    <h5 class="mb-0">
                                        <i class="fas fa-image me-2"></i>Featured Image
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="upload-area" id="uploadArea">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h6>Upload Featured Image</h6>
                                        <p class="text-muted mb-0">Drag & drop or click to upload</p>
                                        <input type="file" id="featured_image" name="featured_image" 
                                               accept="image/*" style="display: none;">
                                    </div>
                                    
                                    <div id="imagePreview" class="text-center mt-3" style="display: none;">
                                        <img id="previewImg" class="image-preview mb-2">
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeImage">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="form-text">
                                        Recommended size: 1200x630 pixels. Max file size: 5MB.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Actions Card -->
                            <div class="card">
                                <div class="card-header bg-white border-0">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-info btn-sm" id="previewBtn">
                                            <i class="fas fa-eye me-2"></i>Preview Post
                                        </button>
                                        <button type="button" class="btn btn-outline-warning btn-sm" id="saveDraftBtn">
                                            <i class="fas fa-save me-2"></i>Save as Draft
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Post Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="previewContent"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Initialize Summernote editor
            $('#content').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'italic', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                placeholder: 'Write your post content here...'
            });

            // File upload handling
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('featured_image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeBtn = document.getElementById('removeImage');

            // Click to upload
            uploadArea.addEventListener('click', () => fileInput.click());

            // Drag and drop
            uploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', () => {
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect(files[0]);
                }
            });

            // File input change
            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    handleFileSelect(e.target.files[0]);
                }
            });

            // Remove image
            removeBtn.addEventListener('click', () => {
                fileInput.value = '';
                imagePreview.style.display = 'none';
                uploadArea.style.display = 'block';
            });

            function handleFileSelect(file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    return;
                }

                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB.');
                    return;
                }

                // Preview image
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    uploadArea.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }

            // Auto-generate excerpt from content
            $('#content').on('summernote.change', function() {
                const content = $(this).summernote('code');
                const plainText = $(content).text();
                if (plainText.length > 0 && $('#excerpt').val() === '') {
                    $('#excerpt').val(plainText.substring(0, 200) + (plainText.length > 200 ? '...' : ''));
                }
            });

            // Preview button
            $('#previewBtn').click(function() {
                const title = $('#title').val() || 'Untitled Post';
                const content = $('#content').summernote('code');
                const category = $('#category_id option:selected').text() || 'Uncategorized';
                const status = $('#status option:selected').text();

                const previewHtml = `
                    <div class="preview-post">
                        <h2 class="mb-3">${title}</h2>
                        <div class="mb-3">
                            <span class="badge bg-info me-2">${category}</span>
                            <span class="badge bg-${status === 'Published' ? 'success' : 'warning'}">${status}</span>
                        </div>
                        <div class="preview-content">
                            ${content}
                        </div>
                    </div>
                `;

                $('#previewContent').html(previewHtml);
                $('#previewModal').modal('show');
            });

            // Save as draft button
            $('#saveDraftBtn').click(function() {
                $('#status').val('draft');
                $('form').submit();
            });

            // Auto-save draft every 30 seconds
            let autoSaveTimer;
            function autoSave() {
                const title = $('#title').val();
                const content = $('#content').summernote('code');
                
                if (title || content) {
                    // Store in localStorage as backup
                    localStorage.setItem('post_draft', JSON.stringify({
                        title: title,
                        content: content,
                        excerpt: $('#excerpt').val(),
                        category_id: $('#category_id').val(),
                        timestamp: new Date().toISOString()
                    }));
                }
            }

            // Start auto-save
            setInterval(autoSave, 30000);

            // Load draft on page load
            const savedDraft = localStorage.getItem('post_draft');
            if (savedDraft) {
                const draft = JSON.parse(savedDraft);
                const draftAge = new Date() - new Date(draft.timestamp);
                
                // Only load draft if it's less than 1 hour old
                if (draftAge < 3600000) {
                    if (confirm('Found a saved draft. Would you like to restore it?')) {
                        $('#title').val(draft.title);
                        $('#content').summernote('code', draft.content);
                        $('#excerpt').val(draft.excerpt);
                        $('#category_id').val(draft.category_id);
                    }
                }
                localStorage.removeItem('post_draft');
            }

            // Clear draft when form is submitted
            $('form').on('submit', function() {
                localStorage.removeItem('post_draft');
            });
        });
    </script>
</body>
</html> 