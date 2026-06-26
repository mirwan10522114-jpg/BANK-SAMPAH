<?php
use App\Models\BalanceHistory;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
?>

<?php
    $rupiah = fn(float $v) => 'Rp ' . number_format($v, 0, ',', '.');
?>

<section class="w-full flex flex-col gap-6 pb-8">

    
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-base-content"><?php echo e(__('Saldo')); ?></h1>
            <p class="text-sm text-base-content/50 mt-0.5"><?php echo e(__('Rincian saldo dan pergerakannya.')); ?></p>
        </div>
        <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['options' => $this->bucketOptions(),'optionLabel' => 'name','optionValue' => 'id','placeholder' => ''.e(__('Semua bucket')).''] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'bucket','class' => 'w-full sm:w-52']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $attributes = $__attributesOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__attributesOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald64144c2287634503c73cd4803d6e578)): ?>
<?php $component = $__componentOriginald64144c2287634503c73cd4803d6e578; ?>
<?php unset($__componentOriginald64144c2287634503c73cd4803d6e578); ?>
<?php endif; ?>
    </div>

    
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="rounded-2xl bg-gradient-to-br from-success/10 to-success/5 border border-success/20 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-success/15 flex items-center justify-center flex-shrink-0">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-banknotes'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6 text-success']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </div>
            <div>
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider"><?php echo e(__('Saldo Tersedia')); ?></div>
                <div class="text-2xl font-black text-success leading-tight mt-0.5">
                    <?php echo e($rupiah((float) ($this->balance->saldo_tersedia ?? 0))); ?>

                </div>
                <div class="text-xs text-base-content/40 mt-0.5"><?php echo e(__('Siap dicairkan kapan saja')); ?></div>
            </div>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-warning/10 to-warning/5 border border-warning/20 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-warning/15 flex items-center justify-center flex-shrink-0">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-clock'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6 text-warning']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
            </div>
            <div>
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wider"><?php echo e(__('Saldo Tertahan')); ?></div>
                <div class="text-2xl font-black text-warning leading-tight mt-0.5">
                    <?php echo e($rupiah((float) ($this->balance->saldo_tertahan ?? 0))); ?>

                </div>
                <div class="text-xs text-base-content/40 mt-0.5"><?php echo e(__('Menunggu release admin')); ?></div>
            </div>
        </div>
    </div>

    
    <div class="rounded-2xl border border-base-200 bg-base-100 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-base-200">
            <div class="text-sm font-bold text-base-content"><?php echo e(__('Riwayat Pergerakan Saldo')); ?></div>
            <span class="text-xs text-base-content/40">
                <?php echo e($this->histories->total()); ?> <?php echo e(__('total transaksi')); ?>

            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-base-200 bg-base-200/40">
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 whitespace-nowrap"><?php echo e(__('Tanggal')); ?></th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 whitespace-nowrap"><?php echo e(__('Bucket')); ?></th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40"><?php echo e(__('Jenis')); ?></th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 text-right whitespace-nowrap"><?php echo e(__('Jumlah')); ?></th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 text-right whitespace-nowrap hidden md:table-cell"><?php echo e(__('Saldo Setelah')); ?></th>
                        <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-base-content/40 hidden lg:table-cell"><?php echo e(__('Keterangan')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200/60">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->histories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-base-200/30 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="font-semibold text-base-content"><?php echo e($row->created_at->format('d M Y')); ?></div>
                                <div class="text-xs text-base-content/40"><?php echo e($row->created_at->format('H:i')); ?></div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->bucket === 'tertahan'): ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-warning/15 text-warning">
                                        <?php echo e(__('Tertahan')); ?>

                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-success/15 text-success">
                                        <?php echo e(__('Tersedia')); ?>

                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-medium text-base-content/70 capitalize"><?php echo e($row->type); ?></span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                    'font-bold tabular-nums',
                                    'text-success' => (float) $row->amount > 0,
                                    'text-error' => (float) $row->amount < 0,
                                    'text-base-content/60' => (float) $row->amount === 0.0,
                                ]); ?>">
                                    <?php echo e((float) $row->amount > 0 ? '+' : ''); ?><?php echo e($rupiah((float) $row->amount)); ?>

                                </span>
                            </td>
                            <td class="px-4 py-3 text-right tabular-nums whitespace-nowrap text-base-content/60 hidden md:table-cell">
                                <?php echo e($rupiah((float) $row->balance_after)); ?>

                            </td>
                            <td class="px-4 py-3 text-sm text-base-content/60 hidden lg:table-cell">
                                <?php echo e($row->description ?? '—'); ?>

                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="6" class="px-5 py-12">
                                <div class="flex flex-col items-center gap-3 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-base-200 flex items-center justify-center">
                                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-banknotes'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-7 text-base-content/20']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $attributes = $__attributesOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__attributesOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce0070e6ae017cca68172d0230e44821)): ?>
<?php $component = $__componentOriginalce0070e6ae017cca68172d0230e44821; ?>
<?php unset($__componentOriginalce0070e6ae017cca68172d0230e44821); ?>
<?php endif; ?>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-base-content/60"><?php echo e(__('Belum ada riwayat saldo')); ?></div>
                                        <div class="text-xs text-base-content/40 mt-1"><?php echo e(__('Pergerakan saldo akan muncul di sini')); ?></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($this->histories->hasPages()): ?>
            <div class="px-5 py-4 border-t border-base-200">
                <?php echo e($this->histories->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section><?php /**PATH C:\laragon\www\BANK-SAMPAH\storage\framework/views/livewire/views/3f2620c9.blade.php ENDPATH**/ ?>