<div class="w-full pb-10">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight"><?php echo e(__('Pinjaman Saya')); ?></h1>
            <p class="text-sm text-gray-500 mt-1"><?php echo e(__('Daftar pinjaman dan status angsuran Anda.')); ?></p>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $anggota): ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 text-center">
            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-exclamation-triangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-10 text-yellow-500 mx-auto mb-3']); ?>
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
            <p class="text-gray-700 font-medium">Akun Anda belum terhubung ke data anggota koperasi.</p>
            <p class="text-sm text-gray-500 mt-1">Silakan hubungi admin untuk menghubungkan akun Anda.</p>
        </div>
    <?php elseif($pinjamans->isEmpty()): ?>
        <div class="bg-white border border-gray-200 rounded-xl p-8 text-center text-gray-400 text-sm">
            Tidak ada data pinjaman.
        </div>
    <?php else: ?>
        <div class="space-y-6">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pinjamans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pinjaman): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="p-5 flex flex-col md:flex-row gap-4 justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Pinjaman</p>
                            <p class="text-xl font-black text-gray-800 mt-1">
                                Rp <?php echo e(number_format($pinjaman->jumlah_pinjaman, 0, ',', '.')); ?>

                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php echo e(\Carbon\Carbon::parse($pinjaman->tanggal_pinjaman)->translatedFormat('d M Y')); ?>

                                &mdash; <?php echo e($pinjaman->tenor); ?> bulan
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Sisa</p>
                            <p class="text-xl font-black text-gray-800 mt-1">
                                Rp <?php echo e(number_format($pinjaman->sisa_pinjaman, 0, ',', '.')); ?>

                            </p>
                            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'px-2 py-0.5 rounded-full text-xs font-semibold mt-1 inline-block',
                                'bg-green-100 text-green-700' => $pinjaman->status === 'lunas',
                                'bg-blue-100 text-blue-700' => $pinjaman->status === 'berjalan',
                                'bg-gray-100 text-gray-600' => ! in_array($pinjaman->status, ['lunas', 'berjalan']),
                            ]); ?>"><?php echo e(ucfirst($pinjaman->status)); ?></span>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pinjaman->angsurans && $pinjaman->angsurans->isNotEmpty()): ?>
                        <div class="border-t border-gray-100 overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] tracking-wider">
                                    <tr>
                                        <th class="px-5 py-2 text-left">Ke-</th>
                                        <th class="px-5 py-2 text-left">Jatuh Tempo</th>
                                        <th class="px-5 py-2 text-right">Jumlah</th>
                                        <th class="px-5 py-2 text-left">Status</th>
                                        <th class="px-5 py-2 text-left">Dibayar</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $pinjaman->angsurans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $angsuran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-5 py-2 text-gray-600"><?php echo e($angsuran->ke); ?></td>
                                            <td class="px-5 py-2 text-gray-600">
                                                <?php echo e(\Carbon\Carbon::parse($angsuran->tanggal_jatuh_tempo)->translatedFormat('d M Y')); ?>

                                            </td>
                                            <td class="px-5 py-2 text-right font-medium">
                                                Rp <?php echo e(number_format($angsuran->jumlah_angsuran, 0, ',', '.')); ?>

                                            </td>
                                            <td class="px-5 py-2">
                                                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                                    'px-2 py-0.5 rounded-full text-xs font-semibold',
                                                    'bg-green-100 text-green-700' => $angsuran->status === 'lunas',
                                                    'bg-yellow-100 text-yellow-700' => $angsuran->status === 'belum',
                                                ]); ?>"><?php echo e(ucfirst($angsuran->status)); ?></span>
                                            </td>
                                            <td class="px-5 py-2 text-gray-500 text-xs">
                                                <?php echo e($angsuran->tanggal_bayar ? \Carbon\Carbon::parse($angsuran->tanggal_bayar)->translatedFormat('d M Y') : '-'); ?>

                                            </td>
                                        </tr>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\BANK-SAMPAH\resources\views/pages/koperasi-member/pinjaman-saya.blade.php ENDPATH**/ ?>