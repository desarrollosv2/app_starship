    </main>
</div>

<div id="loader" style="display: none;">
    <div class="spinner-border text-light" role="status"><span class="visually-hidden">Cargando...</span></div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
<script>
    var BASE_URL = <?php echo json_encode(base_url()); ?>;
</script>
<script src="<?php echo base_url('assets/js/app.js'); ?>"></script>
<?php foreach ($scripts as $js): ?>
<script src="<?php echo base_url('assets/js/' . $js); ?>"></script>
<?php endforeach; ?>
</body>
</html>
