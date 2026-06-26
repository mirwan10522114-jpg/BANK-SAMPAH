<?php

use App\Concerns\WasteCategoryValidationRules;
use App\Models\WasteCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Kategori Sampah')] class extends Component {
    use Toast, WasteCategoryValidationRules, WithPagination, WithFileUploads;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $code_prefix = '';

    public string $description = '';

    public bool $is_active = true;

    public ?int $deletingId = null;

    public bool $formModal = false;

    public bool $deleteModal = false;

    public $image = null;

    public ?string $existingImage = null;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => __('Informasi Kategori'), 'class' => 'w-80'],
            ['key' => 'code_prefix', 'label' => __('Kode Prefix'), 'class' => 'hidden sm:table-cell w-36'],
            ['key' => 'items_count', 'label' => __('Jumlah Barang'), 'class' => 'hidden md:table-cell w-36'],
            ['key' => 'status_label', 'label' => __('Status'), 'class' => 'w-32 text-center', 'sortable' => false],
        ];
    }

    #[Computed]
    public function categories()
    {
        return WasteCategory::query()
            ->when($this->search !== '', fn ($q) => $q->where(function ($inner) {
                $inner->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('code_prefix', 'like', '%'.$this->search.'%');
            }))
            ->withCount('items')
            ->orderBy('code_prefix')
            ->paginate(15);
    }

    public function rules(): array
    {
        $rules = $this->wasteCategoryRules($this->editingId);
        $rules['image'] = 'nullable|image|max:2048';
        return $rules;
    }

    public function startCreating(): void
    {
        $this->resetForm();
        $this->formModal = true;
    }

    public function startEditing(int $id): void
    {
        $category = WasteCategory::findOrFail($id);

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->code_prefix = $category->code_prefix;
        $this->description = (string) ($category->description ?? '');
        $this->is_active = (bool) $category->is_active;
        $this->existingImage = $category->image ?? null;
        $this->image = null;

        $this->formModal = true;
    }

    public function save(): void
    {
        $validated = $this->validate();
        $validated['code_prefix'] = strtoupper($validated['code_prefix']);
        unset($validated['image']);

        if ($this->image) {
            $path = $this->image->store('categories', 'public');
            $validated['image'] = $path;
        }

        if ($this->editingId) {
            $category = WasteCategory::findOrFail($this->editingId);

            // Hapus gambar lama jika ada gambar baru
            if ($this->image && $category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->update($validated);
            $this->success(__('Kategori berhasil diperbarui.'));
        } else {
            WasteCategory::create([
                ...$validated,
                'slug' => Str::slug($validated['name']).'-'.Str::random(4),
            ]);
            $this->success(__('Kategori berhasil ditambahkan.'));
        }

        $this->formModal = false;
        $this->resetForm();
    }

    public function removeImage(): void
    {
        if ($this->editingId) {
            $category = WasteCategory::findOrFail($this->editingId);
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
                $category->update(['image' => null]);
            }
        }
        $this->existingImage = null;
        $this->image = null;
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->deleteModal = true;
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $category = WasteCategory::withCount('items')->findOrFail($this->deletingId);

        if ($category->items_count > 0) {
            $this->error(__('Tidak bisa dihapus: masih ada :n barang di kategori ini.', ['n' => $category->items_count]));
            $this->deleteModal = false;
            $this->deletingId = null;

            return;
        }

        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        $this->deletingId = null;
        $this->deleteModal = false;

        $this->success(__('Kategori dihapus.'));
    }

    private function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'code_prefix', 'description', 'is_active', 'image', 'existingImage']);
        $this->is_active = true;
        $this->resetErrorBag();
    }
}; ?>

<section class="w-full space-y-4">
    <x-mary-header
        title="{{ __('Kategori Sampah') }}"
        subtitle="{{ __('Grup barang sampah (Kertas, Logam, Botol, Plastik, dll). Detail harga per barang ada di menu Barang Sampah.') }}"
        separator
        progress-indicator
    >
        <x-slot:middle class="!justify-end">
            <x-mary-input
                wire:model.live.debounce.300ms="search"
                icon="o-magnifying-glass"
                placeholder="{{ __('Cari kategori atau kode...') }}"
                class="input-md bg-base-100 shadow-sm border-base-300 focus:border-primary w-72"
                clearable
            />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button
                icon="o-plus"
                class="btn-primary shadow-sm font-semibold text-sm"
                wire:click="startCreating"
                label="{{ __('Tambah Kategori') }}"
                data-test="category-create-button"
            />
        </x-slot:actions>
    </x-mary-header>

    <div class="overflow-hidden rounded-xl border border-base-200 bg-base-100 shadow-sm">
        <x-mary-table
            :headers="$this->headers"
            :rows="$this->categories"
            with-pagination
            per-page="perPage"
            class="table-sm"
        >
            @scope('cell_name', $row)
                <div class="flex items-center gap-4 py-2">
                    <div class="avatar">
                        {{-- Tampilkan gambar jika ada, fallback ke icon --}}
                        @if ($row->image)
                            <div class="w-16 h-16 rounded-xl overflow-hidden border border-base-300/60 shadow-sm">
                                <img src="{{ Storage::url($row->image) }}" alt="{{ $row->name }}" class="w-full h-full object-cover" />
                            </div>
                        @else
                            <div class="w-16 h-16 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20 shadow-sm text-primary">
                                <x-mary-icon name="o-folder-open" class="w-10 h-10" />
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="font-semibold text-[15px] leading-tight tracking-tight text-base-content hover:text-primary transition-colors cursor-pointer">
                            {{ $row->name }}
                        </span>
                        @if ($row->description)
                            <span class="text-xs text-base-content/45 font-normal leading-relaxed max-w-[260px] truncate" title="{{ $row->description }}">
                                {{ $row->description }}
                            </span>
                        @endif
                    </div>
                </div>
            @endscope

            @scope('cell_code_prefix', $row)
                <span class="text-sm font-mono font-extrabold tracking-widest text-primary bg-primary/10 px-3 py-1.5 rounded-lg border border-primary/20">
                    {{ $row->code_prefix }}
                </span>
            @endscope

            @scope('cell_items_count', $row)
                <div class="flex flex-col items-start gap-0">
                    <span class="font-bold text-xl leading-none text-base-content/90">
                        {{ $row->items_count }}
                    </span>
                    <span class="text-[10px] text-base-content/45 font-semibold uppercase tracking-widest mt-0.5">{{ __('Barang') }}</span>
                </div>
            @endscope

            @scope('cell_status_label', $row)
                <div class="text-center">
                    @if ($row->is_active)
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-success/10 text-success border border-success/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-success inline-block"></span>
                            {{ __('Aktif') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-base-200 text-base-content/50 border border-base-300/40">
                            <span class="w-1.5 h-1.5 rounded-full bg-base-content/30 inline-block"></span>
                            {{ __('Non-aktif') }}
                        </span>
                    @endif
                </div>
            @endscope

            @scope('actions', $row)
                <div class="flex items-center gap-0.5 justify-end">
                    <x-mary-button
                        icon="o-pencil-square"
                        wire:click="startEditing({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/70 hover:text-primary hover:bg-primary/10 rounded-lg"
                        data-test="category-edit-{{ $row->id }}"
                    />
                    <x-mary-button
                        icon="o-trash"
                        wire:click="confirmDelete({{ $row->id }})"
                        class="btn-ghost btn-sm text-base-content/40 hover:text-error hover:bg-error/10 rounded-lg"
                        data-test="category-delete-{{ $row->id }}"
                    />
                </div>
            @endscope
        </x-mary-table>
    </div>

    <x-mary-modal
        wire:model="formModal"
        title="{{ $editingId ? __('Edit Data Kategori') : __('Tambah Kategori Baru') }}"
        subtitle="{{ __('Kode prefix dipakai untuk men-generate kode unik barang secara otomatis (contoh KT = Kertas → KT1, KT2).') }}"
        separator
        box-class="max-w-2xl rounded-2xl"
    >
        <x-mary-form wire:submit="save" no-separator class="space-y-6">

            {{-- Upload Gambar --}}
            <div class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-base-content">{{ __('Gambar Kategori (Opsional)') }}</span>

                {{-- Preview gambar yang sudah ada --}}
                @if ($existingImage && !$image)
                    <div class="relative w-fit">
                        <img src="{{ Storage::url($existingImage) }}" alt="Gambar saat ini" class="w-32 h-32 object-cover rounded-xl border border-base-300 shadow-sm" />
                        <button
                            type="button"
                            wire:click="removeImage"
                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-error text-white flex items-center justify-center shadow-md hover:bg-error/80 transition"
                            title="Hapus gambar"
                        >
                            <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                @endif

                {{-- Preview gambar baru yang dipilih --}}
                @if ($image)
                    <div class="relative w-fit">
                        <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="w-32 h-32 object-cover rounded-xl border border-primary/30 shadow-sm" />
                        <button
                            type="button"
                            wire:click="$set('image', null)"
                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-error text-white flex items-center justify-center shadow-md hover:bg-error/80 transition"
                            title="Batal"
                        >
                            <x-mary-icon name="o-x-mark" class="w-3.5 h-3.5" />
                        </button>
                    </div>
                @endif

                {{-- Area upload --}}
                @if (!$existingImage || $image)
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-base-300 rounded-xl cursor-pointer bg-base-200/30 hover:bg-base-200/60 hover:border-primary/40 transition-all group">
                        <div class="flex flex-col items-center gap-1.5 text-base-content/40 group-hover:text-primary/60 transition-colors">
                            <x-mary-icon name="o-arrow-up-tray" class="w-8 h-8" />
                            <span class="text-sm font-medium">{{ __('Klik untuk upload gambar') }}</span>
                            <span class="text-xs">{{ __('PNG, JPG, WEBP — Maks. 2MB') }}</span>
                        </div>
                        <input type="file" wire:model="image" accept="image/*" class="hidden" />
                    </label>
                @endif

                @error('image')
                    <span class="text-xs text-error font-medium">{{ $message }}</span>
                @enderror

                <div wire:loading wire:target="image" class="text-xs text-primary font-medium flex items-center gap-1.5">
                    <span class="loading loading-spinner loading-xs"></span>
                    {{ __('Mengupload gambar...') }}
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-3">

                <div class="md:col-span-1">
                    <x-mary-input
                        wire:model="code_prefix"
                        label="{{ __('Kode Prefix') }}"
                        icon="o-hashtag"
                        placeholder="CTH: KT"
                        maxlength="8"
                        class="uppercase font-mono font-bold input-bordered tracking-widest"
                        required
                    />
                </div>

                <div class="md:col-span-2">
                    <x-mary-input
                        wire:model="name"
                        label="{{ __('Nama Kategori') }}"
                        icon="o-tag"
                        placeholder="Contoh: Kertas dan Kardus"
                        class="input-bordered font-medium"
                        required
                    />
                </div>

                <div class="md:col-span-3">
                    <x-mary-textarea
                        wire:model="description"
                        label="{{ __('Deskripsi Kategori (Opsional)') }}"
                        placeholder="Tuliskan spesifikasi detail mengenai kategori ini..."
                        rows="2"
                        class="textarea-bordered text-sm leading-relaxed"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between p-4 rounded-xl border border-base-200 bg-base-200/30">
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-semibold tracking-tight text-base-content">{{ __('Status Aktif Kategori') }}</span>
                    <span class="text-xs text-base-content/55 leading-relaxed">{{ __('Kategori yang non-aktif tidak akan bisa dipilih saat menambah barang sampah baru.') }}</span>
                </div>
                <x-mary-toggle wire:model="is_active" class="toggle-success" right />
            </div>

            <x-slot:actions>
                <x-mary-button label="{{ __('Batalkan') }}" @click="$wire.formModal = false" class="btn-ghost text-sm font-medium" />
                <x-mary-button
                    label="{{ __('Simpan Kategori') }}"
                    class="btn-primary font-semibold shadow-sm px-5 text-sm"
                    type="submit"
                    icon="o-check"
                    spinner="save"
                    data-test="category-save-button"
                />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    <x-mary-modal wire:model="deleteModal" title="{{ __('Konfirmasi Hapus Kategori') }}" box-class="max-w-md rounded-2xl">
        <div class="flex flex-col gap-3 py-2">
            <p class="text-sm text-base-content/65 leading-relaxed">
                {{ __('Kategori hanya bisa dihapus jika tidak memiliki barang sampah yang terdaftar di dalamnya. Jika Anda ingin menghapusnya, pastikan untuk menghapus atau memindahkan barang terkait di menu Barang Sampah terlebih dahulu.') }}
            </p>
        </div>

        <x-slot:actions>
            <x-mary-button label="{{ __('Batal') }}" @click="$wire.deleteModal = false" class="btn-ghost text-sm font-medium" />
            <x-mary-button
                label="{{ __('Ya, Hapus') }}"
                class="btn-error font-semibold text-sm px-4"
                wire:click="delete"
                spinner
                data-test="category-confirm-delete"
            />
        </x-slot:actions>
    </x-mary-modal>
</section>