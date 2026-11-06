<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Estado</th>
            <th scope="col">Fecha</th>
        </tr>
    </thead>
    <tbody>


        <?php

        foreach ($list_status as $key => $value) { ?>


            <tr>
                <td><?= $key + 1 ?></td>
                <td><?= $value->name_status ?></td>
                <td><?= $value->status_date ?></td>
            </tr>

        <?php

        }

        ?>


    </tbody>
</table>