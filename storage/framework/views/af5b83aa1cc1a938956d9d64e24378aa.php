<?php
    use App\Models\Article;
    use App\Models\Product;
    use App\Models\SavingTransaction;
    use App\Models\User;

    $nasabahCount = User::nasabah()->count();
    $totalWeight = (float) SavingTransaction::sum('total_weight');
    $latestArticles = Article::published()->with('author:id,name')->orderByDesc('published_at')->limit(3)->get();
    $featuredProducts = Product::active()->where('stock', '>', 0)->orderBy('name')->limit(4)->get();
?>

<?php if (isset($component)) { $__componentOriginala3ffd7b6f4ed8b1b5263d00782f431a3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala3ffd7b6f4ed8b1b5263d00782f431a3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'f4ac99e09542ff494432bc959d4fee61::public','data' => ['title' => __('Selamat Datang')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts::public'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Selamat Datang'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    
    <section class="bg-gradient-to-b from-primary/10 to-transparent">
        <div class="mx-auto max-w-6xl px-4 py-16 md:py-24 text-center">
            <div class="mx-auto mb-6 flex aspect-square size-16 items-center justify-center rounded-xl bg-primary text-primary-content">
                <?php if (isset($component)) { $__componentOriginal159d6670770cb479b1921cea6416c26c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal159d6670770cb479b1921cea6416c26c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-logo-icon','data' => ['class' => 'size-10 fill-current']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-logo-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-10 fill-current']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal159d6670770cb479b1921cea6416c26c)): ?>
<?php $attributes = $__attributesOriginal159d6670770cb479b1921cea6416c26c; ?>
<?php unset($__attributesOriginal159d6670770cb479b1921cea6416c26c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal159d6670770cb479b1921cea6416c26c)): ?>
<?php $component = $__componentOriginal159d6670770cb479b1921cea6416c26c; ?>
<?php unset($__componentOriginal159d6670770cb479b1921cea6416c26c); ?>
<?php endif; ?>
            </div>
            <h1 class="text-3xl md:text-5xl font-bold tracking-tight">
                <?php echo e(__('Bank Sampah Pak Toni')); ?>

            </h1>
            <p class="mx-auto mt-4 max-w-xl text-base-content/70">
                <?php echo e(__('Sistem operasional pengumpulan dan daur ulang sampah masyarakat. Tabung sampah, dapatkan saldo & poin, atau sedekahkan untuk bumi yang lebih baik.')); ?>

            </p>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                <?php if (isset($component)) { $__componentOriginal602b228a887fab12f0012a3179e5b533 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal602b228a887fab12f0012a3179e5b533 = $attributes; } ?>
<?php $component = Mary\View\Components\Button::resolve(['link' => ''.e(route('public.edukasi.index')).'','label' => ''.e(__('Baca Edukasi')).'','icon' => 'o-book-open'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-primary']); ?>
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
<?php $component = Mary\View\Components\Button::resolve(['link' => ''.e(route('public.merchandise.index')).'','label' => ''.e(__('Lihat Produk')).'','icon' => 'o-cube'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Button::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'btn-ghost']); ?>
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
        </div>
    </section>

    
    <section>
        <div class="mx-auto max-w-6xl px-4 py-10">
            <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                <div class="card-body grid grid-cols-1 gap-4 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-base-300">
                    <div class="text-center sm:pe-4">
                        <div class="text-3xl font-bold text-primary"><?php echo e(number_format($nasabahCount)); ?></div>
                        <div class="text-sm text-base-content/60 mt-1"><?php echo e(__('Nasabah terdaftar')); ?></div>
                    </div>
                    <div class="text-center sm:px-4 pt-4 sm:pt-0">
                        <div class="text-3xl font-bold text-primary">
                            <?php echo e(rtrim(rtrim(number_format($totalWeight, 3, ',', '.'), '0'), ',')); ?> kg
                        </div>
                        <div class="text-sm text-base-content/60 mt-1"><?php echo e(__('Total sampah tertabung')); ?></div>
                    </div>
                    <div class="text-center sm:ps-4 pt-4 sm:pt-0">
                        <div class="text-3xl font-bold text-primary"><?php echo e($latestArticles->count() + $featuredProducts->count()); ?></div>
                        <div class="text-sm text-base-content/60 mt-1"><?php echo e(__('Konten edukasi & produk')); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($latestArticles->isNotEmpty()): ?>
        <section>
            <div class="mx-auto max-w-6xl px-4 py-12">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold"><?php echo e(__('Edukasi Terbaru')); ?></h2>
                        <p class="text-sm text-base-content/60"><?php echo e(__('Belajar tentang daur ulang & dampak lingkungan.')); ?></p>
                    </div>
                    <a href="<?php echo e(route('public.edukasi.index')); ?>" wire:navigate class="link link-primary text-sm hidden md:inline">
                        <?php echo e(__('Lihat semua')); ?> →
                    </a>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $latestArticles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $article): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <a href="<?php echo e(route('public.edukasi.show', $article)); ?>" wire:navigate class="card bg-base-100 border border-base-300 shadow-sm hover:shadow-lg transition-shadow overflow-hidden rounded-2xl">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($article->featured_image): ?>
                                <figure class="aspect-video">
                                    <img src="<?php echo e($article->featured_image); ?>" alt="<?php echo e($article->title); ?>" class="w-full h-full object-cover" />
                                </figure>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <div class="card-body">
                                <h3 class="card-title text-base"><?php echo e($article->title); ?></h3>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($article->excerpt): ?>
                                    <p class="text-sm text-base-content/70 line-clamp-3"><?php echo e($article->excerpt); ?></p>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="text-xs text-base-content/50 mt-2">
                                    <?php echo e($article->published_at->format('d M Y')); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($article->author): ?> • <?php echo e($article->author->name); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featuredProducts->isNotEmpty()): ?>
        <section class="border-t border-base-300">
            <div class="mx-auto max-w-6xl px-4 py-12">
                <div class="flex items-end justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold"><?php echo e(__('Produk Unggulan')); ?></h2>
                        <p class="text-sm text-base-content/60"><?php echo e(__('Hasil olahan sampah yang bisa Anda tukar dengan poin atau beli.')); ?></p>
                    </div>
                    <a href="<?php echo e(route('public.merchandise.index')); ?>" wire:navigate class="link link-primary text-sm hidden md:inline">
                        <?php echo e(__('Lihat semua')); ?> →
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="card bg-base-100 border border-base-300 shadow-sm hover:shadow-lg transition-shadow overflow-hidden rounded-2xl">
                            <figure class="aspect-square bg-primary/5">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($product->image): ?>
                                    <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover" />
                                <?php else: ?>
                                    <div class="flex w-full h-full items-center justify-center text-primary">
                                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-cube'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-12']); ?>
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
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </figure>
                            <div class="card-body p-4">
                                <h3 class="font-semibold text-sm"><?php echo e($product->name); ?></h3>
                                <div class="text-sm text-primary font-semibold">
                                    Rp <?php echo e(number_format((float) $product->price, 0, ',', '.')); ?>

                                </div>
                                <div class="text-xs text-base-content/60">
                                    <?php echo e(__('Stok')); ?>:
                                    <?php echo e(rtrim(rtrim(number_format((float) $product->stock, 3, ',', '.'), '0'), ',')); ?>

                                    <?php echo e($product->unit); ?>

                                </div>
                            </div>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <section>
        <div class="mx-auto max-w-6xl px-4 py-12">
            <h2 class="text-2xl font-bold text-center mb-8"><?php echo e(__('Cara Kerja')); ?></h2>
            <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body items-center text-center">
                        <div class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary mb-2">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-scale'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6']); ?>
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
                        <h3 class="card-title text-lg">1. <?php echo e(__('Timbang & Catat')); ?></h3>
                        <p class="text-sm text-base-content/70"><?php echo e(__('Bawa sampah Anda ke Pak Toni. Admin akan timbang dan catat ke sistem sesuai harga berlaku.')); ?></p>
                    </div>
                </div>
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body items-center text-center">
                        <div class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary mb-2">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-banknotes'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6']); ?>
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
                        <h3 class="card-title text-lg">2. <?php echo e(__('Saldo Terkumpul')); ?></h3>
                        <p class="text-sm text-base-content/70"><?php echo e(__('Nilai sampah jadi saldo Anda. Member juga dapat poin untuk ditukar merchandise.')); ?></p>
                    </div>
                </div>
                <div class="card bg-base-100 border border-base-300 shadow-sm rounded-2xl">
                    <div class="card-body items-center text-center">
                        <div class="flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary mb-2">
                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-wallet'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-6']); ?>
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
                        <h3 class="card-title text-lg">3. <?php echo e(__('Cairkan')); ?></h3>
                        <p class="text-sm text-base-content/70"><?php echo e(__('Setelah dana siap, admin rilis saldo dan Anda bisa cairkan via cash atau transfer.')); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala3ffd7b6f4ed8b1b5263d00782f431a3)): ?>
<?php $attributes = $__attributesOriginala3ffd7b6f4ed8b1b5263d00782f431a3; ?>
<?php unset($__attributesOriginala3ffd7b6f4ed8b1b5263d00782f431a3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala3ffd7b6f4ed8b1b5263d00782f431a3)): ?>
<?php $component = $__componentOriginala3ffd7b6f4ed8b1b5263d00782f431a3; ?>
<?php unset($__componentOriginala3ffd7b6f4ed8b1b5263d00782f431a3); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\BANK-SAMPAH\resources\views/welcome.blade.php ENDPATH**/ ?>