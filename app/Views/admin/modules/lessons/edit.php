<?= $this->extend('admin/layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Lesson</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="<?= site_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('admin/course') ?>">Courses</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url("admin/course/{$course['id']}/modules") ?>"><?= $course['title'] ?></a></li>
        <li class="breadcrumb-item"><a href="<?= site_url("admin/course/{$course['id']}/modules/{$module['id']}/lessons") ?>"><?= $module['title'] ?> - Lessons</a></li>
        <li class="breadcrumb-item active">Edit</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            Lesson Details
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
            
            <form action="<?= site_url("admin/course/{$course['id']}/modules/{$module['id']}/lessons/{$lesson['id']}") ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= old('title', $lesson['title']) ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $lesson['description']) ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="10"><?= old('content', $lesson['content']) ?></textarea>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="fas fa-video me-1"></i>
                                Video Settings
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="video_url" class="form-label">Video URL <span class="text-danger">*</span></label>
                                    <input type="url" class="form-control" id="video_url" name="video_url" value="<?= old('video_url', $lesson['video_url']) ?>" required>
                                    <div class="form-text">Enter the URL of the video (YouTube, Vimeo, etc.)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="video_duration" class="form-label">Video Duration (minute) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="video_duration" name="video_duration" value="<?= old('video_duration', $lesson['video_duration']) ?>" required>
                                    <div class="form-text">Enter the duration of the video in seconds</div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <i class="fas fa-play me-1"></i>
                                Video Preview
                            </div>
                            <div class="card-body">
                                <div id="video-preview-container" class="ratio ratio-16x9 bg-dark">
                                    <?php 
                                    $videoId = '';
                                    $videoUrl = old('video_url', $lesson['video_url']);
                                    
                                    // Extract YouTube video ID (simplified version)
                                    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $match)) {
                                        $videoId = $match[1];
                                    }
                                    
                                    if ($videoId):
                                    ?>
                                    <iframe 
                                        width="100%" 
                                        height="100%" 
                                        src="https://www.youtube.com/embed/<?= $videoId ?>" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                    </iframe>
                                    <?php else: ?>
                                    <div class="text-white text-center p-3">
                                        <i class="fas fa-film fa-3x mb-3"></i>
                                        <p>Enter a valid YouTube URL to see a preview</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?= site_url("admin/course/{$course['id']}/modules/{$module['id']}/lessons") ?>" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const videoUrlInput = document.getElementById('video_url');
        const videoPreviewContainer = document.getElementById('video-preview-container');
        
        // Function to extract YouTube video ID
        function getYouTubeVideoId(url) {
            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
            const match = url.match(regExp);
            return (match && match[2].length === 11) ? match[2] : null;
        }
        
        // Function to update video preview
        function updateVideoPreview() {
            const url = videoUrlInput.value.trim();
            const videoId = getYouTubeVideoId(url);
            
            if (videoId) {
                videoPreviewContainer.innerHTML = `
                    <iframe 
                        width="100%" 
                        height="100%" 
                        src="https://www.youtube.com/embed/${videoId}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                `;
            } else {
                videoPreviewContainer.innerHTML = `
                    <div class="text-white text-center p-3">
                        <i class="fas fa-film fa-3x mb-3"></i>
                        <p>Enter a valid YouTube URL to see a preview</p>
                    </div>
                `;
            }
        }
        
        // Update preview when URL changes
        videoUrlInput.addEventListener('change', updateVideoPreview);
        videoUrlInput.addEventListener('paste', function() {
            setTimeout(updateVideoPreview, 100);
        });
    });
</script>
<?= $this->endSection() ?>

