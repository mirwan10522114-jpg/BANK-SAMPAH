<?php
use App\Concerns\NasabahValidationRules;
use App\Enums\UserRole;
use App\Models\PointHistory;
use App\Models\Redemption;
use App\Models\SavingTransaction;
use App\Models\SedekahTransaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;
?>

<section class="w-full space-y-4">
    <!-- Header Page -->
    <?php if (isset($component)) { $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d = $attributes; } ?>
<?php $component = Mary\View\Components\Header::resolve(['title' => ''.e(__('Nasabah')).'','subtitle' => ''.e(__('Kelola data nasabah yang menabung atau menyumbang sampah.')).'','separator' => true,'progressIndicator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Header::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

         <?php $__env->slot('middle', null, ['class' => '!justify-end']); ?> 
            <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['icon' => 'o-magnifying-glass','clearable' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'search','placeholder' => ''.e(__('Cari kode, nama, email, alamat...')).'','class' => 'input-md bg-base-100 shadow-sm border-base-300 focus:border-primary w-80']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $attributes = $__attributesOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__attributesOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $component = $__componentOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__componentOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-plus','label' => ''.e(__('Tambah Nasabah')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-primary shadow-sm font-semibold text-sm','wire:click' => 'startCreating','data-test' => 'nasabah-create-button']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $attributes = $__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__attributesOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d)): ?>
<?php $component = $__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d; ?>
<?php unset($__componentOriginal6f99ffca722ef3c8789c4087c5ac9f0d); ?>
<?php endif; ?>

    <!-- Sort Toolbar -->
    <div class="flex items-center gap-2 px-1">
        <span class="text-xs font-semibold uppercase tracking-wider text-base-content/50"><?php echo e(__('Urutkan')); ?>:</span>
        <button
            wire:click="sortField('name')"
            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-colors
                <?php echo e($sortBy === 'name' ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content/60 hover:bg-base-300'); ?>"
        >
            <?php echo e(__('Nama A-Z')); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortBy === 'name'): ?>
                <span><?php echo e($sortDirection === 'asc' ? '↑' : '↓'); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
        <button
            wire:click="sortField('member_code')"
            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-colors
                <?php echo e($sortBy === 'member_code' ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content/60 hover:bg-base-300'); ?>"
        >
            <?php echo e(__('Kode Member')); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortBy === 'member_code'): ?>
                <span><?php echo e($sortDirection === 'asc' ? '↑' : '↓'); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
        <button
            wire:click="sortField('created_at')"
            class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold transition-colors
                <?php echo e($sortBy === 'created_at' ? 'bg-primary text-primary-content' : 'bg-base-200 text-base-content/60 hover:bg-base-300'); ?>"
        >
            <?php echo e(__('Terbaru')); ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sortBy === 'created_at'): ?>
                <span><?php echo e($sortDirection === 'asc' ? '↑' : '↓'); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </button>
    </div>

    <!-- Tabel Data Nasabah -->
    <div class="overflow-hidden rounded-xl border border-base-200 bg-base-100 shadow-sm">
        <?php if (isset($component)) { $__componentOriginal8fbd727209323874b055feef49197909 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8fbd727209323874b055feef49197909 = $attributes; } ?>
<?php $component = Mary\View\Components\Table::resolve(['headers' => $this->headers,'rows' => $this->nasabahList,'withPagination' => true,'perPage' => 'perPage'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Table::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'table-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <!-- Kolom Kode Nasabah -->
            <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_id', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                <span class="text-xs font-mono font-semibold text-base-content/50">
                    <?php echo e($row->member_code ?? '—'); ?>

                </span>
            <?php }); ?>

            <!-- Kolom 1: Gabungan Avatar + Nama + Email (klik untuk buka dashboard) -->
            <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_name', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                <div
                    class="flex items-center gap-3 py-1 cursor-pointer group"
                    wire:click="openDashboard(<?php echo e($row->id); ?>)"
                    data-test="nasabah-open-dashboard-<?php echo e($row->id); ?>"
                >
                    <div class="avatar placeholder">
                        <div class="bg-neutral/10 text-neutral font-bold rounded-full w-9 h-9 flex items-center justify-center text-xs uppercase tracking-wider group-hover:bg-primary/15 group-hover:text-primary transition-colors">
                            <?php echo e(substr($row->name, 0, 2)); ?>

                        </div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-semibold text-sm text-base-content group-hover:text-primary transition-colors">
                            <?php echo e($row->name); ?>

                        </span>
                        <span class="text-xs text-base-content/60 font-medium">
                            <?php echo e($row->email); ?>

                        </span>
                    </div>
                </div>
            <?php }); ?>

            <!-- Kolom 2: Nomor Telepon -->
            <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_phone', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->phone): ?>
                    <span class="text-sm font-mono font-semibold text-base-content/80 bg-base-200/50 px-2.5 py-1 rounded-lg whitespace-nowrap">
                        <?php echo e($row->phone); ?>

                    </span>
                <?php else: ?>
                    <span class="text-base-content/30 text-xs">—</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php }); ?>

            <!-- Kolom 3: Alamat Lengkap -->
            <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_address', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->address): ?>
                    <div class="max-w-xs truncate text-xs text-base-content/70 font-medium" title="<?php echo e($row->address); ?>">
                        <?php echo e($row->address); ?>

                    </div>
                <?php else: ?>
                    <span class="text-base-content/30 text-xs">—</span>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php }); ?>

            <!-- Kolom 4: Status Member -->
            <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_member_label', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                <div class="text-center">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->is_member): ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-success/10 text-success border border-success/20">
                            <?php echo e(__('Aktif')); ?>

                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-base-200 text-base-content/60 border border-base-300/40">
                            <?php echo e(__('Tidak Aktif')); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php }); ?>

            <!-- Kolom 5: Tanggal Bergabung -->
            <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('cell_member_joined_label', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                <div class="text-xs font-medium text-base-content/70">
                    <?php echo e($row->member_joined_at ? $row->member_joined_at->format('d M Y') : '—'); ?>

                </div>
            <?php }); ?>

            <!-- Kolom Aksi -->
            <?php $__bladeCompiler = $__bladeCompiler ?? null; $loop = null; $__env->slot('actions', function($row) use ($__env,$__bladeCompiler) { $loop = (object) $__env->getLoopStack()[0] ?>
                <div class="flex items-center gap-0.5 justify-end">
                    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-chart-bar','tooltip' => ''.e(__('Lihat Dashboard')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'openDashboard('.e($row->id).')','class' => 'btn-ghost btn-sm text-base-content/70 hover:text-info hover:bg-info/10 rounded-lg','data-test' => 'nasabah-dashboard-'.e($row->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-pencil-square','spinner' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'startEditing('.e($row->id).')','class' => 'btn-ghost btn-sm text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg','data-test' => 'nasabah-edit-'.e($row->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['icon' => 'o-trash'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:click' => 'confirmDelete('.e($row->id).')','class' => 'btn-ghost btn-sm text-base-content/40 hover:text-error hover:bg-error/10 rounded-lg','data-test' => 'nasabah-delete-'.e($row->id).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                </div>
            <?php }); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8fbd727209323874b055feef49197909)): ?>
<?php $attributes = $__attributesOriginal8fbd727209323874b055feef49197909; ?>
<?php unset($__attributesOriginal8fbd727209323874b055feef49197909); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8fbd727209323874b055feef49197909)): ?>
<?php $component = $__componentOriginal8fbd727209323874b055feef49197909; ?>
<?php unset($__componentOriginal8fbd727209323874b055feef49197909); ?>
<?php endif; ?>
    </div>

    <!-- Modal Form Tambah/Edit -->
    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => ''.e($editingUserId ? __('Edit Data Nasabah') : __('Tambah Nasabah Baru')).'','subtitle' => ''.e(__('Gunakan alamat email aktif sebagai identitas login unik nasabah.')).'','separator' => true,'boxClass' => 'max-w-2xl rounded-2xl'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'formModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if (isset($component)) { $__componentOriginal6bfd0631c6b8a47111403266db046f63 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6bfd0631c6b8a47111403266db046f63 = $attributes; } ?>
<?php $component = Mary\View\Components\Form::resolve(['noSeparator' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-form'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Form::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:submit' => 'save','class' => 'space-y-5']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>


            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($editingUserId): ?>
                <div class="flex items-center gap-2 -mt-1 mb-1">
                    <span class="text-xs font-mono font-semibold text-base-content/50"><?php echo e(__('ID Nasabah')); ?>: #<?php echo e($editingUserId); ?></span>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="grid gap-4 md:grid-cols-2">
                <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => ''.e(__('Nama Lengkap')).'','icon' => 'o-user'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'name','placeholder' => 'Nama sesuai identitas','required' => true,'class' => 'input-bordered']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $attributes = $__attributesOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__attributesOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $component = $__componentOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__componentOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => ''.e(__('Alamat Email')).'','icon' => 'o-envelope'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'email','type' => 'email','placeholder' => 'contoh@email.com','required' => true,'class' => 'input-bordered']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $attributes = $__attributesOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__attributesOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $component = $__componentOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__componentOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => ''.e(__('Nomor Telepon / WhatsApp')).'','icon' => 'o-phone'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'phone','type' => 'tel','placeholder' => '08xxxxxxxxxx','class' => 'input-bordered']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $attributes = $__attributesOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__attributesOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $component = $__componentOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__componentOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => ''.e(__('Tanggal Bergabung')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'member_joined_at','type' => 'date','disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(! $is_member),'class' => 'input-bordered']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $attributes = $__attributesOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__attributesOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf51438a7488970badd535e5f203e0c1b)): ?>
<?php $component = $__componentOriginalf51438a7488970badd535e5f203e0c1b; ?>
<?php unset($__componentOriginalf51438a7488970badd535e5f203e0c1b); ?>
<?php endif; ?>
            </div>

            <!-- Textarea Alamat Lengkap -->
            <?php if (isset($component)) { $__componentOriginaleda28cbc945270b2059ee861cf34a6bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaleda28cbc945270b2059ee861cf34a6bc = $attributes; } ?>
<?php $component = Mary\View\Components\Textarea::resolve(['label' => ''.e(__('Alamat Rumah Lengkap')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Textarea::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'address','placeholder' => 'Tuliskan nama jalan, nomor rumah, RT/RW, kelurahan, dan kecamatan...','rows' => '3','class' => 'textarea-bordered']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaleda28cbc945270b2059ee861cf34a6bc)): ?>
<?php $attributes = $__attributesOriginaleda28cbc945270b2059ee861cf34a6bc; ?>
<?php unset($__attributesOriginaleda28cbc945270b2059ee861cf34a6bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaleda28cbc945270b2059ee861cf34a6bc)): ?>
<?php $component = $__componentOriginaleda28cbc945270b2059ee861cf34a6bc; ?>
<?php unset($__componentOriginaleda28cbc945270b2059ee861cf34a6bc); ?>
<?php endif; ?>

            <!-- Area Toggle Member -->
            <div class="flex items-center justify-between p-4 rounded-xl border border-base-200 bg-base-200/30">
                <div class="flex flex-col gap-0.5">
                    <span class="text-sm font-semibold text-base-content"><?php echo e(__('Status Aktif Nasabah')); ?></span>
                    <span class="text-xs text-base-content/60"><?php echo e(__('Nasabah akan berhak mengumpulkan dan menukarkan poin tabungan.')); ?></span>
                </div>
                <?php if (isset($component)) { $__componentOriginal91586e22c1998368a30f831eea05043a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal91586e22c1998368a30f831eea05043a = $attributes; } ?>
<?php $component = Mary\View\Components\Toggle::resolve(['right' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-toggle'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Toggle::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'is_member','class' => 'toggle-primary']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal91586e22c1998368a30f831eea05043a)): ?>
<?php $attributes = $__attributesOriginal91586e22c1998368a30f831eea05043a; ?>
<?php unset($__attributesOriginal91586e22c1998368a30f831eea05043a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal91586e22c1998368a30f831eea05043a)): ?>
<?php $component = $__componentOriginal91586e22c1998368a30f831eea05043a; ?>
<?php unset($__componentOriginal91586e22c1998368a30f831eea05043a); ?>
<?php endif; ?>
            </div>

             <?php $__env->slot('actions', null, []); ?> 
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => ''.e(__('Batalkan')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => '$wire.formModal = false','class' => 'btn-ghost text-sm font-medium']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => ''.e(__('Simpan Perubahan')).'','icon' => 'o-check','spinner' => 'save'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-primary font-semibold shadow-sm px-5 text-sm','type' => 'submit','data-test' => 'nasabah-save-button']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
             <?php $__env->endSlot(); ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6bfd0631c6b8a47111403266db046f63)): ?>
<?php $attributes = $__attributesOriginal6bfd0631c6b8a47111403266db046f63; ?>
<?php unset($__attributesOriginal6bfd0631c6b8a47111403266db046f63); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6bfd0631c6b8a47111403266db046f63)): ?>
<?php $component = $__componentOriginal6bfd0631c6b8a47111403266db046f63; ?>
<?php unset($__componentOriginal6bfd0631c6b8a47111403266db046f63); ?>
<?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>

    <!-- Modal Konfirmasi Hapus -->
    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => ''.e(__('Konfirmasi Hapus Data')).'','boxClass' => 'max-w-md rounded-2xl'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'deleteModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <div class="flex flex-col gap-3 py-2">
            <p class="text-sm text-base-content/70 leading-relaxed">
                <?php echo e(__('Apakah Anda yakin ingin menghapus data nasabah ini? Tindakan ini bersifat permanen dan data akun akan dihapus dari sistem, namun riwayat transaksi lama akan tetap dipertahankan demi validitas laporan keuangan.')); ?>

            </p>
        </div>

         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => ''.e(__('Batal')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['@click' => '$wire.deleteModal = false','class' => 'btn-ghost text-sm font-medium']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['label' => ''.e(__('Ya, Hapus Permanen')).'','spinner' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-error font-semibold text-sm px-4','wire:click' => 'delete','data-test' => 'nasabah-confirm-delete']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $attributes = $__attributesOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__attributesOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal602b228a887fab12f0012a3179e5b533)): ?>
<?php $component = $__componentOriginal602b228a887fab12f0012a3179e5b533; ?>
<?php unset($__componentOriginal602b228a887fab12f0012a3179e5b533); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>

    <!-- ============================================== -->
    <!-- MODAL DASHBOARD PERSONAL NASABAH -->
    <!-- ============================================== -->
    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['boxClass' => 'max-w-5xl rounded-2xl !p-0','persistent' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'dashboardModal']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->viewingUser): ?>
            <?php ($user = $this->viewingUser); ?>
            <?php ($balance = $user->balance); ?>

            <div class="bg-base-100 rounded-2xl overflow-hidden">

                <!-- ===== HEADER PROFIL ===== -->
                <div style="padding:24px 24px 20px;border-bottom:1px solid #ECECEC;background:linear-gradient(135deg, rgba(34,197,94,.06), #fff);">
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;">
                        <div style="display:flex;align-items:center;gap:16px;">
                            <div style="width:56px;height:56px;border-radius:16px;background:#22c55e;color:#fff;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:18px;text-transform:uppercase;letter-spacing:.05em;box-shadow:0 1px 3px rgba(0,0,0,.08);flex-shrink:0;">
                                <?php echo e(substr($user->name, 0, 2)); ?>

                            </div>
                            <div style="display:flex;flex-direction:column;gap:4px;">
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <h3 style="font-size:16px;font-weight:700;color:#1a1a1a;letter-spacing:-.01em;margin:0;line-height:1;"><?php echo e($user->name); ?></h3>
                                   <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->is_member): ?>
                                        <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;background:rgba(34,197,94,.12);color:#16803c;"><?php echo e(__('Aktif')); ?></span>
                                    <?php else: ?>
                                        <span style="display:inline-flex;align-items:center;padding:2px 8px;border-radius:99px;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.04em;background:#e5e7eb;color:#6b7280;"><?php echo e(__('Tidak Aktif')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                                <span style="font-size:11px;font-family:monospace;font-weight:700;color:#22c55e;letter-spacing:.08em;"><?php echo e($user->member_code ?? '—'); ?></span>
                            </div>
                        </div>
                        <button @click="$wire.dashboardModal = false" style="width:28px;height:28px;border:none;background:transparent;display:flex;align-items:center;justify-content:center;border-radius:8px;flex-shrink:0;cursor:pointer;" class="text-base-content/40 hover:text-base-content hover:bg-base-200 transition-colors">
                            <svg style="width:16px;height:16px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <!-- Info kontak ringkas -->
                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:20px;margin-top:16px;font-size:13px;color:rgba(0,0,0,.55);font-weight:500;">
                        <span style="display:inline-flex;align-items:center;gap:8px;">
                            <svg style="width:15px;height:15px;flex-shrink:0;opacity:.4;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span style="letter-spacing:.3px;"><?php echo e($user->phone ?? '—'); ?></span>
                        </span>
                        <span style="display:inline-flex;align-items:center;gap:8px;">
                            <svg style="width:15px;height:15px;flex-shrink:0;opacity:.4;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            <span><?php echo e(__('Bergabung')); ?> <?php echo e($user->member_joined_at?->format('d M Y') ?? '—'); ?></span>
                        </span>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->address): ?>
                            <span style="display:inline-flex;align-items:center;gap:8px;max-width:320px;" title="<?php echo e($user->address); ?>">
                                <svg style="width:15px;height:15px;flex-shrink:0;opacity:.4;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 1 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo e($user->address); ?></span>
                            </span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                <!-- Body scrollable -->
                <div class="max-h-[70vh] overflow-y-auto px-6 py-5 space-y-5">

                    <!-- ===== KARTU STATISTIK ===== -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Saldo Tersedia')); ?></span>
                            <div class="text-lg font-bold text-success mt-1.5 leading-none">Rp <?php echo e(number_format((float) ($balance?->saldo_tersedia ?? 0), 0, ',', '.')); ?></div>
                        </div>
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Saldo Tertahan')); ?></span>
                            <div class="text-lg font-bold text-warning mt-1.5 leading-none">Rp <?php echo e(number_format((float) ($balance?->saldo_tertahan ?? 0), 0, ',', '.')); ?></div>
                        </div>
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Poin Saat Ini')); ?></span>
                            <div class="text-lg font-bold text-primary mt-1.5 leading-none"><?php echo e(number_format($balance?->points ?? 0, 0, ',', '.')); ?> <span class="text-xs font-semibold">pt</span></div>
                        </div>
                        <div class="rounded-xl border border-base-200 bg-base-100 p-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Total Ditabung')); ?></span>
                            <div class="text-lg font-bold text-base-content mt-1.5 leading-none"><?php echo e(number_format($this->categoryBreakdown->sum('total_qty'), 1, ',', '.')); ?> <span class="text-xs font-semibold">kg</span></div>
                        </div>
                    </div>

                    <!-- ===== GRAFIK TREN ===== -->
                    <div class="rounded-xl border border-base-200 bg-base-100 p-5">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h4 class="text-sm font-bold text-base-content tracking-tight"><?php echo e(__('Tren Tabungan')); ?></h4>
                                <p class="text-xs text-base-content/45 mt-0.5"><?php echo e(__('Berat sampah (kg) per bulan')); ?></p>
                            </div>
                            <div class="flex items-center gap-1 bg-base-200/70 rounded-lg p-1">
                                <button
                                    wire:click="setTrendRange('3bulan')"
                                    type="button"
                                    class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors <?php echo e($trendRange === '3bulan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content'); ?>"
                                ><?php echo e(__('3 Bulan')); ?></button>
                                <button
                                    wire:click="setTrendRange('6bulan')"
                                    type="button"
                                    class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors <?php echo e($trendRange === '6bulan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content'); ?>"
                                ><?php echo e(__('6 Bulan')); ?></button>
                                <button
                                    wire:click="setTrendRange('1tahun')"
                                    type="button"
                                    class="px-3 py-1 rounded-md text-[11px] font-semibold transition-colors <?php echo e($trendRange === '1tahun' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/50 hover:text-base-content'); ?>"
                                ><?php echo e(__('1 Tahun')); ?></button>
                            </div>
                        </div>

                        <div
                            <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'trend-wrapper-'.e($viewingUserId).'-'.e($trendRange).''; ?>wire:key="trend-wrapper-<?php echo e($viewingUserId); ?>-<?php echo e($trendRange); ?>"
                            wire:ignore
                            x-data="{
                                chart: null,
                                labels: <?php echo \Illuminate\Support\Js::from($this->monthlyTrend['labels'])->toHtml() ?>,
                                weights: <?php echo \Illuminate\Support\Js::from($this->monthlyTrend['weights'])->toHtml() ?>,
                                renderChart() {
                                    if (typeof Chart === 'undefined') {
                                        setTimeout(() => this.renderChart(), 80);
                                        return;
                                    }
                                    if (this.chart) {
                                        this.chart.destroy();
                                        this.chart = null;
                                    }
                                    const ctx = this.$refs.canvas.getContext('2d');
                                    this.chart = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: this.labels,
                                            datasets: [{
                                                label: 'Berat (kg)',
                                                data: this.weights,
                                                backgroundColor: 'rgba(34,197,94,0.55)',
                                                hoverBackgroundColor: 'rgba(34,197,94,0.8)',
                                                borderRadius: 6,
                                                maxBarThickness: 40,
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: {
                                                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 } } },
                                                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                                            }
                                        }
                                    });
                                }
                            }"
                            x-init="
                                $nextTick(() => requestAnimationFrame(() => renderChart()));
                            "
                            @trend-updated.window="
                                labels = $event.detail.labels;
                                weights = $event.detail.weights;
                                $nextTick(() => requestAnimationFrame(() => renderChart()));
                            "
                            class="relative w-full"
                            style="height: 224px;"
                        >
                            <canvas x-ref="canvas"></canvas>
                        </div>
                    </div>

                    <!-- ===== BREAKDOWN KATEGORI ===== -->
                    <div class="rounded-xl border border-base-200 bg-base-100 p-5">
                        <h4 class="text-sm font-bold text-base-content tracking-tight mb-3"><?php echo e(__('Total Sampah per Kategori')); ?></h4>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->categoryBreakdown->isEmpty()): ?>
                            <p class="text-xs text-base-content/40 text-center py-6"><?php echo e(__('Belum ada data tabungan.')); ?></p>
                        <?php else: ?>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $this->categoryBreakdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <div class="flex items-center justify-between p-3 rounded-lg bg-base-200/40">
                                        <span class="text-xs font-semibold text-base-content/75 truncate"><?php echo e($cat->category_name_snapshot); ?></span>
                                        <span class="text-xs font-bold text-success whitespace-nowrap ml-2"><?php echo e(number_format((float) $cat->total_qty, 1, ',', '.')); ?> kg</span>
                                    </div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <!-- ===== TAB RIWAYAT ===== -->
                    <div x-data="{ tab: 'tabungan' }" class="rounded-xl border border-base-200 bg-base-100 overflow-hidden">
                        <div class="flex items-center gap-1 p-2 border-b border-base-200 bg-base-200/20 overflow-x-auto">
                            <button @click="tab = 'tabungan'" :class="tab === 'tabungan' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors"><?php echo e(__('Tabungan')); ?></button>
                            <button @click="tab = 'sedekah'" :class="tab === 'sedekah' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors"><?php echo e(__('Sedekah')); ?></button>
                            <button @click="tab = 'poin'" :class="tab === 'poin' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors"><?php echo e(__('Poin')); ?></button>
                            <button @click="tab = 'redemption'" :class="tab === 'redemption' ? 'bg-primary text-primary-content shadow-sm' : 'text-base-content/55 hover:bg-base-200'" class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition-colors"><?php echo e(__('Penukaran')); ?></button>
                        </div>

                        <!-- Riwayat Tabungan -->
                        <div x-show="tab === 'tabungan'" class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Tanggal')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Barang')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right"><?php echo e(__('Berat')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right"><?php echo e(__('Poin')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right"><?php echo e(__('Nilai')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->savingHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap"><?php echo e($trx->transacted_at->format('d M Y, H:i')); ?></td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[220px] truncate" title="<?php echo e($trx->items->pluck('item_name_snapshot')->join(', ')); ?>">
                                                <?php echo e($trx->items->pluck('item_name_snapshot')->take(3)->join(', ')); ?><?php echo e($trx->items->count() > 3 ? '…' : ''); ?>

                                            </td>
                                            <td class="px-4 py-3 text-xs text-base-content/70 text-right tabular-nums whitespace-nowrap"><?php echo e(number_format((float) $trx->total_weight, 1, ',', '.')); ?> kg</td>
                                            <td class="px-4 py-3 text-xs font-semibold text-primary text-right tabular-nums whitespace-nowrap">+<?php echo e($trx->points_awarded); ?> pt</td>
                                            <td class="px-4 py-3 text-sm font-bold text-success text-right tabular-nums whitespace-nowrap">Rp <?php echo e(number_format((float) $trx->total_value, 0, ',', '.')); ?></td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="5" class="text-center py-8 text-xs text-base-content/40"><?php echo e(__('Belum ada riwayat tabungan.')); ?></td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Riwayat Sedekah -->
                        <div x-show="tab === 'sedekah'" x-cloak class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Tanggal')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Barang')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right"><?php echo e(__('Berat')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->sedekahHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap"><?php echo e($trx->transacted_at->format('d M Y, H:i')); ?></td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[280px] truncate" title="<?php echo e($trx->items->pluck('item_name_snapshot')->join(', ')); ?>">
                                                <?php echo e($trx->items->pluck('item_name_snapshot')->take(3)->join(', ')); ?><?php echo e($trx->items->count() > 3 ? '…' : ''); ?>

                                            </td>
                                            <td class="px-4 py-3 text-sm font-bold text-secondary text-right tabular-nums whitespace-nowrap"><?php echo e(number_format((float) $trx->total_weight, 1, ',', '.')); ?> kg</td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-8 text-xs text-base-content/40"><?php echo e(__('Belum ada riwayat sedekah.')); ?></td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Riwayat Poin -->
                        <div x-show="tab === 'poin'" x-cloak class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Tanggal')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Keterangan')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right"><?php echo e(__('Poin')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->pointHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ph): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap"><?php echo e($ph->created_at->format('d M Y, H:i')); ?></td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[280px] truncate"><?php echo e($ph->description ?? $ph->type); ?></td>
                                            <td class="px-4 py-3 text-sm font-bold text-right tabular-nums whitespace-nowrap <?php echo e($ph->points >= 0 ? 'text-success' : 'text-error'); ?>">
                                                <?php echo e($ph->points >= 0 ? '+' : ''); ?><?php echo e($ph->points); ?> pt
                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-8 text-xs text-base-content/40"><?php echo e(__('Belum ada riwayat poin.')); ?></td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Riwayat Penukaran Poin -->
                        <div x-show="tab === 'redemption'" x-cloak class="max-h-72 overflow-y-auto">
                            <table class="w-full text-left">
                                <thead class="sticky top-0 bg-base-100">
                                    <tr class="border-b border-base-200">
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Tanggal')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40"><?php echo e(__('Produk')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right"><?php echo e(__('Jumlah')); ?></th>
                                        <th class="px-4 py-2.5 text-[10px] font-bold uppercase tracking-wider text-base-content/40 text-right"><?php echo e(__('Poin Terpakai')); ?></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-base-200/60">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->redemptionHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rd): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr class="hover:bg-base-200/30 transition-colors">
                                            <td class="px-4 py-3 text-xs font-semibold text-base-content/80 whitespace-nowrap"><?php echo e($rd->redeemed_at->format('d M Y, H:i')); ?></td>
                                            <td class="px-4 py-3 text-xs text-base-content/55 max-w-[220px] truncate"><?php echo e($rd->product_name_snapshot); ?></td>
                                            <td class="px-4 py-3 text-xs text-base-content/70 text-right tabular-nums whitespace-nowrap"><?php echo e(number_format((float) $rd->quantity, 0)); ?> <?php echo e($rd->unit_snapshot); ?></td>
                                            <td class="px-4 py-3 text-sm font-bold text-warning text-right tabular-nums whitespace-nowrap">-<?php echo e($rd->points_used); ?> pt</td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-8 text-xs text-base-content/40"><?php echo e(__('Belum ada riwayat penukaran.')); ?></td>
                                        </tr>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $attributes = $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__attributesOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356)): ?>
<?php $component = $__componentOriginal89a573612f1f1cb2dd9fc072235d4356; ?>
<?php unset($__componentOriginal89a573612f1f1cb2dd9fc072235d4356); ?>
<?php endif; ?>
</section>

    <?php
        $__scriptKey = '1528952952-0';
        ob_start();
    ?>
<script>
    // Saat trendRange berubah di server, broadcast event browser agar canvas re-render
    // tanpa harus menghancurkan wire:ignore wrapper.
    $wire.on('trend-range-updated', (data) => {
        window.dispatchEvent(new CustomEvent('trend-updated', { detail: data }));
    });
</script>
    <?php
        $__output = ob_get_clean();

        \Livewire\store($this)->push('scripts', $__output, $__scriptKey)
    ?><?php /**PATH C:\laragon\www\BANK-SAMPAH\storage\framework/views/livewire/views/7d6d6a9b.blade.php ENDPATH**/ ?>