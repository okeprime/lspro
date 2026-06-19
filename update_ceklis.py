import sys

file_path = 'resources/views/admin/ceklis.blade.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add @php block at the top
php_block = '''@php
    $statusNormalized = \App\Support\LsproType5Workflow::normalize($pengajuan->status);
    $isTU = str_contains(strtolower(auth()->user()->sub_role ?? ''), 'tatausaha') || strtolower(auth()->user()->role) === 'admin';
    $isAudit = str_contains(strtolower(auth()->user()->sub_role ?? ''), 'audit') || strtolower(auth()->user()->role) === 'admin';
    $show724 = in_array($statusNormalized, ['evaluasi_724_tu', 'evaluasi_724_audit', 'menunggu_ttd', 'billing', 'proses_evaluasi', 'proses_audit', 'keputusan', 'selesai']);
    $disableEvaluasi = !($statusNormalized === 'evaluasi_724_tu' && $isTU);
    $disableKebenaran = !($statusNormalized === 'evaluasi_724_audit' && $isAudit);
@endphp

'''
content = content.replace('@section(\'content\')\n', '@section(\'content\')\n' + php_block)

# 2. Update form action
old_form = '<form action="{{ route(\'admin.pengajuan.ceklis\', $pengajuan->id) }}" method="POST">'
new_form = '''    @if($statusNormalized === 'diajukan')
    <form action="{{ route('admin.pengajuan.terima_awal', $pengajuan->id) }}" method="POST">
    @else
    <form action="{{ route('admin.pengajuan.ceklis', $pengajuan->id) }}" method="POST">
    @endif'''
content = content.replace(old_form, new_form)

# 3. Wrap Form 7.2-4
old_724 = '<div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 16px; background: white;">\n                    <h5 class="fw-bold text-dark mb-3">\n                        <i class="bi bi-ui-checks-grid text-primary me-2"></i>Formulir 7.2-4 / LS Pro\n                    </h5>'
new_724 = '''                @if($show724)
                <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius: 16px; background: white;">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-ui-checks-grid text-primary me-2"></i>Formulir 7.2-4 / LS Pro
                    </h5>'''
content = content.replace(old_724, new_724)

# 4. Disable inputs
old_eval_lengkap = 'value="lengkap" {{ $valEvaluasi == \'lengkap\' ? \'checked\' : \'\' }}'
new_eval_lengkap = 'value="lengkap" {{ $valEvaluasi == \'lengkap\' ? \'checked\' : \'\' }} {{ $disableEvaluasi ? \'disabled\' : \'\' }}'
content = content.replace(old_eval_lengkap, new_eval_lengkap)

old_eval_tidak = 'value="tidak" {{ $valEvaluasi == \'tidak\' ? \'checked\' : \'\' }}'
new_eval_tidak = 'value="tidak" {{ $valEvaluasi == \'tidak\' ? \'checked\' : \'\' }} {{ $disableEvaluasi ? \'disabled\' : \'\' }}'
content = content.replace(old_eval_tidak, new_eval_tidak)

old_keb_benar = 'value="benar" {{ $valKebenaran == \'benar\' ? \'checked\' : \'\' }}'
new_keb_benar = 'value="benar" {{ $valKebenaran == \'benar\' ? \'checked\' : \'\' }} {{ $disableKebenaran ? \'disabled\' : \'\' }}'
content = content.replace(old_keb_benar, new_keb_benar)

old_keb_tidak = 'value="tidak" {{ $valKebenaran == \'tidak\' ? \'checked\' : \'\' }}'
new_keb_tidak = 'value="tidak" {{ $valKebenaran == \'tidak\' ? \'checked\' : \'\' }} {{ $disableKebenaran ? \'disabled\' : \'\' }}'
content = content.replace(old_keb_tidak, new_keb_tidak)

old_ket = 'value="{{ $valKet }}" placeholder="Ket..."'
new_ket = 'value="{{ $valKet }}" placeholder="Ket..." {{ ($disableEvaluasi && $disableKebenaran) ? \'disabled\' : \'\' }}'
content = content.replace(old_ket, new_ket)

# 5. Close @if for 7.2-4
old_724_close = '                    </div>\n                </div>\n            </div>'
new_724_close = '''                    </div>
                </div>
                @else
                <div class="alert alert-info mt-3" style="border-radius: 12px; font-size: 14px;">
                    <i class="bi bi-info-circle-fill me-2"></i> Formulir 7.2-4 / Daftar Ceklis belum tersedia karena Klien belum mengunggah dokumen kelengkapan.
                </div>
                @endif
            </div>'''
content = content.replace(old_724_close, new_724_close)

# 6. Update Kesimpulan Section
old_kesimpulan = '''<div class="form-check bg-dark bg-opacity-25 p-3 mb-2" style="border-radius: 8px; border: 1px solid #334155;">
                                <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusLengkap" value="perjanjian" required {{ in_array(\App\Support\LsproType5Workflow::normalize($pengajuan->status), ['perjanjian'], true) ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-success" for="statusLengkap"><i class="bi bi-check-circle-fill me-1"></i> LENGKAP & MEMENUHI</label>
                            </div>
                            <div class="form-check bg-dark bg-opacity-25 p-3" style="border-radius: 8px; border: 1px solid #334155;">
                                <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusPerbaikan" value="perbaikan" required {{ ($pengajuan->status == 'perbaikan' || $pengajuan->status == 'perbaikan') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-warning" for="statusPerbaikan"><i class="bi bi-exclamation-triangle-fill me-1"></i> TIDAK LENGKAP</label>
                            </div>'''

new_kesimpulan = '''                            @if($statusNormalized === 'diajukan')
                                <div class="form-check bg-dark bg-opacity-25 p-3 mb-2" style="border-radius: 8px; border: 1px solid #334155;">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusLengkap" value="menunggu_lampiran" required>
                                    <label class="form-check-label fw-bold text-success" for="statusLengkap"><i class="bi bi-check-circle-fill me-1"></i> SETUJUI PERMOHONAN AWAL</label>
                                </div>
                            @elseif($statusNormalized === 'evaluasi_724_tu')
                                <div class="form-check bg-dark bg-opacity-25 p-3 mb-2" style="border-radius: 8px; border: 1px solid #334155;">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusLengkap" value="evaluasi_724_audit" required>
                                    <label class="form-check-label fw-bold text-success" for="statusLengkap"><i class="bi bi-check-circle-fill me-1"></i> SELESAI EVALUASI (Lanjut Audit)</label>
                                </div>
                            @elseif($statusNormalized === 'evaluasi_724_audit')
                                <div class="form-check bg-dark bg-opacity-25 p-3 mb-2" style="border-radius: 8px; border: 1px solid #334155;">
                                    <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusLengkap" value="menunggu_ttd" required>
                                    <label class="form-check-label fw-bold text-success" for="statusLengkap"><i class="bi bi-check-circle-fill me-1"></i> SELESAI KEBENARAN (Generate Form)</label>
                                </div>
                            @endif
                            <div class="form-check bg-dark bg-opacity-25 p-3" style="border-radius: 8px; border: 1px solid #334155;">
                                <input class="form-check-input ms-0 me-2" type="radio" name="kesimpulan" id="statusPerbaikan" value="perbaikan" required {{ ($pengajuan->status == 'perbaikan') ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold text-warning" for="statusPerbaikan"><i class="bi bi-exclamation-triangle-fill me-1"></i> TOLAK / REVISI</label>
                            </div>'''
content = content.replace(old_kesimpulan, new_kesimpulan)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Done")
