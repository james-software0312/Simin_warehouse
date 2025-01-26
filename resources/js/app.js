

// Import our custom CSS
import '../sass/app.scss'
import 'bootstrap'
import DataTable from 'datatables.net-bs5';
import jszip from 'jszip'; // For Excel export
import pdfmake from 'pdfmake'; // For PDF export 
import 'datatables.net-buttons-bs5';

import JSZip from 'jszip'; // For Excel export
import PDFMake from 'pdfmake/build/pdfmake'; // For PDF export
// import * as pdfFonts from 'pdfmake/build/vfs_fonts';
// PDFMake.vfs = pdfFonts.pdfMake.vfs;

PDFMake.fonts = {
  Roboto: {
    normal: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Regular.ttf',
    bold: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Medium.ttf',
    italics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-Italic.ttf',
    bolditalics: 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.66/fonts/Roboto/Roboto-MediumItalic.ttf',
  },
};
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-buttons/js/buttons.colVis.mjs';


DataTable.Buttons.jszip(JSZip);
DataTable.Buttons.pdfMake(PDFMake);

import 'datatables.net-responsive-bs5';

import Chart from 'chart.js/auto';
window.Chart = Chart;
// Import all of Bootstrap's JS
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;
import * as bootstrap from 'bootstrap';

$(function() {
	
    "use strict"
  /* Trigger mobile navigation
  ----------------------------------------------------- */
  

    // jQuery('body').on("click", function()  {
    //     jQuery('.sidebar-wrapper, .close-mobile-menu').removeClass('active');
    // });
    const sidebar = jQuery('.sidebar-wrapper, .close-mobile-menu');

    // Add event listener for clicks on the document
    jQuery(document).on('click', function(event) {
      // Check if the clicked element is not inside the sidebar and the sidebar is active
      if (!sidebar.is(event.target) && sidebar.has(event.target).length === 0 && sidebar.hasClass('active')) {
        // Hide the sidebar by removing the 'active' class
        sidebar.removeClass('active');
      }
    });
    jQuery('.menu-trigger').on("click", function(event) {
        jQuery('.sidebar-wrapper, .close-mobile-menu').toggleClass('active');
      event.stopPropagation();
    });
    
    /* Handle Date Time Picker
  ----------------------------------------------------- */

    // $('#datetimepicker1').datetimepicker({
    //   inline: true,
    // });

  /* Trigger Mark as read
  ----------------------------------------------------- */  
    $('.mark-read').on("click", function(event)  {
       $('.item').addClass('d-none');
       $(this).addClass('d-none');
       $('.no-message').removeClass('d-none');
       event.stopPropagation();
    });

    /* Add Modal
    ----------------------------------------------------*/

    jQuery('.addform').on("click", function() {
        jQuery('#addModal').modal();
    });

   /* Dropdown menu
  ----------------------------------------------------- */  
  jQuery('.parent-menu').on("click", function() {
    jQuery(this).toggleClass('parent-menu-active');
      });
    
   /* Preloader
  ----------------------------------------------------- */     
     setTimeout(function() {
      jQuery('#preloader').remove();
      $('#main-wrapper').addClass('show');
    },1800); 

});
