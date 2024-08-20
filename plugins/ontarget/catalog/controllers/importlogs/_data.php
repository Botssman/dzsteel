
<div class="control-list">
    <table class="table data">
        <thead>
        <tr>
            <th><span>№ строки</span></th>
            <th><span>Товар</span></th>
            <th><span>Успешно</span></th>
            <th><span>Результат</span></th>
            <th><span>Действия</span></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($model->results as $row): ?>
            <tr <?php if(!$row['success']): ?>class="negative"<?php endif; ?>
                <td><?=$row['row']?></td>
                <td>
                    <a href="/admin/ontarget/catalog/products/update/<?=$row['product']['id'] ?? ''?>">
                        <?=$row['product']['name'] ?? ''?>
                    </a>
                </td>
                <td>
                    <?=$row['success'] ? '<span class="oc-icon-circle text-success">Успешно</span>':'<span class="oc-icon-circle text-danger">Ошибка</span>'?>
                </td>
                <td><?=$row['message']?></td>
                <td>&nbsp;</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<pre>
    <?php print_r($model->results); ?>
</pre>
