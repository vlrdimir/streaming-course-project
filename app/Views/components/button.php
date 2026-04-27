<button 
    class="<?= isset($variant) && $variant === 'primary' ? 'bg-primary hover:bg-primary/90' : 'bg-gray-700 hover:bg-gray-600' ?> 
           <?= isset($size) && $size === 'sm' ? 'px-3 py-1 text-sm' : 'px-4 py-2' ?> 
           rounded-md text-white transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-opacity-50"
    <?= isset($disabled) && $disabled ? 'disabled' : '' ?>
    <?= isset($attributes) ? $attributes : '' ?>
>
    <?= $text ?>
</button>

