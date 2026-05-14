<?php $ajustes = $ajustes ?? buscar_ajustes($pdo); ?>
<footer class="footer">
  <div class="container footer-grid">
    <div>
      <h3><?= e($ajustes['nome_loja'] ?? 'PROHOSP') ?></h3>
      <p><?= e($ajustes['texto_footer'] ?? 'Materiais hospitalares, medicamentos e descartáveis com gestão simples e segura.') ?></p>
    </div>
    <div><h4>Contato</h4><p><?= e($ajustes['contato_footer'] ?? 'contato@PROHOSP.local') ?></p></div>
    <div><h4>Institucional</h4><a href="#">Privacidade</a><a href="#">Termos</a></div>
  </div>
  <center><div class="container copyright">© <?= date('Y') ?> - PROHOSP Distribuidora de Medicamentos LTDA</div></center>
</footer>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="<?= e(url_base('assets/js/main.js')) ?>"></script>
</body>
</html>
