<div data-control="toolbar loader-container">
    <a
        href="<?= Backend::url('ontarget/catalog/categories/create') ?>"
        class="btn btn-primary">
        <i class="icon-plus"></i>
        <?= __("Новая :name", ['name' => 'категория']) ?>
    </a>

    <div class="toolbar-divider"></div>

    <button
        class="btn btn-secondary"
        data-request="onDelete"
        data-request-message="<?= __("Deleting...") ?>"
        data-request-confirm="<?= __("Are you sure?") ?>"
        data-list-checked-trigger
        data-list-checked-request
        disabled>
        <i class="icon-delete"></i>
        <?= __("Delete") ?>
    </button>
    <div class="toolbar-divider"></div>
    <a href="<?= Backend::url('ontarget/catalog/categories/export') ?>" class="btn btn-default">
        <i class="icon-download"></i> Экспорт
    </a>
    <a href="<?= Backend::url('ontarget/catalog/categories/import') ?>" class="btn btn-default">
        <i class="icon-upload"></i> Импорт
    </a>
</div>
