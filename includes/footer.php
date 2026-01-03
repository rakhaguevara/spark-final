<?php
// footer.php
?>
<footer class="spark-footer">
    <div class="footer-container">

        <!-- BRAND -->
        <div class="footer-brand">
            <img src="<?= BASEURL; ?>/assets/img/logoSpark.png" alt="SPARK Logo">
            <p>
                Smart parking platform that helps you
                reserve parking effortlessly and transparently.
            </p>

            <span class="status">
                ● All systems operational
            </span>
        </div>

        <!-- COLUMN -->
        <div class="footer-col">
            <h5>Product</h5>
            <a href="#">Parking Reservation</a>
            <a href="#">Pricing</a>
            <a href="#">Locations</a>
            <a href="#">API</a>
        </div>

        <div class="footer-col">
            <h5>Company</h5>
            <a href="#">About SPARK</a>
            <a href="#">Careers</a>
            <a href="#">Changelog</a>
            <a href="#">Media Kit</a>
        </div>

        <div class="footer-col">
            <h5>Support</h5>
            <a href="#">Help Center</a>
            <a href="#">Contact</a>
            <a href="#">Terms</a>
            <a href="#">Privacy</a>
        </div>

    </div>

    <!-- BOTTOM -->
    <div class="footer-bottom">
        <span>© <?= date('Y') ?> Parkster Business. All rights reserved.</span>

        <div class="footer-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms</a>
            <a href="#">Code of Conduct</a>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Custom JS -->
<script src="<?= BASEURL; ?>/assets/js/navbar.js"></script>
<script src="<?= BASEURL; ?>/assets/js/navbar-scroll.js"></script>
<script src="<?= BASEURL; ?>/assets/js/map.js"></script>
<script src="<?= BASEURL ?>/assets/js/hero-typing.js"></script>
<script src="<?= BASEURL ?>/assets/js/navbar-underline.js"></script>




</body>
</html>
