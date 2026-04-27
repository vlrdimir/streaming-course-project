<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Course</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item active">Edit Course</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Course Details
        </div>
        <div class="card-body">
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="<?= site_url('admin/course/' . $course['id']) ?>" method="post" enctype="multipart/form-data" id="courseForm">
                <?= csrf_field() ?>
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= old('title', $course['title']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="slug" name="slug" value="<?= old('slug', $course['slug']) ?>" required>
                            <div class="form-text">The slug is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control" id="short_description" name="short_description" rows="2"><?= old('short_description', $course['short_description']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="8"><?= old('description', $course['description']) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">Course Settings</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="draft" <?= old('status', $course['status']) === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="published" <?= old('status', $course['status']) === 'published' ? 'selected' : '' ?>>Published</option>
                                        <option value="private" <?= old('status', $course['status']) === 'private' ? 'selected' : '' ?>>Private</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                                    <select class="form-select" id="level" name="level" required>
                                        <option value="beginner" <?= old('level', $course['level']) === 'beginner' ? 'selected' : '' ?>>Beginner</option>
                                        <option value="intermediate" <?= old('level', $course['level']) === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
                                        <option value="advanced" <?= old('level', $course['level']) === 'advanced' ? 'selected' : '' ?>>Advanced</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="duration" class="form-label">Duration (minutes)</label>
                                    <input type="number" class="form-control" id="duration" name="duration" value="<?= old('duration', $course['duration']) ?>" min="0">
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= old('is_featured', $course['is_featured']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Course
                                    </label>
                                </div>

                                <hr>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="is_premium" name="is_premium" value="1" <?= old('is_premium', $course['is_premium']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_premium">
                                        Premium Course
                                    </label>
                                    <div class="form-text">Require payment before access.</div>
                                </div>

                                <div id="premium-pricing-fields" class="<?= old('is_premium', $course['is_premium']) ? '' : 'd-none' ?>">
                                    <div class="mb-3">
                                        <label for="price_amount" class="form-label">Price Amount <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="price_amount" name="price_amount" value="<?= old('price_amount', $course['price_amount']) ?>" min="1" step="1" <?= old('is_premium', $course['is_premium']) ? '' : 'disabled' ?>>
                                        <div class="form-text">Use the smallest whole amount for the selected currency.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="price_currency" class="form-label">Currency <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="price_currency" name="price_currency" value="<?= old('price_currency', $course['price_currency'] ?? 'IDR') ?>" maxlength="10" <?= old('is_premium', $course['is_premium']) ? '' : 'disabled' ?>>
                                    </div>

                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" id="is_purchasable" name="is_purchasable" value="1" <?= old('is_purchasable', $course['is_purchasable'] ?? 1) ? 'checked' : '' ?> <?= old('is_premium', $course['is_premium']) ? '' : 'disabled' ?>>
                                        <label class="form-check-label" for="is_purchasable">
                                            Available for Purchase
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header">Categories</div>
                            <div class="card-body" style="max-height: 200px; overflow-y: auto;">
                                <?php foreach ($categories as $category): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="category_<?= $category['id'] ?>" name="categories[]" value="<?= $category['id'] ?>" <?= in_array($category['id'], old('categories', $selectedCategories)) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="category_<?= $category['id'] ?>">
                                        <?= $category['name'] ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="card mb-3">
                            <div class="card-header">Thumbnail</div>
                            <div class="card-body">
                                <?php if (!empty($course['thumbnail'])): ?>
                                <div class="mb-3">
                                    <label class="form-label">Current Thumbnail</label>
                                    <div>
                                        <img src="<?= base_url($course['thumbnail']) ?>" alt="<?= $course['title'] ?>" class="img-fluid img-thumbnail">
                                    </div>
                                </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <label for="thumbnail" class="form-label">Change Thumbnail</label>
                                    <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                                    <div class="form-text">Recommended size: 1280x720 pixels. Leave empty to keep current thumbnail.</div>
                                </div>
                                <div id="thumbnail-preview" class="mt-2 d-none">
                                    <img src="/placeholder.svg" alt="Thumbnail Preview" class="img-fluid img-thumbnail">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url('admin/course') ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Course</button>
                </div>
            </form>

        
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission handler
        const courseForm = document.getElementById('courseForm');
        if (courseForm) {
            courseForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    const formData = new FormData(this);
                    const courseId = '<?= $course['id'] ?>';
                     
                    const response = await fetch(`/admin/course/${courseId}`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="<?= csrf_token() ?>"]').value
                        }
                    });
                     
                    const result = await response.json();
                     
                    if (result.success) {
                        // Success
                        alert(result.message);
                        window.location.href = '/admin/course';
                    } else {
                        // Error
                        let errorMessage = result.message;
                        if (result.errors) {
                            errorMessage += '\n\nValidation errors:\n' + Object.values(result.errors).join('\n');
                        }
                        if (result.error) {
                            errorMessage += '\n\nServer error:\n' + result.error;
                        }
                        alert(errorMessage);
                    }
                } catch (error) {
                    alert('An error occurred while updating the course: ' + error.message);
                }
            });
        }

        const premiumToggle = document.getElementById('is_premium');
        const premiumPricingFields = document.getElementById('premium-pricing-fields');
        const premiumPricingInputs = premiumPricingFields.querySelectorAll('input');

        function syncPremiumPricingFields() {
            const isPremium = premiumToggle.checked;
            premiumPricingFields.classList.toggle('d-none', !isPremium);

            premiumPricingInputs.forEach(function(input) {
                input.disabled = !isPremium;
            });
        }

        premiumToggle.addEventListener('change', syncPremiumPricingFields);
        syncPremiumPricingFields();

        // Thumbnail preview
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const previewImg = thumbnailPreview.querySelector('img');
        
        thumbnailInput.addEventListener('change', function() {
            if (thumbnailInput.files && thumbnailInput.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    thumbnailPreview.classList.remove('d-none');
                }
                
                reader.readAsDataURL(thumbnailInput.files[0]);
            }
        });
    });
</script>
<?= $this->endSection() ?>

