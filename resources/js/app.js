/**
 * First we will load all of this project's JavaScript dependencies which
 * includes React and other helpers. It's a great starting point while
 * building robust, powerful web applications using React + Laravel.
 */

require("./bootstrap");

/**
 * Next, we will create a fresh React component instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

require("./components/Cart");
import $ from "jquery";
import "jquery-ui/ui/widgets/autocomplete";
import "select2/dist/css/select2.min.css";
import "select2/dist/js/select2.min.js";
import "select2";
// Importation DataTables avec Buttons
import "datatables.net-buttons-dt/css/buttons.dataTables.css";
import "datatables.net-buttons/js/buttons.html5";
import "datatables.net-buttons/js/buttons.print";

// Importation des dépendances pour Excel et PDF
import JSZip from "jszip";
import pdfMake from "pdfmake";

window.JSZip = JSZip; // Nécessaire pour l'export Excel
window.pdfMake = pdfMake; // Nécessaire pour l'export PDF
