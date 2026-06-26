<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['title' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-theme="greennature">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />

    <title>
        <?php echo e(filled($title ?? null) ? $title.' - '.config('app.name', 'Bank Sampah') : config('app.name', 'Bank Sampah')); ?>

    </title>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml" />

    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=lato:400,500,700,900" rel="stylesheet" />

    
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen font-sans antialiased bg-base-200 text-base-content">

<?php
    use Illuminate\Support\Facades\Route;

    $isStaff = auth()->user()?->isStaff();

    /*
     * Safe route resolver. Returns null (instead of throwing) when a route
     * name isn't registered yet. Lets us build out menu groups for features
     * that are still mid-development (controllers/routes not wired up yet)
     * without taking down every other admin page.
     */
    $safeRoute = function (string $name, array $params = []) {
        return Route::has($name) ? route($name, $params) : null;
    };

    /*
     * Build a nav item. Returns null if the underlying route doesn't exist,
     * so it can simply be filtered out below.
     */
    $navItem = function (string $label, string $icon, string $routeName, string $match, array $opts = []) use ($safeRoute) {
        $href = $safeRoute($routeName, $opts['params'] ?? []);
        if ($href === null) {
            return null;
        }

        return array_merge([
            'label' => $label,
            'icon' => $icon,
            'href' => $href,
            'match' => $match,
            'exact' => $opts['exact'] ?? false,
        ], array_intersect_key($opts, array_flip(['query', 'query_value'])));
    };

    $dashboardRouteName = $isStaff ? 'admin.dashboard' : 'dashboard';
    $dashboardHref = $safeRoute($dashboardRouteName) ?? url('/');
    $dashboardMatch = $isStaff ? '/admin' : '/dashboard';
    // Dashboard always exact — otherwise '/admin' prefix would match every
    // '/admin/*' page and highlight together with the current sub-menu.
    $dashboardExact = true;

    $navSections = [
        [
            'items' => array_filter([
                [
                    'label' => __('Dashboard'),
                    'icon' => 'o-home',
                    'href' => $dashboardHref,
                    'match' => $dashboardMatch,
                    'exact' => $dashboardExact,
                ],
                $isStaff ? ($navItem(__('Manajemen User'), 'o-user-group', 'admin.user.index', '/admin/manajemen-user', ['exact' => false]) ?? null) : null,
            ]),
        ],
    ];

    if (! $isStaff && auth()->user()?->isNasabah()) {
        $navSections[] = [
            'label' => __('Akun Saya'),
            'items' => array_values(array_filter([
                $navItem(__('Saldo'), 'o-banknotes', 'nasabah.saldo', '/saldo', ['exact' => true]),
                $navItem(__('Transaksi Nabung'), 'o-arrow-trending-up', 'nasabah.transaksi', '/transaksi', ['exact' => true]),
                $navItem(__('Pencairan'), 'o-wallet', 'nasabah.pencairan', '/pencairan-saya', ['exact' => true]),
                $navItem(__('Histori Poin'), 'o-sparkles', 'nasabah.poin', '/poin', ['exact' => true]),
            ])),
        ];
    }

    if (! $isStaff && auth()->user()?->isKoperasi()) {
        $navSections[] = [
            'label' => __('Koperasi Saya'),
            'items' => array_values(array_filter([
                $navItem(__('Simpanan Saya'), 'o-wallet', 'koperasi.member.simpanan', '/koperasi/simpanan-saya', ['exact' => true]),
                $navItem(__('Pinjaman Saya'), 'o-credit-card', 'koperasi.member.pinjaman', '/koperasi/pinjaman-saya', ['exact' => true]),
            ])),
        ];
    }

    if ($isStaff) {
        $staffGroups = [
            [
                'label' => __('Manajemen'), 'key' => 'manajemen', 'default_open' => false,
                'items' => [
                    // Bagian ini dikosongkan. Silakan tambahkan menu modul baru Anda di bawah ini:
                    // $navItem(__('Modul Baru'), 'o-star', 'admin.modul.index', '/admin/modul-baru'),
                ],
            ],
            [
                'label' => __('Master Data'), 'key' => 'master', 'default_open' => true,
                'items' => [
                    $navItem(__('Nasabah'), 'o-users', 'admin.nasabah.index', '/admin/nasabah'),
                    $navItem(__('Kategori Sampah'), 'o-tag', 'admin.waste-category.index', '/admin/kategori-sampah'),
                    $navItem(__('Barang Sampah'), 'o-hashtag', 'admin.waste-item.index', '/admin/barang-sampah'),
                    $navItem(__('Mitra'), 'o-building-office', 'admin.partner.index', '/admin/mitra'),
                    $navItem(__('Produk'), 'o-cube', 'admin.product.index', '/admin/produk'),
                    $navItem(__('Master Poin'), 'o-star', 'admin.point-rule.index', '/admin/master-poin'),
                ],
            ],
            [
                'label' => __('Transaksi'), 'key' => 'transaksi', 'default_open' => true,
                'items' => [
                    $navItem(__('Nabung'), 'o-arrow-trending-up', 'admin.saving.index', '/admin/nabung'),
                    $navItem(__('Sedekah'), 'o-heart', 'admin.sedekah.index', '/admin/sedekah'),
                    $navItem(__('Penjualan ke Mitra'), 'o-truck', 'admin.sales.index', '/admin/penjualan'),
                    $navItem(__('Pengolahan'), 'o-cog-8-tooth', 'admin.processing.index', '/admin/pengolahan'),
                    $navItem(__('Penjualan Produk'), 'o-shopping-bag', 'admin.product-sale.index', '/admin/penjualan-produk'),
                ],
            ],
            [
                'label' => __('Inventory'), 'key' => 'inventory', 'default_open' => true,
                'items' => [
                    $navItem(__('Inventory Nabung'), 'o-archive-box', 'admin.inventory.index', '/admin/inventory', [
                        'params' => ['source' => 'nabung'], 'query' => 'source', 'query_value' => 'nabung',
                    ]),
                    $navItem(__('Inventory Sedekah'), 'o-gift', 'admin.inventory.index', '/admin/inventory', [
                        'params' => ['source' => 'sedekah'], 'query' => 'source', 'query_value' => 'sedekah',
                    ]),
                ],
            ],
            [
                'label' => __('Keuangan'), 'key' => 'keuangan', 'default_open' => true,
                'items' => [
                    $navItem(__('Release Saldo'), 'o-arrow-right-circle', 'admin.release.index', '/admin/release-saldo'),
                    $navItem(__('Pencairan'), 'o-wallet', 'admin.withdrawal.index', '/admin/pencairan'),
                ],
            ],
            [
                'label' => __('Loyalti'), 'key' => 'loyalti', 'default_open' => false,
                'items' => [
                    $navItem(__('Tukar Poin → Produk'), 'o-gift', 'admin.redemption.index', '/admin/tukar-poin', ['exact' => true]),
                    $navItem(__('Tukar Poin → Saldo'), 'o-banknotes', 'admin.point-cash-out.index', '/admin/tukar-poin-saldo'),
                    $navItem(__('Histori Poin'), 'o-sparkles', 'admin.point-history.index', '/admin/histori-poin'),
                ],
            ],
            [
                'label' => __('Konten'), 'key' => 'konten', 'default_open' => false,
                'items' => [
                    $navItem(__('Edukasi'), 'o-book-open', 'admin.article.index', '/admin/artikel'),
                ],
            ],
            [
                'label' => __('Koperasi'), 'key' => 'koperasi', 'default_open' => true,
                'items' => [
                    $navItem(__('Dashboard'), 'o-chart-bar', 'admin.koperasi.dashboard', '/admin/koperasi/dashboard'),
                    $navItem(__('Anggota'), 'o-users', 'admin.koperasi.anggota', '/admin/koperasi/anggota'),
                    $navItem(__('Simpanan'), 'o-wallet', 'admin.koperasi.simpanan', '/admin/koperasi/simpanan'),
                    $navItem(__('Penarikan Sukarela'), 'o-banknotes', 'admin.koperasi.penarikan-sukarela', '/admin/koperasi/penarikan-sukarela'),
                    $navItem(__('Pinjaman & Angsuran'), 'o-credit-card', 'admin.koperasi.pinjaman', '/admin/koperasi/pinjaman'),
                    $navItem(__('Laporan'), 'o-document-text', 'admin.koperasi.laporan', '/admin/koperasi/laporan'),
                    $navItem(__('Pengaturan'), 'o-cog-6-tooth', 'admin.koperasi.pengaturan', '/admin/koperasi/pengaturan'),
                ],
            ],
        ];

        foreach ($staffGroups as $group) {
            // Drop missing-route items, then drop the whole group if nothing's left.
            $items = array_values(array_filter($group['items']));

            if (empty($items)) {
                continue;
            }

            $navSections[] = [
                'label' => $group['label'],
                'key' => $group['key'],
                'collapsible' => true,
                'default_open' => $group['default_open'],
                'items' => $items,
            ];
        }
    }

    /*
     * Server-side active state for first-paint. Alpine takes over on
     * `livewire:navigated` so highlight stays correct after client nav.
     */
    $currentPath = '/'.ltrim(request()->path(), '/');
    $currentQuery = request()->query();

    $isItemActive = function (array $item) use ($currentPath, $currentQuery) {
        if (isset($item['query'])) {
            if ($currentPath !== $item['match']) return false;
            return (string) ($currentQuery[$item['query']] ?? '') === (string) $item['query_value'];
        }
        if ($item['exact'] ?? false) return $currentPath === $item['match'];
        return $currentPath === $item['match'] || str_starts_with($currentPath, $item['match'].'/');
    };

    $settingsHref = $safeRoute('settings.index');
    $logoutHref = $safeRoute('logout');
    $homeHref = $safeRoute('home') ?? url('/');
?>


<header class="lg:hidden sticky top-0 z-30 flex items-center justify-between bg-secondary text-secondary-content px-4 py-3 shadow-sm">
    <a href="<?php echo e($homeHref); ?>" class="flex items-center gap-2" wire:navigate>
        <div class="flex aspect-square size-9 items-center justify-center rounded-md bg-primary text-primary-content">
            <?php if (isset($component)) { $__componentOriginal159d6670770cb479b1921cea6416c26c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal159d6670770cb479b1921cea6416c26c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-logo-icon','data' => ['class' => 'size-5 fill-current']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-logo-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 fill-current']); ?>
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
        <div class="leading-tight">
            <div class="text-sm font-bold uppercase tracking-wide"><?php echo e(config('app.name', 'Bank Sampah')); ?></div>
        </div>
    </a>
    <button
        type="button"
        @click="$dispatch('toggle-sidebar')"
        class="btn btn-square btn-ghost btn-sm text-secondary-content hover:bg-secondary-content/10"
        aria-label="<?php echo e(__('Buka menu')); ?>"
    >
        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-bars-3'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
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
    </button>
</header>


<?php app("livewire")->forceAssetInjection(); ?><div x-persist="<?php echo e('app-sidebar'); ?>">
<div
    x-data="{
        mobileOpen: false,
        path: window.location.pathname,
        query: new URLSearchParams(window.location.search),
        isActive(item) {
            if (item.query) {
                if (this.path !== item.match) return false;
                return (this.query.get(item.query) ?? '') === item.query_value;
            }
            if (item.exact) return this.path === item.match;
            return this.path === item.match || this.path.startsWith(item.match + '/');
        },
        syncLocation() {
            this.path = window.location.pathname;
            this.query = new URLSearchParams(window.location.search);
        },
    }"
    @toggle-sidebar.window="mobileOpen = !mobileOpen"
    @keydown.escape.window="mobileOpen = false"
    x-init="document.addEventListener('livewire:navigated', () => { syncLocation(); mobileOpen = false; })"
>
    
    <div
        x-show="mobileOpen"
        x-transition.opacity
        x-cloak
        @click="mobileOpen = false"
        class="fixed inset-0 bg-black/40 z-30 lg:hidden"
        aria-hidden="true"
    ></div>

    
    <aside
        :class="mobileOpen ? '!translate-x-0' : ''"
        class="fixed inset-y-0 left-0 z-40 w-64 bg-secondary text-secondary-content transform transition-transform duration-200 ease-out -translate-x-full lg:translate-x-0"
    >
        <nav class="flex h-screen w-64 flex-col">
            
            <div class="flex h-16 items-center justify-between gap-2 px-4 border-b border-secondary-content/10 shrink-0">
                <a href="<?php echo e($homeHref); ?>" class="flex items-center gap-2 min-w-0" wire:navigate>
                    <div class="flex aspect-square size-9 items-center justify-center rounded-md bg-primary text-primary-content shrink-0">
                        <?php if (isset($component)) { $__componentOriginal159d6670770cb479b1921cea6416c26c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal159d6670770cb479b1921cea6416c26c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-logo-icon','data' => ['class' => 'size-5 fill-current']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-logo-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 fill-current']); ?>
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
                    <div class="leading-tight min-w-0">
                        <div class="text-sm font-bold uppercase tracking-wide truncate"><?php echo e(config('app.name', 'Bank Sampah')); ?></div>
                        <div class="text-[10px] uppercase tracking-[0.2em] text-secondary-content/60">Eco Operational</div>
                    </div>
                </a>
                <a href="<?php echo e($homeHref); ?>" class="btn btn-square btn-ghost btn-sm shrink-0 text-secondary-content hover:bg-secondary-content/10" title="<?php echo e(__('Ke Beranda Situs')); ?>">
                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-home'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5']); ?>
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
                </a>
            </div>

            
            <div
                id="nav-scroll-root"
                class="flex-1 overflow-y-auto py-3"
                x-data="{
                    init() {
                        const el = this.$el;
                        document.addEventListener('click', (e) => {
                            const link = e.target.closest && e.target.closest('a[wire\\:navigate]');
                            if (link) window.__sidebarScroll = el.scrollTop;
                        }, true);
                        document.addEventListener('livewire:navigated', () => {
                            const s = window.__sidebarScroll;
                            if (typeof s === 'number' && s > 0) {
                                requestAnimationFrame(() => { el.scrollTop = s; });
                            }
                        });
                    },
                }"
            >
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $navSections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(empty($section['items'])) continue; ?>

                    <?php
                        $collapsible = $section['collapsible'] ?? false;
                        $sectionKey = $section['key'] ?? ($section['label'] ?? 'nav');
                        $serverHasActive = collect($section['items'])->contains(fn ($i) => $isItemActive($i));
                        $serverOpen = $serverHasActive || ($section['default_open'] ?? true);
                    ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($collapsible && ! empty($section['label'])): ?>
                        <div
                            x-data="{
                                key: 'nav:<?php echo e($sectionKey); ?>',
                                defaultOpen: <?php echo e(($section['default_open'] ?? true) ? 'true' : 'false'); ?>,
                                items: <?php echo e(json_encode($section['items'])); ?>,
                                open: <?php echo e($serverOpen ? 'true' : 'false'); ?>,
                                hasActive() { return this.items.some(i => isActive(i)); },
                                init() {
                                    const stored = localStorage.getItem(this.key);
                                    if (stored === '1' || stored === '0') {
                                        this.open = stored === '1';
                                    } else {
                                        this.open = this.hasActive() || this.defaultOpen;
                                    }
                                    this.$watch('open', v => localStorage.setItem(this.key, v ? '1' : '0'));
                                },
                            }"
                            class="mt-3"
                        >
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex w-full items-center justify-between gap-2 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-content/60 hover:text-secondary-content transition-colors cursor-pointer"
                                :aria-expanded="open"
                            >
                                <span class="flex items-center gap-2">
                                    <?php echo e($section['label']); ?>

                                    <span
                                        x-show="hasActive()"
                                        class="size-1.5 rounded-full bg-accent"
                                        <?php if(! $serverHasActive): ?> style="display:none" <?php endif; ?>
                                    ></span>
                                </span>
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-chevron-down'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-3.5 transition-transform',':class' => 'open ? \'rotate-0\' : \'-rotate-90\'']); ?>
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
                            </button>

                            <ul
                                x-show="open"
                                x-transition.duration.150ms
                                <?php if(! $serverOpen): ?> style="display:none" <?php endif; ?>
                                class="px-2 space-y-0.5 mt-0.5"
                            >
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <?php $itemActive = $isItemActive($item); ?>
                                    <li>
                                        <a
                                            href="<?php echo e($item['href']); ?>"
                                            wire:navigate
                                            class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors <?php echo e($itemActive ? 'bg-accent text-accent-content font-semibold shadow-sm' : 'text-secondary-content/80 hover:bg-secondary-content/10 hover:text-secondary-content'); ?>"
                                            :class="{
                                                'bg-accent text-accent-content font-semibold shadow-sm': isActive(<?php echo e(json_encode($item)); ?>),
                                                'text-secondary-content/80 hover:bg-secondary-content/10 hover:text-secondary-content': !isActive(<?php echo e(json_encode($item)); ?>),
                                            }"
                                        >
                                            <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $item['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 shrink-0']); ?>
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
                                            <span class="truncate"><?php echo e($item['label']); ?></span>
                                        </a>
                                    </li>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($section['label'])): ?>
                            <div class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-[0.15em] text-secondary-content/50">
                                <?php echo e($section['label']); ?>

                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <ul class="px-2 space-y-0.5">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <?php $itemActive = $isItemActive($item); ?>
                                <li>
                                    <a
                                        href="<?php echo e($item['href']); ?>"
                                        wire:navigate
                                        class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm transition-colors <?php echo e($itemActive ? 'bg-accent text-accent-content font-semibold shadow-sm' : 'text-secondary-content/80 hover:bg-secondary-content/10 hover:text-secondary-content'); ?>"
                                        :class="{
                                            'bg-accent text-accent-content font-semibold shadow-sm': isActive(<?php echo e(json_encode($item)); ?>),
                                            'text-secondary-content/80 hover:bg-secondary-content/10 hover:text-secondary-content': !isActive(<?php echo e(json_encode($item)); ?>),
                                        }"
                                    >
                                        <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => $item['icon']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Icon::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'size-5 shrink-0']); ?>
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
                                        <span class="truncate"><?php echo e($item['label']); ?></span>
                                    </a>
                                </li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </div>

            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user = auth()->user()): ?>
                <div class="border-t border-secondary-content/10 p-3 space-y-2 shrink-0">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settingsHref): ?>
                        <a
                            href="<?php echo e($settingsHref); ?>"
                            wire:navigate
                            class="flex items-center gap-3 rounded-lg p-2 transition-colors"
                            :class="{
                                'bg-secondary-content/10': path.startsWith('/settings'),
                                'hover:bg-secondary-content/10': !path.startsWith('/settings'),
                            }"
                        >
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-accent text-accent-content text-sm font-bold">
                                <?php echo e($user->initials()); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-semibold"><?php echo e($user->name); ?></div>
                                <div class="truncate text-xs text-secondary-content/60"><?php echo e($user->email); ?></div>
                            </div>
                        </a>
                    <?php else: ?>
                        <div class="flex items-center gap-3 rounded-lg p-2">
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-accent text-accent-content text-sm font-bold">
                                <?php echo e($user->initials()); ?>

                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-sm font-semibold"><?php echo e($user->name); ?></div>
                                <div class="truncate text-xs text-secondary-content/60"><?php echo e($user->email); ?></div>
                            </div>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <div class="flex gap-1">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($settingsHref): ?>
                            <a href="<?php echo e($settingsHref); ?>" wire:navigate class="btn btn-ghost btn-sm flex-1 justify-start text-secondary-content/80 hover:bg-secondary-content/10 hover:text-secondary-content" title="<?php echo e(__('Pengaturan')); ?>">
                                <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-cog-6-tooth'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                                <span class="text-xs"><?php echo e(__('Pengaturan')); ?></span>
                            </a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logoutHref): ?>
                            <form method="POST" action="<?php echo e($logoutHref); ?>">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn btn-ghost btn-sm text-accent hover:bg-accent/15" title="<?php echo e(__('Keluar')); ?>">
                                    <?php if (isset($component)) { $__componentOriginalce0070e6ae017cca68172d0230e44821 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce0070e6ae017cca68172d0230e44821 = $attributes; } ?>
<?php $component = Mary\View\Components\Icon::resolve(['name' => 'o-arrow-right-on-rectangle'] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
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
                                    <span class="text-xs"><?php echo e(__('Keluar')); ?></span>
                                </button>
                            </form>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </nav>
    </aside>
</div>
</div>


<main class="lg:pl-64 min-h-screen">
    <div class="mx-auto w-full max-w-7xl p-4 md:p-6 lg:p-8">
        <?php echo e($slot); ?>

    </div>
</main>

<?php if (isset($component)) { $__componentOriginal2aca76be1376419dfd37220f36011753 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2aca76be1376419dfd37220f36011753 = $attributes; } ?>
<?php $component = Mary\View\Components\Toast::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('mary-toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Mary\View\Components\Toast::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $attributes = $__attributesOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__attributesOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2aca76be1376419dfd37220f36011753)): ?>
<?php $component = $__componentOriginal2aca76be1376419dfd37220f36011753; ?>
<?php unset($__componentOriginal2aca76be1376419dfd37220f36011753); ?>
<?php endif; ?>
</body>
</html><?php /**PATH C:\laragon\www\BANK-SAMPAH\resources\views/layouts/app.blade.php ENDPATH**/ ?>