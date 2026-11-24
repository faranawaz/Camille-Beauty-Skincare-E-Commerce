<?php include_once 'templates/app/header.php'; ?>
<style>
    .accordion-collapse {
        transition: max-height 0.3s ease-out;
        overflow: hidden;
    }

    .accordion-collapse.show {
        max-height: 1000px;
        transition: max-height 0.5s ease-in;
    }
</style>

<div class="container my-5">
    <h1 class="mb-4">Tata Cara Pengiriman</h1>

    <div class="accordion" id="paymentProcedureAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Menggunakan GoSend
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#paymentProcedureAccordion">
                <div class="accordion-body">
                    <p>Berikut adalah ketentuan pengiriman:</p>
                    <ol>
                        <li>Pilih produk yang ingin Anda beli dan tambahkan ke keranjang belanja Anda.</li>
                        <li>Masukkan alamat pengiriman yang berada di daerah Depok, Jawa Barat.</li>
                        <li>kami akan mengirim barang melalui Gosend</li>
                        <li>status akan berubah menjadi dikirim saat Gosend sudah di pesan</li>
                        <li>jangan lupa untuk memasukan nomor telp anda di profile</li>
                    </ol>
                    <p>Catatan: Ongkos kirim menggunakan GoSend akan ditanggung oleh customer.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var paymentAccordion = document.getElementById('paymentProcedureAccordion');
        var buttons = paymentAccordion.querySelectorAll('.accordion-button');

        buttons.forEach(function(button) {
            button.addEventListener('click', function() {
                var target = button.getAttribute('data-bs-target');
                var collapseElement = document.querySelector(target);

                if (collapseElement.classList.contains('show')) {
                    collapseElement.classList.remove('show');
                } else {
                    var allCollapses = paymentAccordion.querySelectorAll('.accordion-collapse');
                    allCollapses.forEach(function(collapse) {
                        collapse.classList.remove('show');
                    });
                    collapseElement.classList.add('show');
                }
            });
        });
    });
</script>

<?php include_once 'templates/app/footer.php'; ?>