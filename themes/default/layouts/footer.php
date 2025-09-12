<?php
/**
 * themes/default/layouts/footer.php
 * ZPanel default theme page footer
 * Revision 1 [9-12-2025]
 * Zee ^_~
 */
if (!isset($config) || !is_array($config)) {
    $config = include __DIR__ . '/../../../config.php';
}

$assetsUrl = BASE_URL . '/assets';
?>
    </main> <!-- End of main container -->

    <!-- Footer -->
    <footer class="bg-dark text-light py-3 mt-5 shadow-sm">
        <div class="container text-center">
            <small>
                ZPanel version 1.0. By <a href="https://discord.com/users/679171391395856394" target="_blank">Zee </a>
            </small>
        </div>
    </footer>

    <!-- Bootstrap Bundle -->
    <script src="<?= $assetsUrl ?>/bootstrap/js/bootstrap.bundle.min.js"></script>
	<!-- Load theme JS -->
	<script src="<?= $assetsUrl ?>themes/default/js/theme.js"></script>
</body>
</html>
