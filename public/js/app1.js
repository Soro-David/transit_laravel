// Importation DataTables avec Buttons
import "datatables.net-buttons-dt/css/buttons.dataTables.css";
import "datatables.net-buttons/js/buttons.html5";
import "datatables.net-buttons/js/buttons.print";

// Importation des dépendances pour Excel et PDF
import JSZip from "jszip";
import pdfMake from "pdfmake";

window.JSZip = JSZip; // Nécessaire pour l'export Excel
window.pdfMake = pdfMake; // Nécessaire pour l'export PDF
