<?php $i = 0; ?>
<?php foreach($properties as $property): $i++; ?>
    <div class="form-group span-<?= $i % 2 ? 'left' : 'right' ?>">
        <?= $this->createFormWidget($property); ?>
    </div>
<?php endforeach; ?>
