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
        <i class="bi bi-file-earmark-excel-fill text-success"></i>
        <span>Excel</span>
    </button>
    <button type="button" 
            class="btn btn-outline-danger fw-semibold d-inline-flex align-items-center gap-1"
            onclick="ClingestExport.toPdf('{{ $tableId }}', '{{ $filename }}', '{{ addslashes($title) }}')"
            title="Exporter le tableau au format PDF (.pdf)">
        <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
        <span>PDF</span>
    </button>
    <button type="button" 
            class="btn btn-outline-secondary fw-semibold d-inline-flex align-items-center gap-1"
            onclick="ClingestExport.printTable()"
            title="Imprimer ou enregistrer en PDF via le système">
        <i class="bi bi-printer-fill"></i>
        <span>Imprimer</span>
    </button>
</div>
