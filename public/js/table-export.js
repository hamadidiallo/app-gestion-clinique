/**
 * CLINGEST - Table Export Utility
 * Permet d'exporter n'importe quel tableau HTML Bootstrap vers Excel (.xlsx), PDF (.pdf) ou Impression.
 */

window.ClingestExport = {
    /**
     * Extrait les données nettoyées d'un tableau HTML (sans la colonne d'actions ni les formulaires/modales).
     * @param {string} tableId 
     * @returns {{ headers: string[], rows: string[][], footers: string[][] }}
     */
    extractTableData: function(tableId) {
        const table = document.getElementById(tableId);
        if (!table) {
            console.error(`Tableau avec id "${tableId}" introuvable.`);
            return { headers: [], rows: [], footers: [] };
        }

        // Identifier les index des colonnes d'actions à ignorer
        const ignoreColIndexes = new Set();
        const headerCells = table.querySelectorAll('thead tr th');
        headerCells.forEach((th, index) => {
            const text = th.innerText.trim().toLowerCase();
            if (text.includes('action') || th.classList.contains('no-export') || th.getAttribute('data-no-export') === 'true') {
                ignoreColIndexes.add(index);
            }
        });

        // Extraire l'en-tête (headers)
        const headers = [];
        headerCells.forEach((th, index) => {
            if (!ignoreColIndexes.has(index)) {
                headers.push(th.innerText.replace(/[\n\r]+/g, ' ').trim());
            }
        });

        // Extraire les lignes du corps (tbody)
        const rows = [];
        const bodyRows = table.querySelectorAll('tbody tr');
        bodyRows.forEach(tr => {
            // Ignorer les lignes de message vide ou masquées
            if (tr.classList.contains('no-export') || (tr.cells.length <= 1 && tr.innerText.toLowerCase().includes('aucun'))) {
                return;
            }

            const rowData = [];
            const cells = tr.cells;
            for (let index = 0; index < cells.length; index++) {
                if (ignoreColIndexes.has(index)) continue;
                
                const cell = cells[index];
                // Ignorer les formulaires/modales cachés dans la cellule s'il y en a
                let text = cell.innerText.replace(/[\n\r]+/g, ' ').trim();
                // Nettoyer les espaces multiples
                text = text.replace(/\s+/g, ' ');
                rowData.push(text);
            }
            if (rowData.length > 0) {
                rows.push(rowData);
            }
        });

        // Extraire le pied de page (tfoot) si présent
        const footers = [];
        const footRows = table.querySelectorAll('tfoot tr');
        footRows.forEach(tr => {
            const footData = [];
            const cells = tr.cells;
            for (let index = 0; index < cells.length; index++) {
                if (ignoreColIndexes.has(index)) continue;
                const cell = cells[index];
                let text = cell.innerText.replace(/[\n\r]+/g, ' ').trim();
                text = text.replace(/\s+/g, ' ');
                footData.push(text);
            }
            if (footData.length > 0) {
                footers.push(footData);
            }
        });

        return { headers, rows, footers };
    },

    /**
     * Exporte un tableau vers un fichier Excel (.xlsx)
     * @param {string} tableId 
     * @param {string} fileName 
     * @param {string} title 
     */
    toExcel: function(tableId, fileName = 'export_clingest', title = 'Rapport CLINGEST') {
        if (typeof XLSX === 'undefined') {
            alert('La bibliothèque d\'exportation Excel (SheetJS) n\'est pas encore chargée.');
            return;
        }

        const data = this.extractTableData(tableId);
        if (data.rows.length === 0 && data.headers.length === 0) {
            alert('Aucune donnée disponible à exporter.');
            return;
        }

        // Construire la matrice de données pour la feuille Excel
        const sheetData = [];
        sheetData.push([`CLINGEST - ${title}`]);
        sheetData.push([`Généré le : ${new Date().toLocaleString('fr-FR')}`]);
        sheetData.push([]); // Ligne vide
        sheetData.push(data.headers);

        data.rows.forEach(r => sheetData.push(r));

        if (data.footers.length > 0) {
            sheetData.push([]); // Ligne vide avant totaux
            data.footers.forEach(f => sheetData.push(f));
        }

        // Créer le classeur et la feuille
        const ws = XLSX.utils.aoa_to_sheet(sheetData);
        
        // Ajuster la largeur automatique des colonnes
        const colWidths = sheetData.reduce((acc, row) => {
            row.forEach((val, colIdx) => {
                const len = val ? val.toString().length : 10;
                acc[colIdx] = Math.max(acc[colIdx] || 10, Math.min(len + 3, 40));
            });
            return acc;
        }, []);
        ws['!cols'] = colWidths.map(w => ({ wch: w }));

        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Données');

        const cleanFilename = `${fileName.toLowerCase().replace(/[^a-z0-9_]/gi, '_')}_${new Date().toISOString().slice(0, 10)}.xlsx`;
        XLSX.writeFile(wb, cleanFilename);
    },

    /**
     * Exporte un tableau vers un document PDF (.pdf) avec en-tête et mise en page soignée.
     * @param {string} tableId 
     * @param {string} fileName 
     * @param {string} title 
     */
    toPdf: function(tableId, fileName = 'export_clingest', title = 'Rapport CLINGEST') {
        const { jsPDF } = window.jspdf || {};
        if (!jsPDF) {
            alert('La bibliothèque PDF (jsPDF) n\'est pas chargée.');
            return;
        }

        const data = this.extractTableData(tableId);
        if (data.rows.length === 0 && data.headers.length === 0) {
            alert('Aucune donnée disponible à exporter.');
            return;
        }

        // Utiliser le mode paysage (landscape) si plus de 5 colonnes
        const orientation = data.headers.length > 5 ? 'landscape' : 'portrait';
        const doc = new jsPDF({ orientation: orientation, unit: 'mm', format: 'a4' });

        // Titres & En-tête
        doc.setFontSize(16);
        doc.setTextColor(13, 110, 253); // Couleur primaire #0d6efd
        doc.text('CLINGEST - Gestion Clinique', 14, 15);

        doc.setFontSize(12);
        doc.setTextColor(33, 37, 41);
        doc.text(title, 14, 23);

        doc.setFontSize(9);
        doc.setTextColor(108, 117, 125);
        doc.text(`Exporté le : ${new Date().toLocaleString('fr-FR')}`, 14, 29);

        // Combinaison des données du tableau
        const body = [...data.rows];
        if (data.footers.length > 0) {
            data.footers.forEach(f => body.push(f));
        }

        doc.autoTable({
            head: [data.headers],
            body: body,
            startY: 34,
            styles: {
                fontSize: 8,
                cellPadding: 2.5,
                overflow: 'linebreak',
            },
            headStyles: {
                fillColor: [30, 41, 59], // #1e293b
                textColor: [255, 255, 255],
                fontStyle: 'bold',
                halign: 'left'
            },
            alternateRowStyles: {
                fillColor: [248, 250, 252] // #f8fafc
            },
            footStyles: {
                fillColor: [226, 232, 240], // #e2e8f0
                textColor: [15, 23, 42],
                fontStyle: 'bold'
            },
            didDrawPage: function(dataPage) {
                // Pied de page du document PDF
                const str = `Page ${doc.internal.getNumberOfPages()}`;
                doc.setFontSize(8);
                doc.setTextColor(150);
                const pageHeight = doc.internal.pageSize.height || doc.internal.pageSize.getHeight();
                const pageWidth = doc.internal.pageSize.width || doc.internal.pageSize.getWidth();
                doc.text(str, pageWidth - 20, pageHeight - 10);
                doc.text('CLINGEST © ' + new Date().getFullYear(), 14, pageHeight - 10);
            }
        });

        const cleanFilename = `${fileName.toLowerCase().replace(/[^a-z0-9_]/gi, '_')}_${new Date().toISOString().slice(0, 10)}.pdf`;
        doc.save(cleanFilename);
    },

    /**
     * Lance l'impression de la page filtrée sur le tableau concerné.
     */
    printTable: function() {
        window.print();
    }
};
