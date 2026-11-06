<div class="container">

    <h3>Archivos generados</h3>

    <table class="table table-bordered">

        <thead>
            <tr>
                <th>Archivo</th>
                <th>Acción</th>
            </tr>
        </thead>

        <tbody>

            <?php foreach ($archivos as $archivo): ?>

                <tr>

                    <td><?= $archivo['nombre'] ?></td>

                    <td>

                        <a href="<?= site_url(
                                        'zip_splitter/descargar?token='
                                            . $archivo['token']
                                            . '&archivo='
                                            . $archivo['nombre']
                                    ) ?>"
                            class="btn btn-success btn-sm">

                            Descargar

                        </a>

                    </td>

                </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>