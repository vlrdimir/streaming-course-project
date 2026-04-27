<div class="bg-dark rounded-lg border border-gray-700 overflow-hidden <?= isset($class) ? $class : '' ?>">
    <?php if (isset($header)): ?>
        <div class="p-4 border-b border-gray-700">
            <?= $header ?>
        </div>
    <?php endif; ?>
    
    <div class="p-4">
        <?= $content ?>
    </div>
    
    <?php if (isset($footer)): ?>
        <div class="p-4 border-t border-gray-700 bg-gray-800">
            <?= $footer ?>
        </div>
    <?php endif; ?>
</div>

