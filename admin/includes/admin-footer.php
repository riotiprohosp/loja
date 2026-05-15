  </main>
  <footer class="admin-footer">
    <span>© <?= date('Y') ?> PROHOSP Distribuidora de Medicamentos LTDA</span>
    <a href="<?= e(url_base('index.php')) ?>">Ver loja</a>
  </footer>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= e(url_base('assets/js/admin.js')) ?>?v=<?= (int)filemtime(__DIR__ . '/../../assets/js/admin.js') ?>"></script>
</body>
</html>
