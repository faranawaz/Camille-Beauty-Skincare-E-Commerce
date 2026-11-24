 </div>
 <!-- CoreUI and necessary plugins-->
 <script src="<?= BASE_URL ?>assets/vendors/@coreui/coreui/js/coreui.bundle.min.js"></script>
 <script src="<?= BASE_URL ?>assets/vendors/simplebar/js/simplebar.min.js"></script>
 <script>
   const header = document.querySelector('header.header');

   document.addEventListener('scroll', () => {
     if (header) {
       header.classList.toggle('shadow-sm', document.documentElement.scrollTop > 0);
     }
   });
 </script>
 <script src="<?= BASE_URL ?>assets/vendors/chart.js/js/chart.umd.js"></script>
 <script src="<?= BASE_URL ?>assets/js/main.js"></script>

 <script>
   $(document).ready(function() {
     $('#dataTable').DataTable();

   });
 </script>
 </body>

 </html>