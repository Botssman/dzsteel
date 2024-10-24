<div data-control="toolbar loader-container">
    <a
        href="<?= Backend::url('ontarget/catalog/products/create') ?>"
        class="btn btn-primary">
        <i class="icon-plus"></i>
        <?= __("Новый :name", ['name' => 'товар']) ?>
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
    <a href="<?= Backend::url('ontarget/catalog/products/export') ?>" class="btn btn-default">
        <i class="icon-download"></i> Экспорт
    </a>
    <a href="<?= Backend::url('ontarget/catalog/products/import') ?>" class="btn btn-default">
        <i class="icon-upload"></i> Импорт
    </a>
</div>
