@props([
    'tableId',
    'title' => 'Rapport',
    'filename' => 'export',
    'size' => 'sm',
])

<div class="btn-group btn-group-{{ $size }} no-print" role="group" aria-label="Exportation des données">
    <button type="button" 
            class="btn btn-outline-success fw-semibold d-inline-flex align-items-center gap-1"
            onclick="ClingestExport.toExcel('{{ $tableId }}', '{{ $filename }}', '{{ addslashes($title) }}')"
            title="Exporter le tableau vers Microsoft Excel (.xlsx)">
        <i data-lucide="file-spreadsheet" class="lucide-sm text-success"></i>
        <span>Excel</span>
    </button>
    <button type="button" 
            class="btn btn-outline-danger fw-semibold d-inline-flex align-items-center gap-1"
            onclick="ClingestExport.toPdf('{{ $tableId }}', '{{ $filename }}', '{{ addslashes($title) }}')"
            title="Exporter le tableau au format PDF (.pdf)">
        <i data-lucide="file-text" class="lucide-sm text-danger"></i>
        <span>PDF</span>
    </button>
    <button type="button" 
            class="btn btn-outline-secondary fw-semibold d-inline-flex align-items-center gap-1"
            onclick="ClingestExport.printTable()"
            title="Imprimer ou enregistrer en PDF via le système">
        <i data-lucide="printer" class="lucide-sm"></i>
        <span>Imprimer</span>
    </button>
</div>
