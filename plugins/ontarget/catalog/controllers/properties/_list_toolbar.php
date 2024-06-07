<div data-control="toolbar loader-container">
    <a
        href="<?= Backend::url('ontarget/catalog/properties/create') ?>"
        class="btn btn-primary">
        <i class="icon-plus"></i>
        <?= __("Новая :name", ['name' => 'характеристика']) ?>
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
</div>