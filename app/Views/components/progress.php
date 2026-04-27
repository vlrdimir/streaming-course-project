<div class="w-full">
    <?php if (isset($label)): ?>
        <div class="flex justify-between mb-1">
            <span class="text-sm text-gray-400"><?= $label ?></span>
            <span class="text-sm text-gray-400"><?= $value ?>%</span>
        </div>
    <?php endif; ?>
    
    <div class="h-2 bg-gray-700 rounded-full overflow-hidden">
        <div 
            class="h-full <?= isset($color) && $color === 'primary' ? 'bg-primary' : 'bg-green-500' ?> rounded-full" 
            style="width: <?= $value ?>%"
        ></div>
    </div>
</div>

