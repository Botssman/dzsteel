<?= Form::open(['class' => 'layout']) ?>

    <div class="layout-row">
        <?= $this->importRender() ?>
    </div>

    <div class="form-buttons">
        <button
            type="submit"
            data-control="popup"
            data-handler="onImportLoadForm"
            data-keyboard="false"
            class="btn btn-primary">
            Импорт
        </button>
    </div>

<?= Form::close() ?>
