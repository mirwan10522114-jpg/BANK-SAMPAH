<div>
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight"><?php echo e(__('Transaksi Simpanan')); ?></h1>
            <p class="text-sm font-medium text-gray-500 mt-1"><?php echo e(__('Catatan ledger setoran dan penarikan simpanan anggota.')); ?></p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <div class="join shadow-sm">
                <div class="join-item">
                    <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['icon' => 'o-magnifying-glass'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live.debounce.300ms' => 'search','placeholder' => 'Cari No Tx / Anggota...','class' => 'input-sm border-gray-300']); ?>
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
                <select wire:model.live="jenisFilter" class="select select-sm select-bordered join-item font-semibold text-gray-700">
                    <option value="">Semua Jenis</option>
                    <option value="pokok">Pokok</option>
                    <option value="wajib">Wajib</option>
                    <option value="sukarela">Sukarela</option>
                </select>
                <select wire:model.live="tipeFilter" class="select select-sm select-bordered join-item font-semibold text-gray-700">
                    <option value="">Semua Tipe</option>
                    <option value="setor">Setoran (Masuk)</option>
                    <option value="tarik">Penarikan (Keluar)</option>
                </select>
            </div>
            
            <button type="button" wire:click="openTransactionModal" class="btn btn-primary btn-sm bg-black hover:bg-gray-800 text-white border-none shadow-md">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrows-right-left'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
                <?php echo e(__('Transaksi Baru')); ?>

            </button>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm border border-gray-200 rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
                <thead class="bg-gray-50 text-gray-800 font-bold uppercase text-xs tracking-wider border-b border-gray-200">
                    <tr>
                        <th><?php echo e(__('Waktu')); ?></th>
                        <th><?php echo e(__('No. Transaksi')); ?></th>
                        <th><?php echo e(__('Anggota')); ?></th>
                        <th><?php echo e(__('Jenis')); ?></th>
                        <th><?php echo e(__('Tipe')); ?></th>
                        <th class="text-right"><?php echo e(__('Nominal')); ?></th>
                        <th><?php echo e(__('Keterangan')); ?></th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="text-gray-500 text-xs"><?php echo e(\Carbon\Carbon::parse($tx->tanggal_transaksi)->format('d/m/Y H:i')); ?></td>
                            <td class="font-mono font-bold text-gray-700"><?php echo e($tx->nomor_transaksi); ?></td>
                            <td>
    <div class="font-bold text-gray-900"><?php echo e($tx->anggota->nama ?? 'Anggota Dihapus'); ?></div>
    <div class="font-mono text-xs text-gray-500"><?php echo e($tx->anggota->nomor_anggota ?? '-'); ?></div>
</td>
                            <td><span class="badge badge-ghost badge-sm uppercase font-bold"><?php echo e($tx->jenis_simpanan); ?></span></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tx->tipe === 'setor'): ?>
                                    <span class="badge badge-success badge-sm text-white font-bold uppercase"><?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-down'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3 mr-1']); ?>
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
<?php endif; ?> Setor</span>
                                <?php else: ?>
                                    <span class="badge badge-error badge-sm text-white font-bold uppercase"><?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-up'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3 mr-1']); ?>
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
<?php endif; ?> Tarik</span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-right font-black <?php echo e($tx->tipe === 'setor' ? 'text-green-600' : 'text-red-600'); ?>">
                                Rp <?php echo e(number_format($tx->jumlah, 0, ',', '.')); ?>

                            </td>
                            <td class="text-xs text-gray-500 truncate max-w-xs"><?php echo e($tx->keterangan); ?></td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7">
                                <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-document-text'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-16 mb-4 text-gray-300']); ?>
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
                                    <p class="font-bold text-lg"><?php echo e(__('Belum ada riwayat transaksi')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($transactions->hasPages()): ?>
            <div class="p-4 border-t border-gray-100 bg-gray-50">
                <?php echo e($transactions->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['title' => 'Form Transaksi Simpanan','boxClass' => 'w-11/12 max-w-3xl'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'transactionModal','class' => 'backdrop-blur-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <form id="form-transaksi" wire:submit="saveTransaction">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <?php if (isset($component)) { $__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207 = $attributes; } ?>
<?php $component = Mary\View\Components\Choices::resolve(['label' => 'Pilih Anggota Koperasi','options' => $anggotaAktif,'optionLabel' => 'nama_label','optionValue' => 'id','single' => true,'searchable' => true] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-choices'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Choices::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'selectedAnggotaId','placeholder' => 'Ketik NIK, No Anggota, atau Nama...','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207)): ?>
<?php $attributes = $__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207; ?>
<?php unset($__attributesOriginalb2c45e9907fdbe9ac5d66b9b5be51207); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207)): ?>
<?php $component = $__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207; ?>
<?php unset($__componentOriginalb2c45e9907fdbe9ac5d66b9b5be51207); ?>
<?php endif; ?>

                    <div class="grid grid-cols-2 gap-4">
                        <?php if (isset($component)) { $__componentOriginald64144c2287634503c73cd4803d6e578 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald64144c2287634503c73cd4803d6e578 = $attributes; } ?>
<?php $component = Mary\View\Components\Select::resolve(['label' => 'Jenis Simpanan','options' => [['id'=>'pokok', 'name'=>'Pokok'], ['id'=>'wajib', 'name'=>'Wajib'], ['id'=>'sukarela', 'name'=>'Sukarela']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Select::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model.live' => 'jenis_simpanan','required' => true]); ?>
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
                        
                        <div class="form-control w-full">
                            <label class="label"><span class="label-text font-semibold">Tipe Transaksi</span></label>
                            <select wire:model.live="tipe_transaksi" class="select select-bordered w-full" required>
                                <option value="setor">Setor Dana (Masuk)</option>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($jenis_simpanan === 'sukarela'): ?>
                                    <option value="tarik">Tarik Tunai (Keluar)</option>
                                <?php else: ?>
                                    <option value="tarik" disabled>Tarik Tunai (Terkunci)</option>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(in_array($jenis_simpanan, ['pokok', 'wajib'])): ?>
                                <label class="label"><span class="label-text-alt text-error font-medium">* <?php echo e(ucfirst($jenis_simpanan)); ?> tidak bisa ditarik harian.</span></label>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>

                    <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => 'Nominal Transaksi (Rp)'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'jumlah','type' => 'number','placeholder' => 'Contoh: 50000','min' => '500','required' => true]); ?>
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
                    
                    <?php if (isset($component)) { $__componentOriginalca8ac43109ad2f324e5674f65ceaac92 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca8ac43109ad2f324e5674f65ceaac92 = $attributes; } ?>
<?php $component = Mary\View\Components\DateTime::resolve(['label' => 'Waktu Transaksi'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-datetime'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\DateTime::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'tanggal_transaksi','type' => 'datetime-local','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca8ac43109ad2f324e5674f65ceaac92)): ?>
<?php $attributes = $__attributesOriginalca8ac43109ad2f324e5674f65ceaac92; ?>
<?php unset($__attributesOriginalca8ac43109ad2f324e5674f65ceaac92); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca8ac43109ad2f324e5674f65ceaac92)): ?>
<?php $component = $__componentOriginalca8ac43109ad2f324e5674f65ceaac92; ?>
<?php unset($__componentOriginalca8ac43109ad2f324e5674f65ceaac92); ?>
<?php endif; ?>
                    
                    <?php if (isset($component)) { $__componentOriginalf51438a7488970badd535e5f203e0c1b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf51438a7488970badd535e5f203e0c1b = $attributes; } ?>
<?php $component = Mary\View\Components\Input::resolve(['label' => 'Keterangan (Opsional)'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Input::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'keterangan','placeholder' => 'Catatan tambahan...']); ?>
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

                <div class="bg-gray-50 border border-gray-200 rounded-xl p-5 shadow-inner">
                    <h3 class="text-sm font-black text-gray-500 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-wallet'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
<?php endif; ?> Info Saldo Saat Ini
                    </h3>
                    
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($selectedAnggotaId): ?>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 shadow-sm transition-all <?php echo e($jenis_simpanan == 'pokok' ? 'ring-2 ring-primary' : ''); ?>">
                                <span class="font-bold text-gray-600">Simpanan Pokok</span>
                                <span class="font-black text-lg">Rp <?php echo e(number_format($saldoSekarang['pokok'], 0, ',', '.')); ?></span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 shadow-sm transition-all <?php echo e($jenis_simpanan == 'wajib' ? 'ring-2 ring-primary' : ''); ?>">
                                <span class="font-bold text-gray-600">Simpanan Wajib</span>
                                <span class="font-black text-lg">Rp <?php echo e(number_format($saldoSekarang['wajib'], 0, ',', '.')); ?></span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-100 shadow-sm transition-all <?php echo e($jenis_simpanan == 'sukarela' ? 'ring-2 ring-primary' : ''); ?>">
                                <span class="font-bold text-gray-600">Simpanan Sukarela</span>
                                <span class="font-black text-lg text-green-600">Rp <?php echo e(number_format($saldoSekarang['sukarela'], 0, ',', '.')); ?></span>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <p class="text-xs text-gray-500 leading-relaxed font-medium">
                                    Total akumulasi kewajiban dan aset anggota di atas tersinkronisasi *real-time* dari buku besar simpanan.
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-10 text-center opacity-50">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-user-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-16 mb-2 text-gray-400']); ?>
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
                            <p class="font-bold">Pilih anggota terlebih dahulu</p>
                            <p class="text-xs">Saldo akan muncul otomatis.</p>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </form>

         <?php $__env->slot('actions', null, []); ?> 
            <button type="button" class="btn btn-ghost" wire:click="$set('transactionModal', false)">Batal</button>
            <button type="submit" form="form-transaksi" wire:loading.attr="disabled" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <span wire:loading wire:target="saveTransaction" class="loading loading-spinner loading-xs"></span>
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-check-circle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:loading.remove' => true,'wire:target' => 'saveTransaction','class' => 'size-4']); ?>
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
                Proses Transaksi
            </button>
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

    <?php if (isset($component)) { $__componentOriginal89a573612f1f1cb2dd9fc072235d4356 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal89a573612f1f1cb2dd9fc072235d4356 = $attributes; } ?>
<?php $component = Mary\View\Components\Modal::resolve(['boxClass' => 'w-11/12 max-w-md'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Modal::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:model' => 'receiptModal','class' => 'backdrop-blur-sm']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($receiptData): ?>
            <div id="print-area" class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl text-gray-800">
                <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
                    <h2 class="text-xl font-black uppercase tracking-widest"><?php echo e(config('app.name', 'Bank Sampah')); ?></h2>
                    <p class="text-xs font-bold text-gray-500">Unit Koperasi Simpan Pinjam</p>
                    <p class="text-xs text-gray-400 mt-1"><?php echo e(\Carbon\Carbon::parse($receiptData->tanggal_transaksi)->format('d M Y - H:i:s')); ?></p>
                </div>
                
                <div class="space-y-2 text-sm font-medium">
                    <div class="flex justify-between"><span class="text-gray-500">No. Transaksi</span> <span class="font-mono font-bold"><?php echo e($receiptData->nomor_transaksi); ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">No. Anggota</span> <span class="font-bold"><?php echo e($receiptData->anggota->nomor_anggota); ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Nama Anggota</span> <span class="font-bold uppercase"><?php echo e($receiptData->anggota->nama); ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Tipe / Jenis</span> <span class="font-bold uppercase"><?php echo e($receiptData->tipe); ?> - <?php echo e($receiptData->jenis_simpanan); ?></span></div>
                </div>

                <div class="my-4 py-4 border-y border-dashed border-gray-300 text-center bg-gray-50 rounded-lg">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Nominal Transaksi</p>
                    <p class="text-3xl font-black <?php echo e($receiptData->tipe === 'setor' ? 'text-green-600' : 'text-red-600'); ?>">Rp <?php echo e(number_format($receiptData->jumlah, 0, ',', '.')); ?></p>
                </div>

                <div class="space-y-2 text-sm font-medium">
                    <div class="flex justify-between"><span class="text-gray-500">Saldo Sebelumnya</span> <span class="font-bold">Rp <?php echo e(number_format($receiptData->saldo_sebelum, 0, ',', '.')); ?></span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Saldo Akhir</span> <span class="font-bold">Rp <?php echo e(number_format($receiptData->saldo_sesudah, 0, ',', '.')); ?></span></div>
                </div>

                <div class="mt-6 pt-4 border-t border-dashed border-gray-300 text-center">
                    <p class="text-xs text-gray-400 font-bold">Dilayani oleh: <?php echo e($receiptData->user->name ?? 'Admin'); ?></p>
                    <p class="text-[10px] text-gray-400 mt-2 italic">Dokumen ini sah dicetak oleh sistem secara otomatis.</p>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

         <?php $__env->slot('actions', null, []); ?> 
            <button type="button" class="btn btn-ghost" wire:click="$set('receiptModal', false)">Tutup</button>
            <button type="button" onclick="printReceipt()" class="btn btn-primary bg-black text-white hover:bg-gray-800 border-none shadow-md">
                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-printer'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-4']); ?>
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
<?php endif; ?> Cetak Kuitansi
            </button>
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

    <script>
        // Cetak kuitansi diformat untuk printer thermal 48mm (lebar cetak ~32 karakter).
        // Pakai iframe tersembunyi (bukan window baru / innerHTML replace) agar
        // tidak memicu popup-blocker dan tidak merusak state Livewire halaman.
        function printReceipt() {
            var printArea = document.getElementById('print-area');
            if (!printArea) return;

            var printContents = printArea.innerHTML;

            var printStyle = `
                @page { size: 48mm auto; margin: 0; }
                * { box-sizing: border-box; }
                body {
                    font-family: 'Courier New', Courier, monospace;
                    color: #000;
                    width: 48mm;
                    margin: 0;
                    padding: 2mm 1.5mm;
                    font-size: 9px;
                    line-height: 1.35;
                }
                h2 { font-size: 11px; margin: 0 0 2px; text-align: center; text-transform: uppercase; }
                p { margin: 0; }
                .flex { display: flex; justify-content: space-between; gap: 4px; }
                .text-center { text-align: center; }
                .space-y-2 > * + * { margin-top: 2px; }
                .border-b, .border-y, .border-t { border-color: #000 !important; border-style: dashed !important; }
                .border-b { border-bottom-width: 1px; padding-bottom: 4px; margin-bottom: 4px; }
                .border-y { border-top-width: 1px; border-bottom-width: 1px; padding: 4px 0; margin: 4px 0; }
                .border-t { border-top-width: 1px; padding-top: 4px; margin-top: 4px; }
                .bg-gray-50, .bg-white { background: none !important; }
                .rounded-lg, .rounded-xl { border-radius: 0 !important; }
                .text-3xl { font-size: 13px; font-weight: 900; }
                .text-xl { font-size: 11px; }
                .text-green-600, .text-red-600, .text-gray-400, .text-gray-500, .text-primary { color: #000 !important; }
                .badge { border: 1px solid #000; padding: 1px 4px; font-size: 8px; border-radius: 0; }
                .uppercase { text-transform: uppercase; }
                .font-bold, .font-black { font-weight: 700; }
                .italic { font-style: italic; }
            `;

            var iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            // Pakai DOM API (bukan doc.write string-concat) supaya tidak bentrok
            // dengan script yang disuntikkan ekstensi/MCP browser logger ke setiap halaman.
            var idoc = iframe.contentDocument || iframe.contentWindow.document;

            var styleEl = idoc.createElement('style');
            styleEl.textContent = printStyle;
            idoc.head.appendChild(styleEl);

            var bodyWrap = idoc.createElement('div');
            bodyWrap.innerHTML = printContents;
            idoc.body.appendChild(bodyWrap);

            setTimeout(function () {
                try {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                } catch (e) {
                    console.error('Gagal mencetak kuitansi:', e);
                }
                setTimeout(function () {
                    if (iframe.parentNode) {
                        iframe.parentNode.removeChild(iframe);
                    }
                }, 1000);
            }, 100);
        }
    </script>
</div><?php /**PATH C:\laragon\www\BANK-SAMPAH\resources\views/pages/admin/koperasi/simpanan.blade.php ENDPATH**/ ?>