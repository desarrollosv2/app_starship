<div class="container m-top">
    <div class="table-responsive">
        <table  class="table   table-hover" style="width:99%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>ID</th>
                    <th>FECHA</th>
                    <th>DESCRIPCION</th>
                    <th>REFERENCIA</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (isset($lista)) {
                        $c=1;
                        foreach ($lista as $row) {  ?>
                <tr>
                    <td><?php echo $c; ?></td>
                    <td><?php echo $row->idtraspaso; ?></td>
                    <td><?php echo $row->fecha; ?></td>
                    <td><?php echo $row->descripcion; ?></td>
                    <td><?php echo $row->referencia; ?></td>
                </tr>
                <?php $c=$c+1;
                        }
                    }
                    ?>
            </tbody>
        </table>
    </div>
</div>
