<div style="margin-bottom: 1rem;">
    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Nama Layanan *</label>
    <input type="text" name="name" required placeholder="Contoh: Makeup Wedding Adat Sunda"
           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
</div>
<div style="margin-bottom: 1rem;">
    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Kategori *</label>
    <select name="category" required style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
        <option value="">— Pilih Kategori —</option>
        <option value="wedding">💍 Wedding</option>
        <option value="graduation">🎓 Wisuda</option>
        <option value="party">🎉 Party</option>
        <option value="photoshoot">📸 Photoshoot</option>
        <option value="formal">👔 Formal</option>
        <option value="other">✨ Lainnya</option>
    </select>
</div>
<div style="margin-bottom: 1rem;">
    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Harga (Rp) *</label>
    <input type="number" name="price" required min="10000" placeholder="500000"
           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
</div>
<div style="margin-bottom: 1rem;">
    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Deskripsi</label>
    <textarea name="description" rows="3" placeholder="Deskripsi layanan ini..."
              style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem; resize: vertical;"></textarea>
</div>
<div style="display: flex; align-items: center; gap: 0.75rem;">
    <input type="hidden" name="is_active" value="0">
    <input type="checkbox" name="is_active" id="isActiveCheck" value="1" checked
           style="width: 18px; height: 18px; accent-color: #9B6B6B; cursor: pointer;">
    <label for="isActiveCheck" style="font-weight: 500; font-size: 0.875rem; cursor: pointer;">Layanan aktif (ditampilkan ke pelanggan)</label>
</div>
